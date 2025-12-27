@extends('layouts.admin')
@section('title', 'Marketplace Products — Admin')

@section('content')
  <div class="flex items-center justify-between gap-3 flex-wrap">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold">Marketplace Products</h1>
      <p class="text-slate-400 mt-1">
        Produk manual yang dijual (misalnya akun Canva, Netflix, dll).
      </p>
      <a href="{{ route('admin.marketplace.categories.index') }}" class="text-xs text-violet-300 hover:text-violet-200">
        Kelola kategori →
      </a>
    </div>
    <a href="{{ route('admin.marketplace.products.create') }}"
      class="inline-flex items-center h-10 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-medium">
      + Tambah produk
    </a>
  </div>

  @if(session('ok'))
    <div class="mt-4 rounded-xl border border-emerald-900/40 bg-emerald-950/40 text-emerald-200 text-sm px-3 py-2">
      {{ session('ok') }}
    </div>
  @endif
  @if(session('error'))
    <div class="mb-4 rounded-xl border border-rose-900/40 bg-rose-950/40 text-rose-200 text-sm px-3 py-2">
      {{ session('error') }}
    </div>
  @endif


  <form method="get" class="mt-4 flex flex-wrap gap-3 items-center">
    <select name="category_id"
      class="h-10 rounded-xl bg-[#0E1524] border border-slate-800/70 px-3 text-sm text-slate-100">
      <option value="">Semua kategori</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" @selected($categoryId == $cat->id)>{{ $cat->name }}</option>
      @endforeach
    </select>
    <button type="submit" class="h-10 rounded-xl bg-slate-800 hover:bg-slate-700 px-4 text-sm">
      Filter
    </button>
  </form>

  <div class="mt-4 rounded-2xl border border-slate-800/70 bg-[#0E1524] overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="text-xs uppercase text-slate-400 border-b border-slate-800/70">
        <tr class="[&>th]:px-3 [&>th]:py-2.5 [&>th]:text-left">
          <th>Produk</th>
          <th>Kategori</th>
          <th>Status</th>
          <th>Varian</th>
          <th class="text-right pr-4">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-800/70">
        @forelse($products as $product)
          <tr class="[&>td]:px-3 [&>td]:py-2.5">
            <td>
              <div class="text-slate-100">{{ $product->name }}</div>
              <div class="text-xs text-slate-500">{{ $product->slug }}</div>
            </td>
            <td class="text-xs text-slate-300">
              {{ $product->category?->name ?? '-' }}
            </td>
            <td>
              @if($product->is_active)
                <span
                  class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px]">
                  Aktif
                </span>
              @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-700/40 text-slate-300 text-[11px]">
                  Draft
                </span>
              @endif
            </td>
            <td class="text-xs text-slate-300">
              {{ $product->variants()->count() }} varian
            </td>
            <td class="text-right pr-4 space-x-2">
              <a href="{{ route('admin.marketplace.variants.index', $product) }}"
                class="inline-flex items-center h-8 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs">
                Varian
              </a>
              <a href="{{ route('admin.marketplace.products.edit', $product) }}"
                class="inline-flex items-center h-8 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs">
                Edit
              </a>
              <form method="POST" action="{{ route('admin.marketplace.products.destroy', $product) }}" class="inline"
                onsubmit="return confirm('Hapus produk ini? Semua varian (dan mungkin order) akan ikut terhapus.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                  class="inline-flex items-center h-8 px-3 rounded-xl bg-rose-600/80 hover:bg-rose-600 text-xs">
                  Hapus
                </button>
              </form>

            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-3 py-6 text-center text-slate-400 text-sm">
              Belum ada produk marketplace.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $products->links() }}
  </div>
@endsection