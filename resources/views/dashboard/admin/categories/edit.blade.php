@extends('layouts.admin')
@section('title','Edit Kategori — Admin')

@section('content')
  <a href="{{ route('admin.categories.index') }}" class="text-sm text-slate-400 hover:text-slate-200">← Kembali</a>

  <div class="mt-4 rounded-2xl border border-slate-800/70 bg-[#0E1524] p-6 max-w-2xl">
    <h1 class="text-2xl font-semibold">Edit Kategori</h1>
    <p class="text-slate-400 mt-1">Perbarui data kategori.</p>

    <form method="post" action="{{ route('admin.categories.update', $category) }}" class="mt-6 space-y-4">
      @csrf @method('PUT')

      <div>
        <label class="text-sm text-slate-300">Nama</label>
        <input name="name" value="{{ old('name', $category->name) }}" required
               class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
      </div>

      <div>
        <label class="text-sm text-slate-300">Slug</label>
        <input name="slug" value="{{ old('slug', $category->slug) }}"
               class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
        <p class="text-xs text-slate-500 mt-1">Biarkan atau ubah manual. Akan dinormalisasi otomatis.</p>
      </div>

      <label class="inline-flex items-center gap-2 text-sm text-slate-300">
        <input type="checkbox" name="is_active" value="1" class="accent-violet-600" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
        Aktif
      </label>

      <div class="pt-2">
        <button class="h-11 px-5 rounded-xl bg-violet-600 hover:bg-violet-500">Simpan Perubahan</button>
      </div>

      @if ($errors->any())
        <div class="rounded-lg border border-red-900/40 bg-red-950/30 text-red-200 text-sm px-3 py-2">
          {{ $errors->first() }}
        </div>
      @endif
    </form>
  </div>
@endsection
