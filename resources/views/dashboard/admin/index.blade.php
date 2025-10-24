@extends('layouts.admin')
@section('title','Dashboard — Admin')

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
      <a href="#" class="text-sm text-violet-300 hover:text-violet-200">Lihat semua</a>
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
          @foreach(range(1,6) as $i)
          <tr class="border-t border-slate-800/70">
            <td class="px-4 py-3">INV-2025-00{{ $i }}</td>
            <td class="px-4 py-3">Telkomsel Data 10GB</td>
            <td class="px-4 py-3">user{{ $i }}@mail.com</td>
            <td class="px-4 py-3">Rp {{ number_format(52000,0,',','.') }}</td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs bg-emerald-500/10 text-emerald-300 border border-emerald-700/40">Sukses</span>
            </td>
            <td class="px-4 py-3">Baru saja</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </section>
@endsection
