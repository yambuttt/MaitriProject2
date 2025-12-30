@extends('layouts.admin')
@section('title', 'Affiliate Detail — Admin')

@section('content')
    <div class="space-y-5">
        <div class="flex items-start justify-between gap-3 flex-wrap">
            <div>
                <h1 class="text-2xl font-semibold">Affiliate Detail</h1>
                <p class="text-slate-400 text-sm">{{ $user->name }} — {{ $user->email }}</p>
                <p class="text-slate-500 text-xs mt-1">Kode: <span
                        class="font-mono">{{ $user->affiliate_code ?? '-' }}</span></p>
            </div>
            <a href="{{ route('admin.affiliates.index') }}"
                class="px-3 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 text-sm">
                ← Kembali
            </a>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
                <div class="text-xs text-slate-400">Total Point (Ledger)</div>
                <div class="mt-2 text-2xl font-semibold text-slate-100">{{ number_format($summary['total_points_ledger']) }}
                </div>
                <div class="mt-1 text-xs text-slate-500">Saldo user maitri_points:
                    {{ number_format((int) $user->maitri_points) }}</div>
            </div>
            <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
                <div class="text-xs text-slate-400">Conversion Digiflazz</div>
                <div class="mt-2 text-2xl font-semibold">{{ number_format($summary['digiflazz_count']) }}</div>
            </div>
            <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
                <div class="text-xs text-slate-400">Conversion Marketplace</div>
                <div class="mt-2 text-2xl font-semibold">{{ number_format($summary['marketplace_count']) }}</div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
            <div class="text-sm text-slate-400">Affiliate Link</div>
            <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
                <div class="text-sm text-slate-400 mb-2">Set Affiliate Level</div>

                <form method="post" action="{{ route('admin.affiliates.level', $user) }}" class="flex gap-2 items-center">
                    @csrf

                    <select name="affiliate_level_id"
                        class="h-10 w-full rounded-xl bg-slate-900/60 border border-slate-700/70 px-3 text-sm outline-none">
                        <option value="">Default (auto)</option>

                        @foreach($levels as $lvl)
                            <option value="{{ $lvl->id }}" @selected($user->affiliate_level_id == $lvl->id)>
                                {{ $lvl->name }} — Digiflazz: {{ $lvl->digiflazz_points }} | Marketplace:
                                {{ $lvl->marketplace_points }} | Days: {{ $lvl->window_days }}
                                @if(!$lvl->is_active) (NONAKTIF) @endif
                            </option>
                        @endforeach
                    </select>

                    <button class="h-10 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-white text-sm">
                        Simpan
                    </button>
                </form>

                <div class="text-xs text-slate-500 mt-2">
                    Jika pilih <b>Default (auto)</b>, sistem akan pakai level aktif pertama saat menghitung poin.
                </div>
            </div>

            <div class="mt-2 flex gap-2">
                <input id="affLink" value="{{ $affiliateLink ?? '' }}" readonly
                    class="h-10 w-full rounded-xl bg-slate-900/60 border border-slate-700/70 px-3 text-xs font-mono">
                <button onclick="navigator.clipboard.writeText(document.getElementById('affLink').value)"
                    class="h-10 px-4 rounded-xl border border-slate-700/70 hover:border-slate-600 text-xs">
                    Salin
                </button>
            </div>
            <div class="text-xs text-slate-500 mt-2">
                Level: <span class="text-slate-300 font-semibold">{{ $user->affiliateLevel->name ?? 'Default' }}</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] overflow-hidden">
            <div class="p-4 border-b border-slate-800/70 flex items-center justify-between">
                <h2 class="text-sm font-semibold">Ledger Reward (affiliate_conversions)</h2>
                <span class="text-xs text-slate-500">Last 30 entries/page</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#0c1222] text-slate-400">
                        <tr>
                            <th class="text-left px-4 py-3">Tanggal</th>
                            <th class="text-left px-4 py-3">Tipe</th>
                            <th class="text-left px-4 py-3">Order ID</th>
                            <th class="text-left px-4 py-3">Buyer User ID</th>
                            <th class="text-right px-4 py-3">Point</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/70">
                        @forelse($conversions as $c)
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3 text-xs text-slate-300">{{ $c->awarded_at?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-4 py-3">{{ strtoupper($c->order_type) }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-300">{{ $c->order_id }}</td>
                                <td class="px-4 py-3 text-slate-400 text-xs">{{ $c->buyer_user_id ?? '-' }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-emerald-300">
                                    {{ number_format($c->points_awarded) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada reward.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-800/70">
                {{ $conversions->links() }}
            </div>
        </div>
    </div>
@endsection