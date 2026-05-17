@extends('layouts.admin')
@section('title', 'Tambah Produk — Admin')

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
        📦 Products Hub
      </div>
      <h1 class="text-3xl font-extrabold text-white tracking-tight mt-1.5">Tambah Produk</h1>
      <p class="text-sm text-slate-400 font-medium">Buat produk katalog digital baru tanpa varian.</p>
    </div>

    <form method="post" action="{{ route('admin.products.store') }}" class="space-y-5" enctype="multipart/form-data">
      @csrf

      {{-- Hierarchy selection --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Kategori Utama</label>
          <select id="catSel" name="category_id" required
                  class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-3.5 text-xs text-slate-300 outline-none focus:border-violet-500/50 transition-all">
            <option value="">Pilih kategori utama…</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        
        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Sub Kategori</label>
          <select id="subSel" name="subcategory_id" 
                  class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-3.5 text-xs text-slate-300 outline-none focus:border-violet-500/50 transition-all">
            <option value="">Pilih subkategori…</option>
          </select>
        </div>
      </div>

      {{-- Name and Slug --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Nama Produk</label>
          <input name="name" required placeholder="Contoh: Paket Data Telkomsel 10GB..."
                 class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
        </div>
        
        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Slug (Opsional)</label>
          <input name="slug" placeholder="Otomatis jika dikosongkan"
                 class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
        </div>
      </div>

      {{-- Provider & Markup --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Nama Provider (Opsional)</label>
          <input name="provider" placeholder="Contoh: Telkomsel, MLBB, Digiflazz..."
                 class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
        </div>
        
        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Markup Produk (Rupiah)</label>
          <input name="markup_rp" type="number" min="0" step="1" value="0" required
                 class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
        </div>
      </div>

      {{-- Thumbnail Upload --}}
      <div class="space-y-1.5">
        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Upload Gambar Thumbnail (Opsional)</label>
        <input type="file" name="thumbnail" accept="image/*" 
               class="block w-full text-xs text-slate-300 file:mr-4 file:rounded-xl file:border-0 file:bg-white/5 file:border-white/10 file:px-4 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-violet-600 hover:file:text-white transition-all cursor-pointer">
        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">Rekomendasi rasio 1:1 / 4:3, ukuran maksimal 2 MB.</p>
      </div>

      {{-- Description Textarea --}}
      <div class="space-y-1.5">
        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Deskripsi Ringkas (Opsional)</label>
        <textarea name="description" rows="3" placeholder="Masukkan deskripsi penjelas produk jika dibutuhkan..."
                  class="w-full rounded-xl bg-black/40 border border-white/10 p-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all"></textarea>
      </div>

      {{-- Active Toggle checkbox --}}
      <div class="pt-1.5 pb-2">
        <label class="inline-flex items-center gap-2.5 text-xs font-bold text-slate-300 cursor-pointer">
          <input type="checkbox" name="is_active" value="1" class="accent-violet-600 size-4" checked>
          <span>Produk Aktif & Ditampilkan di Halaman Pembeli</span>
        </label>
      </div>

      {{-- Action Buttons --}}
      <div class="pt-3 flex justify-end">
        <button class="h-12 px-6 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-xs font-bold text-white transition-all shadow-[0_0_15px_rgba(139,92,246,0.25)]">
          Simpan Produk Baru
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
          sub.innerHTML = '<option value="">Pilih subkategori…</option>';
          if (!cat.value) return;
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