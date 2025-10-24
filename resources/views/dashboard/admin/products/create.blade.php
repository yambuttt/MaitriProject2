@extends('layouts.admin')
@section('title','Tambah Produk — Admin')

@section('content')
  <a href="{{ route('admin.products.index') }}" class="text-sm text-slate-400 hover:text-slate-200">← Kembali</a>

  <div class="mt-4 rounded-2xl border border-slate-800/70 bg-[#0E1524] p-6 max-w-3xl">
    <h1 class="text-2xl font-semibold">Tambah Produk</h1>
    <p class="text-slate-400 mt-1">Buat produk tanpa varian dulu.</p>

    <form method="post" action="{{ route('admin.products.store') }}" class="mt-6 space-y-4">
      @csrf

      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm text-slate-300">Kategori</label>
          <select id="catSel" name="category_id" required
                  class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
            <option value="">Pilih kategori…</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="text-sm text-slate-300">Subkategori</label>
          <select id="subSel" name="subcategory_id" required
                  class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
            <option value="">Pilih subkategori…</option>
          </select>
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm text-slate-300">Nama Produk</label>
          <input name="name" required placeholder="cth: Paket Data Telkomsel"
                 class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
        </div>
        <div>
          <label class="text-sm text-slate-300">Slug (opsional)</label>
          <input name="slug" placeholder="otomatis jika dikosongkan"
                 class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm text-slate-300">Provider (opsional)</label>
          <input name="provider" placeholder="cth: Telkomsel / MLBB"
                 class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
        </div>
        <div>
          <label class="text-sm text-slate-300">Markup Produk (Rp)</label>
          <input name="markup_rp" type="number" min="0" step="1" value="0" required
                 class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
        </div>
      </div>

      <div>
        <label class="text-sm text-slate-300">Deskripsi (opsional)</label>
        <textarea name="description" rows="3"
                  class="mt-1 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 py-2 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30"></textarea>
      </div>

      <label class="inline-flex items-center gap-2 text-sm text-slate-300">
        <input type="checkbox" name="is_active" value="1" class="accent-violet-600" checked>
        Aktif
      </label>

      <div class="pt-2">
        <button class="h-11 px-5 rounded-xl bg-violet-600 hover:bg-violet-500">Simpan</button>
      </div>

      @if ($errors->any())
        <div class="rounded-lg border border-red-900/40 bg-red-950/30 text-red-200 text-sm px-3 py-2">
          {{ $errors->first() }}
        </div>
      @endif
    </form>
  </div>

  @push('body')
  <script>
    (function(){
      const cat = document.getElementById('catSel');
      const sub = document.getElementById('subSel');
      cat?.addEventListener('change', async ()=>{
        sub.innerHTML = '<option value="">Pilih subkategori…</option>';
        if(!cat.value) return;
        const res = await fetch('{{ route('admin.ajax.subcategories.byCategory', 0) }}'.replace('/0','/'+cat.value));
        const data = await res.json();
        data.forEach(s=>{
          const opt = document.createElement('option');
          opt.value = s.id; opt.textContent = s.name;
          sub.appendChild(opt);
        });
      });
    })();
  </script>
  @endpush
@endsection
