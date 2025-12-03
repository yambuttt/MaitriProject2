@extends('layouts.admin')
@section('title', 'Tambah Marketplace Product — Admin')

@section('content')
    <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold">Tambah Produk Marketplace</h1>
        </div>
        <a href="{{ route('admin.marketplace.products.index') }}" class="text-sm text-slate-400 hover:text-slate-200">←
            Kembali</a>
    </div>

    <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-5 max-w-xl">
        <form method="POST" action="{{ route('admin.marketplace.products.store') }}" enctype="multipart/form-data"
            class="space-y-4">


            @csrf

            <div class="space-y-1">
                <label class="text-xs text-slate-400">Kategori</label>
                <select name="marketplace_category_id"
                    class="w-full h-10 rounded-xl bg-slate-950 border border-slate-800/80 px-3 text-sm text-slate-100">
                    <option value="">Pilih kategori…</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('marketplace_category_id') == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('marketplace_category_id')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="text-xs text-slate-400">Nama produk</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full h-10 rounded-xl bg-slate-950 border border-slate-800/80 px-3 text-sm text-slate-100">
                @error('name') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1">
                <label class="text-xs text-slate-400">Gambar (opsional)</label>
                <input type="file" name="thumbnail" accept="image/*"
                    class="w-full rounded-xl bg-slate-950 border border-slate-800/80 px-3 py-2 text-sm text-slate-100
                                  file:bg-slate-800 file:border-0 file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:text-xs file:text-slate-100">
                @error('thumbnail')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>


            <div class="space-y-1">
                <label class="text-xs text-slate-400">Deskripsi</label>
                <textarea name="description" rows="4"
                    class="w-full rounded-xl bg-slate-950 border border-slate-800/80 px-3 py-2 text-sm text-slate-100">{{ old('description') }}</textarea>
            </div>
            <div class="space-y-1">
                <label class="text-xs text-slate-400">Gambar produk (boleh lebih dari satu)</label>
                <input type="file" name="images[]" multiple accept="image/*"
                    class="w-full rounded-xl bg-slate-950 border border-slate-800/80 px-3 py-2 text-sm text-slate-100
                          file:bg-slate-800 file:border-0 file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:text-xs file:text-slate-100">
                @error('images.*')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>


            <label class="inline-flex items-center gap-2 text-xs text-slate-300">
                <input type="checkbox" name="is_active" value="1">
                <span>Produk aktif (muncul di halaman marketplace user)</span>
            </label>

            <button type="submit"
                class="mt-2 inline-flex items-center h-10 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-medium">
                Simpan
            </button>
        </form>
    </div>
@endsection