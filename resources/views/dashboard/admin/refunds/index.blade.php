@extends('layouts.admin')
@section('title', 'Refund — Admin')

@section('content')
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold">Refund</h1>
      <p class="mt-1 text-slate-400 text-sm">Daftar refund yang sudah diproses dan oleh admin siapa.</p>
    </div>

    <a href="{{ route('admin.refunds.create') }}"
       class="inline-flex items-center justify-center rounded-2xl border border-violet-700/50 bg-violet-600/10 px-4 py-2 text-sm font-medium text-violet-200 hover:bg-violet-600/15">
      + Buat Refund
    </a>
  </div>

  <section class="mt-6 rounded-2xl border border-slate-800/70 bg-[#0E1524] overflow-hidden">
    <div class="p-4 border-b border-slate-800/70 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <h2 class="font-medium">Riwayat Refund</h2>

      <form method="GET" class="flex items-center gap-2">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Cari kode order / admin / user..."
               class="bg-slate-900/60 border border-slate-700/70 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:border-violet-400 w-64 max-w-full">
        <button class="text-sm text-violet-300 hover:text-violet-200">Cari</button>
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-[#0c1222] text-slate-400">
          <tr>
            <th class="text-left px-4 py-3">Waktu</th>
            <th class="text-left px-4 py-3">Kode</th>
            <th class="text-left px-4 py-3">Produk</th>
            <th class="text-left px-4 py-3">Metode</th>
            <th class="text-left px-4 py-3">Jumlah</th>
            <th class="text-left px-4 py-3">Target</th>
            <th class="text-left px-4 py-3">Admin</th>
            <th class="text-right px-4 py-3">Bukti</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/70">
          @forelse($refunds as $r)
            <tr class="hover:bg-white/5">
              <td class="px-4 py-3 text-xs text-slate-400 whitespace-nowrap">
                {{ $r->created_at->format('d M Y H:i') }}
              </td>
              <td class="px-4 py-3 font-mono whitespace-nowrap">{{ $r->order?->code }}</td>
              <td class="px-4 py-3">
                <div class="font-medium text-slate-100">{{ $r->order?->product?->name ?? '-' }}</div>
                <div class="text-xs text-slate-400">{{ $r->order?->variant?->name ?? '-' }}</div>
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex px-2 py-1 rounded-lg text-[11px] border border-slate-700/70">
                  {{ $r->refund_method === 'wallet' ? 'Saldo Maitri' : 'Transfer Manual' }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                Rp {{ number_format($r->amount, 0, ',', '.') }}
              </td>
              <td class="px-4 py-3">
                @if($r->refund_method === 'wallet')
                  <div class="text-slate-100">{{ $r->targetUser?->name ?? '-' }}</div>
                  <div class="text-xs text-slate-400">{{ $r->targetUser?->email ?? '-' }}</div>
                @else
                  <span class="text-xs text-slate-400">—</span>
                @endif
              </td>
              <td class="px-4 py-3">
                <div class="text-slate-100">{{ $r->admin?->name ?? '-' }}</div>
                <div class="text-xs text-slate-400">{{ $r->admin?->email ?? '-' }}</div>
              </td>
              <td class="px-4 py-3 text-right">
                @if($r->refund_method === 'manual_transfer' && $r->manual_proof_path)
                  <a class="text-violet-300 hover:text-violet-200 text-xs underline"
                     href="{{ asset('storage/' . $r->manual_proof_path) }}" target="_blank" rel="noopener">
                    Lihat
                  </a>
                @else
                  <span class="text-xs text-slate-500">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-400">
                Belum ada refund.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4 border-t border-slate-800/70">
      {{ $refunds->links() }}
    </div>
  </section>
@endsection
