@extends('layouts.app')

@section('title','Invoice Marketplace')

@section('content')
  <div class="max-w-3xl mx-auto py-8 px-4 md:px-0">
    <a href="{{ route('marketplace.index') }}" class="text-sm text-slate-400 hover:text-slate-200">
      ← Kembali ke marketplace
    </a>

    <div class="mt-4 rounded-3xl border border-slate-800/70 bg-[#111826] p-6 space-y-4">
      <div class="flex items-start justify-between gap-4">
        <div>
          <p class="text-xs text-slate-400">Kode Invoice</p>
          <h1 class="text-xl font-semibold text-slate-50">{{ $order->invoice_number }}</h1>
        </div>
        <div class="text-right">
          @php
            $label = match($order->status) {
              'not_paid'        => 'NOT PAID',
              'paid_received'   => 'PAID & PESANAN DITERIMA',
              'paid_processing' => 'PAID & PESANAN DIPROSES',
              'paid_rejected'   => 'PAID & PESANAN DITOLAK',
              'paid_finished'   => 'PAID & PESANAN SELESAI',
              default           => strtoupper($order->status),
            };
          @endphp
          <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                @if($order->status === 'paid_finished') bg-emerald-500/20 text-emerald-300
                @elseif($order->status === 'paid_rejected') bg-rose-500/20 text-rose-300
                @elseif($order->status === 'not_paid') bg-amber-500/20 text-amber-300
                @else bg-sky-500/20 text-sky-300 @endif">
            {{ $label }}
          </span>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2 text-sm">
        <div class="space-y-2">
          <p class="text-xs text-slate-400">Produk</p>
          <p class="font-medium text-slate-100">{{ $order->product->name }}</p>
          <p class="text-xs text-slate-400">
            Varian: {{ $order->variant->name }}
            @if($order->variant->duration_days)
              ({{ $order->variant->duration_days }} hari)
            @endif
          </p>
          <p class="text-xs text-slate-400 mt-2">
            Email: {{ $order->customer_email }}<br>
            No HP: {{ $order->customer_phone ?: '-' }}
          </p>
        </div>

        <div class="space-y-2">
          <p class="text-xs text-slate-400">Ringkasan Pembayaran</p>
          <div class="flex justify-between">
            <span class="text-slate-400">Total Bayar</span>
            <span class="text-slate-50 font-semibold">
              Rp {{ number_format($order->total_amount, 0, ',', '.') }}
            </span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-400">Metode</span>
            <span class="text-slate-200 text-xs">
              @php
                $pm = $order->payment_method;
                $labelPay = match($pm) {
                  'wallet'                => 'Saldo Maitri',
                  'paydisini_qris'        => 'QRIS Paydisini',
                  'paydisini_va_mandiri'  => 'VA Mandiri Paydisini',
                  'paydisini_alfamart'    => 'Alfamart (Paydisini)',
                  'paydisini_indomaret'   => 'Indomaret (Paydisini)',
                  default                 => $pm,
                };
              @endphp
              {{ $labelPay ?? '-' }}
            </span>
          </div>
        </div>
      </div>

      @if($order->user_note)
        <div class="mt-4">
          <p class="text-xs text-slate-400 mb-1">Catatan dari kamu</p>
          <div class="rounded-xl border border-slate-800/80 bg-slate-900/60 px-3 py-2 text-sm text-slate-200">
            {!! nl2br(e($order->user_note)) !!}
          </div>
        </div>
      @endif

      <div class="mt-4">
        <p class="text-xs text-slate-400 mb-1">Catatan dari admin</p>
        <div class="rounded-xl border border-slate-800/80 bg-slate-900/60 px-3 py-2 text-sm text-slate-200">
          @if($order->admin_note)
            {!! nl2br(e($order->admin_note)) !!}
          @else
            <span class="text-slate-500">Belum ada catatan. Admin akan mengisi di sini setelah pesanan selesai
              (misalnya detail akun yang kamu beli).</span>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
