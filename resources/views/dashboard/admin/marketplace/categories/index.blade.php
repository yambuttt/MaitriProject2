@extends('layouts.admin')
@section('title','Marketplace Categories — Admin')

@section('content')
  <div class="flex items-center justify-between gap-3 flex-wrap">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold">Marketplace Categories</h1>
      <p class="text-slate-400 mt-1">Kategori untuk produk marketplace (akun-akun digital, dsb).</p>
    </div>
    <a href="{{ route('admin.marketplace.categories.create') }}"
       class="inline-flex items-center h-10 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-medium">
      + Tambah kategori
    </a>
  </div>

  @if(session('ok'))
    <div class="mt-4 rounded-xl border border-emerald-900/40 bg-emerald-950/40 text-emerald-200 text-sm px-3 py-2">
      {{ session('ok') }}
    </div>
  @endif

  <div class="mt-4 rounded-2xl border border-slate-800/70 bg-[#0E1524] overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="text-xs uppercase text-slate-400 border-b border-slate-800/70">
      <tr class="[&>th]:px-3 [&>th]:py-2.5 [&>th]:text-left">
        <th>Nama</th>
        <th>Slug</th>
        <th>Status</th>
        <th class="text-right pr-4">Aksi</th>
      </tr>
      </thead>
      <tbody class="divide-y divide-slate-800/70">
      @forelse($categories as $category)
        <tr class="[&>td]:px-3 [&>td]:py-2.5">
          <td class="text-slate-100">{{ $category->name }}</td>
          <td class="font-mono text-xs text-slate-400">{{ $category->slug }}</td>
          <td>
            @if($category->is_active)
              <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px]">
                Aktif
              </span>
            @else
              <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-700/40 text-slate-300 text-[11px]">
                Nonaktif
              </span>
            @endif
          </td>
          <td class="text-right pr-4">
            <a href="{{ route('admin.marketplace.categories.edit', $category) }}"
               class="inline-flex items-center h-8 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs">
              Edit
            </a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="px-3 py-6 text-center text-slate-400 text-sm">
            Belum ada kategori.
          </td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>
@endsection
