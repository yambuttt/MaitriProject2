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

class CheckoutController extends Controller
{
    public function checkoutSaldo(Request $request, DigiflazzService $digiflazzService)
    {

        $user = Auth::user();

        // 1. Validasi data dari form (UI tidak diubah)
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],             // dari input hidden name="product_id"
            'variant_id' => ['required', 'exists:product_variants,id'],     // dari input hidden name="variant_id"
            'target' => ['required', 'string'],                         // dari input hidden name="target"
            'email' => ['nullable', 'email'],                          // dari input hidden name="email"
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
        $amount = (int) $variant->final_price;        // nominal yang diambil dari varian (sudah + markup)

        // 4. Cek saldo Maitri cukup
        if ($user->maitri_balance < $amount) {
            return back()
                ->withErrors(['saldo' => 'Saldo Maitri tidak mencukupi.'])
                ->withInput();
        }

        // 5. Generate kode invoice MP-00001, MP-00002, dst
        $nextNumber = (Order::max('id') ?? 0) + 1;
        $code = 'MP-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);

        // 6. Transaksi database: kurangi saldo + buat order
        $order = null;

        DB::transaction(function () use ($user, $variant, $validated, $amount, &$order) {
            // generate kode unik untuk order
            $code = Order::generateCode();

            // refresh user biar saldo terbaru
            $user->refresh();

            if ($user->maitri_balance < $amount) {
                throw new \RuntimeException('Saldo Maitri tidak mencukupi.');
            }

            // Kurangi saldo Maitri (sekalian catat mutasi kalau kamu mau)
            $user->maitri_balance -= $amount;
            $user->save();

            // Buat order LENGKAP (pakai blok yang tadi)
            $order = Order::create([
                'user_id' => $user->id,
                'code' => $code,

                'product_id' => $validated['product_id'],
                'product_variant_id' => $validated['variant_id'],
                'buyer_sku_code' => $variant->buyer_sku_code,
                'target' => $validated['target'],
                'email' => $validated['email'] ?? null,

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

                'method' => 'saldo_maitri',
                'status' => 'processing',
            ]);
        });


        // 7. Hit Digiflazz untuk bikin transaksi sebenarnya
        try {
            $providerData = $digiflazzService->createTransaction($order, $variant);

            // DEBUG SEMENTARA


            $status = strtolower($providerData['status'] ?? '');
            $mappedStatus = match ($status) {
                'sukses', 'success' => 'success',
                'gagal', 'failed' => 'failed',
                default => 'processing',
            };

            $order->update([
                'provider_ref_id' => $providerData['ref_id'] ?? null,
                'provider_status' => $providerData['status'] ?? null,
                'provider_message' => $providerData['message'] ?? null,
                'provider_rc' => $providerData['rc'] ?? null,
                'provider_sn' => $providerData['sn'] ?? null,
                'provider_price' => $providerData['selling_price'] ?? ($providerData['price'] ?? null),
                'provider_raw' => $providerData,
                'status' => $mappedStatus,
            ]);
        } catch (\Throwable $e) {

            $order->update([
                'status' => 'failed',
                'provider_status' => 'ERROR',
                'provider_message' => $e->getMessage(),
            ]);
        }

        // 8. Redirect ke halaman invoice berdasarkan kode
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
        if (auth()->id() !== $order->user_id) {
            abort(403);
        }

        // Pakai view invoice yang sama seperti sebelumnya
        return view('invoices.show', compact('order'));
    }
    /**
     * Detail order sederhana
     */
    public function show(Order $order)
    {
        // Pastikan hanya pemilik order yang boleh lihat
        if (auth()->id() !== $order->user_id) {
            abort(403);
        }

        return view('invoices.show', compact('order'));
    }
    public function status(string $code)
    {
        $order = Order::where('code', $code)->firstOrFail();

        return response()->json([
            'status' => $order->status,
        ]);
    }
}
