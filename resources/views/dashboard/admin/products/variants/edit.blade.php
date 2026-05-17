@extends('layouts.admin')
@section('title','Edit Variant — ' . $product->name)

@push('head')
<style>
  .variant-form-card {
    background: rgba(17, 24, 39, 0.25);
    backdrop-filter: blur(25px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 20px 50px -15px rgba(0, 0, 0, 0.6);
  }
</style>
@endpush

@section('content')
  {{-- Back Button --}}
  <div class="reveal flex items-center justify-between gap-4">
    <a href="{{ route('admin.products.variants.index', $product) }}" 
       class="h-10 px-4 inline-flex items-center rounded-xl bg-white/5 border border-white/10 text-slate-300 hover:text-white text-xs font-bold transition-all shadow-sm">
       ← Kembali
    </a>
  </div>

  {{-- Main Container Card --}}
  <div class="reveal mt-6 rounded-[2rem] variant-form-card p-6 md:p-8 max-w-3xl space-y-6">
    <div>
      <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-[9px] font-extrabold uppercase tracking-widest text-violet-300">
        📝 Edit Mode
      </div>
      <h1 class="text-3xl font-extrabold text-white tracking-tight mt-1.5">Edit Varian</h1>
      <p class="text-sm text-slate-400 font-medium">Produk Induk: <span class="text-violet-300 font-extrabold">{{ $product->name }}</span></p>
    </div>

    <form method="post" action="{{ route('admin.products.variants.update', [$product,$variant]) }}" class="space-y-5">
      @csrf @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Buyer SKU Code</label>
          <input name="buyer_sku_code" value="{{ old('buyer_sku_code',$variant->buyer_sku_code) }}" required
                 class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
        </div>
        
        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Nama Varian</label>
          <input name="name" value="{{ old('name',$variant->name) }}" required
                 class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Markup Varian (Rupiah)</label>
          <input name="markup_rp" type="number" min="0" step="1" value="{{ old('markup_rp',$variant->markup_rp) }}"
                 placeholder="kosongkan = pakai markup produk"
                 class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
          <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">Markup produk saat ini: Rp {{ number_format($product->markup_rp,0,',','.') }}</p>
        </div>
      </div>

      <div class="pt-1.5 pb-2">
        <label class="inline-flex items-center gap-2.5 text-xs font-bold text-slate-300 cursor-pointer">
          <input type="checkbox" name="is_active" value="1" class="accent-violet-600 size-4" {{ old('is_active',$variant->is_active) ? 'checked' : '' }}>
          <span>Varian Aktif & Dapat Dibeli Pelanggan</span>
        </label>
      </div>

      <div class="pt-3 flex justify-end">
        <button class="h-11 px-6 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-xs font-bold text-white transition-all shadow-[0_0_15px_rgba(139,92,246,0.25)]">
          Simpan Perubahan
        </button>
      </div>

      @if ($errors->any())
        <div class="mt-4 rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-2.5 text-xs font-bold text-rose-300">
          ⚠️ {{ $errors->first() }}
        </div>
      @endif
    </form>
  </div>
@endsection
