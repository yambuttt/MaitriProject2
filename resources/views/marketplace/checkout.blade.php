@extends('layouts.app')

@section('title','Checkout Marketplace')
@section('page','marketplace-checkout')

@section('content')
  <section class="py-8">
    <div class="mx-auto max-w-[1120px] px-4 md:px-6 lg:px-8">

      {{-- breadcrumb / back link --}}
      <div class="flex items-center justify-between gap-3 text-xs text-slate-400">
        <div class="flex items-center gap-1">
          <a href="{{ route('marketplace.index') }}" class="hover:text-slate-200">
            Marketplace
          </a>
          <span>/</span>
          <a href="{{ route('marketplace.product.show', $order->product) }}" class="hover:text-slate-200">
            {{ \Illuminate\Support\Str::limit($order->product->name, 32) }}
          </a>
          <span>/</span>
          <span class="text-slate-500">Checkout</span>
        </div>

        <a href="{{ route('marketplace.product.show', $order->product) }}"
           class="hidden md:inline text-xs text-slate-400 hover:text-slate-200">
          ← Kembali ke produk
        </a>
      </div>

      {{-- step progress --}}
      <div class="mt-5 mb-4">
        <div class="flex items-center justify-center gap-4 text-xs">
          <div class="flex items-center gap-2">
            <div class="h-6 w-6 rounded-full bg-emerald-500 text-[11px] flex items-center justify-center text-slate-950 font-semibold">
              1
            </div>
            <span class="text-slate-200">Shopping cart</span>
          </div>

          <div class="h-px w-8 md:w-16 bg-slate-700"></div>

          <div class="flex items-center gap-2">
            <div class="h-6 w-6 rounded-full border border-emerald-400 bg-emerald-500/10 text-[11px] flex items-center justify-center text-emerald-300 font-semibold">
              2
            </div>
            <span class="text-emerald-300 font-medium">Checkout</span>
          </div>

          <div class="h-px w-8 md:w-16 bg-slate-700"></div>

          <div class="flex items-center gap-2">
            <div class="h-6 w-6 rounded-full border border-slate-700 text-[11px] flex items-center justify-center text-slate-400 font-semibold">
              3
            </div>
            <span class="text-slate-500">Finish</span>
          </div>
        </div>
      </div>

      <div class="mt-3 grid gap-6 md:grid-cols-[minmax(0,1.7fr)_minmax(0,1fr)] items-start">
        {{-- KIRI: form detail billing + payment --}}
        <div class="space-y-4 md:order-1 order-2">
          <h1 class="text-2xl font-semibold text-slate-50 mb-1">Checkout</h1>
          <p class="text-xs text-slate-400 mb-1">Isi detail kontakmu dan pilih metode pembayaran.</p>

          <div class="rounded-3xl border border-slate-800/80 bg-slate-900/70 p-4 md:p-5 space-y-6">
            {{-- Billing details --}}
            <div class="space-y-3">
              <h2 class="text-sm font-semibold text-slate-100">Detail pembeli</h2>

              <form method="POST"
                    action="{{ route('marketplace.checkout.process', $order) }}"
                    class="space-y-5">
                @csrf

                <div class="space-y-1">
                  <label class="text-xs text-slate-400">Email</label>
                  <input type="email"
                         name="customer_email"
                         value="{{ old('customer_email', $order->customer_email) }}"
                         class="w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/60">
                  @error('customer_email')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                  @enderror
                </div>

                <div class="space-y-1">
                  <label class="text-xs text-slate-400">Nomor WhatsApp / HP</label>
                  <input type="text"
                         name="customer_phone"
                         value="{{ old('customer_phone', $order->customer_phone) }}"
                         class="w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/60"
                         placeholder="08xxxxxxxxxx">
                </div>

                <div class="space-y-1">
                  <label class="text-xs text-slate-400">Catatan untuk admin (opsional)</label>
                  <textarea name="user_note" rows="3"
                            class="w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/60"
                            placeholder="Contoh: kirim akses ke email lain, jam aktif akun, dsb.">{{ old('user_note', $order->user_note) }}</textarea>
                </div>
                

                {{-- Payment method --}}
                <div class="pt-2 border-t border-slate-800/70 space-y-3">
                  <h2 class="text-sm font-semibold text-slate-100">Metode pembayaran</h2>

                  <div class="space-y-2">
                    @auth
                      <label class="flex items-center gap-3 rounded-2xl border border-slate-800/80 px-3.5 py-2.5 text-sm cursor-pointer hover:border-violet-500/70">
                        <input type="radio" name="payment_method" value="wallet"
                               {{ old('payment_method','wallet') === 'wallet' ? 'checked' : '' }}>
                        <div>
                          <div class="text-slate-100">Saldo Maitri</div>
                          <div class="text-[11px] text-slate-400">
                            Saldo: Rp {{ number_format(auth()->user()->maitri_balance, 0, ',', '.') }}
                          </div>
                        </div>
                      </label>
                    @endauth

                    <label class="flex items-center gap-3 rounded-2xl border border-slate-800/80 px-3.5 py-2.5 text-sm cursor-pointer hover:border-violet-500/70">
                      <input type="radio" name="payment_method" value="paydisini_qris"
                             {{ old('payment_method') === 'paydisini_qris' ? 'checked' : '' }}>
                      <span>QRIS (Paydisini)</span>
                    </label>

                    <label class="flex items-center gap-3 rounded-2xl border border-slate-800/80 px-3.5 py-2.5 text-sm cursor-pointer hover:border-violet-500/70">
                      <input type="radio" name="payment_method" value="paydisini_va_mandiri"
                             {{ old('payment_method') === 'paydisini_va_mandiri' ? 'checked' : '' }}>
                      <span>VA Mandiri (Paydisini)</span>
                    </label>

                    <label class="flex items-center gap-3 rounded-2xl border border-slate-800/80 px-3.5 py-2.5 text-sm cursor-pointer hover:border-violet-500/70">
                      <input type="radio" name="payment_method" value="paydisini_alfamart"
                             {{ old('payment_method') === 'paydisini_alfamart' ? 'checked' : '' }}>
                      <span>Alfamart (Paydisini)</span>
                    </label>

                    <label class="flex items-center gap-3 rounded-2xl border border-slate-800/80 px-3.5 py-2.5 text-sm cursor-pointer hover:border-violet-500/70">
                      <input type="radio" name="payment_method" value="paydisini_indomaret"
                             {{ old('payment_method') === 'paydisini_indomaret' ? 'checked' : '' }}>
                      <span>Indomaret (Paydisini)</span>
                    </label>

                    @error('payment_method')
                      <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                @if(auth()->user() && auth()->user()->hasPaymentPin())
    <div class="space-y-1">
        <label class="text-xs text-slate-400">PIN Pembayaran</label>
        <input type="password"
               name="payment_pin"
               maxlength="6"
               class="w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2.5 text-sm text-slate-100"
               placeholder="Masukkan PIN pembayaran">
        @error('payment_pin')
            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
        @enderror
    </div>
@endif

                {{-- terms + submit --}}
                <div class="pt-3 space-y-3 border-t border-slate-800/70">
                  <p class="text-[11px] text-slate-500">
                    Dengan menekan tombol di bawah, kamu menyetujui ketentuan penggunaan layanan marketplace Maitri.
                  </p>
                  
                  @if($order->payment_status !== 'not_paid')
                    <button disabled class="...opacity-50 cursor-not-allowed">Pesanan sudah diproses</button>
                    @else
                  <button type="submit"
                          class="w-full h-11 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-sm font-semibold text-slate-950 shadow-md shadow-emerald-500/30">
                    Buat pesanan
                  </button>
                  @endif
                </div>
              </form>
            </div>
          </div>
        </div>

        {{-- KANAN: ringkasan order --}}
        <aside class="md:order-2 order-1">
          <div class="rounded-3xl border border-slate-800/80 bg-slate-900/70 p-4 md:p-5 space-y-4">
            <h2 class="text-sm font-semibold text-slate-100 flex items-center justify-between">
              Ringkasan pesanan
              <span class="text-[11px] text-slate-500">Invoice {{ $order->invoice_number }}</span>
            </h2>

            <div class="space-y-2 text-sm">
              <div>
                <p class="text-xs text-slate-400">Produk marketplace</p>
                <p class="text-sm font-semibold text-slate-100 mt-0.5">
                  {{ $order->product->name }}
                </p>
                <p class="text-xs text-slate-400 mt-1">
                  Varian: {{ $order->variant->name }}
                  @if($order->variant->duration_days)
                    ({{ $order->variant->duration_days }} hari)
                  @endif
                </p>
              </div>
            </div>

                        <div class="border-t border-slate-800/70 pt-3 space-y-1 text-sm">
              <div class="flex justify-between">
                <span class="text-slate-400">Harga</span>
                <span id="mpPrice"
                      class="text-slate-100"
                      data-base-price="{{ (int) $order->price }}">
                  Rp {{ number_format($order->price, 0, ',', '.') }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-400">Biaya admin</span>
                <span id="mpAdminFee" class="text-emerald-400">
                  Gratis
                </span>
              </div>
              <div class="flex justify-between pt-2 border-t border-slate-800/70 mt-1">
                <span class="text-slate-300 font-medium">Total bayar</span>
                <span id="mpTotal" class="text-slate-50 font-semibold">
                  {{-- default = harga, nanti di-update JS sesuai metode bayar --}}
                  Rp {{ number_format($order->price, 0, ',', '.') }}
                </span>
              </div>
            </div>


            <p class="text-[11px] text-slate-500">
              Setelah pembayaran berhasil, detail akun / produk akan dikirim manual oleh admin ke email atau WhatsApp yang kamu isi.
            </p>
          </div>
        </aside>
      </div>
    </div>
  </section>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const basePriceEl = document.getElementById('mpPrice');
        const adminFeeEl  = document.getElementById('mpAdminFee');
        const totalEl     = document.getElementById('mpTotal');
        const radios      = document.querySelectorAll('input[name="payment_method"]');

        if (!basePriceEl || !adminFeeEl || !totalEl || radios.length === 0) {
            return;
        }

        const baseAmount = Number(basePriceEl.dataset.basePrice || 0);

        function calcAdminFee(method) {
            if (method === 'paydisini_qris') {
                // 0.7% dibulatkan ke atas
                return Math.ceil(baseAmount * 0.007);
            }

            if (
                method === 'paydisini_va_mandiri' ||
                method === 'paydisini_alfamart' ||
                method === 'paydisini_indomaret'
            ) {
                // VA Mandiri / Alfamart / Indomaret: Rp 2.500
                return 2500;
            }

            // Saldo Maitri / lainnya: tanpa admin gateway
            return 0;
        }

        function formatIdr(value) {
            return 'Rp ' + Number(value).toLocaleString('id-ID');
        }

        function updateSummary() {
            let selectedMethod = null;
            radios.forEach(radio => {
                if (radio.checked) {
                    selectedMethod = radio.value;
                }
            });

            const fee   = calcAdminFee(selectedMethod);
            const total = baseAmount + fee;

            if (fee > 0) {
                adminFeeEl.textContent = formatIdr(fee);
                adminFeeEl.classList.remove('text-emerald-400');
                adminFeeEl.classList.add('text-slate-100');
            } else {
                adminFeeEl.textContent = 'Gratis';
                adminFeeEl.classList.remove('text-slate-100');
                adminFeeEl.classList.add('text-emerald-400');
            }

            totalEl.textContent = formatIdr(total);
        }

        radios.forEach(radio => {
            radio.addEventListener('change', updateSummary);
        });

        // Inisialisasi saat halaman pertama kali dibuka
        updateSummary();
    });
</script>
