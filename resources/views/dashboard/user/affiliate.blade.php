@extends('layouts.dashboard')

@section('title', 'Affiliate')
@section('breadcrumb', 'Dashboard • Affiliate')

@section('content')
@php
  $user = auth()->user();

  /**
   * Controller kamu kemungkinan sudah mengirim variabel seperti:
   * $application, $affiliateLink, $levelName, $totalLedger, $convDigi, $convMarket, $redeems, $rewards
   * Tapi supaya aman, kita kasih fallback.
   */
  $application = $application ?? null;

  $levelName = $levelName
    ?? ($affiliateLevel ?? ($user->affiliate_level ?? 'Bronze'));

  $affiliateLink = $affiliateLink
    ?? ($refLink ?? url('/?ref=' . ($user->affiliate_code ?? '')));

  $points = $points ?? ($user->maitri_points ?? 0);

  $totalLedger = $totalLedger ?? ($ledgerTotal ?? 0);
$convDigi = $convDigi ?? ($conversionDigiflazz ?? 0);

  $convMarket = $convMarket ?? ($conversionMarketplace ?? 0);

  $redeems = $redeems ?? ($pointRedemptions ?? collect());
  $rewards = $rewards ?? ($affiliateConversions ?? collect());

  // Level rules sesuai request kamu
  $levels = [
    [
      'key' => 'bronze',
      'name' => 'Bronze',
      'badge' => 'bg-amber-500/10 border-amber-500/30 text-amber-200',
      'accent' => 'from-amber-500/25 to-transparent',
      'up_digi' => 100, 'up_market' => 50,
      'stay_digi' => 100, 'stay_market' => 50,
      'note' => 'Level awal setelah pengajuan kamu disetujui.',
    ],
    [
      'key' => 'gold',
      'name' => 'Gold',
      'badge' => 'bg-yellow-400/10 border-yellow-400/30 text-yellow-200',
      'accent' => 'from-yellow-400/20 to-transparent',
      // Bronze -> Gold pakai syarat Bronze
      'up_digi' => 100, 'up_market' => 50,
      'stay_digi' => 100, 'stay_market' => 50,
      'note' => 'Naik dari Bronze dengan target Bronze. Evaluasi tiap 2 bulan.',
    ],
    [
      'key' => 'platinum',
      'name' => 'Platinum',
      'badge' => 'bg-sky-500/10 border-sky-500/30 text-sky-200',
      'accent' => 'from-sky-500/20 to-transparent',
      // Gold -> Platinum pakai syarat Platinum
      'up_digi' => 200, 'up_market' => 100,
      'stay_digi' => 200, 'stay_market' => 100,
      'note' => 'Level tertinggi dengan benefit terbaik.',
    ],
  ];
@endphp

{{-- =========================
   CASE 1: SUDAH AFFILIATE
========================= --}}
@if($user->is_affiliate)

  <section class="space-y-6">

    {{-- HERO / STATUS + LINK --}}
    <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5 md:p-7 overflow-hidden relative">
      <div class="pointer-events-none absolute -right-24 -top-24 size-[340px] rounded-full bg-violet-600/15 blur-3xl"></div>

      <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">
        <div class="min-w-0">
          <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-2 text-[11px] px-2.5 py-1 rounded-full border border-emerald-500/25 bg-emerald-500/10 text-emerald-200">
              <span class="size-1.5 rounded-full bg-emerald-400"></span>
              Affiliate Aktif
            </span>

            <span class="inline-flex items-center gap-2 text-[11px] px-2.5 py-1 rounded-full border border-slate-700/60 bg-slate-950/30 text-slate-300">
              Level: <b class="text-slate-100">{{ $levelName }}</b>
            </span>

            <span class="inline-flex items-center gap-2 text-[11px] px-2.5 py-1 rounded-full border border-violet-500/20 bg-violet-500/10 text-violet-200">
              Point: <b class="text-slate-100">{{ number_format($points) }}</b>
            </span>
          </div>

          <h1 class="mt-3 text-xl md:text-2xl font-semibold text-slate-100">
            Affiliate Dashboard
          </h1>
          <p class="mt-2 text-sm text-slate-400 max-w-2xl">
            Bagikan link affiliate kamu, kumpulkan transaksi, dan redeem point langsung dari sini.
            Panduan program & aturan level tetap tersedia di bawah.
          </p>

          {{-- SUPER SINGKAT --}}
          <div class="mt-4 rounded-2xl border border-slate-800/70 bg-[#0B1222]/40 p-4">
            <div class="text-xs font-semibold text-slate-200">Versi singkat</div>
            <ul class="mt-2 text-xs text-slate-400 space-y-1">
              <li>• Link memakai <b>last-click</b> dan cookie <b>30 hari</b>.</li>
              <li>• Evaluasi level tiap <b>2 bulan</b>. Tidak capai target → level turun 1 tingkat.</li>
              <li>• Bronze: <b>100</b> Digital Goods + <b>50</b> Marketplace | Platinum: <b>200</b> + <b>100</b>.</li>
            </ul>
          </div>
        </div>

        {{-- LINK CARD --}}
        <div class="w-full lg:w-[420px]">
          <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5">
            <div class="text-sm font-semibold text-slate-100">Affiliate Link</div>
            <p class="mt-1 text-xs text-slate-400">
              Bagikan link ini. Transaksi dari link kamu akan dihitung sebagai TRX affiliate.
            </p>

            <div class="mt-3 flex gap-2">
              <input id="affiliateLinkInput" readonly
                value="{{ $affiliateLink }}"
                class="w-full h-11 rounded-2xl bg-slate-950/40 border border-slate-800/70 px-4 text-xs text-slate-200
                      focus:outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/40" />
              <button id="copyAffiliateLinkBtn" type="button"
                class="h-11 px-4 rounded-2xl border border-slate-800/70 hover:bg-slate-900/40 text-xs transition">
                Salin
              </button>
            </div>

            <div id="copyHint" class="mt-2 text-[11px] text-slate-500">
              Tip: tempel di bio / grup. Makin sering dibagikan, makin cepat naik level.
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- STATS --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5">
        <div class="text-xs text-slate-400">Total Point (Ledger)</div>
        <div class="mt-2 text-2xl font-semibold text-slate-100">{{ number_format($totalLedger) }}</div>
        <div class="mt-2 text-[11px] text-slate-500">Total akumulasi point dari reward (ledger).</div>
      </div>

      <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5">
        <div class="text-xs text-slate-400">Conversion Digital Goods</div>
        <div class="mt-2 text-2xl font-semibold text-slate-100">{{ number_format($convDigi) }}</div>
        <div class="mt-2 text-[11px] text-slate-500">Jumlah reward dari transaksi Digital Goods.</div>
      </div>

      <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5">
        <div class="text-xs text-slate-400">Conversion Marketplace</div>
        <div class="mt-2 text-2xl font-semibold text-slate-100">{{ number_format($convMarket) }}</div>
        <div class="mt-2 text-[11px] text-slate-500">Jumlah reward dari transaksi Marketplace.</div>
      </div>
    </div>

    {{-- GUIDE ACCORDION --}}
    <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 overflow-hidden">
      <button type="button" id="toggleAffiliateGuide"
        class="w-full p-5 md:p-6 flex items-center justify-between gap-4 hover:bg-slate-950/30 transition">
        <div class="text-left">
          <div class="text-sm font-semibold text-slate-100">Panduan Program & Aturan Level</div>
          <div class="text-xs text-slate-400 mt-1">Klik untuk buka/tutup penjelasan lengkap.</div>
        </div>
        <span id="guideChevron" class="text-slate-400 transition-transform">
          <svg class="size-5" viewBox="0 0 24 24" fill="none">
            <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </span>
      </button>

      <div id="affiliateGuideBody" class="hidden border-t border-slate-800/70 p-5 md:p-7 space-y-6">

        {{-- How it works --}}
        <div class="grid gap-4 md:grid-cols-3">
          @php
            $steps = [
              ['t' => 'Bagikan Link', 'd' => 'Share link affiliate kamu ke teman / grup / social media.'],
              ['t' => 'Transaksi Terhitung', 'd' => 'Pembelian lewat link kamu dihitung jadi TRX affiliate.'],
              ['t' => 'Evaluasi 2 Bulan', 'd' => 'Tidak capai target level → turun 1 tingkat (manual admin).'],
            ];
          @endphp
          @foreach($steps as $i => $s)
            <div class="rounded-3xl border border-slate-800/70 bg-slate-950/35 p-5">
              <div class="flex items-center justify-between">
                <div class="text-sm font-semibold text-slate-100">{{ $s['t'] }}</div>
                <div class="text-[11px] px-2 py-1 rounded-full border border-slate-800/70 bg-slate-950/30 text-slate-400">
                  Step {{ $i+1 }}
                </div>
              </div>
              <p class="mt-2 text-sm text-slate-400">{{ $s['d'] }}</p>
            </div>
          @endforeach
        </div>

        {{-- Levels --}}
        <div class="rounded-3xl border border-slate-800/70 bg-slate-950/35 p-5 md:p-6">
          <div class="flex items-end justify-between gap-3 flex-wrap">
            <div>
              <h3 class="text-base font-semibold text-slate-100">Level & Persyaratan</h3>
              <p class="mt-1 text-sm text-slate-400">Target dihitung dalam periode 2 bulan.</p>
            </div>
            <div class="text-[11px] text-slate-500">TRX = transaksi affiliate</div>
          </div>

          <div class="mt-5 grid gap-4 lg:grid-cols-3">
            @foreach($levels as $lv)
              <div class="rounded-3xl border border-slate-800/70 bg-slate-950/30 overflow-hidden relative">
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-b {{ $lv['accent'] }}"></div>
                <div class="p-5 relative">
                  <div class="flex items-center justify-between">
                    <div class="text-sm font-semibold text-slate-100">{{ $lv['name'] }}</div>
                    <span class="text-[11px] px-2 py-0.5 rounded-full border {{ $lv['badge'] }}">Target 2 bulan</span>
                  </div>
                  <p class="mt-2 text-xs text-slate-400">{{ $lv['note'] }}</p>

                  <div class="mt-4 grid gap-3">
                    <div class="rounded-2xl border border-slate-800/70 bg-[#0B1222]/35 p-4">
                      <div class="text-xs font-semibold text-slate-200">Syarat naik ke level ini</div>
                      <div class="mt-2 text-xs text-slate-400 space-y-1">
                        <div class="flex items-center justify-between">
                          <span>Digital Goods</span>
                          <span class="font-semibold text-slate-100">{{ $lv['up_digi'] }} TRX</span>
                        </div>
                        <div class="flex items-center justify-between">
                          <span>Marketplace</span>
                          <span class="font-semibold text-slate-100">{{ $lv['up_market'] }} TRX</span>
                        </div>
                      </div>
                    </div>

                    <div class="rounded-2xl border border-slate-800/70 bg-[#0B1222]/35 p-4">
                      <div class="text-xs font-semibold text-slate-200">Syarat bertahan (evaluasi 2 bulan)</div>
                      <div class="mt-2 text-xs text-slate-400 space-y-1">
                        <div class="flex items-center justify-between">
                          <span>Digital Goods</span>
                          <span class="font-semibold text-slate-100">{{ $lv['stay_digi'] }} TRX</span>
                        </div>
                        <div class="flex items-center justify-between">
                          <span>Marketplace</span>
                          <span class="font-semibold text-slate-100">{{ $lv['stay_market'] }} TRX</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="mt-4 text-[11px] text-slate-500">
                    *Naik/turun level ditetapkan manual oleh admin.
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        {{-- Evaluation highlight --}}
        <div class="rounded-3xl border border-amber-500/30 bg-amber-500/10 p-5">
          <div class="text-sm font-semibold text-amber-200">Aturan evaluasi 2 bulan</div>
          <ul class="mt-2 text-sm text-amber-100/80 space-y-1">
            <li>• Jika target level saat ini terpenuhi → level tetap.</li>
            <li>• Jika tidak terpenuhi → level turun 1 tingkat pada evaluasi berikutnya.</li>
            <li>• Penetapan naik/turun level: <b>manual admin</b>.</li>
          </ul>
        </div>

      </div>
    </div>

    {{-- REDEEM --}}
    <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5 md:p-7">
      <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
          <div class="text-base font-semibold text-slate-100">Redeem Maitri Point</div>
          <p class="mt-1 text-sm text-slate-400">
            Tukar point jadi Saldo Maitri (instan) atau Uang Cash (butuh approval admin).
          </p>
        </div>
        <div class="text-xs text-slate-400">
          Point kamu: <b class="text-slate-100">{{ number_format($points) }}</b>
        </div>
      </div>

      <form method="post" action="{{ route('dashboard.affiliate.redeem') }}" class="mt-5 grid gap-3 lg:grid-cols-12">
        @csrf

        <div class="lg:col-span-3">
          <label class="text-xs text-slate-400">Jumlah Point</label>
          <input name="points" type="number" min="1" value="{{ old('points', 100) }}"
            class="mt-1 w-full h-11 rounded-2xl bg-slate-950/40 border border-slate-800/70 px-4 text-sm
                   focus:outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/40">
        </div>

        <div class="lg:col-span-4">
          <label class="text-xs text-slate-400">Metode</label>
          <select id="redeemMethod" name="method"
            class="mt-1 w-full h-11 rounded-2xl bg-slate-950/40 border border-slate-800/70 px-4 text-sm
                   focus:outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/40">
            <option value="wallet" @selected(old('method')==='wallet')>Saldo Maitri (instan)</option>
            <option value="cash" @selected(old('method')==='cash')>Uang Cash (butuh approval admin)</option>
          </select>
        </div>

        <div id="waField" class="lg:col-span-5 hidden">
          <label class="text-xs text-slate-400">No WhatsApp (wajib untuk cash)</label>
          <input name="whatsapp" value="{{ old('whatsapp') }}" placeholder="contoh: 08xxxxxxxxxx"
            class="mt-1 w-full h-11 rounded-2xl bg-slate-950/40 border border-slate-800/70 px-4 text-sm
                   placeholder:text-slate-500 focus:outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/40">
        </div>

        <div class="lg:col-span-12 flex justify-end pt-2">
          <button class="h-11 px-5 rounded-2xl bg-violet-600 hover:bg-violet-500 text-white text-sm font-medium transition">
            Kirim Redeem
          </button>
        </div>
      </form>
    </div>

    {{-- TABLES --}}
    <div class="grid gap-4 lg:grid-cols-2">

      {{-- Redeem history --}}
      <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 overflow-hidden">
        <div class="p-5 border-b border-slate-800/70">
          <div class="text-sm font-semibold text-slate-100">Riwayat Redeem</div>
          <div class="text-xs text-slate-400 mt-1">Permintaan redeem point kamu.</div>
        </div>

        <div class="p-4 overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="text-xs text-slate-400">
              <tr>
                <th class="text-left py-2 px-2">Tanggal</th>
                <th class="text-left py-2 px-2">Metode</th>
                <th class="text-left py-2 px-2">Status</th>
                <th class="text-left py-2 px-2">No WA</th>
                <th class="text-right py-2 px-2">Point</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/70">
              @forelse($redeems as $r)
                @php
                  $st = strtoupper($r->status ?? '');
                  $badge = match ($st) {
                    'APPROVED' => 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300',
                    'REJECTED' => 'bg-rose-500/10 border-rose-500/30 text-rose-300',
                    'INSTANT'  => 'bg-violet-500/10 border-violet-500/30 text-violet-300',
                    default    => 'bg-amber-500/10 border-amber-500/30 text-amber-300',
                  };
                @endphp
                <tr>
                  <td class="py-2 px-2 text-slate-300">{{ $r->created_at?->format('d M Y H:i') }}</td>
                  <td class="py-2 px-2 text-slate-300">{{ ($r->method ?? '') === 'cash' ? 'Uang Cash' : 'Saldo Maitri' }}</td>
                  <td class="py-2 px-2">
                    <span class="text-[11px] px-2 py-0.5 rounded-full border {{ $badge }}">{{ $st }}</span>
                  </td>
                  <td class="py-2 px-2 text-slate-400">{{ $r->whatsapp ?? '-' }}</td>
                  <td class="py-2 px-2 text-right text-slate-100 font-semibold">{{ number_format($r->points ?? 0) }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="py-6 text-center text-slate-500">Belum ada redeem.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- Reward history --}}
      <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 overflow-hidden">
        <div class="p-5 border-b border-slate-800/70">
          <div class="text-sm font-semibold text-slate-100">Riwayat Reward</div>
          <div class="text-xs text-slate-400 mt-1">Point yang masuk dari transaksi affiliate.</div>
        </div>

        <div class="p-4 overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="text-xs text-slate-400">
              <tr>
                <th class="text-left py-2 px-2">Tanggal</th>
                <th class="text-left py-2 px-2">Tipe</th>
                <th class="text-left py-2 px-2">Kode Transaksi</th>
                <th class="text-right py-2 px-2">Point</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/70">
              @forelse($rewards as $rw)
                <tr>
                  <td class="py-2 px-2 text-slate-300">{{ $rw->created_at?->format('d M Y H:i') }}</td>
                  <td class="py-2 px-2 text-slate-300">{{ ($rw->type ?? '') === 'marketplace' ? 'Marketplace' : 'Digital Goods' }}</td>
                  <td class="py-2 px-2 text-slate-400 font-mono">{{ $rw->order_code ?? '-' }}</td>
                  <td class="py-2 px-2 text-right text-slate-100 font-semibold">{{ number_format($rw->points ?? 0) }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="py-6 text-center text-slate-500">Belum ada reward.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </section>


{{-- =========================
   CASE 2: BELUM AFFILIATE
========================= --}}
@else

  <section class="space-y-6">

    {{-- HERO --}}
    <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5 md:p-7 overflow-hidden relative">
      <div class="pointer-events-none absolute -right-24 -top-24 size-[320px] rounded-full bg-violet-600/15 blur-3xl"></div>

      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
        <div class="min-w-0">
          <div class="inline-flex items-center gap-2 text-[11px] px-2.5 py-1 rounded-full border border-violet-500/20 bg-violet-500/10 text-violet-200">
            <span class="size-1.5 rounded-full bg-violet-400"></span>
            Program Affiliate MaitriProject
          </div>

          <h2 class="mt-3 text-xl md:text-2xl font-semibold text-slate-100">
            Dapatkan point dari setiap pembelian melalui link kamu
          </h2>

          <p class="mt-2 text-sm text-slate-400 max-w-2xl">
            Bagikan link affiliate kamu, lalu sistem akan menghitung transaksi dari user lain yang membeli lewat link tersebut.
            Semakin banyak transaksi, semakin tinggi level affiliate kamu.
          </p>

          {{-- SUPER SINGKAT --}}
          <div class="mt-4 rounded-2xl border border-slate-800/70 bg-[#0B1222]/40 p-4">
            <div class="text-xs font-semibold text-slate-200">Versi singkat</div>
            <ul class="mt-2 text-xs text-slate-400 space-y-1">
              <li>• Bagikan link → dapat TRX dari pembelian user lain.</li>
              <li>• Evaluasi tiap <b>2 bulan</b>. Tidak capai target → level turun 1 tingkat.</li>
              <li>• Bronze: <b>100</b> Digital Goods + <b>50</b> Marketplace | Platinum: <b>200</b> + <b>100</b>.</li>
            </ul>
          </div>
        </div>

        {{-- CTA --}}
        <div class="shrink-0 w-full lg:w-[360px]">
          <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5">
            <div class="text-sm font-semibold text-slate-100">Mulai jadi affiliate</div>
            <p class="mt-1 text-xs text-slate-400">
              Gratis. Cukup ajukan, lalu admin akan review pengajuan kamu.
            </p>

            <div class="mt-4 space-y-3">
              @if($application && ($application->status ?? null) === 'pending')
                <div class="rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4 text-sm text-amber-200">
                  Pengajuan kamu <b>sedang diproses</b>. Silakan tunggu admin.
                </div>
              @elseif($application && ($application->status ?? null) === 'rejected')
                <div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 p-4 text-sm text-rose-200">
                  Pengajuan kamu <b>ditolak</b>.
                  @if(!empty($application->note))
                    <div class="mt-2 text-xs text-slate-300">Catatan: {{ $application->note }}</div>
                  @endif
                </div>

                <form method="post" action="{{ route('dashboard.affiliate.apply') }}">
                  @csrf
                  <button class="w-full h-11 rounded-2xl bg-violet-600 hover:bg-violet-500 text-white text-sm font-medium transition">
                    Ajukan Lagi
                  </button>
                </form>
              @else
                <form method="post" action="{{ route('dashboard.affiliate.apply') }}">
                  @csrf
                  <button class="w-full h-11 rounded-2xl bg-violet-600 hover:bg-violet-500 text-white text-sm font-medium transition">
                    Daftar Affiliate
                  </button>
                </form>

                <div class="text-[11px] text-slate-500">
                  Dengan mendaftar, kamu setuju program ini memiliki evaluasi level per 2 bulan & level dapat berubah sesuai kebijakan admin.
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- HOW IT WORKS --}}
    <div class="grid gap-4 md:grid-cols-3">
      @php
        $steps = [
          ['t' => 'Daftar Affiliate', 'd' => 'Ajukan pendaftaran, admin akan review.'],
          ['t' => 'Bagikan Link', 'd' => 'Share ke teman / grup / social media.'],
          ['t' => 'Naik Level', 'd' => 'Transaksi dari link kamu dihitung jadi TRX.'],
        ];
      @endphp

      @foreach($steps as $i => $s)
        <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5">
          <div class="flex items-center justify-between">
            <div class="text-sm font-semibold text-slate-100">{{ $s['t'] }}</div>
            <div class="text-[11px] px-2 py-1 rounded-full border border-slate-800/70 bg-slate-950/30 text-slate-400">
              Step {{ $i+1 }}
            </div>
          </div>
          <p class="mt-2 text-sm text-slate-400">{{ $s['d'] }}</p>
        </div>
      @endforeach
    </div>

    {{-- LEVELS --}}
    <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5 md:p-7">
      <div class="flex items-end justify-between gap-3 flex-wrap">
        <div>
          <h3 class="text-base md:text-lg font-semibold text-slate-100">Level Affiliate & Persyaratan</h3>
          <p class="mt-1 text-sm text-slate-400">
            Target dihitung berdasarkan jumlah transaksi melalui link affiliate kamu.
          </p>
        </div>
        <div class="text-[11px] text-slate-500">TRX = transaksi affiliate (Digital Goods / Marketplace)</div>
      </div>

      <div class="mt-5 grid gap-4 lg:grid-cols-3">
        @foreach($levels as $lv)
          <div class="rounded-3xl border border-slate-800/70 bg-slate-950/35 overflow-hidden relative">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-b {{ $lv['accent'] }}"></div>

            <div class="p-5 relative">
              <div class="flex items-center justify-between">
                <div class="text-sm font-semibold text-slate-100">{{ $lv['name'] }}</div>
                <span class="text-[11px] px-2 py-0.5 rounded-full border {{ $lv['badge'] }}">
                  Target 2 bulan
                </span>
              </div>

              <p class="mt-2 text-xs text-slate-400">{{ $lv['note'] }}</p>

              <div class="mt-4 grid gap-3">
                <div class="rounded-2xl border border-slate-800/70 bg-[#0B1222]/35 p-4">
                  <div class="text-xs font-semibold text-slate-200">Syarat naik ke level ini</div>
                  <div class="mt-2 text-xs text-slate-400 space-y-1">
                    <div class="flex items-center justify-between">
                      <span>Digital Goods</span>
                      <span class="font-semibold text-slate-100">{{ $lv['up_digi'] }} TRX</span>
                    </div>
                    <div class="flex items-center justify-between">
                      <span>Marketplace</span>
                      <span class="font-semibold text-slate-100">{{ $lv['up_market'] }} TRX</span>
                    </div>
                  </div>
                </div>

                <div class="rounded-2xl border border-slate-800/70 bg-[#0B1222]/35 p-4">
                  <div class="text-xs font-semibold text-slate-200">Syarat bertahan (evaluasi 2 bulan)</div>
                  <div class="mt-2 text-xs text-slate-400 space-y-1">
                    <div class="flex items-center justify-between">
                      <span>Digital Goods</span>
                      <span class="font-semibold text-slate-100">{{ $lv['stay_digi'] }} TRX</span>
                    </div>
                    <div class="flex items-center justify-between">
                      <span>Marketplace</span>
                      <span class="font-semibold text-slate-100">{{ $lv['stay_market'] }} TRX</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="mt-4 text-[11px] text-slate-500">
                *Jika tidak memenuhi target, level bisa turun pada evaluasi berikutnya.
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- RULES --}}
    <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5 md:p-7">
      <div class="flex items-start gap-4">
        <div class="size-10 rounded-2xl bg-violet-600/15 border border-violet-500/20 grid place-items-center">
          <svg class="size-5 text-violet-200" viewBox="0 0 24 24" fill="none">
            <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="1.5"/>
          </svg>
        </div>

        <div class="min-w-0">
          <h3 class="text-base md:text-lg font-semibold text-slate-100">Aturan Evaluasi Level (2 Bulan)</h3>
          <p class="mt-1 text-sm text-slate-400">
            Level affiliate akan dievaluasi setiap <b>2 bulan</b>. Target yang dipakai adalah target level saat ini.
          </p>

          <div class="mt-4 grid gap-3 md:grid-cols-2">
            <div class="rounded-2xl border border-slate-800/70 bg-[#0B1222]/35 p-4">
              <div class="text-xs font-semibold text-slate-200">Naik level</div>
              <p class="mt-1 text-xs text-slate-400">
                Bronze → Gold memakai target Bronze. Gold → Platinum memakai target Platinum.
              </p>
            </div>

            <div class="rounded-2xl border border-slate-800/70 bg-[#0B1222]/35 p-4">
              <div class="text-xs font-semibold text-slate-200">Turun level</div>
              <p class="mt-1 text-xs text-slate-400">
                Jika target 2 bulan tidak terpenuhi, level turun 1 tingkat pada evaluasi berikutnya.
              </p>
            </div>
          </div>

          <div class="mt-4 rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4 text-xs text-amber-200">
            Penentuan naik/turun level saat ini <b>diatur manual oleh admin</b>.
          </div>
        </div>
      </div>
    </div>

    {{-- CTA AKHIR --}}
    <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5 md:p-7">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <div class="text-base font-semibold text-slate-100">Siap mulai?</div>
          <p class="mt-1 text-sm text-slate-400">
            Daftar sekarang dan mulai kumpulkan transaksi melalui link affiliate kamu.
          </p>
        </div>

        @if(!($application && ($application->status ?? null) === 'pending'))
          <form method="post" action="{{ route('dashboard.affiliate.apply') }}">
            @csrf
            <button class="h-11 px-5 rounded-2xl bg-violet-600 hover:bg-violet-500 text-white text-sm font-medium transition">
              Daftar Affiliate Sekarang
            </button>
          </form>
        @endif
      </div>
    </div>

  </section>

@endif
@endsection

@push('body')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Copy affiliate link
  const input = document.getElementById('affiliateLinkInput');
  const btn = document.getElementById('copyAffiliateLinkBtn');
  const hint = document.getElementById('copyHint');

  btn?.addEventListener('click', async () => {
    try {
      await navigator.clipboard.writeText(input?.value || '');
      if (hint) hint.textContent = 'Link berhasil disalin ✅';
      setTimeout(() => {
        if (hint) hint.textContent = 'Tip: tempel di bio / grup. Makin sering dibagikan, makin cepat naik level.';
      }, 2000);
    } catch (e) {
      if (hint) hint.textContent = 'Gagal menyalin. Silakan blok link lalu Ctrl+C.';
    }
  });

  // Toggle guide accordion (CASE affiliate aktif)
  const toggle = document.getElementById('toggleAffiliateGuide');
  const body = document.getElementById('affiliateGuideBody');
  const chev = document.getElementById('guideChevron');

  toggle?.addEventListener('click', () => {
    body?.classList.toggle('hidden');
    chev?.classList.toggle('rotate-180');
  });

  // Toggle WA field for redeem (CASE affiliate aktif)
  const method = document.getElementById('redeemMethod');
  const wa = document.getElementById('waField');

  function syncWa() {
    const isCash = (method?.value === 'cash');
    if (!wa) return;
    wa.classList.toggle('hidden', !isCash);
  }
  method?.addEventListener('change', syncWa);
  syncWa();
});
</script>
@endpush
