@extends('layouts.app')

@section('title','Katalog Produk — MaitriProject')
@section('meta_description','Pilih kategori, telusuri sub-kategori, lalu pilih produk top up.')
@section('page','catalog')

@section('content')
<section class="py-8">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl md:text-3xl font-semibold">Katalog Produk</h1>
        <p class="text-slate-400 text-sm">Pilih kategori, telusuri sub-kategori, lalu pilih produk.</p>
      </div>
      <a href="{{ route('landing') }}" class="text-sm text-slate-400 hover:text-slate-200">← Kembali</a>
    </div>

    {{-- Top bar: kategori tabs + search --}}
    <div class="mt-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
      {{-- Category Tabs (scrollable) --}}
      <div class="relative -mx-4 px-4">
        <div class="flex gap-3 overflow-x-auto no-scrollbar scroll-px-4">
          @foreach($categories as $c)
            <a href="{{ route('catalog', array_filter(['category'=>$c->slug, 'subcategory'=>null, 'q'=>$q ?: null, 'per_page'=>$pp !== 9 ? $pp : null])) }}"
               class="whitespace-nowrap px-3 py-2 rounded-xl border
                      {{ optional($category)->id === $c->id
                          ? 'border-violet-600/70 bg-violet-600/10 text-violet-300'
                          : 'border-slate-800/70 hover:border-slate-700 text-slate-300' }}">
              {{ $c->name }}
            </a>
          @endforeach
        </div>
      </div>

      {{-- Search + per page --}}
      <form method="get" class="flex items-center gap-2">
        @if($category)   <input type="hidden" name="category" value="{{ $category->slug }}"> @endif
        @if($subcategory)<input type="hidden" name="subcategory" value="{{ $subcategory->slug }}"> @endif

        <div class="relative">
          <input name="q" value="{{ $q }}" placeholder="Cari game, pulsa, atau voucher…"
                 class="h-11 w-[min(74vw,640px)] md:w-[420px] rounded-2xl bg-[#0E1524] border border-slate-800/70 ps-10 pe-3 outline-none placeholder:text-slate-500 focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-5 text-slate-500" viewBox="0 0 24 24" fill="none">
            <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="1.5"/>
          </svg>
        </div>

        <select name="per_page" class="h-11 rounded-2xl bg-[#0E1524] border border-slate-800/70 px-3 outline-none">
          @foreach([9,18,27,36,48] as $n)
            <option value="{{ $n }}" @selected($pp==$n)>{{ $n }}/hal</option>
          @endforeach
        </select>

        <button class="h-11 px-4 rounded-2xl bg-violet-600 hover:bg-violet-500">Cari</button>
      </form>
    </div>

    {{-- Subcategory Pills --}}
    <div class="mt-4">
      <div class="flex gap-2 overflow-x-auto no-scrollbar">
        {{-- "Semua" --}}
        <a href="{{ route('catalog', array_filter(['category'=>$category?->slug, 'q'=>$q ?: null, 'per_page'=>$pp !== 9 ? $pp : null])) }}"
           class="whitespace-nowrap px-4 py-2 rounded-full border
                  {{ $subcategory ? 'border-slate-800/70 hover:border-slate-700 text-slate-300' : 'border-violet-600/70 bg-violet-600/10 text-violet-300' }}">
          Semua
        </a>

        @if($category && $subcategories->count())
          @foreach($subcategories as $s)
            @php
              $active = optional($subcategory)->id === $s->id;
              $hasLive = ($s->live_products_count ?? 0) > 0;
            @endphp
            <a href="{{ $hasLive ? route('catalog', array_filter(['category'=>$category->slug,'subcategory'=>$s->slug,'q'=>$q ?: null,'per_page'=>$pp !== 9 ? $pp : null])) : '#' }}"
               class="whitespace-nowrap px-4 py-2 rounded-full border
                      {{ $active ? 'border-violet-600/70 bg-violet-600/10 text-violet-300' : 'border-slate-800/70 hover:border-slate-700 text-slate-300' }}
                      {{ $hasLive ? '' : 'opacity-50 pointer-events-none' }}"
               title="{{ $hasLive ? '' : 'Belum ada produk aktif' }}">
              {{ $s->name }}
            </a>
          @endforeach
        @endif
      </div>
    </div>

    {{-- Info hasil --}}
    <div class="mt-3 text-sm text-slate-400">
      Menampilkan {{ $products->count() }} dari {{ $products->total() }} produk
      @if($category) • Kategori: <span class="text-slate-300">{{ $category->name }}</span>@endif
      @if($subcategory) • Subkategori: <span class="text-slate-300">{{ $subcategory->name }}</span>@endif
    </div>

    {{-- Product Grid --}}
    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @forelse($products as $p)
        @php
          $minFinal = $p->variants->min(fn($v) => $v->final_price);
        @endphp

        <a href="{{ route('catalog.product.show', $p->slug) }}"
           class="group rounded-3xl border border-slate-800/70 bg-[#0E1524] p-4 hover:border-slate-700 transition">
          <div class="flex items-center justify-between text-xs text-slate-400">
            <span>
              {{ $p->category?->name }}
              @if($p->subcategory) — {{ $p->subcategory->name }} @endif
            </span>
            @if($p->provider)<span>{{ $p->provider }}</span>@endif
          </div>

          <h3 class="mt-2 text-lg font-semibold group-hover:text-white">{{ $p->name }}</h3>
          <p class="mt-1 text-sm text-slate-400 line-clamp-2">{{ $p->description ?? '—' }}</p>

          <div class="mt-3 flex items-center justify-between">
            <span class="text-sm text-slate-300">Mulai
              <strong>Rp {{ number_format($minFinal,0,',','.') }}</strong>
            </span>
            <span class="text-xs text-slate-400">{{ $p->variants->count() }} pilihan</span>
          </div>

          <div class="mt-3">
            <span class="inline-flex h-10 items-center justify-center rounded-2xl bg-violet-600 px-4 text-sm hover:bg-violet-500">
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

    {{-- Pagination (Prev / Info / Next) --}}
    <div class="mt-6 flex items-center justify-between">
      <a href="{{ $products->previousPageUrl() ? $products->appends(request()->except('page'))->previousPageUrl() : '#' }}"
         class="px-3 py-2 rounded-xl border border-slate-800/70 {{ $products->onFirstPage() ? 'opacity-40 pointer-events-none' : 'hover:border-slate-700' }}">
        Sebelumnya
      </a>

      <div class="text-sm text-slate-400">
        Halaman {{ $products->currentPage() }} / {{ $products->lastPage() }}
      </div>

      <a href="{{ $products->hasMorePages() ? $products->appends(request()->except('page'))->nextPageUrl() : '#' }}"
         class="px-3 py-2 rounded-xl border border-slate-800/70 {{ $products->hasMorePages() ? 'hover:border-slate-700' : 'opacity-40 pointer-events-none' }}">
        Berikutnya
      </a>
    </div>
  </div>
</section>
@endsection
