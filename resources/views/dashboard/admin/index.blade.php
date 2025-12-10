@extends('layouts.admin')
@section('title', 'Dashboard — Admin')

@section('content')
  <h1 class="text-2xl md:text-3xl font-semibold">Dashboard Admin</h1>
  <p class="mt-1 text-slate-400">Ringkasan singkat performa toko digital kamu.</p>

  {{-- KPI Cards --}}
  <section class="mt-6 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
      <div class="text-sm text-slate-400">Pendapatan Hari Ini</div>
      <div class="mt-2 text-2xl font-semibold">Rp 1.250.000</div>
      <div class="mt-1 text-xs text-emerald-400">+8.2% dari kemarin</div>
    </div>
    <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
      <div class="text-sm text-slate-400">Order Baru</div>
      <div class="mt-2 text-2xl font-semibold">47</div>
      <div class="mt-1 text-xs text-emerald-400">+3.1%</div>
    </div>
    <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
      <div class="text-sm text-slate-400">Sukses</div>
      <div class="mt-2 text-2xl font-semibold">45</div>
      <div class="mt-1 text-xs text-emerald-400">95.7% success rate</div>
    </div>
    <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
      <div class="text-sm text-slate-400">Pending</div>
      <div class="mt-2 text-2xl font-semibold">12</div>
      <div class="mt-1 text-xs text-amber-400">cek payment gateway</div>
    </div>
  </section>

  {{-- Table Placeholder --}}
  <section class="mt-6 rounded-2xl border border-slate-800/70 bg-[#0E1524] overflow-hidden">
    <div class="p-4 border-b border-slate-800/70 flex items-center justify-between">
      <h2 class="font-medium">Order Terbaru</h2>
      <a href="{{ route('admin.marketplace.orders.index') }}" class="text-sm text-violet-300 hover:text-violet-200">Lihat
        semua</a>

    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-[#0c1222] text-slate-400">
          <tr>
            <th class="text-left px-4 py-3">Order ID</th>
            <th class="text-left px-4 py-3">Produk</th>
            <th class="text-left px-4 py-3">User</th>
            <th class="text-left px-4 py-3">Total</th>
            <th class="text-left px-4 py-3">Status</th>
            <th class="text-left px-4 py-3">Waktu</th>
          </tr>
        </thead>
        <tbody>
          @forelse($latestMarketplaceOrders as $order)
            @php
              $label = match ($order->status) {
                'not_paid' => 'NOT PAID',
                'paid_received' => 'PAID & PESANAN DITERIMA',
                'paid_processing' => 'PAID & PESANAN DIPROSES',
                'paid_rejected' => 'PAID & PESANAN DITOLAK',
                'paid_finished' => 'PAID & PESANAN SELESAI',
                default => strtoupper($order->status),
              };
            @endphp

            <tr class="border-t border-slate-800/70">
              {{-- Order ID / Invoice --}}
              <td class="px-4 py-3 font-mono text-xs text-slate-300">
                {{ $order->invoice_number }}
              </td>

              {{-- Produk & varian --}}
              <td class="px-4 py-3">
                <div class="text-slate-100">
                  {{ $order->product->name ?? '-' }}
                </div>
                <div class="text-xs text-slate-400">
                  Varian: {{ $order->variant->name ?? '-' }}
                </div>
              </td>

              {{-- User / email --}}
              <td class="px-4 py-3 text-xs text-slate-300">
                @if($order->customer_email)
                  {{ $order->customer_email }}
                @elseif($order->user)
                  {{ $order->user->email }}
                @else
                  -
                @endif
              </td>

              {{-- Total --}}
              <td class="px-4 py-3">
                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
              </td>

              {{-- Status (sukses / gagal / not paid, dll) --}}
              <td class="px-4 py-3">
                <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-medium border
                        @if($order->status === 'paid_finished')
                          bg-emerald-500/15 text-emerald-300 border-emerald-600/40
                        @elseif($order->status === 'paid_rejected')
                          bg-rose-500/15 text-rose-300 border-rose-600/40
                        @elseif($order->status === 'not_paid')
                          bg-amber-500/15 text-amber-300 border-amber-600/40
                        @else
                          bg-sky-500/15 text-sky-300 border-sky-600/40
                        @endif
                    ">
                  {{ $label }}
                </span>
              </td>

              {{-- Waktu --}}
              <td class="px-4 py-3 text-xs text-slate-400">
                {{ $order->created_at?->diffForHumans() ?? '-' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-400">
                Belum ada transaksi marketplace.
              </td>
            </tr>
          @endforelse
        </tbody>

      </table>
    </div>
  </section>
@endsection