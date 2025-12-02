<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderPayment;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MarketplaceCheckoutController extends Controller
{
    /**
     * Step 1: setelah user pilih varian, buat draft order.
     * Route: POST /marketplace/product/{product:slug}/checkout
     */
    public function createOrder(Request $request, MarketplaceProduct $product)
    {
        $data = $request->validate([
            'variant_id' => ['required', 'exists:marketplace_variants,id'],
        ]);

        $variant = MarketplaceVariant::where('id', $data['variant_id'])
            ->where('marketplace_product_id', $product->id)
            ->where('is_active', true)
            ->firstOrFail();

        $lastId = MarketplaceOrder::max('id') ?? 0;
        $invoiceNumber = 'MPM-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

        $user = Auth::user();

        $order = MarketplaceOrder::create([
            'invoice_number'         => $invoiceNumber,
            'user_id'                => $user?->id,
            'marketplace_product_id' => $product->id,
            'marketplace_variant_id' => $variant->id,
            'customer_email'         => $user?->email ?? '',
            'customer_phone'         => '',
            'price'                  => (int) $variant->price,
            'fee'                    => 0,
            'total_amount'           => (int) $variant->price,
            'payment_method'         => 'not_set',
            'payment_status'         => 'not_paid',
            'status'                 => 'not_paid',
        ]);

        return redirect()->route('marketplace.checkout.show', $order);
    }

    /**
     * Step 2: halaman checkout marketplace.
     * Route: GET /marketplace/orders/{order:invoice_number}/checkout
     */
    public function showCheckout(MarketplaceOrder $order)
    {
        $order->load(['product', 'variant']);
        return view('marketplace.checkout', compact('order'));
    }

    /**
     * Step 3: proses form checkout (isi email, hp, catatan, metode bayar).
     * Route: POST /marketplace/orders/{order:invoice_number}/checkout
     */
    public function processCheckout(Request $request, MarketplaceOrder $order)
    {
        $data = $request->validate([
            'customer_email' => ['required', 'email'],
            'customer_phone' => ['nullable', 'string'],
            'user_note'      => ['nullable', 'string'],
            'payment_method' => [
                'required',
                'in:wallet,paydisini_qris,paydisini_va_mandiri,paydisini_alfamart,paydisini_indomaret',
            ],
        ]);

        // update data order
        $order->update([
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'],
            'user_note'      => $data['user_note'] ?? null,
            'payment_method' => $data['payment_method'],
        ]);

        // ==========================
        //  A. Pembayaran pakai WALLET
        // ==========================
        if ($data['payment_method'] === 'wallet') {
            $user = Auth::user();
            if (!$user) {
                return back()
                    ->withErrors(['payment_method' => 'Harus login untuk menggunakan Saldo Maitri.'])
                    ->withInput();
            }

            $amount = (int) $order->total_amount;

            if ($user->maitri_balance < $amount) {
                return back()
                    ->withErrors(['payment_method' => 'Saldo Maitri tidak mencukupi.'])
                    ->withInput();
            }

            // kurangi saldo
            $user->maitri_balance -= $amount;
            $user->save();

            // tandai order sudah dibayar, menunggu diproses admin
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'paid_received',
                'paid_at'        => now(),
            ]);

            return redirect()->route('marketplace.invoice.show', $order);
        }

        // ======================================
        //  B. Pembayaran via Paydisini (gateway)
        // ======================================

        // mapping method -> channel + service_id seperti di CheckoutController
        $method  = $data['payment_method']; // paydisini_qris, dll
        $channel = match ($method) {
            'paydisini_qris'        => 'qris',
            'paydisini_va_mandiri'  => 'va_mandiri',
            'paydisini_alfamart'    => 'alfamart',
            'paydisini_indomaret'   => 'indomaret',
            default                 => null,
        };

        if (!$channel) {
            return back()
                ->withErrors(['payment_method' => 'Channel pembayaran tidak dikenali.'])
                ->withInput();
        }

        $serviceId = match ($channel) {
            'qris'       => 11,
            'va_mandiri' => 5,
            'alfamart'   => (int) env('PAYDISINI_ALFAMART_SERVICE_ID'),
            'indomaret'  => (int) env('PAYDISINI_INDOMARET_SERVICE_ID'),
        };

        if (!$serviceId) {
            return back()
                ->withErrors(['payment_method' => 'Service ID Paydisini belum diset untuk channel ini.'])
                ->withInput();
        }

        $apiKey   = env('PAYDISINI_API_KEY');
        $baseUrl  = rtrim(env('PAYDISINI_BASE_URL', 'https://api.paydisini.co.id/v1/'), '/');
        $validTime = (int) env('PAYDISINI_VALID_TIME', 1800); // detik

        $amount = (int) $order->total_amount;

        $payment = null;

        // bikin payment di DB
        DB::transaction(function () use ($order, $method, $serviceId, $amount, &$payment) {
            $uniqueCode = 'MKTPAY' . $order->id . now()->format('ymdHis');

            $payment = MarketplaceOrderPayment::create([
                'marketplace_order_id' => $order->id,
                'method'               => $method,
                'amount'               => $amount,
                'status'               => 'pending',
                'paydisini_unique_code'=> $uniqueCode,
                'paydisini_service_id' => (string) $serviceId,
            ]);
        });

        // panggil Paydisini: request=new
        $uniqueCode = $payment->paydisini_unique_code;

        $signature = md5($apiKey . $uniqueCode . $serviceId . $amount . $validTime . 'NewTransaction');

        $payload = [
            'key'          => $apiKey,
            'request'      => 'new',
            'unique_code'  => $uniqueCode,
            'service'      => $serviceId,
            'amount'       => $amount,
            'note'         => 'Pembayaran pesanan marketplace ' . $order->invoice_number,
            'valid_time'   => $validTime,
            'type_fee'     => 1,        // fee ditanggung customer
            'payment_guide'=> false,
            'callback_count'=> 0,
            'signature'    => $signature,
        ];

        $response = Http::asForm()->post($baseUrl . '/', $payload);

        if (!$response->successful()) {
            $payment->update([
                'status'           => 'canceled',
                'response_payload' => ['http_error' => $response->status()],
            ]);

            $order->update([
                'payment_status' => 'canceled',
                'status'         => 'not_paid',
            ]);

            return back()
                ->withErrors(['payment' => 'Gagal menghubungi Paydisini (HTTP ' . $response->status() . ').'])
                ->withInput();
        }

        $json = $response->json();

        if (!isset($json['success']) || $json['success'] !== true) {
            $payment->update([
                'status'           => 'canceled',
                'response_payload' => $json,
            ]);

            $order->update([
                'payment_status' => 'canceled',
                'status'         => 'not_paid',
            ]);

            return back()
                ->withErrors(['payment' => 'Paydisini error: ' . ($json['msg'] ?? 'unknown')])
                ->withInput();
        }

        // sukses create transaksi di Paydisini
        $dataPay = $json['data'] ?? [];

        $payment->update([
            'paydisini_pay_id' => $dataPay['pay_id'] ?? null,
            'response_payload' => $json,
            'expired_at'       => now()->addSeconds($validTime),
        ]);

        // tandai order sedang menunggu pembayaran
        $order->update([
            'payment_status' => 'pending',
            'status'         => 'not_paid',
        ]);

        return redirect()->route('marketplace.payment.show', $payment);
    }
}
