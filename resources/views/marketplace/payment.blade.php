@extends('layouts.app')

@section('title', 'Pembayaran Marketplace')

@section('content')
    <div class="max-w-3xl mx-auto py-8 px-4 md:px-0">
        <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-6 space-y-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold text-slate-50">Pembayaran Pesanan Marketplace</h1>
                    @php $order = $payment->order; @endphp
                    <p class="text-sm text-slate-400 mt-1">
                        Invoice: {{ $order?->invoice_number ?? '-' }}
                    </p>

                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-400">Status</p>
                    <p id="payment-status-text" class="text-sm mt-1
                  @if($payment->status === 'paid') text-emerald-400
                  @elseif(in_array($payment->status, ['canceled', 'expired'])) text-rose-400
                  @else text-amber-400 @endif">
                        @if($payment->status === 'paid')
                            Pembayaran berhasil.
                        @elseif($payment->status === 'canceled')
                            Pembayaran dibatalkan.
                        @elseif($payment->status === 'expired')
                            Waktu pembayaran habis.
                        @else
                            Menunggu pembayaran...
                        @endif
                    </p>
                </div>
            </div>

            @php
                $channel = $payment->method;
                $qrcodeUrl = $data['qrcode_url'] ?? null;
                $virtualAccount = $data['virtual_account'] ?? null;
                $paymentCode = $data['payment_code'] ?? $data['code'] ?? null;
              @endphp

            <div id="payment-body" class="mt-4 space-y-4
            @if(in_array($payment->status, ['canceled', 'expired'])) opacity-40 pointer-events-none @endif">

                {{-- QRIS --}}
                @if($channel === 'paydisini_qris')
                    <p class="text-sm text-slate-400">
                        Scan QR berikut dengan aplikasi pembayaran yang kamu gunakan.
                    </p>
                    @if($qrcodeUrl)
                        <div class="flex justify-center">
                            <div class="rounded-2xl bg-white p-3">
                                <img src="{{ $qrcodeUrl }}" alt="QRIS" class="w-56 h-56 object-contain">
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-rose-400">QR code tidak tersedia.</p>
                    @endif

                    {{-- VA Mandiri --}}
                @elseif($channel === 'paydisini_va_mandiri')
                    <p class="text-sm text-slate-400">
                        Lakukan pembayaran ke Virtual Account Mandiri berikut:
                    </p>
                    <div class="flex items-center gap-2">
                        <code id="va-number"
                            class="px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-lg font-mono">
                      {{ $virtualAccount ?? '-' }}
                    </code>
                    </div>

                    {{-- Alfamart / Indomaret --}}
                @elseif(in_array($channel, ['paydisini_alfamart', 'paydisini_indomaret']))
                    <p class="text-sm text-slate-400">
                        Tunjukkan kode berikut ke kasir {{ $channel === 'paydisini_alfamart' ? 'Alfamart' : 'Indomaret' }}:
                    </p>
                    <div class="flex items-center gap-2">
                        <code class="px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-lg font-mono">
                      {{ $paymentCode ?? '-' }}
                    </code>
                    </div>
                @else
                    <p class="text-sm text-rose-400">
                        Channel pembayaran tidak dikenali.
                    </p>
                @endif
            </div>

            <p class="text-xs text-slate-500 mt-4">
                Batas waktu pembayaran: {{ $expiresAt->format('d M Y H:i') }} WIB.
            </p>

            <p class="text-xs text-slate-500">
                Halaman ini akan mengecek status pembayaran secara berkala. Jika pembayaran berhasil, kamu akan
                diarahkan ke halaman invoice.
            </p>
        </div>
    </div>

    <script>
        (function () {
            const pollUrl = "{{ route('marketplace.payment.status', $payment) }}";
            const statusText = document.getElementById('payment-status-text');

            function poll() {
                fetch(pollUrl)
                    .then(r => r.json())
                    .then(res => {
                        if (!res.ok) return;

                        if (res.status === 'paid') {
                            statusText.textContent = 'Pembayaran berhasil.';
                            statusText.className = 'text-sm mt-1 text-emerald-400';
                            if (res.redirect_url) {
                                window.location.href = res.redirect_url;
                            }
                        }
                    })
                    .catch(() => { });
            }

            setInterval(poll, 5000);
        })();
    </script>
@endsection