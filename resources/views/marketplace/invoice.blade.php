@extends('layouts.app')

@section('title','Invoice Marketplace')

@section('content')
  <section class="py-8">
    <div class="mx-auto max-w-4xl px-4 md:px-0">

      <div class="flex items-center justify-between gap-3">
        <a href="{{ route('marketplace.index') }}"
           class="text-sm text-slate-400 hover:text-slate-200">
          ← Kembali ke marketplace
        </a>

        <span class="text-xs text-slate-500">
          Invoice created at {{ $order->created_at?->format('d M Y H:i') }}
        </span>
      </div>

      <div class="mt-5 rounded-3xl border border-slate-800/70 bg-slate-950/90 p-5 md:p-6 space-y-5 shadow-lg shadow-black/40">
        {{-- Header --}}
        <div class="flex items-start justify-between gap-3">
          <div>
            <h1 class="text-xl md:text-2xl font-semibold text-slate-50">
              Invoice Detail
            </h1>
            <p class="text-xs text-slate-400 mt-1">
              Ringkasan pesanan marketplace dan status pembayaranmu.
            </p>
          </div>

          @php
            $statusLabel = match($order->status) {
              'not_paid'        => 'NOT PAID',
              'paid_received'   => 'PAID & PESANAN DITERIMA',
              'paid_processing' => 'PAID & PESANAN DIPROSES',
              'paid_rejected'   => 'PAID & PESANAN DITOLAK',
              'paid_finished'   => 'PAID & PESANAN SELESAI',
              default           => strtoupper($order->status),
            };
          @endphp

          <div class="text-right space-y-1">
            <p class="text-[11px] text-slate-400">Status pesanan</p>
            <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-medium
                @if($order->status === 'paid_finished') bg-emerald-500/20 text-emerald-300
                @elseif($order->status === 'paid_rejected') bg-rose-500/20 text-rose-300
                @elseif($order->status === 'not_paid') bg-amber-500/20 text-amber-300
                @else bg-sky-500/20 text-sky-300 @endif">
              {{ $statusLabel }}
            </span>
          </div>
        </div>

        {{-- Card utama --}}
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/80 divide-y divide-slate-800/70">

          {{-- Bagian 1: Info invoice & billed to --}}
          <div class="p-4 md:p-5 space-y-4">
            <div class="flex items-center justify-between gap-4 flex-wrap">
              <div class="space-y-2">
                <p class="text-[11px] text-slate-400 uppercase tracking-wide">Billed To</p>

                <div class="flex items-center gap-3">
                  <div class="h-9 w-9 rounded-full bg-violet-600 text-xs flex items-center justify-center text-slate-50 font-semibold">
                    @php
                      $name = $order->user?->name ?: ($order->customer_email ?: 'User');
                      $initials = trim(collect(explode(' ', $name))->map(fn($p) => mb_substr($p,0,1))->join(''));
                    @endphp
                    {{ $initials }}
                  </div>
                  <div class="text-sm">
                    <p class="font-medium text-slate-100">
                      {{ $order->user?->name ?? ($order->customer_email ?: 'Pengguna Marketplace') }}
                    </p>
                    <p class="text-xs text-slate-400">
                      {{ $order->customer_email ?: '-' }}
                    </p>
                    <p class="text-xs text-slate-500">
                      {{ $order->customer_phone ?: 'No HP belum diisi' }}
                    </p>
                  </div>
                </div>
              </div>

              <div class="space-y-2 text-sm min-w-[220px]">
                <div class="grid grid-cols-2 gap-2">
                  <div class="space-y-1">
                    <p class="text-[11px] text-slate-400">Invoice Number</p>
                    <div class="h-9 px-3 flex items-center rounded-xl bg-slate-950/80 border border-slate-800 text-xs font-mono text-slate-100">
                      {{ $order->invoice_number }}
                    </div>
                  </div>
                  <div class="space-y-1">
                    <p class="text-[11px] text-slate-400">Currency</p>
                    <div class="h-9 px-3 flex items-center justify-between rounded-xl bg-slate-950/80 border border-slate-800 text-xs text-slate-100">
                      <span>IDR — Rupiah</span>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                  <div class="space-y-1">
                    <p class="text-[11px] text-slate-400">Issued Date</p>
                    <div class="h-9 px-3 flex items-center rounded-xl bg-slate-950/80 border border-slate-800 text-xs text-slate-100">
                      {{ $order->created_at?->format('d M Y') }}
                    </div>
                  </div>
                  <div class="space-y-1">
                    <p class="text-[11px] text-slate-400">Paid / Due</p>
                    <div class="h-9 px-3 flex items-center rounded-xl bg-slate-950/80 border border-slate-800 text-xs text-slate-100">
                      @if($order->paid_at)
                        Dibayar {{ $order->paid_at->format('d M Y') }}
                      @else
                        Belum dibayar
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- Bagian 2: Detail project / item --}}
          <div class="p-4 md:p-5 space-y-3">
            <div class="space-y-1">
              <p class="text-[11px] text-slate-400 uppercase tracking-wide">Project / Order Detail</p>
              <p class="text-sm text-slate-200">
                {{ $order->product?->name ?? 'Produk marketplace' }}
              </p>
              <p class="text-xs text-slate-400">
                Varian: {{ $order->variant?->name ?? '-' }}
                @if($order->variant?->duration_days)
                  ({{ $order->variant->duration_days }} hari)
                @endif
              </p>
            </div>

            <div class="mt-3 rounded-2xl border border-slate-800/80 bg-slate-950/70 overflow-hidden">
              <table class="w-full text-xs md:text-sm">
                <thead class="bg-slate-900/80 text-slate-400">
                  <tr>
                    <th class="text-left px-4 py-2.5">Item</th>
                    <th class="text-center px-3 py-2.5 w-24">QTY</th>
                    <th class="text-right px-3 py-2.5 w-32">Cost</th>
                    <th class="text-right px-4 py-2.5 w-32">Amount</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/70 text-slate-100">
                <tr>
                  <td class="px-4 py-2.5">
                    <div class="font-medium">{{ $order->product?->name ?? '-' }}</div>
                    <div class="text-[11px] text-slate-400">
                      {{ $order->variant?->name ?? '-' }}
                    </div>
                  </td>
                  <td class="px-3 py-2.5 text-center">
                    1
                  </td>
                  <td class="px-3 py-2.5 text-right">
                    Rp {{ number_format($order->price, 0, ',', '.') }}
                  </td>
                  <td class="px-4 py-2.5 text-right font-semibold">
                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                  </td>
                </tr>
                </tbody>
              </table>
            </div>

            <div class="flex justify-end mt-2 text-sm">
              <div class="space-y-1 w-full max-w-xs">
                <div class="flex justify-between text-slate-400">
                  <span>Subtotal</span>
                  <span>Rp {{ number_format($order->price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-400">
                  <span>Fee</span>
                  <span>
                    @if($order->fee && $order->fee > 0)
                      Rp {{ number_format($order->fee, 0, ',', '.') }}
                    @else
                      Gratis
                    @endif
                  </span>
                </div>
                <div class="flex justify-between pt-2 border-t border-slate-800/70 text-slate-50 font-semibold">
                  <span>Total</span>
                  <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
              </div>
            </div>
          </div>

          {{-- Bagian 3: Notes --}}
          <div class="p-4 md:p-5 space-y-4">
            @if($order->user_note)
              <div>
                <p class="text-[11px] text-slate-400 mb-1 uppercase tracking-wide">
                  Notes from you
                </p>
                <div class="rounded-xl border border-slate-800/80 bg-slate-950/80 px-3 py-2 text-sm text-slate-200">
                  {!! nl2br(e($order->user_note)) !!}
                </div>
              </div>
            @endif

            <div>
              <p class="text-[11px] text-slate-400 mb-1 uppercase tracking-wide">
                Notes from admin
              </p>
              <div class="rounded-xl border border-slate-800/80 bg-slate-950/80 px-3 py-2 text-sm text-slate-200">
                @if($order->admin_note)
                  {!! nl2br(e($order->admin_note)) !!}
                @else
                  <span class="text-slate-500">
                    Belum ada catatan. Admin akan mengisi di sini setelah pesanan selesai
                    (misalnya detail akun atau informasi penting lainnya).
                  </span>
                @endif
              </div>
            </div>
          </div>
        </div>

        {{-- Aksi --}}
        <div class="flex items-center justify-between flex-wrap gap-3 pt-1">
          <div class="text-[11px] text-slate-500">
            Simpan invoice ini untuk referensi jika nanti kamu perlu cek riwayat pesanan.
          </div>

          @if($order->payment && $order->payment->status === 'pending')
            <a href="{{ route('marketplace.payment.show', $order->payment) }}"
               class="inline-flex h-9 items-center justify-center rounded-xl border border-violet-500/70 px-4 text-xs font-medium text-violet-200 hover:bg-violet-600/10">
              Lanjutkan pembayaran
            </a>
          @endif
        </div>
      </div>
    </div>
  </section>
@endsection
