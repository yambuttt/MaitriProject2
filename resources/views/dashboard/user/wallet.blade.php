@extends('layouts.dashboard') {{-- kalau sudah punya layouts.dashboard bisa ganti ke situ --}}
@section('title', 'Saldo & PIN Maitri')

@section('content')
<div class="max-w-5xl mx-auto px-4 lg:px-0 space-y-8">

    <h1 class="text-xl md:text-2xl font-semibold text-slate-100">
        Saldo &amp; PIN Maitri
    </h1>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    {{-- Grid 2 kolom di desktop, 1 kolom di mobile --}}
    <div class="grid gap-6 lg:grid-cols-2">

        {{-- KIRI: Saldo + PIN + Topup --}}
        <div class="space-y-4">

            {{-- Saldo --}}
            <div class="rounded-2xl border border-slate-800/70 bg-slate-900/60 p-4 md:p-5">
                <div class="text-sm font-medium text-slate-400 mb-2">Saldo Maitri</div>
                <div class="text-2xl md:text-3xl font-semibold text-slate-50">
                    Rp {{ number_format($user->maitri_balance, 0, ',', '.') }}
                </div>
            </div>

            {{-- PIN --}}
            <div class="rounded-2xl border border-slate-800/70 bg-slate-900/60 p-4 md:p-5 space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold text-slate-100">PIN Pembayaran Maitri</div>
                        <p class="text-xs text-slate-400 mt-0.5">
                            PIN digunakan untuk konfirmasi pembayaran dengan saldo.
                        </p>
                    </div>

                    @if ($user->hasPaymentPin())
                        <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-400">
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-rose-500/10 px-3 py-1 text-xs font-medium text-rose-400">
                            Belum diatur
                        </span>
                    @endif
                </div>

                <form method="post" action="{{ route('dashboard.wallet.pin.update') }}" class="space-y-3">
                    @csrf

                    <div class="space-y-1">
                        <label class="text-xs font-medium text-slate-300">PIN Baru</label>
                        <input type="password" name="pin" inputmode="numeric"
                               class="h-10 w-full rounded-xl bg-slate-900 border border-slate-700/80 px-3 text-sm text-slate-100"
                               placeholder="4–6 digit angka">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-medium text-slate-300">Konfirmasi PIN</label>
                        <input type="password" name="pin_confirmation" inputmode="numeric"
                               class="h-10 w-full rounded-xl bg-slate-900 border border-slate-700/80 px-3 text-sm text-slate-100"
                               placeholder="Ulangi PIN baru">
                    </div>

                    @error('pin')
                        <p class="text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                    @error('pin_confirmation')
                        <p class="text-xs text-rose-400">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                            class="inline-flex h-10 items-center justify-center rounded-xl bg-violet-500 px-4 text-sm font-medium text-white hover:bg-violet-600">
                        Simpan PIN
                    </button>
                </form>
            </div>

            {{-- Topup saldo --}}
            <div class="rounded-2xl border border-slate-800/70 bg-slate-900/60 p-4 md:p-5 space-y-4">
                <div class="text-sm font-semibold text-slate-100">Topup Saldo</div>
                <p class="text-xs text-slate-400">
                    Isi nominal saldo yang ingin ditambahkan, lalu pilih metode pembayaran.
                </p>

                <form method="post" action="{{ route('dashboard.wallet.topup') }}" class="space-y-3">
                    @csrf

                    <div class="flex gap-3">
                        <input type="number" name="amount" min="1000" step="1000"
                               class="h-10 flex-1 rounded-xl bg-slate-900 border border-slate-700/80 px-3 text-sm text-slate-100"
                               placeholder="Contoh: 50000" value="{{ old('amount') }}">
                    </div>

                    <div class="flex gap-4 text-sm text-slate-300">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="method" value="qris"
                                   class="rounded border-slate-600"
                                   {{ old('method', 'qris') === 'qris' ? 'checked' : '' }}>
                            <span>QRIS</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="method" value="va_mandiri"
                                   class="rounded border-slate-600"
                                   {{ old('method') === 'va_mandiri' ? 'checked' : '' }}>
                            <span>VA Mandiri</span>
                        </label>
                    </div>

                    @error('amount')
                        <p class="text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                    @error('method')
                        <p class="text-xs text-rose-400">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                            class="h-10 w-full md:w-auto px-4 rounded-xl bg-violet-500 hover:bg-violet-600 text-sm font-medium text-white">
                        Topup 
                    </button>
                </form>
            </div>

        </div>

        {{-- KANAN: Riwayat topup --}}
        <div class="rounded-2xl border border-slate-800/70 bg-slate-900/60 p-4 md:p-5 flex flex-col">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div>
                    <div class="text-sm font-semibold text-slate-100">Riwayat Topup Saldo</div>
                    <p class="text-xs text-slate-400">
                        Menampilkan hingga 10 transaksi topup terakhir.
                    </p>
                </div>
            </div>

            <div class="flex-1 overflow-x-auto">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400">
                            <th class="py-2 pr-2 text-left font-medium">Tanggal</th>
                            <th class="py-2 px-2 text-left font-medium hidden sm:table-cell">Keterangan</th>
                            <th class="py-2 px-2 text-right font-medium">Jumlah</th>
                            <th class="py-2 pl-2 text-right font-medium hidden sm:table-cell">Saldo Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
    @forelse ($topups as $t)
        <tr>
            <td class="py-2 pr-2 align-top text-xs text-slate-300">
                {{ $t->created_at->format('d M Y') }}
            </td>
            <td class="py-2 px-2 align-top text-xs text-slate-300">
                Rp {{ number_format($t->amount, 0, ',', '.') }}
            </td>
            <td class="py-2 px-2 align-top">
                @if ($t->status === 'success')
                    <span class="inline-flex rounded-full bg-emerald-500/10 px-2 py-0.5 text-[11px] font-medium text-emerald-400">
                        Success
                    </span>
                @elseif ($t->status === 'canceled')
                    <span class="inline-flex rounded-full bg-rose-500/10 px-2 py-0.5 text-[11px] font-medium text-rose-400">
                        Canceled
                    </span>
                @else
                    <span class="inline-flex rounded-full bg-amber-500/10 px-2 py-0.5 text-[11px] font-medium text-amber-400">
                        Pending
                    </span>
                @endif
            </td>
            <td class="py-2 pl-2 align-top text-right">
                <a href="{{ route('dashboard.wallet.topup.show', $t) }}"
                   class="inline-flex items-center rounded-xl border border-slate-700 px-3 py-1 text-[11px] font-medium text-slate-200 hover:bg-slate-800">
                    @if($t->status === 'pending')
                        Lanjutkan
                    @else
                        Detail
                    @endif
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="py-4 text-center text-xs text-slate-500">
                Belum ada riwayat topup saldo.
            </td>
        </tr>
    @endforelse
</tbody>

                </table>
            </div>

            <div class="mt-3 text-[11px] text-slate-500">
                Topup yang belum berhasil akan muncul sebagai transaksi baru setelah pembayaran terkonfirmasi.
            </div>
        </div>

    </div>
</div>
@endsection
