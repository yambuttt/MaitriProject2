@extends('layouts.app')

@section('title', 'MaitriProject — Portal Top Up & Game Voucher Premium')
@section('meta_description', 'Pulsa, paket data, voucher game, dan e-wallet resmi. Pengisian instan 24 jam dengan sistem otomatis terpercaya.')
@section('page', 'landing')

@php
  use Illuminate\Support\Facades\Storage;
@endphp

@push('head')
<style>
  .portal-bg {
    background-color: #03050a;
    background-image: 
      radial-gradient(circle at 50% -20%, rgba(124, 58, 237, 0.18) 0%, transparent 50%),
      radial-gradient(circle at 10% 30%, rgba(236, 72, 153, 0.05) 0%, transparent 40%),
      radial-gradient(circle at 90% 80%, rgba(139, 92, 246, 0.05) 0%, transparent 45%);
  }
  .portal-grid {
    background-image: 
      linear-gradient(rgba(255, 255, 255, 0.007) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255, 255, 255, 0.007) 1px, transparent 1px);
    background-size: 50px 50px;
    mask-image: radial-gradient(ellipse at 50% 50%, black 40%, transparent 100%);
  }
  .glow-portal {
    box-shadow: 0 0 80px 10px rgba(139, 92, 246, 0.15);
  }
  .text-neon {
    background: linear-gradient(135deg, #fff 30%, #c4b5fd 70%, #a78bfa 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 0 40px rgba(167, 139, 250, 0.2);
  }
  .search-glow:focus-within {
    box-shadow: 0 0 35px rgba(139, 92, 246, 0.35);
    border-color: rgba(139, 92, 246, 0.6);
  }
  .glass-pill {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .glass-pill:hover {
    background: rgba(139, 92, 246, 0.1);
    border-color: rgba(139, 92, 246, 0.4);
    transform: translateY(-2px);
  }
  .floating-badge {
    animation: float 4s ease-in-out infinite;
  }
  @keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
  }
</style>
@endpush

@section('content')
<div class="portal-bg min-h-screen relative overflow-hidden pb-20">
  
  {{-- Cosmic Grids --}}
  <div class="portal-grid absolute inset-0 pointer-events-none -z-10"></div>

  {{-- Portal Aura --}}
  <div class="absolute top-[-30%] left-[25%] w-[800px] h-[800px] bg-violet-600/10 blur-[150px] rounded-full pointer-events-none -z-10 glow-portal"></div>

  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8 pt-16">
    
    {{-- ==============================
         CINEMATIC HERO HEADER
         ============================== --}}
    <div class="text-center max-w-4xl mx-auto space-y-6 relative z-10 reveal">
      
      {{-- Floating Badges --}}
      <div class="flex justify-center gap-3">
        <div class="floating-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] font-extrabold uppercase tracking-widest text-violet-300">
          ⚡ Instan & Otomatis 24/7
        </div>
      </div>

      <h1 class="text-4xl md:text-7xl font-extrabold leading-[1.05] tracking-tight text-white">
        THE NEXT GEN <br class="hidden md:block"/>
        <span class="text-neon">DIGITAL PORTAL</span>
      </h1>

      <p class="text-base md:text-xl text-slate-400 font-medium max-w-2xl mx-auto leading-relaxed">
        Pusat top up saldo game, paket data, dan marketplace digital premium tercepat dengan jaminan transaksi 100% aman & terpercaya.
      </p>

      {{-- ==============================
           IMMEDIATE COMMAND SEARCH BAR
           ============================== --}}
      <div class="max-w-2xl mx-auto pt-4">
        <form action="{{ route('search') }}" method="get" class="search-glow relative flex items-center p-2 rounded-2xl md:rounded-[2rem] bg-black/40 border border-white/5 transition-all duration-300 backdrop-blur-xl">
          <input
            type="search"
            name="q"
            placeholder="Ketik nama game, voucher, atau saldo tujuan..."
            class="h-14 w-full rounded-xl bg-transparent ps-12 pe-32 outline-none text-sm md:text-base text-white placeholder:text-slate-500 border-none focus:ring-0"
          >
          <svg class="absolute left-5 top-1/2 -translate-y-1/2 size-5 text-slate-500" viewBox="0 0 24 24" fill="none">
            <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
          </svg>

          <button type="submit"
            class="absolute right-3 top-1/2 -translate-y-1/2 h-12 px-6 rounded-xl md:rounded-2xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-xs md:text-sm font-extrabold text-white tracking-wide transition-all shadow-[0_0_20px_rgba(139,92,246,0.4)]">
            Cari Produk
          </button>
        </form>
      </div>

      {{-- Fast Links / Categories --}}
      <div class="flex flex-wrap justify-center items-center gap-3 pt-4 text-xs font-bold text-slate-400">
        <span class="text-slate-600 uppercase tracking-widest text-[10px]">Akses Cepat:</span>
        <a href="{{ route('catalog') }}?category=games" class="glass-pill px-4 py-2 rounded-xl text-slate-200">🎮 Mobile Games</a>
        <a href="{{ route('catalog') }}?category=e-wallet" class="glass-pill px-4 py-2 rounded-xl text-slate-200">💳 E-Wallet</a>
        <a href="{{ route('catalog') }}?category=pulsa" class="glass-pill px-4 py-2 rounded-xl text-slate-200">📱 Pulsa & Data</a>
        <a href="{{ route('marketplace.index') }}" class="glass-pill px-4 py-2 rounded-xl text-violet-300 border-violet-500/20">🛒 Marketplace</a>
      </div>

    </div>

    {{-- ==============================
         CINEMATIC HOT SLIDER
         ============================== --}}
    <div class="mt-16 relative z-10 reveal">
      
      @php
        $heroProducts = ($heroProducts ?? collect());
        $heroMarketplaceProducts = ($heroMarketplaceProducts ?? collect());
      @endphp

      <div class="grid md:grid-cols-2 gap-6" data-hero-tabs>
        
        {{-- Digital Goods Hot Card --}}
        <div class="relative rounded-[2.5rem] p-6 border border-white/5 bg-[#111827]/10 backdrop-blur-xl flex flex-col justify-between min-h-[300px] overflow-hidden group hover:border-violet-500/30 transition-all duration-300">
          <div class="absolute -right-24 -top-24 w-60 h-60 rounded-full blur-[90px] bg-violet-600/15 pointer-events-none group-hover:bg-violet-600/25 transition-all"></div>
          
          <div>
            <div class="flex items-center justify-between">
              <span class="px-3 py-1 rounded-full bg-violet-600/10 border border-violet-500/20 text-[10px] font-extrabold uppercase tracking-widest text-violet-300">
                ⚡ Hot Top Up
              </span>
              <span class="text-[9px] font-extrabold text-slate-500 uppercase tracking-wider">Layanan Instan</span>
            </div>
            
            <h2 class="text-2xl font-extrabold text-white mt-4 leading-tight group-hover:text-violet-300 transition-colors">
              Pilihan Favorit <br/>
              Top Up Game Instan
            </h2>
            <p class="text-xs text-slate-400 mt-2 max-w-sm leading-relaxed">
              Isi ulang diamond, token, atau koin game favorit Anda langsung masuk ke akun tanpa waktu tunggu lama.
            </p>
          </div>

          <div class="mt-8 space-y-3">
            <div class="grid grid-cols-2 gap-2">
              @foreach($heroProducts->take(2) as $p)
                @php $minPrice = $p->variants->min(fn($v) => $v->final_price); @endphp
                <a href="{{ route('catalog.product.show', $p->slug) }}" class="flex items-center gap-3 p-3 rounded-2xl bg-black/40 border border-white/5 hover:border-violet-500/40 hover:bg-slate-900/40 transition-all">
                  <div class="size-9 rounded-lg bg-slate-800 overflow-hidden shrink-0">
                    @if($p->thumbnail) <img src="{{ Storage::url($p->thumbnail) }}" class="w-full h-full object-cover"> @endif
                  </div>
                  <div class="min-w-0">
                    <div class="text-xs font-bold text-white truncate">{{ $p->name }}</div>
                    <div class="text-[10px] text-violet-300 font-extrabold">Rp {{ number_format($minPrice, 0, ',', '.') }}</div>
                  </div>
                </a>
              @endforeach
            </div>
            <a href="{{ route('catalog') }}" class="h-11 w-full inline-flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 hover:border-violet-500/50 hover:bg-violet-600 hover:text-white text-xs font-bold text-slate-300 transition-all">
              Jelajahi Semua Produk Game
            </a>
          </div>
        </div>

        {{-- Marketplace Hot Card --}}
        <div class="relative rounded-[2.5rem] p-6 border border-white/5 bg-[#111827]/10 backdrop-blur-xl flex flex-col justify-between min-h-[300px] overflow-hidden group hover:border-fuchsia-500/30 transition-all duration-300">
          <div class="absolute -right-24 -top-24 w-60 h-60 rounded-full blur-[90px] bg-fuchsia-600/15 pointer-events-none group-hover:bg-fuchsia-600/25 transition-all"></div>
          
          <div>
            <div class="flex items-center justify-between">
              <span class="px-3 py-1 rounded-full bg-fuchsia-600/10 border border-fuchsia-500/20 text-[10px] font-extrabold uppercase tracking-widest text-fuchsia-300">
                🛒 Marketplace
              </span>
              <span class="text-[9px] font-extrabold text-slate-500 uppercase tracking-wider">Premium Items</span>
            </div>
            
            <h2 class="text-2xl font-extrabold text-white mt-4 leading-tight group-hover:text-fuchsia-300 transition-colors">
              Marketplace <br/>
              Kebutuhan Premium
            </h2>
            <p class="text-xs text-slate-400 mt-2 max-w-sm leading-relaxed">
              Miliki akun premium, aplikasi penunjang kerja, voucher belanja, dan item digital premium lainnya dengan harga terbaik.
            </p>
          </div>

          <div class="mt-8 space-y-3">
            <div class="grid grid-cols-2 gap-2">
              @foreach($heroMarketplaceProducts->take(2) as $p)
                @php $minPrice = $p->variants->where('is_active', true)->min('price'); @endphp
                <a href="{{ route('marketplace.product.show', $p->slug) }}" class="flex items-center gap-3 p-3 rounded-2xl bg-black/40 border border-white/5 hover:border-fuchsia-500/40 hover:bg-slate-900/40 transition-all">
                  <div class="size-9 rounded-lg bg-slate-800 overflow-hidden shrink-0">
                    @if($p->thumbnail) <img src="{{ Storage::url($p->thumbnail) }}" class="w-full h-full object-cover"> @endif
                  </div>
                  <div class="min-w-0">
                    <div class="text-xs font-bold text-white truncate">{{ $p->name }}</div>
                    <div class="text-[10px] text-fuchsia-300 font-extrabold">Rp {{ number_format($minPrice, 0, ',', '.') }}</div>
                  </div>
                </a>
              @endforeach
            </div>
            <a href="{{ route('marketplace.index') }}" class="h-11 w-full inline-flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 hover:border-fuchsia-500/50 hover:bg-fuchsia-600 hover:text-white text-xs font-bold text-slate-300 transition-all">
              Kunjungi Marketplace Premium
            </a>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

{{-- SUB-SECTIONS (Kategori, Ramai Dibeli, Cara Kerja, FAQ, etc.) --}}
@include('pages.partials.landing-sections')

@endsection