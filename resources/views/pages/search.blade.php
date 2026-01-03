@extends('layouts.app')

@section('title', 'Pencarian — MaitriProject')
@section('page', 'search')

@php
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Str;

  $isAll = ($tab ?? 'all') === 'all';
  $isDigital = ($tab ?? 'all') === 'digital';
  $isMarketplace = ($tab ?? 'all') === 'marketplace';

  // hitung jumlah hasil (untuk badge ringkas)
  $digitalCount = 0;
  $marketCount  = 0;

  if ($isAll) {
    $digitalCount = is_countable($digital ?? []) ? count($digital) : 0;
    $marketCount  = is_countable($marketplace ?? []) ? count($marketplace) : 0;
  } else {
    if ($isDigital && isset($digital)) $digitalCount = method_exists($digital, 'total') ? $digital->total() : (is_countable($digital) ? count($digital) : 0);
    if ($isMarketplace && isset($marketplace)) $marketCount = method_exists($marketplace, 'total') ? $marketplace->total() : (is_countable($marketplace) ? count($marketplace) : 0);
  }

  $btnClass = function ($active) {
    return $active
      ? 'bg-violet-600/20 text-violet-200 border-violet-700/40'
      : 'bg-slate-950/30 text-slate-300 border-slate-800/70 hover:bg-slate-900/40';
  };
@endphp

@section('content')
<section class="relative overflow-hidden py-8">
  {{-- BG glow --}}
  <div class="pointer-events-none absolute inset-0">
    <div class="absolute -top-24 -right-24 w-[520px] h-[520px] rounded-full blur-3xl bg-violet-600/15"></div>
    <div class="absolute -bottom-24 -left-24 w-[560px] h-[560px] rounded-full blur-3xl bg-indigo-500/10"></div>
    <div class="absolute inset-0 [background-image:linear-gradient(rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.03)_1px,transparent_1px)] [background-size:48px_48px] [mask-image:radial-gradient(closest-side,black,transparent)] opacity-70"></div>
  </div>

  <div class="relative mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8 space-y-6">

    {{-- HERO --}}
    <div class="rounded-3xl border border-slate-800/70 bg-slate-900/45 p-5 md:p-7">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="min-w-0">
          <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400">
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-800/70 bg-slate-950/30 px-3 py-1">
              <span class="size-1.5 rounded-full bg-emerald-400"></span>
              Hasil Pencarian
            </span>

            <span class="hidden md:inline text-slate-600">•</span>
            <span>Kata kunci:</span>
            <span class="text-slate-200 font-medium">“{{ $q }}”</span>
          </div>

          <h1 class="mt-2 text-2xl md:text-3xl font-semibold tracking-tight text-slate-50">
            Temukan produk yang kamu cari
          </h1>

          <p class="mt-1 text-sm text-slate-400 max-w-prose">
            Cari dari <b>Digital Goods</b> (pulsa/data/e-wallet/game) dan <b>Marketplace</b> (akun premium, dsb).
          </p>

          {{-- Quick stats --}}
          <div class="mt-4 flex flex-wrap gap-2 text-xs">
            <span class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/70 bg-slate-950/30 px-3 py-2 text-slate-300">
              Digital: <b class="text-slate-100">{{ $digitalCount }}</b>
            </span>
            <span class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/70 bg-slate-950/30 px-3 py-2 text-slate-300">
              Marketplace: <b class="text-slate-100">{{ $marketCount }}</b>
            </span>
          </div>
        </div>

        <div class="shrink-0 flex items-center gap-2">
          <a href="{{ route('landing') }}"
             class="h-10 px-4 rounded-2xl border border-slate-800/70 bg-slate-950/30 hover:bg-slate-900/40 text-sm transition">
            ← Kembali
          </a>
          <a href="{{ route('catalog') }}"
             class="h-10 px-4 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-medium transition shadow-lg shadow-violet-900/25">
            Buka Katalog
          </a>
        </div>
      </div>
    </div>

    {{-- Tabs --}}
    <div class="rounded-3xl border border-slate-800/70 bg-slate-950/30 p-4 md:p-5">
      <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-wrap gap-2">
          <a href="{{ route('search', ['q' => $q, 'tab' => 'all']) }}"
             class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-sm border transition {{ $btnClass($isAll) }}">
            Semua
            <span class="text-[11px] px-2 py-0.5 rounded-full border border-slate-800/70 bg-slate-950/30 text-slate-300">
              {{ $digitalCount + $marketCount }}
            </span>
          </a>

          <a href="{{ route('search', ['q' => $q, 'tab' => 'digital']) }}"
             class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-sm border transition {{ $btnClass($isDigital) }}">
            Digital Goods
            <span class="text-[11px] px-2 py-0.5 rounded-full border border-slate-800/70 bg-slate-950/30 text-slate-300">
              {{ $digitalCount }}
            </span>
          </a>

          <a href="{{ route('search', ['q' => $q, 'tab' => 'marketplace']) }}"
             class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-sm border transition {{ $btnClass($isMarketplace) }}">
            Marketplace
            <span class="text-[11px] px-2 py-0.5 rounded-full border border-slate-800/70 bg-slate-950/30 text-slate-300">
              {{ $marketCount }}
            </span>
          </a>
        </div>

        <div class="text-xs text-slate-500">
          Tip: pakai kata kunci spesifik (misal: <span class="text-slate-300">Netflix 1 Bulan</span>)
        </div>
      </div>
    </div>

    {{-- CONTENT --}}
    @if($isAll)
      <div class="grid gap-6 lg:grid-cols-2 items-start">

        {{-- DIGITAL SECTION --}}
        <div class="rounded-3xl border border-slate-800/70 bg-slate-900/45 p-5 md:p-6">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-lg font-semibold text-slate-100">Digital Goods</h2>
              <p class="text-xs text-slate-400 mt-1">Top up cepat: pulsa, data, e-wallet, game, dll.</p>
            </div>

            <a href="{{ route('search', ['q' => $q, 'tab' => 'digital']) }}"
               class="text-sm text-violet-300 hover:text-violet-200">
              Lihat semua →
            </a>
          </div>

          <div class="mt-4 grid gap-3 sm:grid-cols-2">
            @forelse($digital as $p)
              @php
                $minFinal = $p->variants->min(fn($v) => $v->final_price);
                $meta = trim(($p->category?->name ?? '') . ($p->subcategory ? ' • '.$p->subcategory->name : ''));
              @endphp

              <a href="{{ route('catalog.product.show', $p->slug) }}"
                 class="group rounded-3xl border border-slate-800/70 bg-slate-950/25 hover:bg-slate-950/35 hover:border-violet-700/50 transition p-4">
                <div class="flex items-start gap-3">
                  <div class="size-11 rounded-2xl border border-slate-800/70 bg-slate-950/40 overflow-hidden shrink-0 grid place-items-center text-[10px] text-slate-500">
                    @if(!empty($p->thumbnail))
                      <img src="{{ Storage::url($p->thumbnail) }}" class="w-full h-full object-cover" alt="{{ $p->name }}">
                    @else
                      DG
                    @endif
                  </div>

                  <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 text-[11px] text-slate-400">
                      <span class="px-2 py-0.5 rounded-full border border-violet-700/30 bg-violet-600/15 text-violet-200">
                        Digital Goods
                      </span>
                      <span class="truncate">{{ $meta }}</span>
                    </div>

                    <div class="mt-1 font-semibold text-slate-100 group-hover:text-violet-100 truncate">
                      {{ $p->name }}
                    </div>

                    <div class="mt-2 flex items-center justify-between">
                      <div class="text-xs text-slate-400">
                        Mulai <span class="text-slate-100 font-semibold">Rp {{ number_format($minFinal,0,',','.') }}</span>
                      </div>
                      <span class="inline-flex items-center justify-center h-9 px-3 rounded-2xl bg-violet-600 hover:bg-violet-500 text-xs font-medium transition">
                        Top Up
                      </span>
                    </div>
                  </div>
                </div>
              </a>
            @empty
              <div class="col-span-full rounded-3xl border border-dashed border-slate-800/70 bg-slate-950/20 p-8 text-center">
                <div class="text-sm text-slate-300 font-medium">Tidak ada hasil Digital Goods</div>
                <p class="mt-1 text-xs text-slate-500">Coba kata kunci lain (mis: Axis Data, Pulsa, DANA).</p>
              </div>
            @endforelse
          </div>
        </div>

        {{-- MARKETPLACE SECTION --}}
        <div class="rounded-3xl border border-slate-800/70 bg-slate-900/45 p-5 md:p-6">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-lg font-semibold text-slate-100">Marketplace</h2>
              <p class="text-xs text-slate-400 mt-1">Akun premium dan produk marketplace lainnya.</p>
            </div>

            <a href="{{ route('search', ['q' => $q, 'tab' => 'marketplace']) }}"
               class="text-sm text-violet-300 hover:text-violet-200">
              Lihat semua →
            </a>
          </div>

          <div class="mt-4 grid gap-3 sm:grid-cols-2">
            @forelse($marketplace as $mp)
              @php
                $minPrice = $mp->variants->where('is_active', true)->min('price') ?? $mp->variants->min('price');
                $thumb = !empty($mp->thumbnail) ? Storage::url($mp->thumbnail) : null;
                $cat = $mp->category?->name ?? 'Marketplace';
              @endphp

              <a href="{{ route('marketplace.product.show', $mp->slug) }}"
                 class="group rounded-3xl border border-slate-800/70 bg-slate-950/25 hover:bg-slate-950/35 hover:border-violet-700/50 transition p-4">
                <div class="flex items-start gap-3">
                  <div class="size-11 rounded-2xl border border-slate-800/70 bg-slate-950/40 overflow-hidden shrink-0 grid place-items-center text-[10px] text-slate-500">
                    @if($thumb)
                      <img src="{{ $thumb }}" class="w-full h-full object-cover" alt="{{ $mp->name }}">
                    @else
                      MP
                    @endif
                  </div>

                  <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 text-[11px] text-slate-400">
                      <span class="px-2 py-0.5 rounded-full border border-slate-700/60 bg-slate-800/30 text-slate-200">
                        Marketplace
                      </span>
                      <span class="truncate">{{ $cat }}</span>
                    </div>

                    <div class="mt-1 font-semibold text-slate-100 group-hover:text-violet-100 truncate">
                      {{ $mp->name }}
                    </div>

                    <div class="mt-2 flex items-center justify-between">
                      <div class="text-xs text-slate-400">
                        Mulai <span class="text-slate-100 font-semibold">Rp {{ number_format((int)$minPrice,0,',','.') }}</span>
                      </div>
                      <span class="inline-flex items-center justify-center h-9 px-3 rounded-2xl bg-violet-600 hover:bg-violet-500 text-xs font-medium transition">
                        Lihat
                      </span>
                    </div>
                  </div>
                </div>
              </a>
            @empty
              <div class="col-span-full rounded-3xl border border-dashed border-slate-800/70 bg-slate-950/20 p-8 text-center">
                <div class="text-sm text-slate-300 font-medium">Tidak ada hasil Marketplace</div>
                <p class="mt-1 text-xs text-slate-500">Coba cari “Netflix”, “Canva”, “YouTube”, “VPN”, dll.</p>
                <a href="{{ route('marketplace.index') }}"
                   class="inline-flex mt-4 h-10 px-4 rounded-2xl border border-slate-800/70 hover:bg-slate-900/40 text-sm transition">
                  Jelajahi marketplace
                </a>
              </div>
            @endforelse
          </div>
        </div>

      </div>

    @elseif($isDigital)
      {{-- DIGITAL FULL (PAGINATED) --}}
      <div class="rounded-3xl border border-slate-800/70 bg-slate-900/45 p-5 md:p-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
          <div>
            <h2 class="text-lg font-semibold text-slate-100">Digital Goods</h2>
            <p class="text-xs text-slate-400 mt-1">Total hasil: <b class="text-slate-200">{{ $digital->total() }}</b></p>
          </div>
          <a href="{{ route('catalog') }}" class="text-sm text-violet-300 hover:text-violet-200">Buka katalog →</a>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          @forelse($digital as $p)
            @php
              $minFinal = $p->variants->min(fn($v) => $v->final_price);
              $meta = trim(($p->category?->name ?? '') . ($p->subcategory ? ' • '.$p->subcategory->name : ''));
            @endphp

            <a href="{{ route('catalog.product.show', $p->slug) }}"
               class="group rounded-3xl border border-slate-800/70 bg-slate-950/25 hover:bg-slate-950/35 hover:border-violet-700/50 transition p-5">
              <div class="flex items-center gap-2 text-[11px] text-slate-400">
                <span class="px-2 py-0.5 rounded-full border border-violet-700/30 bg-violet-600/15 text-violet-200">
                  Digital Goods
                </span>
                <span class="truncate">{{ $meta }}</span>
              </div>

              <div class="mt-2 font-semibold text-slate-100 group-hover:text-violet-100">
                {{ $p->name }}
              </div>

              <div class="mt-4 flex items-end justify-between gap-3">
                <div class="text-sm text-slate-300">
                  Mulai <span class="font-semibold text-slate-50">Rp {{ number_format($minFinal,0,',','.') }}</span>
                </div>
                <span class="inline-flex items-center justify-center h-10 px-4 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-medium transition">
                  Top Up
                </span>
              </div>
            </a>
          @empty
            <div class="col-span-full rounded-3xl border border-dashed border-slate-800/70 bg-slate-950/20 p-10 text-center">
              <div class="text-sm text-slate-300 font-medium">Tidak ada hasil</div>
              <p class="mt-1 text-xs text-slate-500">Coba kata kunci lain atau gunakan tab Marketplace.</p>
            </div>
          @endforelse
        </div>

        <div class="mt-6">
          {{ $digital->links() }}
        </div>
      </div>

    @else
      {{-- MARKETPLACE FULL (PAGINATED) --}}
      <div class="rounded-3xl border border-slate-800/70 bg-slate-900/45 p-5 md:p-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
          <div>
            <h2 class="text-lg font-semibold text-slate-100">Marketplace</h2>
            <p class="text-xs text-slate-400 mt-1">Total hasil: <b class="text-slate-200">{{ $marketplace->total() }}</b></p>
          </div>
          <a href="{{ route('marketplace.index') }}" class="text-sm text-violet-300 hover:text-violet-200">Jelajahi marketplace →</a>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          @forelse($marketplace as $mp)
            @php
              $minPrice = $mp->variants->where('is_active', true)->min('price') ?? $mp->variants->min('price');
              $thumb = !empty($mp->thumbnail) ? Storage::url($mp->thumbnail) : null;
              $cat = $mp->category?->name ?? 'Marketplace';
              $desc = Str::limit(strip_tags((string)$mp->description), 90);
            @endphp

            <a href="{{ route('marketplace.product.show', $mp->slug) }}"
               class="group rounded-3xl border border-slate-800/70 bg-slate-950/25 hover:bg-slate-950/35 hover:border-violet-700/50 transition overflow-hidden">
              <div class="h-36 bg-slate-950/40 border-b border-slate-800/70 overflow-hidden">
                @if($thumb)
                  <img src="{{ $thumb }}" alt="{{ $mp->name }}" class="w-full h-full object-cover">
                @else
                  <div class="w-full h-full grid place-items-center text-xs text-slate-500">No image</div>
                @endif
              </div>

              <div class="p-5">
                <div class="flex items-center gap-2 text-[11px] text-slate-400">
                  <span class="px-2 py-0.5 rounded-full border border-slate-700/60 bg-slate-800/30 text-slate-200">
                    Marketplace
                  </span>
                  <span class="truncate">{{ $cat }}</span>
                </div>

                <div class="mt-2 font-semibold text-slate-100 group-hover:text-violet-100">
                  {{ $mp->name }}
                </div>

                <p class="mt-2 text-xs text-slate-400 line-clamp-2">
                  {{ $desc ?: 'Klik untuk lihat varian & harga.' }}
                </p>

                <div class="mt-4 flex items-end justify-between gap-3">
                  <div class="text-sm text-slate-300">
                    Mulai <span class="font-semibold text-slate-50">Rp {{ number_format((int)$minPrice,0,',','.') }}</span>
                  </div>
                  <span class="inline-flex items-center justify-center h-10 px-4 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-medium transition">
                    Lihat
                  </span>
                </div>
              </div>
            </a>
          @empty
            <div class="col-span-full rounded-3xl border border-dashed border-slate-800/70 bg-slate-950/20 p-10 text-center">
              <div class="text-sm text-slate-300 font-medium">Tidak ada hasil</div>
              <p class="mt-1 text-xs text-slate-500">Coba kata kunci lain (mis: Netflix, Canva, YouTube Premium).</p>
            </div>
          @endforelse
        </div>

        <div class="mt-6">
          {{ $marketplace->links() }}
        </div>
      </div>
    @endif

  </div>
</section>
@endsection
