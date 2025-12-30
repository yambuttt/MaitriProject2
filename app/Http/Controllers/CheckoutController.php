<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use App\Services\DigiflazzService;
use App\Models\User;
use App\Models\OrderPayment;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    public function checkoutSaldo(Request $request, DigiflazzService $digiflazzService)
    {
        $user = Auth::user();

        // 1. Validasi data dari form (UI tidak diubah)
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['required', 'exists:product_variants,id'],
            'target' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'pin' => ['required', 'string'],
        ]);

        // 2. Cek PIN pembayaran
        if (!$user->checkPaymentPin($validated['pin'])) {
            return back()
                ->withErrors(['pin' => 'PIN pembayaran salah.'])
                ->withInput();
        }

        // 3. Ambil varian produk & nominal yang harus dibayar
        $variant = ProductVariant::findOrFail($validated['variant_id']);
        $amount = (int) $variant->final_price; // nominal yang diambil dari varian (sudah + markup)
        $master = $variant->digiflazzVariant;
        if (
            $master && (
                $master->seller_product_status === false ||
                str_contains(strtolower((string) $master->status), 'gangguan')
            )
        ) {
            return back()
                ->withErrors(['variant' => 'Varian yang kamu pilih sedang gangguan / nonaktif dari penyedia. Silakan pilih nominal lain.'])
                ->withInput();
        }

        // 4. Cek saldo Maitri cukup
        if ($user->maitri_balance < $amount) {
            return back()
                ->withErrors(['saldo' => 'Saldo Maitri tidak mencukupi.'])
                ->withInput();
        }

        // 5. Transaksi database: kurangi saldo + buat order + catat order_payment
        $order = null;
        $affiliateId = app(\App\Services\AffiliateAttributionService::class)
            ->resolveAffiliateUserId($request);

        DB::transaction(function () use ($user, $variant, $validated, $amount, &$order, $affiliateId) {
            // generate kode unik untuk order
            $code = Order::generateCode();

            // refresh user biar saldo terbaru
            $user->refresh();

            if ($user->maitri_balance < $amount) {
                throw new \RuntimeException('Saldo Maitri tidak mencukupi.');
            }

            // Kurangi saldo Maitri
            $user->maitri_balance -= $amount;
            $user->save();


            // Buat order
            $order = Order::create([
                'user_id' => $user->id,
                'code' => $code,

                'product_id' => $validated['product_id'],
                'product_variant_id' => $validated['variant_id'],
                'buyer_sku_code' => $variant->buyer_sku_code,
                'target' => $validated['target'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,

                'provider' => 'digiflazz',
                'provider_ref_id' => null,
                'provider_status' => null,
                'provider_message' => null,
                'provider_rc' => null,
                'provider_sn' => null,
                'provider_price' => null,
                'provider_raw' => null,

                'base_price' => (int) $variant->base_price,
                'subtotal' => $amount,
                'admin_fee' => 0,
                'total' => $amount,
                'profit' => $amount - (int) $variant->base_price,

                // 🔹 field baru pembayaran
                'payment_method' => 'wallet',
                'payment_status' => 'paid',      // karena saldo langsung dipotong

                // metode teknis internal order (biarkan seperti sebelumnya)
                'method' => 'saldo_maitri',

                // status order (untuk proses ke Digiflazz)
                'status' => 'processing',
                'affiliate_user_id' => $affiliateId,
                'affiliate_attributed_at' => $affiliateId ? now() : null,

            ]);

            // Catat pembayaran di order_payments
            OrderPayment::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'method' => 'wallet',
                'provider' => 'wallet',
                'amount' => $amount,
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        });

        // 6. Hit Digiflazz untuk bikin transaksi sebenarnya
        try {
            $providerData = $digiflazzService->createTransaction($order, $variant);
            if (
                ($providerData['customer_no'] ?? null) !== $order->target ||
                ($providerData['buyer_sku_code'] ?? null) !== $variant->buyer_sku_code
            ) {
                throw new \RuntimeException('Response Digiflazz tidak cocok dengan order (kemungkinan kena transaksi lama).');
            }


            $status = strtolower($providerData['status'] ?? '');
            $mappedStatus = match ($status) {
                'sukses', 'success' => 'success',
                'gagal', 'failed' => 'failed',
                default => 'processing',
            };

            $updateData = [
                'provider_ref_id' => $providerData['ref_id'] ?? null,
                'provider_status' => $providerData['status'] ?? null,
                'provider_message' => $providerData['message'] ?? null,
                'provider_rc' => $providerData['rc'] ?? null,
                'provider_sn' => $providerData['sn'] ?? null,
                'provider_price' => $providerData['sell_price'] ?? null,
                'provider_raw' => $providerData,

                'status' => $mappedStatus,
                'paid_at' => $mappedStatus === 'success' ? now() : null,
                'completed_at' => $mappedStatus === 'success' ? now() : null,
                'failed_at' => $mappedStatus === 'failed' ? now() : null,
            ];

            // 🔴 DI SINI: kalau Digiflazz bilang gagal → profit harus 0
            if ($mappedStatus === 'failed') {
                $updateData['profit'] = 0;
            }

            $order->update($updateData);
            if ($mappedStatus === 'failed') {
                app(\App\Services\OrderRefundService::class)->refundToWalletIfEligible(
                    $order,
                    'Digiflazz direct response failed (wallet)'
                );
            }
            // kirim notifikasi jika sukses
            if ($mappedStatus === 'success') {
                app(\App\Services\OrderNotificationService::class)
                    ->notifySuccess($order);
                app(\App\Services\AffiliateRewardService::class)->awardForDigiflazzSuccess($order);

            }

        } catch (\Throwable $e) {
            // fallback ref_id yang pasti sama dengan yang dipakai Digiflazz
            $fallbackRefId = 'MP2-' . $order->code;

            $order->update([
                'status' => 'failed',

                // pastikan provider terisi
                'provider' => $order->provider ?: 'digiflazz',

                // simpan ref_id walaupun gagal, supaya tidak pernah dipakai order lain
                'provider_ref_id' => $order->provider_ref_id ?: $fallbackRefId,

                'provider_status' => 'ERROR',
                'provider_message' => $e->getMessage(),
                'provider_raw' => [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                ],

                // tandai kapan gagal
                'failed_at' => $order->failed_at ?? now(),
                'profit' => 0,
            ]);
            app(\App\Services\OrderRefundService::class)->refundToWalletIfEligible(
                $order,
                'Exception when creating Digiflazz transaction (wallet)'
            );

        }


        // 7. Redirect ke halaman invoice berdasarkan kode
        return redirect()
            ->route('invoices.show', $order->code)
            ->with('status', 'success')
            ->with('message', 'Pembayaran berhasil menggunakan Saldo Maitri.');
    }


    public function showByCode(string $code)
    {
        // Ambil order berdasarkan kode invoice, misal MP-00001
        $order = Order::where('code', $code)->firstOrFail();

        // Pastikan hanya pemilik order yang boleh lihat
        // if (auth()->id() !== $order->user_id) {
        //     abort(403);
        // }

        // Pakai view invoice yang sama seperti sebelumnya
        return view('invoices.show', compact('order'));
    }
    /**
     * Detail order sederhana
     */
    public function show(Order $order)
    {
        // // Pastikan hanya pemilik order yang boleh lihat
        // if (auth()->id() !== $order->user_id) {
        //     abort(403);
        // }

        return view('invoices.show', compact('order'));
    }
    public function status(string $code)
    {
        $order = Order::where('code', $code)->firstOrFail();

        return response()->json([
            'status' => $order->status,
        ]);
    }

    /**
     * Checkout menggunakan Paydisini (QRIS/VA/Alfamart/Indomaret).
     *
     * Route: POST /checkout/paydisini
     */
    public function checkoutPaydisini(Request $request)
    {
        // user boleh null (guest)
        $user = Auth::user();

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['required', 'exists:product_variants,id'],
            'target' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'payment_channel' => ['required', 'in:qris,va_mandiri,alfamart,indomaret'],
        ]);

        $variant = ProductVariant::with('digiflazzVariant')->findOrFail($validated['variant_id']);
        $amount = (int) $variant->final_price;

        $master = $variant->digiflazzVariant;
        if (
            $master && (
                $master->seller_product_status === false ||
                str_contains(strtolower((string) $master->status), 'gangguan')
            )
        ) {
            return back()
                ->withErrors(['variant' => 'Varian yang kamu pilih sedang gangguan / nonaktif dari penyedia. Silakan pilih nominal lain.'])
                ->withInput();
        }


        $channel = $validated['payment_channel'];

        $serviceId = match ($channel) {
            'qris' => 11,
            'va_mandiri' => 5,
            'alfamart' => (int) env('PAYDISINI_ALFAMART_SERVICE_ID'),
            'indomaret' => (int) env('PAYDISINI_INDOMARET_SERVICE_ID'),
        };

        if (!$serviceId) {
            return back()
                ->withErrors(['payment_channel' => 'Service ID Paydisini untuk channel ini belum diset.'])
                ->withInput();
        }

        $apiKey = env('PAYDISINI_API_KEY');
        $baseUrl = rtrim(env('PAYDISINI_BASE_URL', 'https://api.paydisini.co.id/v1/'), '/');
        $validTime = (int) env('PAYDISINI_VALID_TIME', 1800);

        $order = null;
        $payment = null;
        $affiliateId = app(\App\Services\AffiliateAttributionService::class)
            ->resolveAffiliateUserId($request);


        DB::transaction(function () use ($user, $variant, $validated, $amount, $channel, $serviceId, &$order, &$payment, $affiliateId) {
            $code = Order::generateCode();

            $order = Order::create([
                'user_id' => $user?->id,     // 👈 boleh null
                'code' => $code,
                'product_id' => $validated['product_id'],
                'product_variant_id' => $validated['variant_id'],
                'buyer_sku_code' => $variant->buyer_sku_code,
                'target' => $validated['target'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,

                'provider' => 'digiflazz',
                'provider_ref_id' => null,
                'provider_status' => null,
                'provider_message' => null,
                'provider_rc' => null,
                'provider_sn' => null,
                'provider_price' => null,
                'provider_raw' => null,

                'base_price' => (int) $variant->base_price,
                'subtotal' => $amount,
                'admin_fee' => 0,
                'total' => $amount,
                'profit' => $amount - (int) $variant->base_price,

                'payment_method' => 'paydisini_' . $channel,
                'payment_status' => 'pending',
                'method' => 'saldo_maitri', // biarkan dulu, nanti kalau mau bisa bikin enum baru
                'status' => 'pending',
                'affiliate_user_id' => $affiliateId,
                'affiliate_attributed_at' => $affiliateId ? now() : null,
            ]);

            $uniqueCode = 'ORDPAY' . $order->id . now()->format('ymdHis');

            $payment = OrderPayment::create([
                'order_id' => $order->id,
                'user_id' => $user?->id,            // 👈 boleh null
                'method' => 'paydisini_' . $channel,
                'provider' => 'paydisini',
                'amount' => $amount,
                'status' => 'pending',
                'paydisini_unique_code' => $uniqueCode,
                'paydisini_service_id' => (string) $serviceId,
            ]);
        });

        $uniqueCode = $payment->paydisini_unique_code;

        $signature = md5($apiKey . $uniqueCode . $serviceId . $amount . $validTime . 'NewTransaction');

        $payload = [
            'key' => $apiKey,
            'request' => 'new',
            'unique_code' => $uniqueCode,
            'service' => $serviceId,
            'amount' => $amount,
            'note' => 'Pembayaran pesanan ' . $order->code . ($user ? (' user ' . $user->id) : ' (guest)'),
            'valid_time' => $validTime,
            'type_fee' => 1,
            'payment_guide' => false,
            'callback_count' => 3,
            'signature' => $signature,
        ];

        $response = Http::asForm()->post($baseUrl . '/', $payload);

        if (!$response->successful()) {
            // kalau error HTTP, tandai payment & order gagal
            $payment->update([
                'status' => 'canceled',
                'response_payload' => ['http_error' => $response->status()],
            ]);

            $order->update([
                'payment_status' => 'canceled',
                'status' => 'failed',
                'profit' => 0,
            ]);

            return back()
                ->withErrors(['payment' => 'Gagal menghubungi Paydisini (HTTP ' . $response->status() . ').'])
                ->withInput();
        }

        $json = $response->json();

        if (!isset($json['success']) || $json['success'] !== true) {
            $payment->update([
                'status' => 'canceled',
                'response_payload' => $json,
            ]);

            $order->update([
                'payment_status' => 'canceled',
                'status' => 'failed',
                'profit' => 0,
            ]);

            return back()
                ->withErrors(['payment' => 'Paydisini error: ' . ($json['msg'] ?? 'unknown')])
                ->withInput();
        }

        // sukses membuat transaksi di Paydisini
        $data = $json['data'] ?? [];

        $payment->update([
            'paydisini_pay_id' => $data['pay_id'] ?? null,
            'response_payload' => $json,
            'expired_at' => now()->addSeconds($validTime),
        ]);

        // redirect ke halaman pembayaran order
        return redirect()->route('orders.payment.show', [$order, $payment]);

    }

    /**
     * Halaman pembayaran untuk sebuah order (lihat QR/VA/kode minimarket).
     */
    public function showPaydisiniPayment(Order $order, OrderPayment $payment)
    {
        // Pastikan payment memang milik order ini
        if ($payment->order_id !== $order->id) {
            abort(404);
        }

        $raw = $payment->response_payload;
        if (is_string($raw)) {
            $payload = $raw !== '' ? (json_decode($raw, true) ?: []) : [];
        } elseif (is_array($raw)) {
            $payload = $raw;
        } else {
            $payload = [];
        }

        $data = $payload['data'] ?? [];

        // hitung expired
        if ($payment->expired_at) {
            $expiresAt = $payment->expired_at->copy();
        } else {
            $seconds = (int) config('services.paydisini.valid_time', 1800);
            $expiresAt = now()->addSeconds($seconds);
        }

        return view('invoices.payment', compact('order', 'payment', 'data', 'expiresAt'));
    }



    /**
     * AJAX: cek status pembayaran di Paydisini berdasarkan OrderPayment.
     */
    public function checkPaymentStatus(OrderPayment $payment)
    {
        $payment->refresh();
        $order = $payment->order()->first(); // atau $payment->order

        return response()->json([
            'ok' => true,
            'status' => $payment->status,      // pending/paid/canceled
            'order_status' => $order?->status, // opsional
        ]);
    }


    /**
     * Dipanggil setelah pembayaran NON-saldo berhasil:
     * kirim order ke Digiflazz dan update status.
     */
    public function processOrderAfterPayment(Order $order, DigiflazzService $digiflazzService): void
    {
        $variant = ProductVariant::find($order->product_variant_id);

        if (!$variant) {
            $order->update([
                'status' => 'failed',
                'provider_status' => 'ERROR',
                'provider_message' => 'Variant produk tidak ditemukan.',
            ]);
            return;
        }

        try {
            $providerData = $digiflazzService->createTransaction($order, $variant);
            // sanity check: response harus cocok dengan order yang baru dibuat
            if (
                ($providerData['customer_no'] ?? null) !== $order->target ||
                ($providerData['buyer_sku_code'] ?? null) !== $variant->buyer_sku_code
            ) {
                throw new \RuntimeException('Response Digiflazz tidak cocok dengan order (kemungkinan kena transaksi lama).');
            }


            $status = strtolower($providerData['status'] ?? '');
            $mappedStatus = match ($status) {
                'sukses', 'success' => 'success',
                'gagal', 'failed' => 'failed',
                default => 'processing',
            };

            $updateData = [
                'provider_ref_id' => $providerData['ref_id'] ?? null,
                'provider_status' => $providerData['status'] ?? null,
                'provider_message' => $providerData['message'] ?? null,
                'provider_rc' => $providerData['rc'] ?? null,
                'provider_sn' => $providerData['sn'] ?? null,
                'provider_price' => $providerData['sell_price'] ?? null,
                'provider_raw' => $providerData,

                'status' => $mappedStatus,
                'paid_at' => $mappedStatus === 'success' ? now() : $order->paid_at,
                'completed_at' => $mappedStatus === 'success' ? now() : $order->completed_at,
                'failed_at' => $mappedStatus === 'failed' ? now() : $order->failed_at,
            ];

            // kalau transaksi akhirnya gagal → profit 0
            if ($mappedStatus === 'failed') {
                $updateData['profit'] = 0;
            }

            $order->update($updateData);
            if ($mappedStatus === 'failed') {
                app(\App\Services\OrderRefundService::class)->refundToWalletIfEligible(
                    $order,
                    'Digiflazz failed after Paydisini payment'
                );
            }

            // kirim notifikasi jika sukses
            if ($mappedStatus === 'success') {
                app(\App\Services\OrderNotificationService::class)
                    ->notifySuccess($order);
                app(\App\Services\AffiliateRewardService::class)->awardForDigiflazzSuccess($order);

            }

        } catch (\Throwable $e) {
            $fallbackRefId = 'MP2-' . $order->code;

            $order->update([
                'status' => 'failed',

                'provider' => $order->provider ?: 'digiflazz',
                'provider_ref_id' => $order->provider_ref_id ?: $fallbackRefId,

                'provider_status' => 'ERROR',
                'provider_message' => $e->getMessage(),
                'provider_raw' => [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                ],

                'failed_at' => $order->failed_at ?? now(),
                'profit' => 0,
            ]);
            app(\App\Services\OrderRefundService::class)->refundToWalletIfEligible(
                $order,
                'Exception when creating Digiflazz transaction (after Paydisini)'
            );
        }

    }

    public function expirePayment(OrderPayment $payment)
    {
        $user = Auth::user();

        if ($payment->user_id !== $user->id) {
            abort(403);
        }

        // kalau sudah final, tidak usah diapa-apakan
        if (in_array($payment->status, ['paid', 'canceled', 'expired'], true)) {
            return response()->json(['ok' => true, 'status' => $payment->status]);
        }

        $order = $payment->order;

        $payment->update([
            'status' => 'expired',
            'expired_at' => now(),
        ]);

        // update order juga
        $order->update([
            'payment_status' => 'expired',
            'status' => 'failed', // atau 'canceled' sesuai kebijakanmu
            'profit' => 0,
        ]);

        return response()->json(['ok' => true, 'status' => 'expired']);
    }







}
