@extends('layouts.app')

@section('title','Checkout Marketplace')
@section('page','marketplace-checkout')

@section('content')
  <section class="py-8">
    <div class="mx-auto max-w-[960px] px-4 md:px-6 lg:px-8">

      <a href="{{ route('marketplace.product.show', $order->product) }}"
         class="text-sm text-slate-400 hover:text-slate-200">
        ← Kembali ke produk
      </a>

      <div class="mt-4 grid gap-6 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
        {{-- Ringkasan --}}
        <div class="space-y-4">
          <h1 class="text-2xl font-semibold text-slate-50">Checkout</h1>

          <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-4 space-y-3">
            <div>
              <p class="text-xs text-slate-400">Produk marketplace</p>
              <p class="text-sm font-semibold text-slate-100">{{ $order->product->name }}</p>
              <p class="text-xs text-slate-400 mt-1">
                Varian: {{ $order->variant->name }}
                @if($order->variant->duration_days)
                  ({{ $order->variant->duration_days }} hari)
                @endif
              </p>
            </div>

            <div class="border-t border-slate-800/70 pt-3 space-y-1 text-sm">
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
              <div class="flex justify-between pt-2 border-t border-slate-800/70 mt-1">
                <span class="text-slate-300 font-medium">Total Bayar</span>
                <span class="text-slate-50 font-semibold">
                  Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                </span>
              </div>
            </div>
          </div>
        </div>

        {{-- Form --}}
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-4 space-y-4">
          <form method="POST" action="{{ route('marketplace.checkout.process', $order) }}" class="space-y-4">
            @csrf

            <div class="space-y-1">
              <label class="text-xs text-slate-400">Email</label>
              <input type="email" name="customer_email" value="{{ old('customer_email', $order->customer_email) }}"
                     class="w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 text-sm text-slate-100">
              @error('customer_email')
                <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div class="space-y-1">
              <label class="text-xs text-slate-400">Nomor WhatsApp / HP</label>
              <input type="text" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}"
                     class="w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 text-sm text-slate-100">
            </div>

            <div class="space-y-1">
              <label class="text-xs text-slate-400">Catatan (opsional)</label>
              <textarea name="user_note" rows="3"
                        class="w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 text-sm text-slate-100">{{ old('user_note', $order->user_note) }}</textarea>
            </div>

            {{-- Metode bayar --}}
            <div class="space-y-2">
              <p class="text-xs text-slate-400">Metode Pembayaran</p>

              @auth
                <label class="flex items-center gap-3 rounded-xl border border-slate-800/70 px-3 py-2 text-sm">
                  <input type="radio" name="payment_method" value="wallet"
                         {{ old('payment_method','wallet') === 'wallet' ? 'checked' : '' }}>
                  <div>
                    <div class="text-slate-100">Saldo Maitri</div>
                    <div class="text-xs text-slate-400">
                      Saldo: Rp {{ number_format(auth()->user()->maitri_balance, 0, ',', '.') }}
                    </div>
                  </div>
                </label>
              @endauth

              {{-- Gateway --}}
              <label class="flex items-center gap-3 rounded-xl border border-slate-800/70 px-3 py-2 text-sm">
                <input type="radio" name="payment_method" value="paydisini_qris"
                       {{ old('payment_method') === 'paydisini_qris' ? 'checked' : '' }}>
                <span>QRIS (Paydisini)</span>
              </label>

              <label class="flex items-center gap-3 rounded-xl border border-slate-800/70 px-3 py-2 text-sm">
                <input type="radio" name="payment_method" value="paydisini_va_mandiri"
                       {{ old('payment_method') === 'paydisini_va_mandiri' ? 'checked' : '' }}>
                <span>VA Mandiri (Paydisini)</span>
              </label>

              <label class="flex items-center gap-3 rounded-xl border border-slate-800/70 px-3 py-2 text-sm">
                <input type="radio" name="payment_method" value="paydisini_alfamart"
                       {{ old('payment_method') === 'paydisini_alfamart' ? 'checked' : '' }}>
                <span>Alfamart (Paydisini)</span>
              </label>

              <label class="flex items-center gap-3 rounded-xl border border-slate-800/70 px-3 py-2 text-sm">
                <input type="radio" name="payment_method" value="paydisini_indomaret"
                       {{ old('payment_method') === 'paydisini_indomaret' ? 'checked' : '' }}>
                <span>Indomaret (Paydisini)</span>
              </label>

              @error('payment_method')
                <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
              @enderror
            </div>

            <button type="submit"
                    class="w-full px-5 py-3 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-medium">
              Buat pesanan
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
@endsection
