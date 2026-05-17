@extends('layouts.admin')
@section('title', 'Edit Produk — Admin')

@push('head')
<style>
  .product-form-card {
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
    <a href="{{ route('admin.products.index') }}" 
       class="h-10 px-4 inline-flex items-center rounded-xl bg-white/5 border border-white/10 text-slate-300 hover:text-white text-xs font-bold transition-all shadow-sm">
       ← Kembali
    </a>
  </div>

  {{-- Main Form Container --}}
  <div class="reveal mt-6 rounded-[2rem] product-form-card p-6 md:p-8 max-w-4xl space-y-6">
    <div>
      <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-[9px] font-extrabold uppercase tracking-widest text-violet-300">
        📝 Edit Mode
      </div>
      <h1 class="text-3xl font-extrabold text-white tracking-tight mt-1.5">Edit Produk</h1>
      <p class="text-sm text-slate-400 font-medium">Perbarui parameter data produk katalog digital Anda.</p>
    </div>

    <form method="post" action="{{ route('admin.products.update', $product) }}" class="space-y-5" enctype="multipart/form-data">
      @csrf @method('PUT')

      {{-- Hierarchy selection --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Kategori Utama</label>
          <select id="catSel" name="category_id" required
                  class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-3.5 text-xs text-slate-300 outline-none focus:border-violet-500/50 transition-all">
            @foreach($categories as $c)
              <option value="{{ $c->id }}" @selected($product->category_id == $c->id)>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        
        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Sub Kategori</label>
          <select id="subSel" name="subcategory_id" 
                  class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-3.5 text-xs text-slate-300 outline-none focus:border-violet-500/50 transition-all">
            @foreach($subcategories as $s)
              <option value="{{ $s->id }}" @selected($product->subcategory_id == $s->id)>{{ $s->name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- Name and Slug --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Nama Produk</label>
          <input name="name" value="{{ old('name', $product->name) }}" required
                 class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
        </div>
        
        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Slug</label>
          <input name="slug" value="{{ old('slug', $product->slug) }}"
                 class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
        </div>
      </div>

      {{-- Provider & Markup --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Nama Provider (Opsional)</label>
          <input name="provider" value="{{ old('provider', $product->provider) }}" placeholder="Contoh: Telkomsel, MLBB, Digiflazz..."
                 class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
        </div>
        
        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Markup Produk (Rupiah)</label>
          <input name="markup_rp" type="number" min="0" step="1" value="{{ old('markup_rp', $product->markup_rp) }}" required
                 class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
        </div>
      </div>

      {{-- Thumbnail Upload & Preview --}}
      <div class="grid grid-cols-1 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)] gap-5 items-start">
        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Upload Gambar Thumbnail (Opsional)</label>
          <input type="file" name="thumbnail" accept="image/*" 
                 class="block w-full text-xs text-slate-300 file:mr-4 file:rounded-xl file:border-0 file:bg-white/5 file:border-white/10 file:px-4 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-violet-600 hover:file:text-white transition-all cursor-pointer">
          <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">Biarkan kosong jika tidak ingin mengubah thumbnail gambar saat ini.</p>
        </div>

        @if($product->thumbnail)
          <div class="space-y-2">
            <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-500 block">🖼️ Preview Gambar Saat Ini</span>
            <div class="rounded-2xl border border-white/10 bg-black/40 overflow-hidden size-28 shadow-lg">
              <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}"
                   class="w-full h-full object-cover">
            </div>
          </div>
        @endif
      </div>

      {{-- Description Textarea --}}
      <div class="space-y-1.5">
        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Deskripsi Ringkas (Opsional)</label>
        <textarea name="description" rows="3" placeholder="Masukkan deskripsi penjelas produk jika dibutuhkan..."
                  class="w-full rounded-xl bg-black/40 border border-white/10 p-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">{{ old('description', $product->description) }}</textarea>
      </div>

      {{-- Active Toggle checkbox --}}
      <div class="pt-1.5 pb-2">
        <label class="inline-flex items-center gap-2.5 text-xs font-bold text-slate-300 cursor-pointer">
          <input type="checkbox" name="is_active" value="1" class="accent-violet-600 size-4" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
          <span>Produk Aktif & Ditampilkan di Halaman Pembeli</span>
        </label>
      </div>

      {{-- Action Buttons --}}
      <div class="pt-3 flex justify-end">
        <button class="h-12 px-6 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-xs font-bold text-white transition-all shadow-[0_0_15px_rgba(139,92,246,0.25)]">
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

  @push('body')
    <script>
      (function () {
        const cat = document.getElementById('catSel');
        const sub = document.getElementById('subSel');
        cat?.addEventListener('change', async () => {
          sub.innerHTML = '';
          if (!cat.value) {
            const opt = document.createElement('option'); opt.value = ''; opt.textContent = 'Pilih subkategori…'; sub.appendChild(opt); return;
          }
          const res = await fetch('{{ route('admin.ajax.subcategories.byCategory', 0) }}'.replace('/0', '/' + cat.value));
          const data = await res.json();
          data.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id; opt.textContent = s.name;
            sub.appendChild(opt);
          });
        });
      })();
    </script>
  @endpush
@endsection