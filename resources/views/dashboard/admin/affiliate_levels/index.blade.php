@extends('layouts.admin')
@section('title', 'Affiliate Levels — Admin')

@section('content')
<div class="space-y-5">
  <div class="flex items-center justify-between gap-3 flex-wrap">
    <div>
      <h1 class="text-2xl font-semibold">Affiliate Levels</h1>
      <p class="text-slate-400 text-sm">Atur reward point (Digiflazz / Marketplace) & window days.</p>
    </div>

    <a href="{{ route('admin.affiliate-levels.create') }}"
       class="px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 text-white text-sm">
      + Buat Level
    </a>
  </div>

  @if(session('ok'))
    <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
      {{ session('ok') }}
    </div>
  @endif

  <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-[#0c1222] text-slate-400">
          <tr>
            <th class="text-left px-4 py-3">Nama</th>
            <th class="text-left px-4 py-3">Window Days</th>
            <th class="text-left px-4 py-3">Digiflazz</th>
            <th class="text-left px-4 py-3">Marketplace</th>
            <th class="text-left px-4 py-3">Active</th>
            <th class="text-right px-4 py-3">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/70">
          @foreach($levels as $lvl)
            <tr class="hover:bg-white/5">
              <td class="px-4 py-3 font-medium text-slate-100">{{ $lvl->name }}</td>
              <td class="px-4 py-3 text-slate-300">{{ $lvl->window_days }}</td>
              <td class="px-4 py-3 text-slate-300">{{ number_format($lvl->digiflazz_points) }}</td>
              <td class="px-4 py-3 text-slate-300">{{ number_format($lvl->marketplace_points) }}</td>
              <td class="px-4 py-3">
                <span class="text-xs px-2 py-1 rounded-lg border {{ $lvl->is_active ? 'border-emerald-500/40 text-emerald-200' : 'border-slate-700/70 text-slate-400' }}">
                  {{ $lvl->is_active ? 'AKTIF' : 'NONAKTIF' }}
                </span>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex justify-end gap-2">
                  <a href="{{ route('admin.affiliate-levels.edit', $lvl) }}"
                     class="px-3 py-2 rounded-xl border border-slate-700/70 hover:border-slate-600 text-xs">
                    Edit
                  </a>
                  <form method="post" action="{{ route('admin.affiliate-levels.toggle', $lvl) }}">
                    @csrf
                    <button class="px-3 py-2 rounded-xl border border-slate-700/70 hover:border-violet-500/60 text-xs">
                      Toggle
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="p-4 border-t border-slate-800/70">
      {{ $levels->links() }}
    </div>
  </div>
</div>
@endsection
