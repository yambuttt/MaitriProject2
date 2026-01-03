@extends('layouts.dashboard')
@section('title','Dashboard — Member')
@section('breadcrumb','Dashboard')

@section('content')
  <div class="space-y-6">

    {{-- Hero / Greeting --}}
    <section class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5 md:p-7 overflow-hidden relative">
      <div class="pointer-events-none absolute -right-24 -top-24 size-[260px] rounded-full bg-violet-600/15 blur-3xl"></div>

      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-xl md:text-2xl font-semibold">Halo, {{ auth()->user()->name }} 👋</h1>
          <p class="mt-1 text-sm text-slate-400">
            Kelola saldo, riwayat transaksi, dan affiliate kamu dari sini.
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <a href="{{ route('dashboard.wallet') }}"
             class="h-10 inline-flex items-center px-4 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-medium transition">
            Topup Saldo
          </a>
          <a href="{{ route('catalog') }}"
             class="h-10 inline-flex items-center px-4 rounded-2xl border border-slate-800/70 hover:bg-slate-900/40 text-sm transition">
            Buka Katalog
          </a>
        </div>
      </div>
    </section>

    {{-- Stats cards --}}
    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5">
        <div class="text-xs text-slate-400">Saldo Maitri</div>
        <div class="mt-2 text-2xl font-semibold">
          Rp {{ number_format(auth()->user()->maitri_balance ?? 0, 0, ',', '.') }}
        </div>
        <div class="mt-2 text-[11px] text-slate-500">Dipakai untuk bayar dengan PIN</div>
      </div>

      <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5">
        <div class="text-xs text-slate-400">Maitri Points</div>
        <div class="mt-2 text-2xl font-semibold">
          {{ number_format(auth()->user()->maitri_points ?? 0) }}
        </div>
        <div class="mt-2 text-[11px] text-slate-500">Dapat dari affiliate (jika aktif)</div>
      </div>

      <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5">
        <div class="text-xs text-slate-400">Status Affiliate</div>
        <div class="mt-2 text-lg font-semibold">
          @if(auth()->user()->is_affiliate)
            <span class="text-emerald-300">Aktif</span>
          @else
            <span class="text-slate-300">Belum aktif</span>
          @endif
        </div>
        <a href="{{ route('dashboard.affiliate') }}" class="mt-3 inline-flex text-xs text-violet-300 hover:text-violet-200">
          Kelola Affiliate →
        </a>
      </div>

      <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5">
        <div class="text-xs text-slate-400">Quick Tips</div>
        <div class="mt-2 text-sm text-slate-300">
          Set PIN dulu agar bisa bayar pakai saldo.
        </div>
        <a href="{{ route('dashboard.wallet') }}" class="mt-3 inline-flex text-xs text-violet-300 hover:text-violet-200">
          Atur PIN →
        </a>
      </div>
    </section>

    {{-- Main grid --}}
    <section class="grid gap-4 lg:grid-cols-3">
      {{-- Aktivitas / placeholder yang “niat” --}}
      <div class="lg:col-span-2 rounded-3xl border border-slate-800/70 bg-slate-950/40 overflow-hidden">
        <div class="p-5 border-b border-slate-800/70 flex items-center justify-between">
          <div>
            <div class="text-sm font-semibold">Aktivitas Terakhir</div>
            <div class="text-xs text-slate-400">Riwayat topup & transaksi terbaru</div>
          </div>
          <a href="{{ route('dashboard.orders') }}" class="text-xs text-violet-300 hover:text-violet-200">Lihat semua →</a>
        </div>

        <div class="p-5">
  @if(($latestTransactions ?? collect())->isEmpty())
    <div class="rounded-2xl border border-slate-800/70 bg-[#0B1222]/50 p-4 text-sm text-slate-400">
      Belum ada transaksi. Yuk mulai topup atau beli produk.
    </div>
  @else
    <div class="space-y-2">
      @foreach($latestTransactions as $tx)
        <a href="{{ $tx['url'] }}"
           class="block rounded-2xl border border-slate-800/70 bg-[#0B1222]/40 hover:bg-[#0B1222]/60 transition p-4">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <div class="flex items-center gap-2">
                <span class="text-[11px] px-2 py-0.5 rounded-full border
                  @if($tx['source']==='digiflazz')
                    border-violet-500/20 bg-violet-500/10 text-violet-200
                  @else
                    border-sky-500/20 bg-sky-500/10 text-sky-200
                  @endif
                ">
                  {{ $tx['source']==='digiflazz' ? 'Digital Goods' : 'Marketplace' }}

                </span>

                <span class="text-xs text-slate-500">• {{ $tx['code'] }}</span>
              </div>

              <div class="mt-1 font-medium truncate">
                {{ $tx['title'] }}
              </div>

              <div class="mt-1 text-xs text-slate-500">
                {{ optional($tx['created_at'])->diffForHumans() }}
              </div>
            </div>

            <div class="text-right shrink-0">
              <div class="font-semibold">
                Rp {{ number_format($tx['amount'] ?? 0, 0, ',', '.') }}
              </div>

              <div class="mt-1 text-[11px] px-2 py-0.5 rounded-full inline-flex border border-slate-700/60 text-slate-300">
                {{ $tx['status'] }}
              </div>
            </div>
          </div>
        </a>
      @endforeach
    </div>
  @endif
</div>

      </div>

      {{-- Quick actions --}}
      <div class="rounded-3xl border border-slate-800/70 bg-slate-950/40 p-5 space-y-3">
        <div>
          <div class="text-sm font-semibold">Aksi Cepat</div>
          <div class="text-xs text-slate-400">Biar user gampang navigasi</div>
        </div>

        <a href="{{ route('dashboard.wallet') }}"
           class="w-full h-11 rounded-2xl border border-slate-800/70 hover:bg-slate-900/40 px-4 flex items-center justify-between transition">
          <span class="text-sm">Saldo & Topup</span>
          <span class="text-slate-500">→</span>
        </a>

        <a href="{{ route('dashboard.marketplace.orders') }}"
           class="w-full h-11 rounded-2xl border border-slate-800/70 hover:bg-slate-900/40 px-4 flex items-center justify-between transition">
          <span class="text-sm">Pesanan Marketplace</span>
          <span class="text-slate-500">→</span>
        </a>

        <a href="{{ route('dashboard.affiliate') }}"
           class="w-full h-11 rounded-2xl bg-violet-600 hover:bg-violet-500 px-4 flex items-center justify-between transition">
          <span class="text-sm font-medium">Affiliate</span>
          <span class="text-white/80">→</span>
        </a>
      </div>
    </section>

  </div>
@endsection
