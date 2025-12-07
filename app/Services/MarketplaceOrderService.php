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

            Http::post('http://167.172.66.220:31500/send', [
                'to' => $phone,
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
            "👤 *Pemesan*: {$who} \n" .
            "🕒 *Waktu pesan*: {$when}\n" .
            "Catatan: {$order->user_note}\n" .
            "BUAT ADMIN, SEGERA DI PROSES YA 😊";


        // $adminText = $baseText
        //     . "\n\n👤 *Pemesan*: {$who}"
        //     . "\n🕒 *Waktu pesan*: {$when}";

        Http::post('http://167.172.66.220:31500/send-group', [
            'groupJid' => '120363420542063843@g.us',
            'text' => $adminText,
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
            'wallet' => 'Saldo Maitri',
            'paydisini_qris' => 'QRIS Paydisini',
            'paydisini_va_mandiri' => 'VA Mandiri (Paydisini)',
            'paydisini_alfamart' => 'Alfamart (Paydisini)',
            'paydisini_indomaret' => 'Indomaret (Paydisini)',
            default => ucfirst($method),
        };
    }

    /**
     * Kirim WhatsApp ke pelanggan ketika admin meng-update status pesanan
     * (paid_processing, paid_rejected, paid_finished).
     */
    public function notifyAdminStatusUpdate(MarketplaceOrder $order): void
    {
        // Kalau tidak ada nomor HP, tidak usah kirim apa-apa
        if (!$order->customer_phone) {
            return;
        }

        $phone = $this->normalizePhone($order->customer_phone);

        // Data umum yang sering dipakai di pesan
        $invoice = $order->invoice_number;
        $product = $order->product->name ?? 'Produk marketplace';
        $variant = $order->variant->name ?? '-';
        $total = number_format($order->total_amount, 0, ',', '.');

        $status = $order->status;
        $text = null;

        if ($status === 'paid_processing') {
            // ✅ PAID & PESANAN DIPROSES
            $text =
                "Pesanan marketplace kamu sedang *diproses* oleh admin.\n\n" .
                "📄 *Invoice*: {$invoice}\n" .
                "🛍️ *Produk*: {$product}\n" .
                "🔢 *Varian*: {$variant}\n" .
                "💰 *Total*: Rp {$total}\n\n" .
                "Mohon ditunggu ya, kamu akan mendapat pemberitahuan lagi ketika pesanan sudah selesai. 😊";

        } elseif ($status === 'paid_rejected') {
            // ❌ PAID & PESANAN DITOLAK
            $note = $order->admin_note ?: '-';

            $text =
                "Maaf, pesanan marketplace kamu *ditolak* oleh admin.\n\n" .
                "📄 *Invoice*: {$invoice}\n" .
                "🛍️ *Produk*: {$product}\n" .
                "🔢 *Varian*: {$variant}\n" .
                "💰 *Total*: Rp {$total}\n\n" .
                "✏️ *Catatan admin*: {$note}\n\n" .
                "Admin kami akan segera menghubungimu untuk penjelasan lebih lanjut.";

        } elseif ($status === 'paid_finished') {
            // ✅ PAID & PESANAN SELESAI
            $note = $order->admin_note ?: '-';

            $text =
                "Pesanan marketplace kamu sudah *SELESAI* 🎉\n\n" .
                "📄 *Invoice*: {$invoice}\n" .
                "🛍️ *Produk*: {$product}\n" .
                "🔢 *Varian*: {$variant}\n" .
                "💰 *Total*: Rp {$total}\n\n" .
                "✏️ *Catatan admin*: {$note}\n\n" .
                "Terima kasih sudah berbelanja di MaitriProject 🙏";
        }

        // Kalau status bukan salah satu di atas, tidak kirim apa-apa
        if (!$text) {
            return;
        }

        // Kirim ke gateway WA — dibungkus try/catch supaya tidak bikin error 500
        try {
            Http::timeout(5)->post('http://167.172.66.220:31500/send', [
                'to' => $phone,
                'text' => $text,
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Gagal kirim WA status update marketplace', [
                'order_id' => $order->id,
                'status' => $order->status,
                'error' => $e->getMessage(),
            ]);
        }
    }

}
