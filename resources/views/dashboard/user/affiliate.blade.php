@extends('layouts.dashboard')

@section('title', 'Affiliate')
@section('breadcrumb', 'Affiliate')

@section('content')
<div class="max-w-5xl space-y-6">

    <h1 class="text-xl md:text-2xl font-semibold text-slate-100">Affiliate</h1>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    {{-- CASE 1: Sudah affiliate --}}
    @if($user->is_affiliate)
        <div class="rounded-2xl border border-slate-800/70 bg-slate-950/70 p-5 space-y-4">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <div class="text-sm text-slate-400">Status</div>
                    <div class="text-lg font-semibold text-emerald-300">Affiliate Aktif</div>

                    <div class="mt-2 text-sm text-slate-300">
                        Level: <span class="font-semibold">{{ $level->name ?? 'Default' }}</span><br>
                        Point Digiflazz: <span class="font-semibold">{{ (int)($level->digiflazz_points ?? 50) }}</span><br>
                        Point Marketplace: <span class="font-semibold">{{ (int)($level->marketplace_points ?? 2000) }}</span>
                    </div>
                </div>

                <div class="min-w-[280px]">
                    <div class="text-sm text-slate-400 mb-2">Affiliate Link</div>
                    @if($affiliateLink)
                        <div class="flex gap-2">
                            <input id="affLink" value="{{ $affiliateLink }}" readonly
                                   class="h-10 w-full rounded-xl bg-slate-900 border border-slate-700/80 px-3 text-xs text-slate-100 font-mono" />
                            <button type="button" onclick="copyAffLink()"
                                    class="h-10 px-4 rounded-xl border border-slate-700 hover:bg-slate-800 text-xs">
                                Salin
                            </button>
                        </div>
                        <p class="mt-2 text-[11px] text-slate-500">
                            Gunakan link ini untuk share. Sistem pakai last-click-wins + window 30 hari.
                        </p>
                    @else
                        <div class="text-sm text-amber-300">
                            Affiliate kamu aktif, tapi <b>affiliate_code</b> belum ada. (Nanti admin yang set saat approve)
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-800/70 bg-slate-950/70 p-4">
                <div class="text-xs text-slate-400">Total Point (Ledger)</div>
                <div class="text-2xl font-semibold text-slate-100">{{ number_format($summary['total_points']) }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Saldo point di user: {{ number_format((int)$user->maitri_points) }}</div>
            </div>
            <div class="rounded-2xl border border-slate-800/70 bg-slate-950/70 p-4">
                <div class="text-xs text-slate-400">Conversion Digiflazz</div>
                <div class="text-2xl font-semibold text-slate-100">{{ number_format($summary['digiflazz_count']) }}</div>
            </div>
            <div class="rounded-2xl border border-slate-800/70 bg-slate-950/70 p-4">
                <div class="text-xs text-slate-400">Conversion Marketplace</div>
                <div class="text-2xl font-semibold text-slate-100">{{ number_format($summary['marketplace_count']) }}</div>
            </div>
        </div>

        {{-- Riwayat conversions --}}
        <div class="rounded-2xl border border-slate-800/70 bg-slate-950/70 p-5">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-100">Riwayat Reward</h2>
                <div class="text-xs text-slate-500">Menampilkan ledger affiliate_conversions</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 text-xs">
                            <th class="py-2 pr-3 text-left">Tanggal</th>
                            <th class="py-2 px-3 text-left">Tipe</th>
                            <th class="py-2 px-3 text-left">Order ID</th>
                            <th class="py-2 px-3 text-right">Point</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($conversions as $c)
                            <tr>
                                <td class="py-2 pr-3 text-slate-300 text-xs">{{ $c->awarded_at?->format('d M Y H:i') ?? '-' }}</td>
                                <td class="py-2 px-3 text-slate-200">{{ strtoupper($c->order_type) }}</td>
                                <td class="py-2 px-3 text-slate-300 font-mono text-xs">{{ $c->order_id }}</td>
                                <td class="py-2 px-3 text-right font-semibold text-emerald-300">{{ number_format($c->points_awarded) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-sm text-slate-500">Belum ada reward.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $conversions->links() }}
            </div>
        </div>

    {{-- CASE 2: Belum affiliate --}}
    @else
        <div class="rounded-2xl border border-slate-800/70 bg-slate-950/70 p-5 space-y-3">
            <div class="text-slate-100 font-semibold">Kamu belum terdaftar sebagai affiliate</div>

            @if($application && $application->status === 'pending')
                <div class="text-sm text-amber-300">
                    Pengajuan kamu <b>sedang diproses</b>. Silakan tunggu admin.
                </div>
            @elseif($application && $application->status === 'rejected')
                <div class="text-sm text-rose-300">
                    Pengajuan kamu <b>ditolak</b>.
                    @if($application->note)
                        <div class="text-xs text-slate-400 mt-1">Catatan: {{ $application->note }}</div>
                    @endif
                </div>

                <form method="post" action="{{ route('dashboard.affiliate.apply') }}">
                    @csrf
                    <button class="h-10 px-4 rounded-xl bg-slate-200 text-slate-900 text-sm font-medium hover:bg-white">
                        Ajukan Lagi
                    </button>
                </form>
            @else
                <p class="text-sm text-slate-400">
                    Daftar affiliate untuk mendapatkan point dari pembelian melalui link kamu.
                </p>
                <form method="post" action="{{ route('dashboard.affiliate.apply') }}">
                    @csrf
                    <button class="h-10 px-4 rounded-xl bg-slate-200 text-slate-900 text-sm font-medium hover:bg-white">
                        Daftar Affiliate
                    </button>
                </form>
            @endif
        </div>
    @endif

</div>
@endsection

@push('body')
<script>
function copyAffLink() {
    const el = document.getElementById('affLink');
    if (!el) return;
    const text = el.value || '';
    navigator.clipboard.writeText(text).then(() => {
        alert('Link disalin!');
    });
}
</script>
@endpush
