<?php

namespace App\Services;

use App\Mail\MarketplaceOrderPaidMail;
use App\Models\MarketplaceOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class MarketplaceOrderService
{
    public function markAsPaid(MarketplaceOrder $order): void
    {
        // Hindari double proses
        if ($order->payment_status === 'paid') {
            return;
        }

        // Update status order
        $order->payment_status = 'paid';

        if ($order->status === 'not_paid') {
            $order->status = 'paid_received';
        }

        $order->paid_at = now();
        $order->save();

        // ===========================================
        // 1) KIRIM EMAIL (kalau ada email)
        // ===========================================
        if ($order->customer_email) {
            Mail::to($order->customer_email)->send(new MarketplaceOrderPaidMail($order));
        }

        // ===========================================
        // 2) SIAPKAN TEKS WHATSAPP (dipakai untuk user & admin)
        // ===========================================
        $baseText =
            "Pesanan Marketplace kamu sudah diterima!\n\n" .
            "📄 *Invoice*: {$order->invoice_number}\n" .
            "🛍️ *Produk*: {$order->product->name}\n" .
            "🔢 *Varian*: " . ($order->variant->name ?? '-') . "\n" .
            "💰 *Total Bayar*: Rp " . number_format($order->total_amount, 0, ',', '.') . "\n" .
            "💳 *Metode Pembayaran*: {$this->formatPaymentMethod($order->payment_method)}\n" .
            "📌 *Status*: PAID & PESANAN DITERIMA\n\n" .
            "Terima kasih sudah berbelanja di MaitriProject 😊";

        // ===========================================
        // 3) KIRIM WHATSAPP KE PELANGGAN (kalau ada nomor)
        // ===========================================
        if ($order->customer_phone) {
            $phone = $this->normalizePhone($order->customer_phone);

            Http::post('http://167.172.66.220:3000/send', [
                'to'   => $phone,
                'text' => $baseText,
            ]);
        }

        // ===========================================
        // 4) KIRIM WHATSAPP KE GROUP ADMIN
        // ===========================================
        // tentukan siapa yang pesan
        $who = 'Guest';
        if ($order->user && $order->user->name) {
            $who = $order->user->name;
        } elseif ($order->customer_email) {
            $who = $order->customer_email; // fallback kalau ada email tapi tidak login
        }

        // waktu pesan (pakai created_at order)
        $when = $order->created_at
            ? $order->created_at->format('d M Y H:i')
            : now()->format('d M Y H:i');
        
        $adminText = 
         "HALO ADMIN Pesanan Marketplace diterima!\n\n" .
            "📄 *Invoice*: {$order->invoice_number}\n" .
            "🛍️ *Produk*: {$order->product->name}\n" .
            "🔢 *Varian*: " . ($order->variant->name ?? '-') . "\n" .
            "💰 *Total Bayar*: Rp " . number_format($order->total_amount, 0, ',', '.') . "\n" .
            "💳 *Metode Pembayaran*: {$this->formatPaymentMethod($order->payment_method)}\n" .
            "📌 *Status*: PAID & PESANAN DITERIMA\n\n" .
            "👤 *Pemesan*: {$who} \n".
            "🕒 *Waktu pesan*: {$when}\n".
            "Catatan: {$order->user_note}\n".
            "BUAT ADMIN, SEGERA DI PROSES YA 😊";


        // $adminText = $baseText
        //     . "\n\n👤 *Pemesan*: {$who}"
        //     . "\n🕒 *Waktu pesan*: {$when}";

        Http::post('http://167.172.66.220:3000/send-group', [
            'groupJid' => '120363420542063843@g.us',
            'text'     => $adminText,
        ]);
    }

    /**
     * Normalisasi no HP jadi format 62xxxxxxxx
     */
    private function normalizePhone(string $phone): string
    {
        $p = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($p, '0')) {
            return '62' . substr($p, 1);
        }

        if (str_starts_with($p, '62')) {
            return $p;
        }

        // fallback: anggap nomor lokal Indonesia
        return '62' . $p;
    }

    /**
     * Format metode pembayaran
     */
    private function formatPaymentMethod(string $method): string
    {
        return match ($method) {
            'wallet'                   => 'Saldo Maitri',
            'paydisini_qris'           => 'QRIS Paydisini',
            'paydisini_va_mandiri'     => 'VA Mandiri (Paydisini)',
            'paydisini_alfamart'       => 'Alfamart (Paydisini)',
            'paydisini_indomaret'      => 'Indomaret (Paydisini)',
            default                    => ucfirst($method),
        };
    }
}
