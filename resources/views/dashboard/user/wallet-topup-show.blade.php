@extends('layouts.dashboard')
@section('title', 'Topup Saldo')

@section('content')
    <div class="max-w-xl mx-auto space-y-6">
        <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-6 space-y-4">

            <h1 class="text-xl font-semibold text-slate-50">
                Topup Saldo Maitri
            </h1>

            <p class="text-sm text-slate-400">
                Kode topup:
                <span class="font-mono text-slate-200">{{ $topup->unique_code }}</span><br>
                Metode: {{ $topup->method === 'qris' ? 'QRIS' : 'VA Mandiri' }}
            </p>

            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-4 space-y-3">
                <p class="text-sm text-slate-300">
                    Nominal yang harus dibayar:
                </p>
                <p class="text-2xl font-semibold text-slate-50">
                    Rp {{ number_format($topup->amount, 0, ',', '.') }}
                </p>

                <p class="text-sm text-slate-300 mt-3">
                    Sisa waktu pembayaran:
                    <span id="countdown" class="font-mono text-slate-100">--:--</span>
                </p>

                <p id="topup-status-text" class="text-sm text-amber-400">
                    Status: Menunggu pembayaran (pending)...
                </p>

                <div id="payment-body" class="mt-4">
                    @if($topup->method === 'qris')
                        <div class="mt-4 flex flex-col items-center gap-3">
                            <p class="text-sm text-slate-400">
                                Scan QR berikut dengan aplikasi pembayaran kamu.
                            </p>
                            <div class="rounded-2xl bg-white p-3">
                                <img src="{{ $data['qrcode_url'] ?? '' }}" alt="QRIS" class="w-60 h-60 object-contain">
                            </div>
                        </div>
                    @else
                        <div class="mt-4 space-y-2">
                            <p class="text-sm text-slate-400">Nomor Virtual Account Mandiri:</p>
                            <div class="flex items-center gap-2">
                                <code id="va-number"
                                      class="px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-lg font-mono">
                                    {{ $data['virtual_account'] ?? '000000000000' }}
                                </code>
                                <button type="button" onclick="copyVA()"
                                        class="h-10 px-3 rounded-xl bg-slate-700 hover:bg-slate-600 text-xs font-medium">
                                    Salin
                                </button>
                            </div>
                            <p class="text-xs text-slate-500">
                                Silakan bayar sebelum waktu kedaluwarsa.
                            </p>
                        </div>
                    @endif
                </div>

                <p class="mt-4 text-xs text-slate-500">
                    Sistem akan memeriksa status pembayaran setiap beberapa detik.
                    Jika sudah berhasil, saldo kamu akan otomatis bertambah.
                </p>
            </div>

            <a href="{{ route('dashboard.wallet') }}"
               class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-700 px-4 text-sm text-slate-200 hover:bg-slate-800">
                Kembali ke Wallet
            </a>
        </div>
    </div>

@endsection


@push('body')
<script>
/* ================================
   Copy VA
================================ */
function copyVA() {
    const el = document.getElementById('va-number');
    if (!el) return;
    const text = el.textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        alert('Nomor VA disalin: ' + text);
    });
}

/* ================================
   MAIN SCRIPT (Countdown + Polling)
================================ */
(function () {
    const statusEl    = document.getElementById('topup-status-text');
    const countdownEl = document.getElementById('countdown');
    const paymentBody = document.getElementById('payment-body');

    const pollUrl   = "{{ route('dashboard.wallet.topup.status', $topup) }}";
    const expireUrl = "{{ route('dashboard.wallet.topup.expire', $topup) }}";
    const csrfToken = "{{ csrf_token() }}";

    // ExpiresAt dari controller (hasil dari created_at + valid_time)
    const expiresAt = {{ $expiresAt->getTimestamp() }} * 1000;

    let stopPolling = false;
    let expiredNotified = false;

    /* ==================
       Update Status UI
    ================== */
    function setStatus(status) {
        if (status === 'success') {
            statusEl.textContent = 'Status: Pembayaran berhasil. Saldo akan segera diperbarui...';
            statusEl.className = 'text-sm text-emerald-400';
        }
        else if (status === 'canceled') {
            statusEl.textContent = 'Status: Pembayaran dibatalkan.';
            statusEl.className = 'text-sm text-rose-400';
        }
        else {
            statusEl.textContent = 'Status: Menunggu pembayaran (pending)...';
            statusEl.className = 'text-sm text-amber-400';
        }
    }

    function onExpiredUI() {
        statusEl.textContent = 'Status: Waktu pembayaran habis, kode pembayaran tidak dapat digunakan.';
        statusEl.className   = 'text-sm text-rose-400';
        paymentBody.classList.add('opacity-40', 'pointer-events-none');
    }

    function formatTime(seconds) {
        seconds = Math.max(seconds, 0);
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        return String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
    }

    /* ==================
       Notify Server Expired
    ================== */
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
            console.error('notifyExpire error:', e);
        }
    }

    /* ==================
       Countdown Timer
    ================== */
    function tickCountdown() {
        const now  = Date.now();
        const diff = Math.floor((expiresAt - now) / 1000);

        if (diff <= 0) {
            countdownEl.textContent = '00:00';
            onExpiredUI();
            notifyExpire();
            stopPolling = true;
            return;
        }

        countdownEl.textContent = formatTime(diff);
        setTimeout(tickCountdown, 1000);
    }

    /* ==================
       Polling Pembayaran
    ================== */
    async function pollStatus() {

        if (stopPolling) return;

        try {
            const res = await fetch(pollUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            const data = await res.json();

            if (data.ok) {

                setStatus(data.status);

                if (data.status === 'success') {
                    stopPolling = true;
                    setTimeout(() => {
                        window.location.href = "{{ route('dashboard.wallet') }}";
                    }, 2000);
                }
                else if (data.status === 'canceled') {
                    stopPolling = true;
                }
            }
        }
        catch (e) {
            console.error('pollStatus error:', e);
        }

        if (!stopPolling) {
            setTimeout(pollStatus, 5000);
        }
    }

    /* ==================
       Init
    ================== */
    document.addEventListener('DOMContentLoaded', () => {
        tickCountdown();
        pollStatus();
    });

})();
</script>
@endpush
