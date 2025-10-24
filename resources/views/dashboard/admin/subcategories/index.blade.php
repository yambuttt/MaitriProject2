@extends('layouts.admin')
@section('title','Subcategories — Admin')

@section('content')
  <div class="flex items-center justify-between gap-3 flex-wrap">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold">Subcategories</h1>
      <p class="text-slate-400 mt-1">Kelola subkategori per kategori.</p>
    </div>
    @if (session('ok'))
      <div class="rounded-lg border border-emerald-900/30 bg-emerald-950/30 text-emerald-200 text-sm px-3 py-2">
        {{ session('ok') }}
      </div>
    @endif
  </div>

  {{-- Filter --}}
  <form method="get" class="mt-4 grid sm:grid-cols-2 lg:grid-cols-[1fr_220px_140px] gap-3">
    <div class="relative">
      <input name="q" value="{{ $q }}" type="search" placeholder="Cari nama/slug…"
             class="w-full h-11 rounded-xl bg-[#0E1524] border border-slate-800/70 ps-10 pe-3 outline-none placeholder:text-slate-500 focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
      <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-5 text-slate-500" viewBox="0 0 24 24" fill="none">
        <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="1.5"/>
      </svg>
    </div>
    <select name="category_id" class="h-11 rounded-xl bg-[#0E1524] border border-slate-800/70 px-3 outline-none focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
      <option value="">Semua kategori</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" @selected((string)$catId === (string)$cat->id)>{{ $cat->name }}</option>
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

  {{-- Form tambah cepat --}}
  <div class="mt-4 rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
    <form method="post" action="{{ route('admin.subcategories.store') }}" class="grid md:grid-cols-[1fr_320px_120px] gap-3">
      @csrf
      <div class="grid sm:grid-cols-[220px_1fr] gap-3">
        <div>
          <label class="text-sm text-slate-300">Kategori</label>
          <select name="category_id" required
                  class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
            <option value="">Pilih kategori…</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" @selected((string)$catId === (string)$cat->id)>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="text-sm text-slate-300">Nama Subkategori</label>
          <input name="name" required placeholder="cth: Telkomsel / Indosat / GoPay"
                 class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
        </div>
      </div>
      <div>
        <label class="text-sm text-slate-300">Slug (opsional)</label>
        <input name="slug" placeholder="otomatis jika dikosongkan"
               class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
      </div>
      <div class="flex items-end justify-end gap-3">
        <label class="inline-flex items-center gap-2 text-sm text-slate-300">
          <input type="checkbox" name="is_active" value="1" class="accent-violet-600" checked>
          Aktif
        </label>
        <button class="h-11 px-4 rounded-xl bg-violet-600 hover:bg-violet-500">Tambah</button>
      </div>
    </form>

    @if ($errors->any())
      <div class="mt-3 rounded-lg border border-red-900/40 bg-red-950/30 text-red-200 text-sm px-3 py-2">
        {{ $errors->first() }}
      </div>
    @endif
  </div>

  {{-- Tabel --}}
  <section class="mt-4 rounded-2xl border border-slate-800/70 bg-[#0E1524] overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-[#0c1222] text-slate-400">
          <tr>
            <th class="text-left px-4 py-3">Kategori</th>
            <th class="text-left px-4 py-3">Nama</th>
            <th class="text-left px-4 py-3">Slug</th>
            <th class="text-left px-4 py-3">Status</th>
            <th class="text-left px-4 py-3 w-44">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($subs as $s)
            <tr class="border-t border-slate-800/70">
              <td class="px-4 py-3 text-slate-300">{{ $s->category?->name }}</td>
              <td class="px-4 py-3 font-medium">{{ $s->name }}</td>
              <td class="px-4 py-3 text-slate-400">{{ $s->slug }}</td>
              <td class="px-4 py-3">
                <form method="post" action="{{ route('admin.subcategories.toggle', $s) }}">
                  @csrf @method('PATCH')
                  <button class="inline-flex items-center px-2 py-1 rounded-lg text-xs
                    {{ $s->is_active ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-700/40' : 'bg-slate-500/10 text-slate-300 border border-slate-700/40' }}">
                    {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                  </button>
                </form>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <a href="{{ route('admin.subcategories.edit', $s) }}" class="px-3 py-1.5 rounded-lg border border-slate-800/70 hover:border-slate-700">Edit</a>
                  <form method="post" action="{{ route('admin.subcategories.destroy', $s) }}"
                        onsubmit="return confirm('Hapus subkategori ini?')">
                    @csrf @method('DELETE')
                    <button class="px-3 py-1.5 rounded-lg border border-red-900/40 text-red-300 hover:bg-red-950/30">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-10 text-center text-slate-400">Belum ada subkategori.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-4 border-t border-slate-800/70">
      {{ $subs->links() }}
    </div>
  </section>
@endsection
