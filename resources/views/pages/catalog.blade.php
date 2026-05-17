@extends('layouts.app')

@section('title', 'Katalog Produk — MaitriProject')
@section('meta_description', 'Pilih kategori, telusuri sub-kategori, lalu pilih produk top up.')
@section('page', 'catalog')

@section('content')

@push('head')
<style>
  .glass-panel {
    background: rgba(17, 24, 39, 0.4);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.08);
  }
  .product-card {
    background: linear-gradient(180deg, rgba(30, 41, 59, 0.3) 0%, rgba(15, 23, 42, 0.6) 100%);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(139, 92, 246, 0.15);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .product-card:hover {
    border-color: rgba(167, 139, 250, 0.5);
    box-shadow: 0 15px 40px -15px rgba(139, 92, 246, 0.3);
    transform: translateY(-6px);
  }
  .text-gradient {
    background: linear-gradient(to right, #E9D5FF, #A78BFA);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  .hide-scroll::-webkit-scrollbar {
    display: none;
  }
  .hide-scroll {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }
  /* Animation */
  .fade-up-stagger {
    animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    opacity: 0;
    transform: translateY(30px);
  }
  @keyframes fadeUp {
    to { opacity: 1; transform: translateY(0); }
  }
  .floating-orb {
    animation: floatOrb 10s ease-in-out infinite alternate;
  }
  @keyframes floatOrb {
    0% { transform: translateY(0) scale(1); }
    100% { transform: translateY(-30px) scale(1.1); }
  }
</style>
@endpush

<section class="min-h-screen pb-20 relative overflow-hidden bg-[#050810]">
  
  {{-- GLOBAL AMBIENT BACKGROUND --}}
  <div class="fixed inset-0 pointer-events-none -z-10">
    <div class="absolute top-[10%] left-[20%] w-[600px] h-[600px] bg-violet-900/10 blur-[150px] rounded-full mix-blend-screen floating-orb"></div>
    <div class="absolute bottom-[20%] right-[10%] w-[500px] h-[500px] bg-fuchsia-900/10 blur-[120px] rounded-full mix-blend-screen floating-orb" style="animation-delay: -5s;"></div>
  </div>

  {{-- ==============================
       1. HERO SECTION (FULL WIDTH)
       ============================== --}}
  <div class="relative pt-12 pb-24 px-4 md:px-8">
    <div class="mx-auto max-w-6xl relative reveal">
      
      {{-- Hero Container --}}
      <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-indigo-950/80 via-[#0B0F19] to-purple-950/80 border border-white/10 p-8 md:p-16 text-center shadow-[0_20px_50px_rgba(0,0,0,0.6)]">
        
        {{-- Hero Orbs / Textures --}}
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-30 mix-blend-overlay"></div>
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-violet-600/40 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-fuchsia-600/30 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="relative z-10 flex flex-col items-center">
          <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-black/40 border border-white/10 backdrop-blur-md mb-6">
            <span class="relative flex h-2.5 w-2.5">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
            </span>
            <span class="text-xs md:text-sm font-bold tracking-widest text-slate-300 uppercase">Proses Instan & Otomatis</span>
          </div>
          
          <h1 class="text-3xl md:text-6xl font-extrabold text-white leading-tight mb-4 md:mb-6 max-w-4xl mx-auto">
            Top Up Apapun, Kapanpun <br class="hidden md:block"/>
            <span class="text-gradient">Cepat & Termurah</span>
          </h1>
          
          <p class="text-base md:text-lg text-slate-400 max-w-2xl mx-auto mb-4">
            Pilih dari ratusan layanan digital. Mulai dari Game, Pulsa, hingga e-Wallet. Dukungan pembayaran instan 24 jam nonstop.
          </p>
        </div>
      </div>

    </div>
  </div>

  {{-- ==============================
       2. FLOATING SEARCH BAR
       ============================== --}}
  <div class="mx-auto max-w-4xl px-4 md:px-8 relative z-20 -mt-20 reveal">
    <div class="glass-panel p-2.5 rounded-2xl md:rounded-[2rem] shadow-2xl">
      <form method="get" class="flex flex-col sm:flex-row items-center gap-2 w-full">
        @if($category) <input type="hidden" name="category" value="{{ $category->slug }}"> @endif
        @if($subcategory) <input type="hidden" name="subcategory" value="{{ $subcategory->slug }}"> @endif

        <div class="relative flex-1 w-full">
          <input name="q" value="{{ $q }}"
                 placeholder="Cari game, provider..."
                 class="h-14 w-full rounded-xl md:rounded-3xl bg-slate-900/60 border border-transparent focus:border-violet-500/50 focus:bg-black/60 focus:ring-0 ps-12 pe-4 outline-none text-sm md:text-base text-white placeholder:text-slate-500 transition-all">
          <svg class="absolute left-4 top-1/2 -translate-y-1/2 size-5 text-slate-400" viewBox="0 0 24 24" fill="none">
            <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
          <select name="per_page" class="h-14 w-[110px] sm:w-auto flex-shrink-0 rounded-xl md:rounded-3xl bg-slate-900/60 border border-transparent focus:border-violet-500/50 px-3 text-sm font-medium text-slate-300 outline-none appearance-none cursor-pointer transition-all text-center">
            @foreach([10, 20, 30, 40, 50] as $n)
              <option value="{{ $n }}" @selected($pp == $n) class="bg-slate-900">{{ $n }} Item</option>
            @endforeach
          </select>
          <button class="h-14 flex-1 sm:flex-none px-6 rounded-xl md:rounded-3xl bg-violet-600 hover:bg-violet-500 text-white text-sm font-bold tracking-wide transition-all shadow-[0_0_20px_rgba(139,92,246,0.4)]">
            Cari
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- ==============================
       3. HORIZONTAL CATEGORIES
       ============================== --}}
  <div class="mx-auto max-w-[1400px] px-4 md:px-8 mt-12 mb-10 reveal">
    
    <div class="flex items-center justify-start xl:justify-center gap-3 overflow-x-auto hide-scroll pb-4 px-2">
      {{-- Semua Produk --}}
      <a href="{{ route('catalog', array_filter(['q' => $q ?: null, 'per_page' => $pp !== 9 ? $pp : null])) }}"
         class="flex items-center gap-2 whitespace-nowrap rounded-full px-6 py-3 text-sm font-bold transition-all border
         {{ !$category ? 'bg-violet-600 border-violet-500 text-white shadow-[0_0_20px_rgba(139,92,246,0.4)]' : 'bg-[#111827]/80 border-white/10 text-slate-400 hover:bg-slate-800 hover:text-white' }}">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
        Semua
      </a>

      {{-- Looping Kategori Utama --}}
      @foreach($categories as $c)
        @php $isActive = optional($category)->id === $c->id; @endphp
        <a href="{{ route('catalog', array_filter(['category' => $c->slug, 'subcategory' => null, 'q' => $q ?: null, 'per_page' => $pp !== 9 ? $pp : null])) }}"
           class="flex items-center gap-2 whitespace-nowrap rounded-full px-6 py-3 text-sm font-bold transition-all border
           {{ $isActive ? 'bg-violet-600 border-violet-500 text-white shadow-[0_0_20px_rgba(139,92,246,0.4)]' : 'bg-[#111827]/80 border-white/10 text-slate-400 hover:bg-slate-800 hover:text-white' }}">
          {{ $c->name }}
        </a>
      @endforeach
    </div>

    {{-- Subcategories Pills (jika kategori terpilih) --}}
    @if($category && $subcategories->count())
      <div class="mt-4 flex items-center justify-start xl:justify-center gap-2 overflow-x-auto hide-scroll pb-2">
        <div class="h-px bg-white/10 w-8 hidden sm:block"></div>
        <a href="{{ route('catalog', array_filter(['category' => $category->slug, 'q' => $q ?: null, 'per_page' => $pp !== 9 ? $pp : null])) }}"
           class="whitespace-nowrap rounded-full px-4 py-2 text-xs font-semibold transition-all border
           {{ !$subcategory ? 'bg-slate-700 border-slate-600 text-white' : 'bg-transparent border-white/10 text-slate-500 hover:border-slate-500 hover:text-slate-300' }}">
          Semua {{ $category->name }}
        </a>
        @foreach($subcategories as $s)
          @php
            $active = optional($subcategory)->id === $s->id;
            $hasLive = ($s->live_products_count ?? 0) > 0;
          @endphp
          <a href="{{ $hasLive ? route('catalog', array_filter(['category' => $category->slug, 'subcategory' => $s->slug, 'q' => $q ?: null, 'per_page' => $pp !== 9 ? $pp : null])) : '#' }}"
             class="whitespace-nowrap rounded-full px-4 py-2 text-xs font-semibold transition-all border flex items-center gap-1.5
             {{ $active ? 'bg-slate-700 border-slate-600 text-white' : 'bg-transparent border-white/10 text-slate-500 hover:border-slate-500 hover:text-slate-300' }}
             {{ $hasLive ? '' : 'opacity-30 pointer-events-none' }}">
            {{ $s->name }}
            @if($hasLive)
              <span class="px-1.5 py-0.5 rounded-md bg-black/50 text-[9px]">{{ $s->live_products_count }}</span>
            @endif
          </a>
        @endforeach
        <div class="h-px bg-white/10 w-8 hidden sm:block"></div>
      </div>
    @endif
  </div>

  {{-- ==============================
       4. PRODUCTS GRID (FULL WIDTH)
       ============================== --}}
  <div class="mx-auto max-w-[1400px] px-4 md:px-8">
    
    {{-- Hasil Pencarian Info --}}
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/5 reveal">
      <h2 class="text-xl md:text-2xl font-bold text-white">
        @if($q) Hasil Pencarian: <span class="text-violet-400">"{{ $q }}"</span>
        @elseif($subcategory) {{ $subcategory->name }}
        @elseif($category) {{ $category->name }}
        @else Semua Layanan @endif
      </h2>
      <div class="text-sm font-medium text-slate-500 hidden sm:block">
        Menampilkan <span class="text-slate-300">{{ $products->count() }}</span> dari <span class="text-slate-300">{{ $products->total() }}</span> produk
      </div>
    </div>

    {{-- Grid Ekstra Luas --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
      @forelse($products as $index => $p)
        @php
          $minFinal = $p->variants->min(fn($v) => $v->final_price);
          $animationDelay = $index * 0.05;
        @endphp

        <a href="{{ route('catalog.product.show', $p->slug) }}"
           class="product-card group block overflow-hidden rounded-[2rem] p-4 flex flex-col h-full fade-up-stagger"
           style="animation-delay: {{ $animationDelay }}s;">
           
          {{-- Thumbnail Area (Besar di Atas) --}}
          <div class="relative w-full aspect-square rounded-2xl bg-black/40 border border-white/5 overflow-hidden mb-4 group-hover:border-violet-500/30 transition-colors flex items-center justify-center">
            @if($p->thumbnail)
              <img src="{{ Storage::url($p->thumbnail) }}" alt="{{ $p->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
            @else
              <div class="text-xs text-slate-600 font-bold tracking-widest uppercase">No Image</div>
            @endif
            {{-- Glossy Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-[#0B0F19]/80 via-transparent to-white/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            
            {{-- Provider Badge (Melayang) --}}
            @if($p->provider)
              <div class="absolute bottom-2 left-2 right-2">
                <div class="mx-auto w-max max-w-full px-3 py-1 rounded-full bg-black/60 backdrop-blur-md border border-white/10 text-[9px] font-bold tracking-widest text-violet-300 uppercase truncate">
                  {{ $p->provider }}
                </div>
              </div>
            @endif
          </div>

          {{-- Konten Teks --}}
          <div class="flex flex-col flex-grow">
            <div class="text-[10px] font-bold tracking-widest text-slate-500 uppercase mb-1 truncate">
              {{ $p->category?->name ?? 'Layanan' }}
            </div>
            
            <h3 class="text-sm md:text-base font-bold text-slate-200 group-hover:text-white transition-colors line-clamp-2 leading-snug mb-3">
              {{ $p->name }}
            </h3>

            <div class="mt-auto pt-4 border-t border-white/5 flex items-end justify-between">
              <div>
                <div class="text-[10px] text-slate-500 mb-0.5">Harga Mulai</div>
                <div class="text-sm md:text-base font-extrabold text-white group-hover:text-violet-300 transition-colors">
                  Rp {{ number_format($minFinal, 0, ',', '.') }}
                </div>
              </div>
              
              {{-- Icon Panah --}}
              <div class="size-8 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-violet-600 group-hover:shadow-[0_0_15px_rgba(139,92,246,0.6)] transition-all">
                <svg class="size-4 text-slate-400 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
              </div>
            </div>
          </div>
        </a>
      @empty
        <div class="col-span-full py-24 glass-panel rounded-[2.5rem] text-center reveal">
          <div class="inline-flex size-20 rounded-full bg-slate-800/50 items-center justify-center mb-6 border border-white/5">
            <svg class="size-10 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-white mb-3">Tidak Ada Produk</h3>
          <p class="text-base text-slate-400 max-w-md mx-auto mb-8">
            Pencarian atau filter Anda tidak membuahkan hasil. Silakan coba menggunakan kata kunci lain.
          </p>
          <a href="{{ route('catalog') }}" class="inline-block px-8 py-3.5 rounded-2xl bg-violet-600 hover:bg-violet-500 text-white text-sm font-bold tracking-wide transition-all shadow-[0_0_20px_rgba(139,92,246,0.4)]">
            Tampilkan Semua Produk
          </a>
        </div>
      @endforelse
    </div>

    {{-- ==============================
         5. PAGINATION
         ============================== --}}
    @if($products->hasPages())
      <div class="mt-14 flex justify-center reveal">
        <div class="inline-flex items-center gap-1 glass-panel p-2 rounded-3xl">
          
          {{-- Prev --}}
          <a href="{{ $products->previousPageUrl() ? $products->appends(request()->except('page'))->previousPageUrl() : '#' }}"
             class="p-3 rounded-2xl text-slate-400 {{ $products->onFirstPage() ? 'opacity-30 pointer-events-none' : 'hover:bg-white/10 hover:text-white transition-colors' }}">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
          </a>

          <div class="flex items-center gap-1 px-3 border-x border-white/5">
            @for($page = 1; $page <= $products->lastPage(); $page++)
              @if($page == $products->currentPage())
                <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-violet-600 shadow-[0_0_15px_rgba(139,92,246,0.5)] text-white text-sm font-bold">
                  {{ $page }}
                </span>
              @elseif(
                $page === 1 ||
                $page === $products->lastPage() ||
                ($page >= $products->currentPage() - 1 && $page <= $products->currentPage() + 1)
              )
                <a href="{{ $products->appends(request()->except('page'))->url($page) }}"
                   class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:bg-white/10 hover:text-white text-sm font-bold transition-colors">
                  {{ $page }}
                </a>
              @elseif($page === 2 && $products->currentPage() > 3)
                <span class="w-10 h-10 flex items-center justify-center text-slate-500 font-bold">…</span>
              @elseif($page === $products->lastPage() - 1 && $products->currentPage() < $products->lastPage() - 2)
                <span class="w-10 h-10 flex items-center justify-center text-slate-500 font-bold">…</span>
              @endif
            @endfor
          </div>

          {{-- Next --}}
          <a href="{{ $products->hasMorePages() ? $products->appends(request()->except('page'))->nextPageUrl() : '#' }}"
             class="p-3 rounded-2xl text-slate-400 {{ $products->hasMorePages() ? 'hover:bg-white/10 hover:text-white transition-colors' : 'opacity-30 pointer-events-none' }}">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
          </a>
        </div>
      </div>
    @endif

  </div>
</section>
@endsection
