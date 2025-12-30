@extends('layouts.admin')
@section('title', $mode === 'create' ? 'Create Affiliate Level — Admin' : 'Edit Affiliate Level — Admin')

@section('content')
<div class="max-w-2xl space-y-5">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold">{{ $mode === 'create' ? 'Buat Level' : 'Edit Level' }}</h1>
      <p class="text-slate-400 text-sm">Atur window dan reward point.</p>
    </div>
    <a href="{{ route('admin.affiliate-levels.index') }}"
       class="px-3 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 text-sm">
      ← Kembali
    </a>
  </div>

  @if($errors->any())
    <div class="rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
      <ul class="list-disc ml-4">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="post"
        action="{{ $mode==='create' ? route('admin.affiliate-levels.store') : route('admin.affiliate-levels.update', $level) }}"
        class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-5 space-y-4">
    @csrf
    @if($mode==='edit')
      @method('PUT')
    @endif

    <div>
      <label class="text-sm text-slate-300">Nama Level</label>
      <input name="name" value="{{ old('name', $level->name) }}"
             class="mt-1 h-11 w-full rounded-xl bg-slate-900/60 border border-slate-700/70 px-3 text-sm outline-none">
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="text-sm text-slate-300">Window Days</label>
        <input name="window_days" type="number" min="1" max="365"
               value="{{ old('window_days', $level->window_days ?? 30) }}"
               class="mt-1 h-11 w-full rounded-xl bg-slate-900/60 border border-slate-700/70 px-3 text-sm outline-none">
      </div>

      <div class="flex items-center gap-2 mt-6">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', $level->is_active ?? true) ? 'checked' : '' }}>
        <span class="text-sm text-slate-300">Active</span>
      </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="text-sm text-slate-300">Digiflazz Points</label>
        <input name="digiflazz_points" type="number" min="0"
               value="{{ old('digiflazz_points', $level->digiflazz_points ?? 50) }}"
               class="mt-1 h-11 w-full rounded-xl bg-slate-900/60 border border-slate-700/70 px-3 text-sm outline-none">
      </div>

      <div>
        <label class="text-sm text-slate-300">Marketplace Points</label>
        <input name="marketplace_points" type="number" min="0"
               value="{{ old('marketplace_points', $level->marketplace_points ?? 2000) }}"
               class="mt-1 h-11 w-full rounded-xl bg-slate-900/60 border border-slate-700/70 px-3 text-sm outline-none">
      </div>
    </div>

    <div class="pt-2 flex justify-end">
      <button class="px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 text-white text-sm">
        Simpan
      </button>
    </div>
  </form>
</div>
@endsection
