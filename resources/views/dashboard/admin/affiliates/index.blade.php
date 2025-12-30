@extends('layouts.admin')
@section('title', 'Affiliates — Admin')

@section('content')
<div class="space-y-5">
  <div class="flex items-center justify-between gap-3 flex-wrap">
    <div>
      <h1 class="text-2xl font-semibold">Affiliates</h1>
      <p class="text-slate-400 text-sm">Daftar user yang sudah affiliate.</p>
    </div>

    <form method="get" class="flex gap-2">
      <input name="q" value="{{ $q }}" placeholder="Cari nama/email/kode..."
             class="h-10 w-64 rounded-xl bg-slate-900/60 border border-slate-700/70 px-3 text-sm outline-none">
      <button class="h-10 px-4 rounded-xl border border-slate-700/70 hover:border-slate-600 text-sm">
        Cari
      </button>
    </form>
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
            <th class="text-left px-4 py-3">User</th>
            <th class="text-left px-4 py-3">Email</th>
            <th class="text-left px-4 py-3">Kode</th>
            <th class="text-right px-4 py-3">Point</th>
            <th class="text-right px-4 py-3">Detail</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/70">
          @foreach($affiliates as $u)
            <tr class="hover:bg-white/5">
              <td class="px-4 py-3 font-medium text-slate-100">{{ $u->name }}</td>
              <td class="px-4 py-3 text-slate-300">{{ $u->email }}</td>
              <td class="px-4 py-3 text-slate-300 font-mono text-xs">{{ $u->affiliate_code ?? '-' }}</td>
              <td class="px-4 py-3 text-right text-emerald-300 font-semibold">{{ number_format((int)$u->maitri_points) }}</td>
              <td class="px-4 py-3 text-right">
                <a href="{{ route('admin.affiliates.show', $u) }}"
                   class="px-3 py-2 rounded-xl border border-slate-700/70 hover:border-violet-500/60 text-xs">
                  Lihat
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="p-4 border-t border-slate-800/70">
      {{ $affiliates->links() }}
    </div>
  </div>
</div>
@endsection
