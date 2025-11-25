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
    public function checkoutSaldo(Request $request)
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

        DB::transaction(function () use ($user, $variant, $validated, $amount, $code, &$order) {
            // refresh user biar saldo terbaru
            $user->refresh();

            if ($user->maitri_balance < $amount) {
                throw new \RuntimeException('Saldo Maitri tidak mencukupi.');
            }

            // Kurangi saldo Maitri
            $user->maitri_balance -= $amount;
            $user->save();

            // Buat order (SIMPAN ke kolom product_variant_id,
            // tapi AMBIL dari field form "variant_id" sesuai UI)
            $order = Order::create([
                'user_id' => $user->id,
                'code' => $code,

                'product_id' => $validated['product_id'],
                'product_variant_id' => $validated['variant_id'],   // <— penting: cocokkan dengan UI

                'buyer_sku_code' => $variant->buyer_sku_code,
                'target' => $validated['target'],
                'email' => $validated['email'] ?? null,

                // provider belum dihubungkan ke Digiflazz di tahap ini
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
                'status' => 'processing',   // nanti bisa di-update setelah hit Digiflazz
            ]);
        });

        // 7. Redirect ke halaman invoice berdasarkan kode (bukan ID)
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
        return view('orders.show', compact('order'));
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

        return view('orders.show', compact('order'));
    }
}
