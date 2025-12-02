@extends('layouts.app')

@section('title','Marketplace — MaitriProject')
@section('page','marketplace')

@section('content')
  <section class="py-8">
    <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
      <div class="flex items-center justify-between gap-3 mb-6">
        <div>
          <h1 class="text-2xl md:text-3xl font-semibold">Marketplace</h1>
          <p class="text-slate-400 text-sm">Beli akun digital (Canva, dsb) dengan aman melalui MaitriProject.</p>
        </div>
        <a href="{{ route('landing') }}" class="text-sm text-slate-400 hover:text-slate-200">← Kembali</a>
      </div>

      <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
        @forelse($products as $product)
          <a href="{{ route('marketplace.product.show', $product) }}"
             class="rounded-2xl border border-slate-800/80 bg-slate-900/50 p-4 hover:border-violet-500/60 transition">
            <h2 class="font-semibold text-slate-50">{{ $product->name }}</h2>
            <p class="mt-1 text-xs text-slate-400">
              {{ $product->category?->name ?? 'Marketplace' }}
            </p>
            <p class="mt-3 text-sm text-slate-300 line-clamp-3">
              {{ Str::limit($product->description, 120) }}
            </p>
            <p class="mt-4 text-xs text-slate-500">Klik untuk lihat varian & harga.</p>
          </a>
        @empty
          <div class="col-span-full text-center text-slate-400 py-12">
            Belum ada produk marketplace yang aktif.
          </div>
        @endforelse
      </div>
    </div>
  </section>
@endsection
