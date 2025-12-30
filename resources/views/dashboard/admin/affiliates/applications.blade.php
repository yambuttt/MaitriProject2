@extends('layouts.admin')
@section('title', 'Affiliate Applicants — Admin')

@section('content')
<div class="space-y-5">
  <div class="flex items-center justify-between gap-3 flex-wrap">
    <div>
      <h1 class="text-2xl font-semibold">Affiliate Applicants</h1>
      <p class="text-slate-400 text-sm">Kelola pengajuan affiliate (pending / approved / rejected).</p>
    </div>

    <div class="flex gap-2">
      <a href="{{ route('admin.affiliates.applications', ['status' => 'pending']) }}"
         class="px-3 py-2 rounded-xl border {{ $status==='pending' ? 'border-violet-700/60 bg-[#121a2b]' : 'border-slate-800/70 hover:border-slate-700' }}">
        Pending
      </a>
      <a href="{{ route('admin.affiliates.applications', ['status' => 'approved']) }}"
         class="px-3 py-2 rounded-xl border {{ $status==='approved' ? 'border-violet-700/60 bg-[#121a2b]' : 'border-slate-800/70 hover:border-slate-700' }}">
        Approved
      </a>
      <a href="{{ route('admin.affiliates.applications', ['status' => 'rejected']) }}"
         class="px-3 py-2 rounded-xl border {{ $status==='rejected' ? 'border-violet-700/60 bg-[#121a2b]' : 'border-slate-800/70 hover:border-slate-700' }}">
        Rejected
      </a>
    </div>
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
            <th class="text-left px-4 py-3">Status</th>
            <th class="text-left px-4 py-3">Tanggal</th>
            <th class="text-right px-4 py-3">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/70">
          @forelse($apps as $app)
            <tr class="hover:bg-white/5">
              <td class="px-4 py-3 font-medium text-slate-100">{{ $app->user->name }}</td>
              <td class="px-4 py-3 text-slate-300">{{ $app->user->email }}</td>
              <td class="px-4 py-3">
                <span class="text-xs px-2 py-1 rounded-lg border border-slate-700/70">
                  {{ strtoupper($app->status) }}
                </span>
              </td>
              <td class="px-4 py-3 text-slate-400 text-xs">
                {{ $app->created_at?->format('d M Y H:i') }}
              </td>
              <td class="px-4 py-3 text-right">
                @if($app->status === 'pending')
                  <div class="flex justify-end gap-2">
                    <form method="post" action="{{ route('admin.affiliates.applications.approve', $app) }}">
                      @csrf
                      <button class="px-3 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 text-white text-xs">
                        Approve
                      </button>
                    </form>

                    <form method="post" action="{{ route('admin.affiliates.applications.reject', $app) }}" class="flex gap-2 items-center">
                      @csrf
                      <input name="note" placeholder="Catatan (opsional)"
                             class="h-9 w-52 rounded-xl bg-slate-900/60 border border-slate-700/70 px-3 text-xs outline-none">
                      <button class="px-3 py-2 rounded-xl border border-rose-500/40 text-rose-200 hover:bg-rose-500/10 text-xs">
                        Reject
                      </button>
                    </form>
                  </div>
                @else
                  <span class="text-xs text-slate-500">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-8 text-center text-slate-500">Tidak ada data.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4 border-t border-slate-800/70">
      {{ $apps->links() }}
    </div>
  </div>
</div>
@endsection
