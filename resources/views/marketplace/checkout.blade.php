@extends('layouts.app')

@section('title','Checkout Marketplace')
@section('page','marketplace-checkout')

@section('content')
<section class="relative overflow-hidden py-8">
  {{-- BG glow --}}
  <div class="pointer-events-none absolute inset-0">
    <div class="absolute -top-24 -right-24 w-[520px] h-[520px] rounded-full blur-3xl bg-violet-600/15"></div>
    <div class="absolute -bottom-24 -left-24 w-[560px] h-[560px] rounded-full blur-3xl bg-indigo-500/10"></div>
    <div class="absolute inset-0 [background-image:linear-gradient(rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.03)_1px,transparent_1px)] [background-size:48px_48px] [mask-image:radial-gradient(closest-side,black,transparent)] opacity-70"></div>
  </div>

  <div class="relative mx-auto max-w-[1120px] px-4 md:px-6 lg:px-8">

    {{-- breadcrumb / back link --}}
    <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-400">
      <div class="flex items-center gap-1 min-w-0">
        <a href="{{ route('marketplace.index') }}" class="hover:text-slate-200">Marketplace</a>
        <span>/</span>
        <a href="{{ route('marketplace.product.show', $order->product) }}" class="hover:text-slate-200 truncate">
          {{ \Illuminate\Support\Str::limit($order->product->name, 32) }}
        </a>
        <span>/</span>
        <span class="text-slate-500">Checkout</span>
      </div>

      <a href="{{ route('marketplace.product.show', $order->product) }}"
         class="hidden md:inline h-9 px-3 rounded-2xl border border-slate-800/70 bg-slate-950/30 hover:bg-slate-900/40 text-xs text-slate-200 transition">
        ← Kembali ke produk
      </a>
    </div>

    {{-- HERO --}}
    <div class="mt-5 rounded-3xl border border-slate-800/70 bg-slate-900/45 p-5 md:p-7">
      <div class="flex flex-col gap-2">
        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400">
          <span class="inline-flex items-center gap-2 rounded-full border border-slate-800/70 bg-slate-950/30 px-3 py-1">
            <span class="size-1.5 rounded-full bg-emerald-400"></span>
            Step 2 • Checkout
          </span>
          <span class="hidden md:inline text-slate-600">•</span>
          <span class="hidden md:inline">Invoice {{ $order->invoice_number }}</span>
        </div>

        <h1 class="text-2xl md:text-3xl font-semibold tracking-tight text-slate-50">
          Checkout
        </h1>
        <p class="text-sm text-slate-400 max-w-prose">
          Isi detail kontakmu, pilih metode pembayaran, lalu buat pesanan. Detail akun / produk dikirim manual oleh admin setelah pembayaran berhasil.
        </p>

        {{-- Step progress (rapih & ringkas) --}}
        <div class="mt-3 flex items-center gap-3 text-xs">
          <div class="flex items-center gap-2">
            <div class="h-6 w-6 rounded-full bg-emerald-500 text-[11px] flex items-center justify-center text-slate-950 font-semibold">1</div>
            <span class="text-slate-300">Cart</span>
          </div>
          <div class="h-px w-10 bg-slate-700"></div>
          <div class="flex items-center gap-2">
            <div class="h-6 w-6 rounded-full border border-emerald-400 bg-emerald-500/10 text-[11px] flex items-center justify-center text-emerald-300 font-semibold">2</div>
            <span class="text-emerald-300 font-medium">Checkout</span>
          </div>
          <div class="h-px w-10 bg-slate-700"></div>
          <div class="flex items-center gap-2">
            <div class="h-6 w-6 rounded-full border border-slate-700 text-[11px] flex items-center justify-center text-slate-400 font-semibold">3</div>
            <span class="text-slate-500">Finish</span>
          </div>
        </div>
      </div>
    </div>

    {{-- MAIN --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1.7fr)_minmax(0,1fr)] items-start">

      {{-- LEFT: form --}}
      <div class="space-y-4 order-2 lg:order-1">
        <div class="rounded-3xl border border-slate-800/70 bg-slate-900/55 p-4 md:p-5">
          <form method="POST" action="{{ route('marketplace.checkout.process', $order) }}" class="space-y-6" id="checkoutForm">
            @csrf

            {{-- buyer info --}}
            <div class="space-y-3">
              <div class="flex items-center justify-between gap-2">
                <h2 class="text-sm font-semibold text-slate-100">Detail pembeli</h2>
                <span class="text-[11px] text-slate-500">Pastikan email/WA aktif</span>
              </div>

              <div class="grid gap-3 sm:grid-cols-2">
                <div class="space-y-1">
                  <label class="text-xs text-slate-400">Email</label>
                  <input type="email"
                         name="customer_email"
                         value="{{ old('customer_email', $order->customer_email) }}"
                         class="w-full h-11 rounded-2xl bg-slate-950/40 border border-slate-800/70 px-3 text-sm text-slate-100
                                focus:outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/40">
                  @error('customer_email')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                  @enderror
                </div>

                <div class="space-y-1">
                  <label class="text-xs text-slate-400">Nomor WhatsApp / HP</label>
                  <input type="text"
                         name="customer_phone"
                         value="{{ old('customer_phone', $order->customer_phone) }}"
                         class="w-full h-11 rounded-2xl bg-slate-950/40 border border-slate-800/70 px-3 text-sm text-slate-100
                                focus:outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/40"
                         placeholder="08xxxxxxxxxx">
                  @error('customer_phone')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                  @enderror
                </div>
              </div>

              <div class="space-y-1">
                <label class="text-xs text-slate-400">Catatan untuk admin (opsional)</label>
                <textarea name="user_note" rows="3"
                          class="w-full rounded-2xl bg-slate-950/40 border border-slate-800/70 px-3 py-2.5 text-sm text-slate-100
                                 focus:outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/40"
                          placeholder="Contoh: kirim akses ke email lain, jam aktif akun, dsb.">{{ old('user_note', $order->user_note) }}</textarea>
              </div>
            </div>

            {{-- payment --}}
            <div class="pt-4 border-t border-slate-800/70 space-y-3">
              <div class="flex items-center justify-between gap-2">
                <h2 class="text-sm font-semibold text-slate-100">Metode pembayaran</h2>
                <span class="text-[11px] text-slate-500">Fee otomatis dihitung</span>
              </div>

              <div class="grid gap-2 sm:grid-cols-2">
                @auth
                  {{-- Wallet --}}
                  <label class="pay-item flex items-start gap-3 rounded-2xl border border-slate-800/70 bg-slate-950/20 px-3.5 py-3 text-sm cursor-pointer hover:border-violet-500/70 transition">
                    <input type="radio" name="payment_method" value="wallet"
                           class="mt-1"
                           {{ old('payment_method','wallet') === 'wallet' ? 'checked' : '' }}>
                    <div class="min-w-0">
                      <div class="text-slate-100 font-medium">Saldo Maitri</div>
                      <div class="text-[11px] text-slate-400">
                        Saldo: Rp {{ number_format(auth()->user()->maitri_balance, 0, ',', '.') }}
                      </div>
                      <div class="text-[11px] text-slate-500 mt-1">
                        (Butuh PIN pembayaran)
                      </div>
                    </div>
                  </label>
                @endauth

                <label class="pay-item flex items-start gap-3 rounded-2xl border border-slate-800/70 bg-slate-950/20 px-3.5 py-3 text-sm cursor-pointer hover:border-violet-500/70 transition">
                  <input type="radio" name="payment_method" value="paydisini_qris"
                         class="mt-1"
                         {{ old('payment_method') === 'paydisini_qris' ? 'checked' : '' }}>
                  <div>
                    <div class="text-slate-100 font-medium">QRIS</div>
                    <div class="text-[11px] text-slate-400">Paydisini • Fee 0.7%</div>
                  </div>
                </label>

                <label class="pay-item flex items-start gap-3 rounded-2xl border border-slate-800/70 bg-slate-950/20 px-3.5 py-3 text-sm cursor-pointer hover:border-violet-500/70 transition">
                  <input type="radio" name="payment_method" value="paydisini_va_mandiri"
                         class="mt-1"
                         {{ old('payment_method') === 'paydisini_va_mandiri' ? 'checked' : '' }}>
                  <div>
                    <div class="text-slate-100 font-medium">VA Mandiri</div>
                    <div class="text-[11px] text-slate-400">Paydisini • Fee Rp 2.500</div>
                  </div>
                </label>

                <label class="pay-item flex items-start gap-3 rounded-2xl border border-slate-800/70 bg-slate-950/20 px-3.5 py-3 text-sm cursor-pointer hover:border-violet-500/70 transition">
                  <input type="radio" name="payment_method" value="paydisini_alfamart"
                         class="mt-1"
                         {{ old('payment_method') === 'paydisini_alfamart' ? 'checked' : '' }}>
                  <div>
                    <div class="text-slate-100 font-medium">Alfamart</div>
                    <div class="text-[11px] text-slate-400">Paydisini • Fee Rp 2.500</div>
                  </div>
                </label>

                <label class="pay-item flex items-start gap-3 rounded-2xl border border-slate-800/70 bg-slate-950/20 px-3.5 py-3 text-sm cursor-pointer hover:border-violet-500/70 transition">
                  <input type="radio" name="payment_method" value="paydisini_indomaret"
                         class="mt-1"
                         {{ old('payment_method') === 'paydisini_indomaret' ? 'checked' : '' }}>
                  <div>
                    <div class="text-slate-100 font-medium">Indomaret</div>
                    <div class="text-[11px] text-slate-400">Paydisini • Fee Rp 2.500</div>
                  </div>
                </label>
              </div>

              @error('payment_method')
                <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
              @enderror
            </div>

            {{-- PIN: hanya untuk wallet --}}
            @auth
              @if(auth()->user()->hasPaymentPin())
                <div id="pinWrap" class="hidden pt-4 border-t border-slate-800/70">
                  <div class="rounded-2xl border border-violet-700/40 bg-violet-500/10 p-4">
                    <div class="flex items-start justify-between gap-3">
                      <div>
                        <div class="text-sm font-semibold text-slate-100">PIN Pembayaran</div>
                        <p class="text-xs text-slate-300 mt-1">
                          PIN dibutuhkan hanya jika kamu membayar dengan <b>Saldo Maitri</b>.
                        </p>
                      </div>
                      <span class="text-[11px] text-violet-200 border border-violet-500/30 bg-violet-500/10 px-2.5 py-1 rounded-full">
                        Wallet
                      </span>
                    </div>

                    <div class="mt-3 space-y-1">
                      <label class="text-xs text-slate-300">Masukkan PIN (6 digit)</label>
                      <input type="password"
                             id="pinInput"
                             name="payment_pin"
                             maxlength="6"
                             inputmode="numeric"
                             class="w-full h-11 rounded-2xl bg-slate-950/40 border border-slate-800/70 px-3 text-sm text-slate-100
                                    focus:outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/40"
                             placeholder="••••••">
                      @error('payment_pin')
                        <p class="text-xs text-rose-300 mt-1">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>
                </div>
              @else
                {{-- jika belum punya pin, kasih hint --}}
                <div id="pinMissingWrap" class="hidden pt-4 border-t border-slate-800/70">
                  <div class="rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4">
                    <div class="text-sm font-semibold text-amber-200">Butuh PIN untuk Saldo Maitri</div>
                    <p class="text-xs text-slate-300 mt-1">
                      Kamu belum membuat PIN pembayaran. Silakan buat PIN dulu di menu <b>Saldo &amp; Topup</b>.
                    </p>
                  </div>
                </div>
              @endif
            @endauth

            {{-- submit --}}
            <div class="pt-4 border-t border-slate-800/70 space-y-3">
              <p class="text-[11px] text-slate-500">
                Dengan menekan tombol di bawah, kamu menyetujui ketentuan penggunaan layanan marketplace Maitri.
              </p>

              @if($order->payment_status !== 'not_paid')
                <button disabled class="w-full h-11 rounded-2xl bg-slate-700/40 text-slate-300 text-sm font-semibold cursor-not-allowed">
                  Pesanan sudah diproses
                </button>
              @else
                <button type="submit"
                        class="w-full h-11 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-sm font-semibold text-slate-950 shadow-md shadow-emerald-500/25">
                  Buat pesanan
                </button>
              @endif
            </div>

          </form>
        </div>
      </div>

      {{-- RIGHT: summary (sticky) --}}
      <aside class="order-1 lg:order-2 lg:sticky lg:top-6 space-y-4">
        <div class="rounded-3xl border border-slate-800/70 bg-slate-900/55 p-4 md:p-5 space-y-4">
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
              <span id="mpAdminFee" class="text-emerald-400">Gratis</span>
            </div>

            <div class="flex justify-between pt-2 border-t border-slate-800/70 mt-1">
              <span class="text-slate-300 font-medium">Total bayar</span>
              <span id="mpTotal" class="text-slate-50 font-semibold">
                Rp {{ number_format($order->price, 0, ',', '.') }}
              </span>
            </div>
          </div>

          <p class="text-[11px] text-slate-500">
            Setelah pembayaran berhasil, detail akun / produk akan dikirim manual oleh admin ke email atau WhatsApp yang kamu isi.
          </p>
        </div>

        <a href="{{ route('marketplace.product.show', $order->product) }}"
           class="md:hidden h-10 inline-flex items-center justify-center rounded-2xl border border-slate-800/70 bg-slate-950/30 hover:bg-slate-900/40 text-xs text-slate-200 transition">
          ← Kembali ke produk
        </a>
      </aside>
    </div>

    {{-- MOBILE sticky bottom summary --}}
    <div class="lg:hidden fixed bottom-0 inset-x-0 z-40">
      <div class="mx-auto max-w-[1120px] px-4 pb-4">
        <div class="rounded-3xl border border-slate-800/70 bg-slate-950/60 backdrop-blur p-3 flex items-center justify-between gap-3">
          <div class="min-w-0">
            <div class="text-[11px] text-slate-400">Total bayar</div>
            <div id="mpTotalMobile" class="text-sm font-semibold text-slate-100 truncate">
              Rp {{ number_format($order->price, 0, ',', '.') }}
            </div>
          </div>
          <button type="button" id="btnSubmitMobile"
                  class="h-11 px-4 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-sm font-semibold text-slate-950 transition">
            Buat pesanan
          </button>
        </div>
      </div>
    </div>

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const basePriceEl  = document.getElementById('mpPrice');
      const adminFeeEl   = document.getElementById('mpAdminFee');
      const totalEl      = document.getElementById('mpTotal');
      const totalMobile  = document.getElementById('mpTotalMobile');
      const radios       = document.querySelectorAll('input[name="payment_method"]');
      const payItems     = document.querySelectorAll('.pay-item');

      const pinWrap      = document.getElementById('pinWrap');
      const pinMissing   = document.getElementById('pinMissingWrap');
      const pinInput     = document.getElementById('pinInput');

      const form         = document.getElementById('checkoutForm');
      const btnMobile    = document.getElementById('btnSubmitMobile');

      if (!basePriceEl || !adminFeeEl || !totalEl || radios.length === 0) return;

      const baseAmount = Number(basePriceEl.dataset.basePrice || 0);

      const rupiah = (n) => 'Rp ' + Number(n).toLocaleString('id-ID');

      function calcAdminFee(method) {
        if (method === 'paydisini_qris') return Math.ceil(baseAmount * 0.007);
        if (method === 'paydisini_va_mandiri' || method === 'paydisini_alfamart' || method === 'paydisini_indomaret') return 2500;
        return 0; // wallet / lainnya
      }

      function getSelectedMethod() {
        let val = null;
        radios.forEach(r => { if (r.checked) val = r.value; });
        return val;
      }

      function updatePinVisibility(method) {
        const isWallet = method === 'wallet';

        // show/hide
        if (pinWrap) pinWrap.classList.toggle('hidden', !isWallet);
        if (pinMissing) pinMissing.classList.toggle('hidden', !isWallet);

        // enable/disable input (biar bener-bener "dibutuhkan" hanya saat wallet)
        if (pinInput) {
          pinInput.disabled = !isWallet;
          pinInput.required = isWallet;
          if (!isWallet) pinInput.value = '';
        }
      }

      function updatePaymentHighlight() {
        // highlight card yang kepilih
        payItems.forEach(label => {
          const radio = label.querySelector('input[type="radio"]');
          const on = radio && radio.checked;
          label.classList.toggle('border-violet-500/70', on);
          label.classList.toggle('bg-violet-500/10', on);
        });
      }

      function updateSummary() {
        const method = getSelectedMethod();
        const fee = calcAdminFee(method);
        const total = baseAmount + fee;

        if (fee > 0) {
          adminFeeEl.textContent = rupiah(fee);
          adminFeeEl.classList.remove('text-emerald-400');
          adminFeeEl.classList.add('text-slate-100');
        } else {
          adminFeeEl.textContent = 'Gratis';
          adminFeeEl.classList.remove('text-slate-100');
          adminFeeEl.classList.add('text-emerald-400');
        }

        totalEl.textContent = rupiah(total);
        if (totalMobile) totalMobile.textContent = rupiah(total);

        updatePinVisibility(method);
        updatePaymentHighlight();
      }

      radios.forEach(radio => radio.addEventListener('change', updateSummary));
      updateSummary();

      // mobile submit triggers form submit
      if (btnMobile && form) {
        btnMobile.addEventListener('click', function () {
          form.submit();
        });
      }

      // kalau error payment_pin dari backend, pastikan pinWrap terbuka
      @if($errors->has('payment_pin'))
        updatePinVisibility('wallet');
      @endif
    });
  </script>
</section>
@endsection
