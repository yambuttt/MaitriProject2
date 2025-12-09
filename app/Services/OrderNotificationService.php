<?php

namespace App\Services;

use App\Mail\OrderSuccessMail;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class OrderNotificationService
{
    /**
     * Kirim email & WhatsApp ketika order sudah SUCCESS.
     */
    public function notifySuccess(Order $order): void
    {
        // Safety: cuma kirim kalau status benar-benar success
        if ($order->status !== 'success') {
            return;
        }
        \Log::info('OrderNotificationService::notifySuccess dipanggil', [
            'order_id' => $order->id,
            'code' => $order->code,
            'email' => $order->email,
            'phone' => $order->phone,
        ]);

        // Load relasi supaya nama produk/varian bisa dipakai
        $order->loadMissing(['product', 'variant']);

        // 1. KIRIM EMAIL (kalau customer isi email)
        if (!empty($order->email)) {
            try {
                Mail::to($order->email)->send(new OrderSuccessMail($order));
            } catch (\Throwable $e) {
                \Log::warning('Gagal kirim email sukses order', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // 2. KIRIM WHATSAPP (kalau customer isi nomor hp)
        if (!empty($order->phone)) {
            $phone = $this->normalizePhone($order->phone);

            $text =
                "Transaksi kamu *BERHASIL* 🎉\n\n" .
                "📄 *Kode*: {$order->code}\n" .
                "🛍️ *Produk*: " . ($order->product->name ?? 'Produk') . "\n" .
                "🔢 *Varian*: " . ($order->variant->name ?? '-') . "\n" .
                "🎯 *Tujuan*: {$order->target}\n" .
                "💰 *Total*: Rp " . number_format($order->total, 0, ',', '.') . "\n";

            if ($order->provider_sn) {
                $text .= "🔐 *SN / Ref*: {$order->provider_sn}\n";
            }

            $text .= "\nTerima kasih sudah bertransaksi di MaitriProject 🙏";

            try {
                Http::timeout(5)->post('http://167.172.66.220:31500/send', [
                    'to' => $phone,
                    'text' => $text,
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Gagal kirim WA sukses order', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Ubah 08xxxx jadi 62xxxx supaya cocok dengan API WA.
     */
    private function normalizePhone(string $phone): string
    {
        // buang karakter selain angka
        $p = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($p, '0')) {
            return '62' . substr($p, 1);
        }

        if (str_starts_with($p, '62')) {
            return $p;
        }

        // kalau format aneh, anggap dia belum pakai 62
        return '62' . $p;
    }
}
