@extends('layouts.app') {{-- atau layout dashboardmu --}}
@section('title', 'Saldo & PIN Maitri')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <h1 class="text-xl font-semibold text-slate-100">Saldo & PIN Maitri</h1>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    {{-- Saldo --}}
    <div class="rounded-2xl border border-slate-800/70 bg-slate-900/60 p-4">
        <div class="text-xs font-medium text-slate-400 mb-1">Saldo Maitri</div>
        <div class="text-2xl font-semibold text-slate-50">
            Rp {{ number_format($user->maitri_balance, 0, ',', '.') }}
        </div>
    </div>

    {{-- Form PIN --}}
    <div class="rounded-2xl border border-slate-800/70 bg-slate-900/60 p-4 space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm font-semibold text-slate-100">
                    PIN Pembayaran Maitri
                </div>
                <div class="text-xs text-slate-400">
                    {{ $user->hasPaymentPin() ? 'PIN sudah diatur, kamu bisa menggantinya di sini.' : 'Kamu belum memiliki PIN, silakan buat terlebih dahulu.' }}
                </div>
            </div>
            @if ($user->hasPaymentPin())
                <span class="inline-flex items-center rounded-full bg-emerald-500/10 border border-emerald-500/40 px-3 py-0.5 text-xs text-emerald-300">
                    Aktif
                </span>
            @else
                <span class="inline-flex items-center rounded-full bg-amber-500/10 border border-amber-500/40 px-3 py-0.5 text-xs text-amber-300">
                    Belum diatur
                </span>
            @endif
        </div>

        <form method="post" action="{{ route('dashboard.wallet.pin.update') }}" class="space-y-3">
            @csrf

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">PIN Baru</label>
                <input type="password" name="pin" maxlength="6"
                       class="h-10 w-full rounded-xl bg-slate-900 border border-slate-700/80 px-3 text-sm text-slate-100"
                       placeholder="4–6 digit angka" required>
                @error('pin')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Konfirmasi PIN</label>
                <input type="password" name="pin_confirmation" maxlength="6"
                       class="h-10 w-full rounded-xl bg-slate-900 border border-slate-700/80 px-3 text-sm text-slate-100"
                       required>
                @error('pin_confirmation')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="h-10 px-4 rounded-xl bg-violet-500 hover:bg-violet-600 text-sm font-medium text-white">
                Simpan PIN
            </button>
        </form>
    </div>

    {{-- Topup manual (buat testing dev) --}}
    <div class="rounded-2xl border border-slate-800/70 bg-slate-900/60 p-4 space-y-3">
        <div class="text-sm font-semibold text-slate-100 mb-1">Topup Saldo (Testing)</div>
        <form method="post" action="{{ route('dashboard.wallet.topup') }}" class="flex gap-3 items-center">
            @csrf
            <input type="number" name="amount" min="1000" step="1000"
                   class="h-10 flex-1 rounded-xl bg-slate-900 border border-slate-700/80 px-3 text-sm text-slate-100"
                   placeholder="Contoh: 50000">
            <button type="submit"
                    class="h-10 px-4 rounded-xl bg-slate-700 hover:bg-slate-600 text-sm font-medium text-slate-100">
                Topup
            </button>
        </form>
    </div>

</div>
@endsection
