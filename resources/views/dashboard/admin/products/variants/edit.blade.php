@extends('layouts.admin')
@section('title','Edit Variant — ' . $product->name)

@section('content')
  <a href="{{ route('admin.products.variants.index', $product) }}" class="text-sm text-slate-400 hover:text-slate-200">← Kembali</a>

  <div class="mt-4 rounded-2xl border border-slate-800/70 bg-[#0E1524] p-6 max-w-3xl">
    <h1 class="text-2xl font-semibold">Edit Variant</h1>
    <p class="text-slate-400 mt-1">Produk: <span class="text-slate-200 font-medium">{{ $product->name }}</span></p>

    <form method="post" action="{{ route('admin.products.variants.update', [$product,$variant]) }}" class="mt-6 space-y-4">
      @csrf @method('PUT')

      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm text-slate-300">Buyer SKU Code</label>
          <input name="buyer_sku_code" value="{{ old('buyer_sku_code',$variant->buyer_sku_code) }}" required
                 class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
        </div>
        <div>
          <label class="text-sm text-slate-300">Nama Varian</label>
          <input name="name" value="{{ old('name',$variant->name) }}" required
                 class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm text-slate-300">Base Price (Rp)</label>
          <input name="base_price" type="number" min="0" step="1" value="{{ old('base_price',$variant->base_price) }}" required
                 class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
        </div>
        <div>
          <label class="text-sm text-slate-300">Markup Varian (Rp)</label>
          <input name="markup_rp" type="number" min="0" step="1" value="{{ old('markup_rp',$variant->markup_rp) }}"
                 placeholder="kosongkan = pakai markup produk"
                 class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
          <p class="text-xs text-slate-500 mt-1">Markup produk saat ini: Rp {{ number_format($product->markup_rp,0,',','.') }}</p>
        </div>
      </div>

      <label class="inline-flex items-center gap-2 text-sm text-slate-300">
        <input type="checkbox" name="is_active" value="1" class="accent-violet-600" {{ old('is_active',$variant->is_active) ? 'checked' : '' }}>
        Aktif
      </label>

      <div class="pt-2">
        <button class="h-11 px-5 rounded-xl bg-violet-600 hover:bg-violet-500">Simpan Perubahan</button>
      </div>

      @if ($errors->any())
        <div class="rounded-lg border border-red-900/40 bg-red-950/30 text-red-200 text-sm px-3 py-2">
          {{ $errors->first() }}
        </div>
      @endif
    </form>
  </div>
@endsection
