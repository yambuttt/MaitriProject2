@extends('layouts.admin')
@section('title','Tambah Marketplace Category — Admin')

@section('content')
  <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold">Tambah Kategori Marketplace</h1>
    </div>
    <a href="{{ route('admin.marketplace.categories.index') }}"
       class="text-sm text-slate-400 hover:text-slate-200">← Kembali</a>
  </div>

  <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-5 max-w-xl">
    <form method="POST" action="{{ route('admin.marketplace.categories.store') }}" class="space-y-4">
      @csrf
      <div class="space-y-1">
        <label class="text-xs text-slate-400">Nama kategori</label>
        <input type="text" name="name" value="{{ old('name') }}"
               class="w-full h-10 rounded-xl bg-slate-950 border border-slate-800/80 px-3 text-sm text-slate-100">
        @error('name') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="space-y-1">
        <label class="text-xs text-slate-400">Deskripsi (opsional)</label>
        <textarea name="description" rows="3"
                  class="w-full rounded-xl bg-slate-950 border border-slate-800/80 px-3 py-2 text-sm text-slate-100">{{ old('description') }}</textarea>
      </div>

      <label class="inline-flex items-center gap-2 text-xs text-slate-300">
        <input type="checkbox" name="is_active" value="1" checked>
        <span>Kategori aktif</span>
      </label>

      <button type="submit"
              class="mt-2 inline-flex items-center h-10 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-medium">
        Simpan
      </button>
    </form>
  </div>
@endsection
