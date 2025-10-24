@extends('layouts.admin')
@section('title','Products — Admin')

@section('content')
  <div class="flex items-center justify-between gap-3 flex-wrap">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold">Products</h1>
      <p class="text-slate-400 mt-1">Kelola produk (tanpa varian dulu).</p>
    </div>
    <div class="flex items-center gap-2">
      @if (session('ok'))
        <div class="rounded-lg border border-emerald-900/30 bg-emerald-950/30 text-emerald-200 text-sm px-3 py-2">
          {{ session('ok') }}
        </div>
      @endif
      <a href="{{ route('admin.products.create') }}" class="h-10 px-4 rounded-xl bg-violet-600 hover:bg-violet-500">Tambah</a>
    </div>
  </div>

  <form method="get" class="mt-4 grid md:grid-cols-[1fr_220px_220px_140px] gap-3">
    <div class="relative">
      <input name="q" value="{{ $q }}" type="search" placeholder="Cari nama/slug/provider…"
             class="w-full h-11 rounded-xl bg-[#0E1524] border border-slate-800/70 ps-10 pe-3 outline-none placeholder:text-slate-500 focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
      <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-5 text-slate-500" viewBox="0 0 24 24" fill="none"><path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="1.5"/></svg>
    </div>
    <select name="category_id" id="filterCategory" class="h-11 rounded-xl bg-[#0E1524] border border-slate-800/70 px-3 outline-none focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
      <option value="">Semua kategori</option>
      @foreach($categories as $c)
        <option value="{{ $c->id }}" @selected((string)$cat === (string)$c->id)>{{ $c->name }}</option>
      @endforeach
    </select>
    <select name="subcategory_id" id="filterSubcategory" class="h-11 rounded-xl bg-[#0E1524] border border-slate-800/70 px-3 outline-none focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
      <option value="">Semua subkategori</option>
      @foreach($subcategories as $s)
        <option value="{{ $s->id }}" @selected((string)$sub === (string)$s->id)>{{ $s->name }}</option>
      @endforeach
    </select>
    <div class="flex gap-3">
      <select name="per_page" class="h-11 rounded-xl bg-[#0E1524] border border-slate-800/70 px-3 outline-none focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
        @foreach([12,24,48,96] as $n)
          <option value="{{ $n }}" @selected($pp==$n)>{{ $n }}/hal</option>
        @endforeach
      </select>
      <button class="h-11 rounded-xl bg-violet-600 hover:bg-violet-500 px-4">Terapkan</button>
    </div>
  </form>

  <section class="mt-4 rounded-2xl border border-slate-800/70 bg-[#0E1524] overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-[#0c1222] text-slate-400">
          <tr>
            <th class="text-left px-4 py-3">Nama</th>
            <th class="text-left px-4 py-3">Kategori → Subkategori</th>
            <th class="text-left px-4 py-3">Provider</th>
            <th class="text-left px-4 py-3">Markup (Rp)</th>
            <th class="text-left px-4 py-3">Status</th>
            <th class="text-left px-4 py-3 w-44">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($products as $p)
            <tr class="border-t border-slate-800/70">
              <td class="px-4 py-3 font-medium">{{ $p->name }}</td>
              <td class="px-4 py-3 text-slate-300">{{ $p->category?->name }} → {{ $p->subcategory?->name }}</td>
              <td class="px-4 py-3">{{ $p->provider ?: '-' }}</td>
              <td class="px-4 py-3">Rp {{ number_format($p->markup_rp,0,',','.') }}</td>
              <td class="px-4 py-3">
                <form method="post" action="{{ route('admin.products.toggle', $p) }}">
                  @csrf @method('PATCH')
                  <button class="inline-flex items-center px-2 py-1 rounded-lg text-xs
                    {{ $p->is_active ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-700/40' : 'bg-slate-500/10 text-slate-300 border border-slate-700/40' }}">
                    {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                  </button>
                </form>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <a href="{{ route('admin.products.edit', $p) }}" class="px-3 py-1.5 rounded-lg border border-slate-800/70 hover:border-slate-700">Edit</a>
                  <a href="{{ route('admin.products.variants.index', $p) }}" class="px-3 py-1.5 rounded-lg border border-slate-800/70 hover:border-slate-700">Variants</a>

                  <form method="post" action="{{ route('admin.products.destroy', $p) }}" onsubmit="return confirm('Hapus produk ini?')">
                    @csrf @method('DELETE')
                    <button class="px-3 py-1.5 rounded-lg border border-red-900/40 text-red-300 hover:bg-red-950/30">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-10 text-center text-slate-400">Belum ada produk.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-4 border-t border-slate-800/70">
      {{ $products->links() }}
    </div>
  </section>

  @push('body')
  <script>
    (function(){
      const catSel = document.getElementById('filterCategory');
      const subSel = document.getElementById('filterSubcategory');
      if(!catSel || !subSel) return;
      catSel.addEventListener('change', async ()=>{
        const catId = catSel.value;
        subSel.innerHTML = '<option value="">Semua subkategori</option>';
        if(!catId) return;
        const res = await fetch('{{ route('admin.ajax.subcategories.byCategory', 0) }}'.replace('/0', '/' + catId));
        const data = await res.json();
        data.forEach(s=>{
          const opt = document.createElement('option');
          opt.value = s.id; opt.textContent = s.name;
          subSel.appendChild(opt);
        });
      });
    })();
  </script>
  @endpush
@endsection
