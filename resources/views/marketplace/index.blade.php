@extends('layouts.app')

@section('title', 'Marketplace — MaitriProject')
@section('page', 'marketplace')

@php
  use Illuminate\Support\Str;
  use Illuminate\Support\Facades\Storage;

  // ambil kategori unik dari products (tanpa perlu controller kirim $categories)
  $categories = collect($products)
    ->map(fn($p) => $p->category?->name)
    ->filter()
    ->unique()
    ->values();

  // helper: ambil min price dari variants (kalau relationship ada & sudah eager load).
  // kalau belum eager load, ini akan memicu query tambahan. (lihat opsi controller di bawah)
  $getMinPrice = function ($product) {
    try {
      if (method_exists($product, 'variants')) {
        $variants = $product->variants;
        if ($variants && $variants->count()) {
          $min = $variants->where('is_active', true)->min('price');
          return $min ?: $variants->min('price');
        }
      }
    } catch (\Throwable $e) {}
    return null;
  };
@endphp

@section('content')
  <section class="relative overflow-hidden py-8">
    {{-- Background glow --}}
    <div class="pointer-events-none absolute inset-0">
      <div class="absolute -top-20 -right-20 w-[480px] h-[480px] rounded-full blur-3xl bg-violet-600/15"></div>
      <div class="absolute -bottom-24 -left-24 w-[520px] h-[520px] rounded-full blur-3xl bg-indigo-500/10"></div>
      <div class="absolute inset-0 [background-image:linear-gradient(rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.03)_1px,transparent_1px)] [background-size:48px_48px] [mask-image:radial-gradient(closest-side,black,transparent)] opacity-70"></div>
    </div>

    <div class="relative mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8 space-y-6">

      {{-- Header / Hero --}}
      <div class="rounded-3xl border border-slate-800/70 bg-slate-900/40 p-5 md:p-7">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
          <div class="min-w-0">
            <div class="flex items-center gap-2 text-xs text-slate-400">
              <span class="inline-flex items-center gap-1 rounded-full border border-slate-800/70 bg-slate-950/40 px-2.5 py-1">
                <span class="size-1.5 rounded-full bg-emerald-500"></span>
                Produk aktif
              </span>
              <span class="hidden md:inline text-slate-600">•</span>
              <span class="hidden md:inline">Akun digital & layanan premium</span>
            </div>

            <h1 class="mt-2 text-2xl md:text-3xl font-semibold tracking-tight">
              Marketplace
              <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-300 to-violet-100">
                MaitriProject
              </span>
            </h1>
            <p class="mt-1 text-sm text-slate-400 max-w-prose">
              Beli akun digital (Canva, VPN, YouTube, dsb) dengan aman. Pilih produk, pilih varian, lalu checkout.
            </p>

            <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-400">
              <div class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/70 bg-slate-950/40 px-3 py-2">
                <span class="size-2 rounded-full bg-emerald-500"></span>
                Proses cepat
              </div>
              <div class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/70 bg-slate-950/40 px-3 py-2">
                <span class="size-2 rounded-full bg-violet-500"></span>
                Aman & terverifikasi
              </div>
              <div class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/70 bg-slate-950/40 px-3 py-2">
                <span class="size-2 rounded-full bg-sky-500"></span>
                Support via admin
              </div>
            </div>
          </div>

          <div class="shrink-0 flex items-center gap-2">
            <a href="{{ route('landing') }}"
               class="h-10 px-4 rounded-2xl border border-slate-800/70 hover:bg-slate-900/40 text-sm transition">
              ← Kembali
            </a>
            <a href="{{ route('catalog') }}"
               class="h-10 px-4 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-medium transition shadow-lg shadow-violet-900/25">
              Top Up Digital
            </a>
          </div>
        </div>
      </div>

      {{-- Toolbar: search + category chips + sort --}}
      <div class="rounded-3xl border border-slate-800/70 bg-slate-950/30 p-4 md:p-5 space-y-4">
        <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_220px] items-center">
          {{-- Search --}}
          <div class="relative">
            <input id="mpSearch" type="text"
              placeholder="Cari produk (mis: Canva, VPN, YouTube...)"
              class="w-full h-11 rounded-2xl bg-slate-950/40 border border-slate-800/70 px-4 ps-10 text-sm
                     placeholder:text-slate-500 focus:outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/40">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 size-4 text-slate-500" viewBox="0 0 24 24" fill="none">
              <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="1.5"/>
            </svg>
          </div>

          {{-- Sort --}}
          <div class="flex gap-2 md:justify-end">
            <select id="mpSort"
              class="w-full md:w-auto h-11 rounded-2xl bg-slate-950/40 border border-slate-800/70 px-4 text-sm
                     focus:outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/40">
              <option value="default">Urutan default</option>
              <option value="az">Nama A → Z</option>
              <option value="za">Nama Z → A</option>
            </select>
          </div>
        </div>

        {{-- Category chips --}}
        <div class="flex flex-wrap items-center gap-2">
          <button type="button"
            class="mp-chip px-3 py-1.5 rounded-full text-xs border border-slate-800/70 bg-violet-600/20 text-violet-200 hover:bg-violet-600/25 transition"
            data-cat="">
            Semua
          </button>

          @foreach($categories as $cat)
            <button type="button"
              class="mp-chip px-3 py-1.5 rounded-full text-xs border border-slate-800/70 bg-slate-950/30 text-slate-300 hover:bg-slate-900/40 transition"
              data-cat="{{ $cat }}">
              {{ $cat }}
            </button>
          @endforeach

          <div class="ms-auto text-xs text-slate-500">
            Menampilkan <span id="mpCount" class="text-slate-200 font-medium">{{ count($products) }}</span> produk
          </div>
        </div>
      </div>

      {{-- Grid --}}
      <div id="mpGrid" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($products as $product)
          @php
            $thumb = $product->thumbnail ? Storage::url($product->thumbnail) : null;
            $catName = $product->category?->name ?? 'Marketplace';
            $minPrice = $getMinPrice($product); // bisa null
            $title = $product->name ?? 'Produk';
            $desc = Str::limit(strip_tags((string) $product->description), 140);
          @endphp

          <a href="{{ route('marketplace.product.show', $product) }}"
             class="mp-card group rounded-3xl border border-slate-800/70 bg-slate-900/45 hover:bg-slate-900/60 transition overflow-hidden"
             data-title="{{ Str::lower($title.' '.$catName.' '.$desc) }}"
             data-cat="{{ $catName }}"
             data-name="{{ Str::lower($title) }}">

            {{-- Media --}}
            <div class="relative">
              @if($thumb)
                <img src="{{ $thumb }}" alt="{{ $title }}"
                     class="h-44 w-full object-cover">
              @else
                <div class="h-44 w-full bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900 border-b border-slate-800/70 flex items-center justify-center">
                  <div class="text-center">
                    <div class="mx-auto size-12 rounded-2xl bg-slate-800/60 grid place-items-center text-slate-200 font-semibold">
                      {{ Str::upper(Str::substr($title, 0, 1)) }}
                    </div>
                    <div class="mt-2 text-xs text-slate-500">No image</div>
                  </div>
                </div>
              @endif

              {{-- Gradient overlay --}}
              <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/45 via-black/0 to-black/0"></div>

              {{-- Category pill --}}
              <div class="absolute top-3 left-3">
                <span class="inline-flex items-center gap-1 rounded-full border border-slate-700/60 bg-black/30 backdrop-blur px-2.5 py-1 text-[11px] text-slate-200">
                  <span class="size-1.5 rounded-full bg-violet-400"></span>
                  {{ $catName }}
                </span>
              </div>

              {{-- Price badge (optional) --}}
              @if($minPrice)
                <div class="absolute top-3 right-3">
                  <span class="inline-flex items-center rounded-full border border-slate-700/60 bg-black/30 backdrop-blur px-2.5 py-1 text-[11px] text-slate-200">
                    Mulai Rp {{ number_format((int) $minPrice, 0, ',', '.') }}
                  </span>
                </div>
              @endif
            </div>

            {{-- Content --}}
            <div class="p-4 md:p-5 space-y-3">
              <div class="min-w-0">
                <h2 class="font-semibold text-slate-50 line-clamp-1 group-hover:text-violet-100 transition">
                  {{ $title }}
                </h2>
                <p class="mt-1 text-sm text-slate-400 line-clamp-2">
                  {{ $desc ?: 'Klik untuk lihat varian & harga.' }}
                </p>
              </div>

              {{-- Footer --}}
              <div class="pt-2 border-t border-slate-800/70 flex items-center justify-between">
                <div class="text-xs text-slate-500">
                  Klik untuk detail
                </div>
                <div class="inline-flex items-center gap-2 text-xs text-slate-300">
                  <span class="inline-flex items-center justify-center size-8 rounded-2xl border border-slate-800/70 bg-slate-950/30 group-hover:border-violet-600/40 transition">
                    <svg class="size-4 text-slate-300" viewBox="0 0 24 24" fill="none">
                      <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </span>
                </div>
              </div>
            </div>
          </a>
        @empty
          <div class="col-span-full">
            <div class="rounded-3xl border border-slate-800/70 bg-slate-950/30 p-10 text-center">
              <div class="mx-auto size-12 rounded-2xl bg-slate-800/60 grid place-items-center text-slate-200">
                <svg class="size-6" viewBox="0 0 24 24" fill="none">
                  <path d="M20 7H4m16 0-2 13H6L4 7m16 0-2-3H6L4 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
              </div>
              <h3 class="mt-4 font-semibold text-slate-100">Belum ada produk marketplace</h3>
              <p class="mt-1 text-sm text-slate-400">Admin belum mengaktifkan produk marketplace.</p>
              <a href="{{ route('landing') }}"
                 class="inline-flex mt-5 h-10 px-4 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-medium transition">
                Kembali ke Beranda
              </a>
            </div>
          </div>
        @endforelse
      </div>

      {{-- No results state (client side) --}}
      <div id="mpEmpty" class="hidden">
        <div class="rounded-3xl border border-slate-800/70 bg-slate-950/30 p-10 text-center">
          <h3 class="font-semibold text-slate-100">Tidak ada hasil</h3>
          <p class="mt-1 text-sm text-slate-400">Coba kata kunci lain atau ganti kategori.</p>
          <button id="mpReset"
            class="mt-5 h-10 px-4 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-medium transition">
            Reset Filter
          </button>
        </div>
      </div>

    </div>
  </section>

  {{-- Simple client-side filter + sort --}}
  <script>
    (function () {
      const searchEl = document.getElementById('mpSearch');
      const sortEl   = document.getElementById('mpSort');
      const chips    = Array.from(document.querySelectorAll('.mp-chip'));
      const grid     = document.getElementById('mpGrid');
      const cards    = Array.from(document.querySelectorAll('.mp-card'));
      const emptyEl  = document.getElementById('mpEmpty');
      const countEl  = document.getElementById('mpCount');
      const resetBtn = document.getElementById('mpReset');

      let activeCat = '';
      let query = '';

      function setActiveChip() {
        chips.forEach(c => {
          const isActive = (c.dataset.cat || '') === activeCat;
          c.classList.toggle('bg-violet-600/20', isActive);
          c.classList.toggle('text-violet-200', isActive);
          c.classList.toggle('hover:bg-violet-600/25', isActive);

          c.classList.toggle('bg-slate-950/30', !isActive);
          c.classList.toggle('text-slate-300', !isActive);
        });
      }

      function applySort(visibleCards) {
        const mode = sortEl.value;
        if (mode === 'az') visibleCards.sort((a,b) => (a.dataset.name || '').localeCompare(b.dataset.name || ''));
        if (mode === 'za') visibleCards.sort((a,b) => (b.dataset.name || '').localeCompare(a.dataset.name || ''));
        // default: biarkan urutan awal DOM (tidak diubah)
        if (mode === 'default') return visibleCards;
        visibleCards.forEach(card => grid.appendChild(card));
        return visibleCards;
      }

      function apply() {
        const q = (query || '').trim().toLowerCase();
        let visible = 0;
        const visibleCards = [];

        cards.forEach(card => {
          const hay = (card.dataset.title || '');
          const cat = (card.dataset.cat || '');
          const okQ = !q || hay.includes(q);
          const okC = !activeCat || cat === activeCat;
          const show = okQ && okC;

          card.classList.toggle('hidden', !show);
          if (show) {
            visible++;
            visibleCards.push(card);
          }
        });

        countEl.textContent = visible;
        emptyEl.classList.toggle('hidden', visible !== 0);
        grid.classList.toggle('hidden', visible === 0);

        applySort(visibleCards);
      }

      chips.forEach(c => {
        c.addEventListener('click', () => {
          activeCat = c.dataset.cat || '';
          setActiveChip();
          apply();
        });
      });

      searchEl.addEventListener('input', (e) => {
        query = e.target.value || '';
        apply();
      });

      sortEl.addEventListener('change', apply);

      if (resetBtn) {
        resetBtn.addEventListener('click', () => {
          activeCat = '';
          query = '';
          searchEl.value = '';
          sortEl.value = 'default';
          setActiveChip();
          apply();
        });
      }

      // init
      setActiveChip();
      apply();
    })();
  </script>
@endsection
