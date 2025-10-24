@extends('layouts.admin')
@section('title','Users — Admin')

@section('content')
  <div class="flex items-center justify-between gap-3 flex-wrap">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold">Pengguna</h1>
      <p class="text-slate-400 mt-1">Daftar semua akun (member & admin).</p>
    </div>
    <div class="text-sm text-slate-400">Total: <span class="font-medium text-slate-200">{{ $users->total() }}</span> akun</div>
  </div>

  {{-- Filter bar --}}
  <form method="get" class="mt-4 grid sm:grid-cols-2 lg:grid-cols-[1fr_180px_140px] gap-3">
    <div class="relative">
      <input name="q" value="{{ $q }}" type="search" placeholder="Cari nama, email, atau ID…"
             class="w-full h-11 rounded-xl bg-[#0E1524] border border-slate-800/70 ps-10 pe-3 outline-none placeholder:text-slate-500 focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
      <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-5 text-slate-500" viewBox="0 0 24 24" fill="none">
        <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="1.5"/>
      </svg>
    </div>

    <select name="role" class="h-11 rounded-xl bg-[#0E1524] border border-slate-800/70 px-3 outline-none focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
      <option value="">Semua role</option>
      <option value="member" @selected($role==='member')>Member</option>
      <option value="admin"  @selected($role==='admin')>Admin</option>
    </select>

    <select name="per_page" class="h-11 rounded-xl bg-[#0E1524] border border-slate-800/70 px-3 outline-none focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
      @foreach([12,24,48,96] as $n)
        <option value="{{ $n }}" @selected($perPage==$n)>{{ $n }}/hal</option>
      @endforeach
    </select>

    <button class="h-11 rounded-xl bg-violet-600 hover:bg-violet-500">Terapkan</button>
  </form>

  {{-- Tabel --}}
  <section class="mt-4 rounded-2xl border border-slate-800/70 bg-[#0E1524] overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-[#0c1222] text-slate-400">
          <tr>
            <th class="text-left px-4 py-3">ID</th>
            <th class="text-left px-4 py-3">Nama</th>
            <th class="text-left px-4 py-3">Email</th>
            <th class="text-left px-4 py-3">Role</th>
            <th class="text-left px-4 py-3">Bergabung</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($users as $u)
            <tr class="border-t border-slate-800/70">
              <td class="px-4 py-3 text-slate-400">{{ $u->id }}</td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="size-8 rounded-full bg-violet-600/30 grid place-items-center text-violet-200">
                    <span class="text-xs font-semibold">{{ strtoupper(substr($u->name,0,1)) }}</span>
                  </div>
                  <div class="min-w-0">
                    <div class="font-medium truncate">{{ $u->name }}</div>
                    <div class="text-xs text-slate-400 truncate">#{{ str_pad((string)$u->id, 6, '0', STR_PAD_LEFT) }}</div>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3">{{ $u->email }}</td>
              <td class="px-4 py-3">
                @if($u->role === 'admin')
                  <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs bg-violet-500/10 text-violet-300 border border-violet-700/40">Admin</span>
                @else
                  <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs bg-slate-500/10 text-slate-300 border border-slate-700/40">Member</span>
                @endif
              </td>
              <td class="px-4 py-3 text-slate-400">{{ $u->created_at?->diffForHumans() }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-8 text-center text-slate-400">Tidak ada data.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4 border-t border-slate-800/70">
      {{ $users->links() }}
    </div>
  </section>
@endsection
