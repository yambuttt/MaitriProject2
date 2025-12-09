@component('mail::message')
# Transaksi Berhasil 🎉

Halo,

Transaksi kamu sudah **BERHASIL**.

@php
    $productName = $order->product->name ?? 'Produk';
    $variantName = $order->variant->name ?? '-';
@endphp

- **Kode Transaksi:** {{ $order->code }}
- **Produk:** {{ $productName }}
- **Varian / Nominal:** {{ $variantName }}
- **Tujuan:** {{ $order->target }}
- **Total Bayar:** Rp {{ number_format($order->total, 0, ',', '.') }}

@if($order->provider_sn)
- **SN / Ref:** {{ $order->provider_sn }}
@endif

Terima kasih sudah bertransaksi di MaitriProject 🙏

@endcomponent
