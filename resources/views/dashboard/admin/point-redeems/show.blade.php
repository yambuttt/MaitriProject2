@extends('layouts.admin')
@section('title', 'Redeem Detail')

@section('content')
<div class="space-y-5">
    <div class="flex items-start justify-between gap-3 flex-wrap">
        <div>
            <h1 class="text-2xl font-semibold">Redeem Detail</h1>
            <p class="text-slate-400 text-sm">{{ $redeem->user->name }} — {{ $redeem->user->email }}</p>
        </div>
        <a href="{{ route('admin.point-redeems.index') }}"
           class="px-3 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 text-sm">
            ← Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-5 space-y-2">
        <div class="text-sm text-slate-300">Metode: <b class="text-slate-100">{{ $redeem->method_label }}</b></div>
        <div class="text-sm text-slate-300">Point: <b class="font-mono text-slate-100">{{ number_format($redeem->points) }}</b></div>
        <div class="text-sm text-slate-300">Nominal: <b class="font-mono text-slate-100">Rp {{ number_format($redeem->amount) }}</b></div>
        <div class="text-sm text-slate-300">Status: <b class="text-slate-100">{{ $redeem->status_label }}</b></div>
        <div class="text-sm text-slate-300">WA: <b class="font-mono text-slate-100">{{ $redeem->phone ?? '-' }}</b></div>

        @if($redeem->proof_path)
            <div class="text-sm text-slate-300">
                Bukti: <a class="text-violet-300 underline" target="_blank" href="{{ asset('storage/'.$redeem->proof_path) }}">Lihat</a>
            </div>
        @endif

        @if($redeem->admin_note)
            <div class="text-sm text-slate-300">Catatan Admin: <span class="text-slate-100">{{ $redeem->admin_note }}</span></div>
        @endif
    </div>

    @if($redeem->status === 'pending' && $redeem->method === 'cash')
        <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-5 space-y-4">
            <h2 class="text-sm font-semibold">Proses Redeem Cash</h2>

            <form method="post" action="{{ route('admin.point-redeems.approve', $redeem) }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <div class="text-xs text-slate-400 mb-1">Catatan Admin</div>
                    <textarea name="admin_note" class="w-full rounded-xl bg-slate-900/60 border border-slate-700/70 px-3 py-2 text-sm outline-none" rows="3"></textarea>
                </div>

                <div>
                    <div class="text-xs text-slate-400 mb-1">Upload Bukti Transfer (opsional)</div>
                    <input type="file" name="proof" class="text-sm text-slate-300">
                </div>

                <button class="h-10 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm">
                    Approve (Potong Point)
                </button>
            </form>

            <form method="post" action="{{ route('admin.point-redeems.reject', $redeem) }}" class="pt-3 border-t border-slate-800/70">
                @csrf
                <div class="text-xs text-slate-400 mb-1">Catatan Penolakan (opsional)</div>
                <textarea name="admin_note" class="w-full rounded-xl bg-slate-900/60 border border-slate-700/70 px-3 py-2 text-sm outline-none" rows="2"></textarea>

                <button class="mt-2 h-10 px-4 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-sm">
                    Reject
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
