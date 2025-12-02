@extends('layouts.admin')
@section('title','Marketplace Order '.$order->invoice_number.' — Admin')

@section('content')
  <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold">Detail Marketplace Order</h1>
      <p class="text-slate-400 mt-1">Invoice: {{ $order->invoice_number }}</p>
    </div>
    <a href="{{ route('admin.marketplace.orders.index') }}"
       class="text-sm text-slate-400 hover:text-slate-200">
      ← Kembali ke list
    </a>
  </div>

  @if(session('ok'))
    <div class="mb-4 rounded-xl border border-emerald-900/40 bg-emerald-950/40 text-emerald-200 text-sm px-3 py-2">
      {{ session('ok') }}
    </div>
  @endif

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

  <div class="grid gap-4 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
    {{-- Info utama --}}
    <div class="space-y-4">
      <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4 space-y-3">
        <div class="flex items-center justify-between gap-3">
          <div>
            <p class="text-xs text-slate-400">Status</p>
            <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-medium
                @if($order->status === 'paid_finished') bg-emerald-500/20 text-emerald-300
                @elseif($order->status === 'paid_rejected') bg-rose-500/20 text-rose-300
                @elseif($order->status === 'not_paid') bg-amber-500/20 text-amber-300
                @else bg-sky-500/20 text-sky-300 @endif">
              {{ $label }}
            </span>
          </div>
          <div class="text-right text-xs text-slate-400">
            <div>Dibuat: {{ $order->created_at?->format('d M Y H:i') }}</div>
            @if($order->paid_at)
              <div>Dibayar: {{ $order->paid_at->format('d M Y H:i') }}</div>
            @endif
          </div>
        </div>

        <div class="grid gap-3 md:grid-cols-2 text-sm">
          <div>
            <p class="text-xs text-slate-400">Produk</p>
            <p class="text-slate-100">{{ $order->product->name ?? '-' }}</p>
            <p class="text-xs text-slate-400 mt-1">
              Varian: {{ $order->variant->name ?? '-' }}
              @if($order->variant?->duration_days)
                ({{ $order->variant->duration_days }} hari)
              @endif
            </p>
          </div>
          <div>
            <p class="text-xs text-slate-400">Pelanggan</p>
            <p class="text-sm text-slate-100">{{ $order->customer_email }}</p>
            <p class="text-xs text-slate-400">{{ $order->customer_phone ?: '-' }}</p>
            @if($order->user)
              <p class="text-xs text-slate-500 mt-1">
                User ID: #{{ $order->user->id }} — {{ $order->user->name }}
              </p>
            @endif
          </div>
        </div>

        <div class="border-t border-slate-800/70 pt-3 text-sm space-y-1">
          <div class="flex justify-between">
            <span class="text-slate-400">Harga</span>
            <span class="text-slate-100">
              Rp {{ number_format($order->price, 0, ',', '.') }}
            </span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-400">Biaya Admin</span>
            <span class="text-emerald-400">Gratis</span>
          </div>
          <div class="flex justify-between border-t border-slate-800/70 pt-2 mt-1">
            <span class="text-slate-300 font-medium">Total Bayar</span>
            <span class="text-slate-50 font-semibold">
              Rp {{ number_format($order->total_amount, 0, ',', '.') }}
            </span>
          </div>
        </div>
      </div>

      @if($order->user_note)
        <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
          <p class="text-xs text-slate-400 mb-1">Catatan dari pengguna</p>
          <div class="text-sm text-slate-100 whitespace-pre-line">
            {{ $order->user_note }}
          </div>
        </div>
      @endif
    </div>

    {{-- Form ubah status & catatan admin --}}
    <div class="space-y-4">
      <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
        <h2 class="text-sm font-semibold text-slate-100 mb-3">Proses Pesanan</h2>

        <form method="POST" action="{{ route('admin.marketplace.orders.update-status', $order) }}" class="space-y-3">
          @csrf

          <div class="space-y-1">
            <label class="text-xs text-slate-400">Status</label>
            <select name="status"
                    class="w-full h-10 rounded-xl bg-slate-950 border border-slate-800/80 px-3 text-sm text-slate-100">
              <option value="paid_received" @selected($order->status === 'paid_received')>
                PAID & PESANAN DITERIMA
              </option>
              <option value="paid_processing" @selected($order->status === 'paid_processing')>
                PAID & PESANAN DIPROSES
              </option>
              <option value="paid_rejected" @selected($order->status === 'paid_rejected')>
                PAID & PESANAN DITOLAK
              </option>
              <option value="paid_finished" @selected($order->status === 'paid_finished')>
                PAID & PESANAN SELESAI
              </option>
            </select>
            @error('status')
              <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div class="space-y-1">
            <label class="text-xs text-slate-400">
              Catatan Admin
              <span class="font-normal text-[11px] text-slate-500">(misal: detail akun Canva yang diberikan)</span>
            </label>
            <textarea name="admin_note" rows="5"
                      class="w-full rounded-xl bg-slate-950 border border-slate-800/80 px-3 py-2 text-sm text-slate-100">{{ old('admin_note', $order->admin_note) }}</textarea>
            @error('admin_note')
              <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
            @enderror
          </div>

          <button type="submit"
                  class="w-full h-10 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-medium">
            Simpan perubahan
          </button>

          @if($order->processed_by_admin_id)
            <p class="text-[11px] text-slate-500 mt-2">
              Terakhir diproses oleh admin ID #{{ $order->processed_by_admin_id }} pada
              {{ $order->updated_at?->format('d M Y H:i') }}.
            </p>
          @endif
        </form>
      </div>
    </div>
  </div>
@endsection
