@extends('layouts.app')
@section('title','Dashboard — Member')
@section('page','auth')
@section('content')
<section class="py-10">
  <div class="mx-auto max-w-[1100px] px-4">
    <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-6">
      <h1 class="text-2xl font-semibold">Dashboard Member</h1>
      <p class="mt-2 text-slate-400">Halo, {{ auth()->user()->name }}. Ini dashboard user biasa.</p>
      <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-slate-800/70 p-4">Riwayat transaksi (placeholder)</div>
        <div class="rounded-2xl border border-slate-800/70 p-4">Saldo/point (placeholder)</div>
        <div class="rounded-2xl border border-slate-800/70 p-4">Profil (placeholder)</div>
      </div>
    </div>
  </div>
</section>
@endsection
