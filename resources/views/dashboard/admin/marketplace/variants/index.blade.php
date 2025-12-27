@extends('layouts.admin')
@section('title', 'Marketplace Variants — Admin')

@section('content')
  <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold">Varian {{ $product->name }}</h1>
      <p class="text-slate-400 mt-1">
        Atur pilihan durasi & harga untuk produk ini.
      </p>
      <a href="{{ route('admin.marketplace.products.index') }}" class="text-xs text-slate-400 hover:text-slate-200">←
        Kembali ke daftar produk</a>
    </div>
    <a href="{{ route('admin.marketplace.variants.create', $product) }}"
      class="inline-flex items-center h-10 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-medium">
      + Tambah varian
    </a>
  </div>

  @if(session('ok'))
    <div class="mb-4 rounded-xl border border-emerald-900/40 bg-emerald-950/40 text-emerald-200 text-sm px-3 py-2">
      {{ session('ok') }}
    </div>
  @endif
  @if(session('error'))
  <div class="mb-4 rounded-xl border border-rose-900/40 bg-rose-950/40 text-rose-200 text-sm px-3 py-2">
    {{ session('error') }}
  </div>
@endif


  <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="text-xs uppercase text-slate-400 border-b border-slate-800/70">
        <tr class="[&>th]:px-3 [&>th]:py-2.5 [&>th]:text-left">
          <th>Nama varian</th>
          <th>Durasi</th>
          <th>Harga</th>
          <th>Status</th>
          <th class="text-right pr-4">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-800/70">
        @forelse($variants as $variant)
          <tr class="[&>td]:px-3 [&>td]:py-2.5">
            <td class="text-slate-100">{{ $variant->name }}</td>
            <td class="text-xs text-slate-300">
              @if($variant->duration_days)
                {{ $variant->duration_days }} hari
              @else
                -
              @endif
            </td>
            <td class="text-slate-100">
              Rp {{ number_format($variant->price, 0, ',', '.') }}
            </td>
            <td>
              @if($variant->is_active)
                <span
                  class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px]">
                  Aktif
                </span>
              @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-700/40 text-slate-300 text-[11px]">
                  Nonaktif
                </span>
              @endif
            </td>
            <td class="text-right pr-4">
              <a href="{{ route('admin.marketplace.variants.edit', [$product, $variant]) }}"
                class="inline-flex items-center h-8 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs">
                Edit
              </a>
              <form method="POST" action="{{ route('admin.marketplace.variants.destroy', [$product, $variant]) }}"
                class="inline" onsubmit="return confirm('Hapus varian ini?');">
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
              Belum ada varian untuk produk ini.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection