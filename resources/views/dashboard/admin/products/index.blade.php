@extends('layouts.admin')
@section('title', 'Products — Admin')

@push('head')
<style>
  .product-kpi-card {
    background: rgba(17, 24, 39, 0.2);
    backdrop-filter: blur(25px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.5);
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .product-kpi-card:hover {
    transform: translateY(-4px);
    border-color: rgba(139, 92, 246, 0.2);
    box-shadow: 0 20px 45px -10px rgba(139, 92, 246, 0.1);
  }
  .product-grid-card {
    background: rgba(11, 18, 30, 0.35);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.04);
    box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.6);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .product-grid-card:hover {
    border-color: rgba(139, 92, 246, 0.25);
    box-shadow: 0 15px 40px -15px rgba(139, 92, 246, 0.15);
    transform: scale(1.015);
  }
</style>
@endpush

@section('content')
  {{-- Header --}}
  <div class="reveal flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="space-y-1">
      <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-[9px] font-extrabold uppercase tracking-widest text-violet-300">
        📦 Products Hub
      </div>
      <h1 class="text-3xl font-extrabold text-white tracking-tight">Katalog Produk</h1>
      <p class="text-sm text-slate-400 font-medium">Kelola produk utama, markup harga, provider, dan integrasi varian toko Anda.</p>
    </div>

    <div class="flex items-center gap-3">
      @if (session('ok'))
        <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-2.5 text-xs font-bold text-emerald-300 flex items-center gap-2">
          <span class="size-1.5 rounded-full bg-emerald-400 animate-ping"></span>
          {{ session('ok') }}
        </div>
      @endif
      <a href="{{ route('admin.products.create') }}"
         class="h-11 px-5 inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-xs font-extrabold text-white uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(139,92,246,0.3)]">
         + Tambah Produk
      </a>
    </div>
  </div>

  {{-- Filters floating console --}}
  <form method="get" class="reveal mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_220px_220px_140px] gap-3">
    <div class="relative">
      <input name="q" value="{{ $q }}" type="search" placeholder="Cari nama, slug, atau nama provider produk..."
             class="w-full h-11 rounded-xl bg-black/40 border border-white/10 ps-10 pe-3 text-xs text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
      <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-slate-500" viewBox="0 0 24 24" fill="none">
        <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="1.5" />
      </svg>
    </div>
    
    <select name="category_id" id="filterCategory"
            class="h-11 rounded-xl bg-black/40 border border-white/10 px-3.5 text-xs text-slate-300 outline-none focus:border-violet-500/50 transition-all">
      <option value="">Semua Kategori</option>
      @foreach($categories as $c)
        <option value="{{ $c->id }}" @selected((string) $cat === (string) $c->id)>{{ $c->name }}</option>
      @endforeach
    </select>
    
    <select name="subcategory_id" id="filterSubcategory"
            class="h-11 rounded-xl bg-black/40 border border-white/10 px-3.5 text-xs text-slate-300 outline-none focus:border-violet-500/50 transition-all">
      <option value="">Semua Sub Kategori</option>
      @foreach($subcategories as $s)
        <option value="{{ $s->id }}" @selected((string) $sub === (string) $s->id)>{{ $s->name }}</option>
      @endforeach
    </select>
    
    <div class="flex gap-3">
      <select name="per_page"
              class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-3.5 text-xs text-slate-300 outline-none focus:border-violet-500/50 transition-all">
        @foreach([12, 24, 48, 96] as $n)
          <option value="{{ $n }}" @selected($pp == $n)>{{ $n }} / Hal</option>
        @endforeach
      </select>
      
      <button class="h-11 px-5 rounded-xl bg-violet-600 hover:bg-violet-500 text-xs font-bold text-white transition-all shadow-md shrink-0">
        Terapkan
      </button>
    </div>
  </form>

  {{-- Main Grid / Product Dashboard: Desktop & Mobile Viewports --}}
  <div class="mt-6">
    
    {{-- DESKTOP GRID: 3 or 4 columns --}}
    <div class="hidden md:grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 reveal">
      @forelse($products as $p)
        <div class="product-grid-card rounded-3xl p-5 flex flex-col justify-between gap-5 relative overflow-hidden group">
          
          {{-- Card Header: Thumbnail & Name & Breadcrumb --}}
          <div class="space-y-4">
            <div class="flex items-start justify-between gap-3">
              @if($p->thumbnail)
                <img src="{{ Storage::url($p->thumbnail) }}" alt="{{ $p->name }}"
                     class="size-12 object-cover rounded-2xl border border-white/10 shadow-md">
              @else
                <div class="size-12 rounded-2xl bg-gradient-to-br from-violet-600/20 to-fuchsia-600/20 border border-violet-500/25 flex items-center justify-center text-xs font-extrabold text-violet-300 uppercase tracking-widest">
                  {{ substr($p->name, 0, 2) }}
                </div>
              @endif

              {{-- Active State Pill --}}
              <form method="post" action="{{ route('admin.products.toggle', $p) }}">
                @csrf @method('PATCH')
                <button class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wider border transition-all
                  {{ $p->is_active ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.1)]' : 'bg-slate-500/10 text-slate-400 border border-white/5' }}">
                  {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                </button>
              </form>
            </div>

            <div class="space-y-1">
              <h2 class="text-sm font-extrabold text-white tracking-tight leading-snug group-hover:text-violet-300 transition-colors line-clamp-2 min-h-10" title="{{ $p->name }}">
                {{ $p->name }}
              </h2>
              <div class="text-[10px] text-slate-500 font-semibold tracking-wide truncate max-w-full">
                {{ $p->category?->name ?? '-' }} → <span class="text-slate-400">{{ $p->subcategory?->name ?? '-' }}</span>
              </div>
            </div>
          </div>

          {{-- Card Metadata: Price Markup & Provider --}}
          <div class="pt-3.5 border-t border-white/[0.03] space-y-2 text-[11px] font-semibold">
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Markup Toko</span>
              <span class="text-emerald-400 font-extrabold bg-emerald-500/5 px-2.5 py-1 rounded-lg border border-emerald-500/15">
                + Rp {{ number_format($p->markup_rp, 0, ',', '.') }}
              </span>
            </div>
            
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Provider</span>
              <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider
                {{ $p->provider ? 'bg-violet-600/10 text-violet-300 border border-violet-500/20' : 'bg-slate-500/15 text-slate-400 border border-white/5' }}">
                {{ $p->provider ?: 'Manual' }}
              </span>
            </div>
          </div>

          {{-- Action Hub buttons --}}
          <div class="grid grid-cols-3 gap-1.5 pt-2 border-t border-white/[0.03]">
            <a href="{{ route('admin.products.edit', $p) }}"
               class="h-8 rounded-lg bg-white/5 hover:bg-violet-600/20 border border-white/10 hover:border-violet-500/40 text-[10px] font-bold text-slate-300 hover:text-white flex items-center justify-center transition-all shadow-sm">
               Edit
            </a>
            
            <a href="{{ route('admin.products.variants.index', $p) }}"
               class="h-8 rounded-lg bg-white/5 hover:bg-violet-600/20 border border-white/10 hover:border-violet-500/40 text-[10px] font-bold text-slate-300 hover:text-white flex items-center justify-center transition-all shadow-sm">
               Varian
            </a>

            <form method="post" action="{{ route('admin.products.destroy', $p) }}"
                  onsubmit="return confirm('Hapus produk ini beserta seluruh varian di dalamnya?')">
              @csrf @method('DELETE')
              <button class="w-full h-8 rounded-lg bg-white/5 hover:bg-rose-600 border border-white/10 hover:border-rose-500 text-[10px] font-bold text-rose-400 hover:text-white flex items-center justify-center transition-all shadow-sm">
                Hapus
              </button>
            </form>
          </div>

        </div>
      @empty
        <div class="col-span-full py-16 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
          Belum ada produk yang terdaftar.
        </div>
      @endforelse
    </div>

    {{-- MOBILE LANDSCAPE LIST VIEW --}}
    <div class="block md:hidden space-y-3.5 reveal">
      @forelse($products as $p)
        <div class="product-grid-card rounded-2xl p-4 flex items-center gap-4 relative overflow-hidden">
          
          {{-- Avatar / Image --}}
          @if($p->thumbnail)
            <img src="{{ Storage::url($p->thumbnail) }}" alt="{{ $p->name }}"
                 class="size-11 object-cover rounded-xl border border-white/10 shadow-sm shrink-0">
          @else
            <div class="size-11 rounded-xl bg-gradient-to-br from-violet-600/20 to-fuchsia-600/20 border border-violet-500/25 flex items-center justify-center text-[10px] font-extrabold text-violet-300 uppercase tracking-widest shrink-0">
              {{ substr($p->name, 0, 2) }}
            </div>
          @endif

          {{-- Meta Info --}}
          <div class="flex-1 min-w-0 space-y-1">
            <h2 class="text-xs font-extrabold text-white tracking-tight leading-snug truncate">
              {{ $p->name }}
            </h2>
            <div class="text-[10px] text-slate-400 font-semibold tracking-wide truncate">
              {{ $p->category?->name ?? '-' }} → {{ $p->subcategory?->name ?? '-' }}
            </div>
            
            {{-- Small bottom pills --}}
            <div class="flex items-center gap-2 pt-0.5 text-[9px] font-bold">
              <span class="text-emerald-400">
                + Rp {{ number_format($p->markup_rp, 0, ',', '.') }}
              </span>
              <span class="text-slate-500">•</span>
              <span class="text-slate-400 uppercase tracking-wider">
                {{ $p->provider ?: 'Manual' }}
              </span>
            </div>
          </div>

          {{-- Quick Action Drawer icon / active toggle --}}
          <div class="flex flex-col items-end gap-2.5 shrink-0">
            <form method="post" action="{{ route('admin.products.toggle', $p) }}">
              @csrf @method('PATCH')
              <button class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] font-extrabold uppercase tracking-wider border transition-all
                {{ $p->is_active ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border border-white/5' }}">
                {{ $p->is_active ? 'Aktif' : 'Off' }}
              </button>
            </form>

            <div class="flex items-center gap-1.5">
              <a href="{{ route('admin.products.edit', $p) }}" 
                 class="size-6 rounded-lg bg-white/5 border border-white/10 hover:border-violet-500/50 flex items-center justify-center text-slate-400 hover:text-white transition-all">
                 ✏️
              </a>
              <a href="{{ route('admin.products.variants.index', $p) }}" 
                 class="size-6 rounded-lg bg-white/5 border border-white/10 hover:border-violet-500/50 flex items-center justify-center text-slate-400 hover:text-white transition-all">
                 🔗
              </a>
            </div>
          </div>

        </div>
      @empty
        <div class="py-16 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
          Belum ada produk yang terdaftar.
        </div>
      @endforelse
    </div>

    {{-- Pagination wrapper --}}
    <div class="mt-6 flex justify-center sm:justify-end reveal">
      {{ $products->links() }}
    </div>
  </div>

  @push('body')
    <script>
      (function () {
        const catSel = document.getElementById('filterCategory');
        const subSel = document.getElementById('filterSubcategory');
        if (!catSel || !subSel) return;
        catSel.addEventListener('change', async () => {
          const catId = catSel.value;
          subSel.innerHTML = '<option value="">Semua subkategori</option>';
          if (!catId) return;
          const res = await fetch('{{ route('admin.ajax.subcategories.byCategory', 0) }}'.replace('/0', '/' + catId));
          const data = await res.json();
          data.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id; opt.textContent = s.name;
            subSel.appendChild(opt);
          });
        });
      })();
    </script>
  @endpush
@endsection