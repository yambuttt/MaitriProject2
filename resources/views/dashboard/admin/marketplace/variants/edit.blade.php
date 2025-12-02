@extends('layouts.admin')
@section('title','Edit Variant — Admin')

@section('content')
  <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold">Edit Varian</h1>
      <p class="text-slate-400 text-sm mt-1">Produk: {{ $product->name }}</p>
    </div>
    <a href="{{ route('admin.marketplace.variants.index', $product) }}"
       class="text-sm text-slate-400 hover:text-slate-200">← Kembali</a>
  </div>

  <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-5 max-w-xl">
    <form method="POST" action="{{ route('admin.marketplace.variants.update', [$product, $variant]) }}" class="space-y-4">
      @csrf

      <div class="space-y-1">
        <label class="text-xs text-slate-400">Nama varian</label>
        <input type="text" name="name" value="{{ old('name', $variant->name) }}"
               class="w-full h-10 rounded-xl bg-slate-950 border border-slate-800/80 px-3 text-sm text-slate-100">
        @error('name') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="space-y-1">
        <label class="text-xs text-slate-400">Durasi (hari, opsional)</label>
        <input type="number" name="duration_days" value="{{ old('duration_days', $variant->duration_days) }}"
               class="w-full h-10 rounded-xl bg-slate-950 border border-slate-800/80 px-3 text-sm text-slate-100">
      </div>

      <div class="space-y-1">
        <label class="text-xs text-slate-400">Harga (Rupiah)</label>
        <input type="number" name="price" value="{{ old('price', $variant->price) }}"
               class="w-full h-10 rounded-xl bg-slate-950 border border-slate-800/80 px-3 text-sm text-slate-100">
        @error('price') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
      </div>

      <label class="inline-flex items-center gap-2 text-xs text-slate-300">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $variant->is_active) ? 'checked' : '' }}>
        <span>Varian aktif</span>
      </label>

      <button type="submit"
              class="mt-2 inline-flex items-center h-10 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-medium">
        Simpan perubahan
      </button>
    </form>
  </div>
@endsection
