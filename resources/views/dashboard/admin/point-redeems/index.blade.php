@extends('layouts.admin')
@section('title', 'Point Redeems')

@section('content')
<div class="space-y-5">
    <div class="flex items-start justify-between gap-3 flex-wrap">
        <div>
            <h1 class="text-2xl font-semibold">Point Redeems</h1>
            <p class="text-slate-400 text-sm">Permintaan redeem point menjadi saldo atau cash.</p>
        </div>
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

    <div class="flex gap-2">
        @foreach(['pending','approved','rejected','instant'] as $st)
            <a href="{{ route('admin.point-redeems.index', ['status' => $st]) }}"
               class="px-3 py-2 rounded-xl border {{ $status===$st ? 'border-violet-700/60 bg-[#121a2b]' : 'border-slate-800/70 hover:border-slate-700' }} text-sm">
               {{ ucfirst($st) }}
            </a>
        @endforeach
    </div>

    <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-[#0c1222] text-slate-400">
                    <tr>
                        <th class="text-left px-4 py-3">Tanggal</th>
                        <th class="text-left px-4 py-3">User</th>
                        <th class="text-left px-4 py-3">Metode</th>
                        <th class="text-left px-4 py-3">Point</th>
                        <th class="text-left px-4 py-3">Nominal</th>
                        <th class="text-left px-4 py-3">Status</th>
                        <th class="text-left px-4 py-3">WA</th>
                        <th class="text-right px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/70">
                    @forelse($items as $r)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 text-xs text-slate-300">{{ $r->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="text-slate-100">{{ $r->user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $r->user->email }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $r->method_label }}</td>
                            <td class="px-4 py-3 font-mono">{{ number_format($r->points) }}</td>
                            <td class="px-4 py-3 font-mono">Rp {{ number_format($r->amount) }}</td>
                            <td class="px-4 py-3">{{ $r->status_label }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $r->phone ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a class="px-3 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 text-xs"
                                   href="{{ route('admin.point-redeems.show', $r) }}">
                                   Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800/70">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
