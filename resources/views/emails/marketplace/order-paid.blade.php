<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Marketplace {{ $order->invoice_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; background:#0f172a; color:#e5e7eb; padding:24px;">
    <div style="max-width:600px;margin:0 auto;background:#020617;border-radius:16px;padding:24px;border:1px solid #1f2937;">
        <h2 style="margin-top:0;margin-bottom:12px;color:#f9fafb;">
            Pesananmu sudah diterima 🎉
        </h2>

        <p style="font-size:14px;line-height:1.6;">
            Halo,
            <br><br>
            Pembayaran untuk pesanan marketplace dengan detail berikut sudah kami terima:
        </p>

        <table style="font-size:14px;line-height:1.6;margin-top:8px;margin-bottom:16px;">
            <tr>
                <td style="padding-right:8px;color:#9ca3af;">Invoice</td>
                <td style="color:#e5e7eb;"><strong>{{ $order->invoice_number }}</strong></td>
            </tr>
            <tr>
                <td style="padding-right:8px;color:#9ca3af;">Produk</td>
                <td style="color:#e5e7eb;">
                    {{ $order->product->name ?? 'Produk marketplace' }}
                </td>
            </tr>
            <tr>
                <td style="padding-right:8px;color:#9ca3af;">Varian</td>
                <td style="color:#e5e7eb;">
                    {{ $order->variant->name ?? '-' }}
                </td>
            </tr>
            <tr>
                <td style="padding-right:8px;color:#9ca3af;">Total Bayar</td>
                <td style="color:#e5e7eb;">
                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="padding-right:8px;color:#9ca3af;">Status</td>
                <td style="color:#4ade80;">
                    PAID &amp; PESANAN DITERIMA
                </td>
            </tr>
        </table>

        <p style="font-size:14px;line-height:1.6;">
            Kamu bisa melihat detail lengkap invoice dan status pesanan di halaman berikut:
        </p>

        <p style="text-align:center;margin:20px 0;">
            <a href="{{ route('marketplace.invoice.show', $order) }}"
               style="display:inline-block;padding:10px 18px;background:#8b5cf6;color:#f9fafb;
                      text-decoration:none;border-radius:999px;font-size:13px;">
                Lihat Invoice Marketplace
            </a>
        </p>

        <p style="font-size:13px;line-height:1.6;color:#9ca3af;">
            Jika ada kendala dengan pesanan ini, silakan balas email ini atau hubungi admin melalui
            WhatsApp yang tertera di website.
        </p>

        <p style="font-size:13px;line-height:1.6;margin-top:16px;color:#9ca3af;">
            Terima kasih sudah menggunakan layanan marketplace MaitriProject 🙏
        </p>
    </div>
</body>
</html>
