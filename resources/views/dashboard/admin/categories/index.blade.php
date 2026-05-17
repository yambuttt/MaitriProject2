@extends('layouts.admin')
@section('title','Kategori — Admin')

@push('head')
<style>
  .categories-table-card {
    background: rgba(17, 24, 39, 0.25);
    backdrop-filter: blur(25px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 20px 50px -15px rgba(0, 0, 0, 0.6);
  }
  .quick-add-card {
    background: rgba(139, 92, 246, 0.02);
    border: 1px solid rgba(139, 92, 246, 0.1);
  }
</style>
@endpush

@section('content')
  {{-- Header --}}
  <div class="reveal flex items-center justify-between gap-3 flex-wrap">
    <div class="space-y-1">
      <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-[9px] font-extrabold uppercase tracking-widest text-violet-300">
        🗂️ Catalog Management
      </div>
      <h1 class="text-3xl font-extrabold text-white tracking-tight">Kategori</h1>
      <p class="text-sm text-slate-400 font-medium">Kelola kategori utama untuk katalog produk digital Anda.</p>
    </div>

    @if (session('ok'))
      <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-2.5 text-xs font-bold text-emerald-300 flex items-center gap-2">
        <span class="size-1.5 rounded-full bg-emerald-400"></span>
        {{ session('ok') }}
      </div>
    @endif
  </div>

  {{-- Filter & Pagination Per Page Form --}}
  <form method="get" class="reveal mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_140px_120px] gap-3">
    <div class="relative">
      <input name="q" value="{{ $q }}" type="search" placeholder="Cari nama atau slug kategori..."
             class="w-full h-11 rounded-xl bg-black/40 border border-white/10 ps-10 pe-3 text-xs text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
      <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-slate-500" viewBox="0 0 24 24" fill="none">
        <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="1.5"/>
      </svg>
    </div>
    
    <select name="per_page" class="h-11 rounded-xl bg-black/40 border border-white/10 px-3.5 text-xs text-slate-300 outline-none focus:border-violet-500/50 transition-all">
      @foreach([12,24,48,96] as $n)
        <option value="{{ $n }}" @selected($pp==$n)>{{ $n }} / Halaman</option>
      @endforeach
    </select>
    
    <button class="h-11 rounded-xl bg-violet-600 hover:bg-violet-500 text-xs font-bold text-white transition-all shadow-md">
      Terapkan
    </button>
  </form>

  {{-- Quick Add Form Card --}}
  <div class="reveal mt-4 rounded-3xl quick-add-card p-5">
    <div class="text-[9px] font-extrabold uppercase tracking-widest text-violet-400 mb-3 block">⚡ Tambah Kategori Cepat</div>
    
    <form method="post" action="{{ route('admin.categories.store') }}" class="grid grid-cols-1 md:grid-cols-[1fr_320px_140px] gap-4 items-end">
      @csrf
      <div class="space-y-1.5">
        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Nama Kategori</label>
        <input name="name" required placeholder="Contoh: Game, Pulsa Reguler, Paket Data..."
               class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
      </div>
      
      <div class="space-y-1.5">
        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Slug (Opsional)</label>
        <input name="slug" placeholder="Otomatis jika dikosongkan"
               class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
      </div>
      
      <div class="flex items-center justify-between sm:justify-end gap-5">
        <label class="inline-flex items-center gap-2 text-xs font-bold text-slate-300 cursor-pointer">
          <input type="checkbox" name="is_active" value="1" class="accent-violet-600 size-4" checked>
          <span>Aktif</span>
        </label>
        
        <button class="h-11 px-5 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-xs font-bold text-white transition-all shadow-md shrink-0">
          Tambah
        </button>
      </div>
    </form>

    @if ($errors->any())
      <div class="mt-3.5 rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-2.5 text-xs font-bold text-rose-300">
        ⚠️ {{ $errors->first() }}
      </div>
    @endif
  </div>

  {{-- Table List Grid --}}
  <section class="reveal mt-6 rounded-3xl categories-table-card overflow-hidden">
    <div class="overflow-x-auto p-4 md:p-0">
      <table class="w-full block md:table text-xs font-medium border-collapse">
        <thead class="hidden md:table-header-group">
          <tr class="bg-black/30 border-b border-white/5 font-extrabold text-slate-400 uppercase tracking-widest text-[10px]">
            <th class="text-left px-5 py-4">Nama</th>
            <th class="text-left px-5 py-4">Slug</th>
            <th class="text-left px-5 py-4">Status</th>
            <th class="text-right px-5 py-4 w-44">Aksi</th>
          </tr>
        </thead>
        <tbody class="w-full block md:table-row-group divide-y divide-white/[0.03] md:divide-y-0 space-y-4 md:space-y-0">
          @forelse ($categories as $c)
            <tr class="flex flex-col md:table-row border-b border-white/[0.03] hover:bg-white/[0.015] transition-all duration-300 p-5 md:p-0 gap-3.5 md:gap-0 bg-white/[0.01] md:bg-transparent rounded-2xl md:rounded-none mb-4 md:mb-0 shadow-lg md:shadow-none border border-white/5 md:border-none">
              
              {{-- Nama --}}
              <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4">
                <div class="flex items-center justify-between md:block">
                  <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Nama Kategori</span>
                  <span class="text-white font-extrabold text-sm md:text-xs tracking-tight">{{ $c->name }}</span>
                </div>
              </td>

              {{-- Slug --}}
              <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
                <div class="flex items-center justify-between md:block">
                  <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Slug</span>
                  <span class="font-mono text-xs text-slate-400">{{ $c->slug }}</span>
                </div>
              </td>

              {{-- Status --}}
              <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
                <div class="flex items-center justify-between md:block">
                  <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Status</span>
                  
                  <form method="post" action="{{ route('admin.categories.toggle', $c) }}">
                    @csrf @method('PATCH')
                    <button class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wider border transition-all
                      {{ $c->is_active ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.1)]' : 'bg-slate-500/10 text-slate-400 border border-white/5' }}">
                      {{ $c->is_active ? 'Aktif' : 'Nonaktif' }}
                    </button>
                  </form>
                </div>
              </td>

              {{-- Aksi --}}
              <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <a href="{{ route('admin.categories.edit', $c) }}" 
                     class="h-8 px-4 inline-flex items-center rounded-xl bg-white/5 border border-white/10 hover:border-violet-500/50 hover:bg-violet-600 hover:text-white text-xs font-bold transition-all shadow-sm">
                     Edit
                  </a>
                  
                  <form method="post" action="{{ route('admin.categories.destroy', $c) }}"
                        onsubmit="return confirm('Hapus kategori ini beserta seluruh data di dalamnya?')">
                    @csrf @method('DELETE')
                    <button class="h-8 px-4 inline-flex items-center rounded-xl bg-white/5 border border-rose-500/20 text-rose-400 hover:bg-rose-600 hover:text-white hover:border-rose-500/50 text-xs font-bold transition-all shadow-sm">
                      Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr class="flex flex-col md:table-row">
              <td colspan="4" class="px-5 py-10 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                Belum ada kategori yang terdaftar.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination links --}}
    <div class="p-4 border-t border-white/5 flex justify-center sm:justify-end">
      {{ $categories->links() }}
    </div>
  </section>
@endsection
