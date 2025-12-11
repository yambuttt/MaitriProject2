@extends('layouts.app')

@section('title', 'Katalog Produk — MaitriProject')
@section('meta_description', 'Pilih kategori, telusuri sub-kategori, lalu pilih produk top up.')
@section('page', 'catalog')

@section('content')
  <section class="py-8">
    <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">

      {{-- Header top --}}
      <div class="flex items-center justify-between gap-3">
        <div>
          <h1 class="text-2xl md:text-3xl font-semibold">Katalog Produk</h1>
          <p class="text-slate-400 text-sm">
            Pilih kategori di kiri, lalu cari produk yang kamu mau.
          </p>
        </div>
        <a href="{{ route('landing') }}" class="text-sm text-slate-400 hover:text-slate-200">
          ← Kembali ke beranda
        </a>
      </div>

      {{-- Layout: Sidebar + Main --}}
      <div class="mt-6 grid gap-6 lg:grid-cols-12">

        {{-- ====================== SIDEBAR ====================== --}}
        <aside class="lg:col-span-3 rounded-3xl border border-slate-800/70 bg-[#0E1524] p-4">
          <h2 class="text-sm font-semibold text-slate-200 mb-3">Kategori</h2>

          <nav class="space-y-1 text-sm">
            {{-- Semua kategori --}}
            <a href="{{ route('catalog', array_filter(['q' => $q ?: null, 'per_page' => $pp !== 9 ? $pp : null])) }}"
               class="flex items-center justify-between rounded-xl px-3 py-2
                 {{ !$category ? 'bg-violet-600/10 text-violet-200 border border-violet-600/70' : 'text-slate-300 hover:bg-slate-800/60 border border-transparent' }}">
              <span>Semua Produk</span>
            </a>

            {{-- Loop kategori --}}
            @foreach($categories as $c)
              <a href="{{ route('catalog', array_filter(['category' => $c->slug, 'subcategory' => null, 'q' => $q ?: null, 'per_page' => $pp !== 9 ? $pp : null])) }}"
                 class="flex items-center justify-between rounded-xl px-3 py-2
                 {{ optional($category)->id === $c->id
                    ? 'bg-violet-600/10 text-violet-200 border border-violet-600/70'
                    : 'text-slate-300 hover:bg-slate-800/60 border border-transparent' }}">
                <span>{{ $c->name }}</span>
              </a>
            @endforeach
          </nav>

          {{-- Subkategori (hanya untuk kategori terpilih dan yang punya subkategori) --}}
          @if($category && $subcategories->count())
            <div class="mt-6 border-t border-slate-800/80 pt-4">
              <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-2">
                Subkategori {{ $category->name }}
              </h3>
              <div class="space-y-1 text-sm">
                {{-- Semua di kategori ini --}}
                <a href="{{ route('catalog', array_filter(['category' => $category->slug, 'q' => $q ?: null, 'per_page' => $pp !== 9 ? $pp : null])) }}"
                   class="block rounded-xl px-3 py-2
                     {{ !$subcategory ? 'bg-slate-800/70 text-slate-100' : 'text-slate-300 hover:bg-slate-800/60' }}">
                  Semua di kategori ini
                </a>

                @foreach($subcategories as $s)
                  @php
                    $active = optional($subcategory)->id === $s->id;
                    $hasLive = ($s->live_products_count ?? 0) > 0;
                  @endphp
                  <a href="{{ $hasLive
                              ? route('catalog', array_filter([
                                  'category' => $category->slug,
                                  'subcategory' => $s->slug,
                                  'q' => $q ?: null,
                                  'per_page' => $pp !== 9 ? $pp : null
                                ]))
                              : '#' }}"
                     class="flex items-center justify-between rounded-xl px-3 py-2
                       {{ $active ? 'bg-slate-800/80 text-slate-100' : 'text-slate-300 hover:bg-slate-800/60' }}
                       {{ $hasLive ? '' : 'opacity-50 pointer-events-none' }}"
                     title="{{ $hasLive ? '' : 'Belum ada produk aktif' }}">
                    <span class="truncate">{{ $s->name }}</span>
                    @if($hasLive)
                      <span class="ml-2 text-[11px] px-2 py-0.5 rounded-full bg-slate-900/80 text-slate-400">
                        {{ $s->live_products_count }}
                      </span>
                    @endif
                  </a>
                @endforeach
              </div>
            </div>
          @endif
        </aside>

        {{-- ====================== MAIN CONTENT ====================== --}}
         <div class="lg:col-span-9 space-y-5">

          {{-- HERO BANNER (sesuai mockup) --}}
          <div
            class="relative overflow-hidden rounded-3xl border border-slate-800/70 bg-gradient-to-r from-[#2C3FAF] via-[#5C3AE8] to-[#8C3ADE] px-6 py-6 md:px-8 md:py-7">
            <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
              <div>
                <p class="text-xs uppercase tracking-[0.25em] text-violet-200/90">
                  Top Up Digital
                </p>
                <h2 class="mt-2 text-2xl md:text-3xl font-semibold text-white">
                  TOP UP E-WALLET<br class="hidden sm:block" />
                  INSTANT &amp; TERMURAH
                </h2>
                <p class="mt-3 text-sm text-violet-100/90 max-w-md">
                  Dukungan DANA · OVO · ShopeePay · GoPay<br>
                  Harga otomatis update dari provider.
                </p>
              </div>

              <div class="mt-4 md:mt-0">
                <div
                  class="flex items-center gap-3 rounded-2xl bg-black/20 border border-white/10 px-4 py-3 backdrop-blur">
                  <div
                    class="grid size-10 place-items-center rounded-2xl bg-white/10 text-white/90">
                    {{-- ikon petir --}}
                    <svg class="size-5" viewBox="0 0 24 24" fill="none">
                      <path d="M13 3 4 14h7l-1 7 9-11h-7l1-7Z" stroke="currentColor"
                            stroke-width="1.6" />
                    </svg>
                  </div>
                  <div class="text-sm text-violet-50">
                    <div class="font-medium">Proses &lt; 2 menit</div>
                    <div class="text-xs text-violet-100/80">Auto-check status transaksi</div>
                  </div>
                </div>
              </div>
            </div>

            {{-- dekorasi bulat-bulat blur --}}
            <div
              class="pointer-events-none absolute -right-10 -top-16 h-40 w-40 rounded-full bg-white/20 blur-3xl"></div>
            <div
              class="pointer-events-none absolute -left-10 -bottom-16 h-40 w-40 rounded-full bg-indigo-400/30 blur-3xl"></div>
          </div>

          {{-- SEARCH BAR + PER PAGE --}}
          <form method="get"
                class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            @if($category)
              <input type="hidden" name="category" value="{{ $category->slug }}">
            @endif
            @if($subcategory)
              <input type="hidden" name="subcategory" value="{{ $subcategory->slug }}">
            @endif

            <div class="flex-1">
              <div class="relative">
                <input name="q"
                       value="{{ $q }}"
                       placeholder="Cari game, pulsa, voucher, atau e-wallet…"
                       class="h-11 w-full rounded-2xl bg-[#0E1524] border border-slate-800/70 ps-10 pe-3 outline-none text-sm placeholder:text-slate-500 focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-5 text-slate-500"
                     viewBox="0 0 24 24" fill="none">
                  <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z"
                        stroke="currentColor" stroke-width="1.5" />
                </svg>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <select name="per_page"
                      class="h-11 rounded-2xl bg-[#0E1524] border border-slate-800/70 px-3 text-sm outline-none">
                @foreach([9, 18, 27, 36, 48] as $n)
                  <option value="{{ $n }}" @selected($pp == $n)>{{ $n }}/hal</option>
                @endforeach
              </select>

              <button
                class="h-11 px-4 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-medium">
                Cari
              </button>
            </div>
          </form>

          {{-- INFO HASIL --}}
          <div class="text-sm text-slate-400">
            Menampilkan
            <span class="text-slate-200 font-medium">{{ $products->count() }}</span>
            dari
            <span class="text-slate-200 font-medium">{{ $products->total() }}</span>
            produk
            @if($category)
              • Kategori:
              <span class="text-slate-200">{{ $category->name }}</span>
            @endif
            @if($subcategory)
              • Subkategori:
              <span class="text-slate-200">{{ $subcategory->name }}</span>
            @endif
          </div>

          {{-- GRID PRODUK (card clean, mirip mockup) --}}
          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($products as $p)
              @php
                $minFinal = $p->variants->min(fn($v) => $v->final_price);
              @endphp

              <a href="{{ route('catalog.product.show', $p->slug) }}"
                 class="group rounded-3xl border border-slate-800/70 bg-[#0E1524] p-4 hover:border-violet-700/70 hover:-translate-y-[1px] transition">
                <div class="flex items-start gap-3">
                  {{-- Thumbnail kecil / icon --}}
                  <div
                    class="flex-shrink-0 size-12 rounded-2xl bg-slate-900/80 border border-slate-800/80 overflow-hidden grid place-items-center text-[11px] text-slate-500">
                    @if($p->thumbnail)
                      <img src="{{ Storage::url($p->thumbnail) }}"
                           alt="{{ $p->name }}"
                           class="w-full h-full object-cover">
                    @else
                      IMG
                    @endif
                  </div>

                  <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between text-[11px] text-slate-400">
                      <span class="truncate">
                        {{ $p->category?->name }}
                        @if($p->subcategory) — {{ $p->subcategory->name }} @endif
                      </span>
                      @if($p->provider)
                        <span class="ml-2 truncate">{{ $p->provider }}</span>
                      @endif
                    </div>

                    <h3 class="mt-1 text-sm md:text-base font-semibold group-hover:text-white truncate">
                      {{ $p->name }}
                    </h3>
                    <p class="mt-1 text-xs text-slate-400 line-clamp-2">
                      {{ $p->description ?? '—' }}
                    </p>

                    <div class="mt-3 flex items-center justify-between">
                      <div class="text-xs text-slate-300">
                        Mulai
                        <span class="font-semibold">
                          Rp {{ number_format($minFinal, 0, ',', '.') }}
                        </span>
                      </div>
                      <div class="text-[11px] text-slate-400">
                        {{ $p->variants->count() }} pilihan
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-3 flex justify-end">
                  <span
                    class="inline-flex h-9 items-center justify-center rounded-2xl bg-violet-600 px-4 text-xs md:text-sm font-medium group-hover:bg-violet-500">
                    Pilih
                  </span>
                </div>
              </a>
            @empty
              <div class="col-span-full text-center text-slate-400 py-12">
                Tidak ada produk untuk filter ini.
              </div>
            @endforelse
          </div>

          {{-- PAGINATION (simple, di tengah) --}}
          @if($products->hasPages())
            <div class="mt-6 flex justify-center">
              <div class="inline-flex items-center gap-1 rounded-2xl bg-[#0E1524] border border-slate-800/70 px-2 py-1 text-sm">

                {{-- Prev --}}
                <a href="{{ $products->previousPageUrl() ? $products->appends(request()->except('page'))->previousPageUrl() : '#' }}"
                   class="px-3 py-1 rounded-xl {{ $products->onFirstPage() ? 'opacity-40 pointer-events-none' : 'hover:bg-slate-800/80' }}">
                  ‹
                </a>

                {{-- Numbered pages (sederhana) --}}
                @for($page = 1; $page <= $products->lastPage(); $page++)
                  @if($page == $products->currentPage())
                    <span class="px-3 py-1 rounded-xl bg-violet-600 text-white">
                      {{ $page }}
                    </span>
                  @elseif(
                    $page === 1 ||
                    $page === $products->lastPage() ||
                    ($page >= $products->currentPage() - 1 && $page <= $products->currentPage() + 1)
                  )
                    <a href="{{ $products->appends(request()->except('page'))->url($page) }}"
                       class="px-3 py-1 rounded-xl hover:bg-slate-800/80">
                      {{ $page }}
                    </a>
                  @elseif($page === 2 && $products->currentPage() > 3)
                    <span class="px-2">…</span>
                  @elseif($page === $products->lastPage() - 1 && $products->currentPage() < $products->lastPage() - 2)
                    <span class="px-2">…</span>
                  @endif
                @endfor

                {{-- Next --}}
                <a href="{{ $products->hasMorePages() ? $products->appends(request()->except('page'))->nextPageUrl() : '#' }}"
                   class="px-3 py-1 rounded-xl {{ $products->hasMorePages() ? 'hover:bg-slate-800/80' : 'opacity-40 pointer-events-none' }}">
                  ›
                </a>
              </div>
            </div>
          @endif

        </div>
      </div>
    </div>
  </section>
@endsection
