{{-- resources/views/invoices/payment.blade.php --}}
@extends('layouts.app')

@section('title', 'Pembayaran Pesanan')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-6 space-y-4">

            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold text-slate-50">
                        Pembayaran Pesanan
                    </h1>
                    <p class="text-sm text-slate-400 mt-1">
                        Kode pesanan:
                        <span class="font-mono text-slate-200">{{ $order->code }}</span>
                    </p>
                    <p class="text-sm text-slate-400">
                        Metode pembayaran:
                        <span class="font-semibold text-slate-100">
                            {{ $payment->method_label }}
                        </span>
                    </p>
                </div>

                <div class="text-right">
                    <p class="text-xs text-slate-400">Status pembayaran</p>
                    @php
                        $status = $payment->status;
                        $statusLabel = match($status) {
                            'paid'     => 'Berhasil',
                            'canceled' => 'Dibatalkan',
                            'expired'  => 'Kedaluwarsa',
                            default    => 'Menunggu pembayaran',
                        };
                        $statusClass = match($status) {
                            'paid'     => 'text-emerald-400 bg-emerald-500/10',
                            'canceled' => 'text-rose-400 bg-rose-500/10',
                            'expired'  => 'text-rose-400 bg-rose-500/10',
                            default    => 'text-amber-400 bg-amber-500/10',
                        };
                    @endphp
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                    <p class="text-xs text-slate-500 mt-1">
                        Sisa waktu:
                        <span id="countdown" class="font-mono text-slate-100">--:--</span>
                    </p>
                </div>
            </div>

            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-4 space-y-3">
                <p class="text-sm text-slate-300">
                    Total yang harus dibayar:
                </p>
                <p class="text-2xl font-semibold text-slate-50">
                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                </p>

                <p id="payment-status-text" class="text-sm mt-2
                    @if($payment->status === 'paid') text-emerald-400
                    @elseif(in_array($payment->status, ['canceled','expired'])) text-rose-400
                    @else text-amber-400 @endif">
                    @if($payment->status === 'paid')
                        Status: Pembayaran berhasil.
                    @elseif($payment->status === 'canceled')
                        Status: Pembayaran dibatalkan.
                    @elseif($payment->status === 'expired')
                        Status: Waktu pembayaran habis, kode pembayaran tidak dapat digunakan.
                    @else
                        Status: Menunggu pembayaran (pending)...
                    @endif
                </p>

                @php
                    $channel = $payment->method; // paydisini_qris / paydisini_va_mandiri / ...
                    $qrcodeUrl = $data['qrcode_url'] ?? null;
                    $virtualAccount = $data['virtual_account'] ?? null;
                    // kemungkinan nama field kode pembayaran minimarket
                    $paymentCode = $data['payment_code'] ?? $data['code'] ?? null;
                @endphp

                <div id="payment-body" class="mt-4 space-y-4
                    @if(in_array($payment->status, ['canceled','expired'])) opacity-40 pointer-events-none @endif">

                    {{-- QRIS --}}
                    @if($channel === 'paydisini_qris')
                        <div class="space-y-3">
                            <p class="text-sm text-slate-400">
                                Scan QR berikut dengan aplikasi pembayaran favoritmu.
                            </p>
                            @if($qrcodeUrl)
                                <div class="flex justify-center">
                                    <div class="rounded-2xl bg-white p-3">
                                        <img src="{{ $qrcodeUrl }}" alt="QRIS" class="w-60 h-60 object-contain">
                                    </div>
                                </div>
                            @else
                                <p class="text-sm text-rose-400">
                                    QR code tidak tersedia di response Paydisini.
                                </p>
                            @endif
                        </div>
                    {{-- VA Mandiri --}}
                    @elseif($channel === 'paydisini_va_mandiri')
                        <div class="space-y-3">
                            <p class="text-sm text-slate-400">
                                Nomor Virtual Account Mandiri:
                            </p>
                            <div class="flex items-center gap-2">
                                <code id="va-number"
                                      class="px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-lg font-mono">
                                    {{ $virtualAccount ?? '000000000000' }}
                                </code>
                                <button type="button" onclick="copyVA()"
                                        class="h-10 px-3 rounded-xl bg-slate-700 hover:bg-slate-600 text-xs font-medium">
                                    Salin
                                </button>
                            </div>
                            <p class="text-xs text-slate-500">
                                Silakan lakukan pembayaran melalui ATM / mobile banking sebelum waktu kedaluwarsa.
                            </p>
                        </div>
                    {{-- Alfamart / Indomaret --}}
                    @elseif(in_array($channel, ['paydisini_alfamart','paydisini_indomaret']))
                        <div class="space-y-3">
                            <p class="text-sm text-slate-400">
                                Tunjukkan kode pembayaran berikut ke kasir
                                {{ $channel === 'paydisini_alfamart' ? 'Alfamart' : 'Indomaret' }}:
                            </p>
                            <div class="flex items-center gap-2">
                                <code id="store-code"
                                      class="px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-lg font-mono">
                                    {{ $paymentCode ?? '-' }}
                                </code>
                                <button type="button" onclick="copyStoreCode()"
                                        class="h-10 px-3 rounded-xl bg-slate-700 hover:bg-slate-600 text-xs font-medium">
                                    Salin
                                </button>
                            </div>
                            <p class="text-xs text-slate-500">
                                Simpan kode ini dan selesaikan pembayaran sebelum waktu kedaluwarsa.
                            </p>
                        </div>
                    @else
                        <p class="text-sm text-rose-400">
                            Channel pembayaran tidak dikenali: {{ $channel }}
                        </p>
                    @endif
                </div>

                <p class="mt-4 text-xs text-slate-500">
                    Sistem akan memeriksa status pembayaran setiap beberapa detik.
                    Jika pembayaran sudah berhasil, pesananmu akan segera diproses.
                </p>
            </div>

            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('invoices.show', $order->code) }}"
                   class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-700 px-4 text-sm text-slate-200 hover:bg-slate-800">
                    Kembali ke detail pesanan
                </a>
            </div>
        </div>
    </div>
@endsection

@push('body')
    <script>
        function copyVA() {
            const el = document.getElementById('va-number');
            if (!el) return;
            const text = el.textContent.trim();
            navigator.clipboard.writeText(text).then(() => {
                alert('Nomor VA disalin: ' + text);
            });
        }

        function copyStoreCode() {
            const el = document.getElementById('store-code');
            if (!el) return;
            const text = el.textContent.trim();
            navigator.clipboard.writeText(text).then(() => {
                alert('Kode pembayaran disalin: ' + text);
            });
        }

        (function () {
            const countdownEl = document.getElementById('countdown');
            const statusEl    = document.getElementById('payment-status-text');
            const paymentBody = document.getElementById('payment-body');

            const pollUrl   = "{{ route('orders.payment.status', $payment) }}";
            const expireUrl = "{{ route('orders.payment.expire', $payment) }}";
            const csrfToken = "{{ csrf_token() }}";

            const expiresAt = {{ $expiresAt->getTimestamp() }} * 1000; // ms

            let stopAll       = false;
            let expiredNotified = false;

            function setStatusLabel(status) {
                if (status === 'paid') {
                    statusEl.textContent = 'Status: Pembayaran berhasil.';
                    statusEl.className = 'text-sm text-emerald-400';
                    paymentBody.classList.remove('opacity-40', 'pointer-events-none');
                    stopAll = true;

                    // redirect ke detail invoice setelah beberapa detik
                    setTimeout(function () {
                        window.location.href = "{{ route('invoices.show', $order->code) }}";
                    }, 2000);
                } else if (status === 'canceled') {
                    statusEl.textContent = 'Status: Pembayaran dibatalkan.';
                    statusEl.className = 'text-sm text-rose-400';
                    paymentBody.classList.add('opacity-40', 'pointer-events-none');
                    stopAll = true;
                } else if (status === 'expired') {
                    statusEl.textContent = 'Status: Waktu pembayaran habis, kode pembayaran tidak dapat digunakan.';
                    statusEl.className = 'text-sm text-rose-400';
                    paymentBody.classList.add('opacity-40', 'pointer-events-none');
                    stopAll = true;
                } else {
                    statusEl.textContent = 'Status: Menunggu pembayaran (pending)...';
                    statusEl.className = 'text-sm text-amber-400';
                }
            }

            function formatTime(seconds) {
                seconds = Math.max(seconds, 0);
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            }

            async function notifyExpire() {
                if (expiredNotified) return;
                expiredNotified = true;

                try {
                    await fetch(expireUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: ''
                    });
                } catch (e) {
                    console.error('notifyExpire error', e);
                }
            }

            function tickCountdown() {
                if (stopAll) return;

                const now  = Date.now();
                const diff = Math.floor((expiresAt - now) / 1000);

                if (diff <= 0) {
                    countdownEl.textContent = '00:00';
                    setStatusLabel('expired');
                    notifyExpire();
                    return;
                }

                countdownEl.textContent = formatTime(diff);
                setTimeout(tickCountdown, 1000);
            }

            async function pollStatus() {
                if (stopAll) return;

                try {
                    const res = await fetch(pollUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });

                    const data = await res.json();

                    if (data.ok) {
                        setStatusLabel(data.status);
                    }
                } catch (e) {
                    console.error('pollStatus error', e);
                }

                if (!stopAll) {
                    setTimeout(pollStatus, 5000);
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                tickCountdown();
                pollStatus();
            });
        })();
    </script>
@endpush
