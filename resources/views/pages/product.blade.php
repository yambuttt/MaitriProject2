@extends('layouts.app')

@section('title', 'Detail Produk — MaitriProject')
@section('meta_description', 'Detail produk, pilihan varian, dan pembayaran.')
@section('page', 'product')

@section('content')
  <section class="py-8" id="productPage" data-slug="{{ $product->slug }}">
    <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">

      {{-- Back link --}}
      <a href="{{ route('catalog') }}" class="text-sm text-slate-400 hover:text-slate-200">
        ← Kembali ke katalog
      </a>

      <div class="mt-4 grid lg:grid-cols-12 gap-6">

        {{-- ============================= --}}
        {{-- ======== KIRI: INFO ========= --}}
        {{-- ============================= --}}
        <aside class="lg:col-span-5 space-y-4">

          {{-- Info Produk --}}
          <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-5">
            <div class="flex items-center gap-3">
              <div class="size-10 rounded-xl bg-violet-600/20 grid place-items-center text-violet-300">
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                  <path d="M13 3 4 14h7l-1 7 9-11h-7l1-7Z" stroke="currentColor" stroke-width="1.5" />
                </svg>
              </div>

              <div class="min-w-0">
                <h1 id="pName" class="text-xl font-semibold truncate">
                  {{ $product->name }}
                </h1>

                <p id="pMeta" class="text-sm text-slate-400">
                  {{ $product->category?->name ?? '—' }}
                  @if($product->subcategory) • {{ $product->subcategory->name }} @endif
                  @if($product->provider) • {{ $product->provider }} @endif
                </p>
              </div>
            </div>

            <div id="pDescription" class="mt-4 space-y-3 text-sm leading-relaxed text-slate-300">
              {!! nl2br(e($product->description)) !!}
            </div>
          </div>

          {{-- Step 1: Data Akun --}}
          <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-5">
            <div class="flex items-center gap-2 text-slate-300">
              <div class="size-6 grid place-items-center rounded-full border border-slate-700">1</div>
              <h2 class="font-medium">Masukkan Data Akun</h2>
            </div>

            <div class="mt-4 space-y-3">
              {{-- Target --}}
              <div>
                <label class="text-sm text-slate-400">User ID / Nomor Tujuan</label>
                <input id="fTarget" name="target" type="text" placeholder="Masukkan User ID atau Nomor" class="mt-1 w-full rounded-xl bg-[#0E1524] border border-slate-800/70
                                  px-3 py-2 text-sm outline-none
                                  focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
                <p id="fTargetHelp" class="mt-1 text-xs text-slate-500">
                  Contoh: 081234567890 atau 12345678(1234).
                </p>
              </div>

              {{-- Email --}}
              <div>
                <label class="text-sm text-slate-400">Email (untuk bukti pembayaran)</label>
                <input id="fEmail" name="email" type="email" placeholder="nama@email.com" class="mt-1 w-full rounded-xl bg-[#0E1524] border border-slate-800/70
                                  px-3 py-2 text-sm outline-none
                                  focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
              </div>
            </div>
          </div>

        </aside>
        {{-- ============================= --}}
        {{-- ====== KANAN: VARIAN ========= --}}
        {{-- ============================= --}}
        <section class="lg:col-span-7 space-y-4">

          {{-- Step 2: Pilih Nominal --}}
          <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-5">
            <div class="flex items-center gap-2 text-slate-300">
              <div class="size-6 grid place-items-center rounded-full border border-slate-700">2</div>
              <h2 class="font-medium">Pilih Nominal</h2>
            </div>

            {{-- Tabs (future-ready, default Umum) --}}
            <div id="variantTabs" class="mt-3 flex flex-wrap gap-2">
              <button type="button" class="px-3 py-1.5 rounded-full border border-violet-600/70 bg-violet-600/10
                              text-violet-300 text-sm cursor-default">
                Umum
              </button>
            </div>

            {{-- Grid Varian --}}
            <div id="variantGrid" class="mt-4 grid sm:grid-cols-2 xl:grid-cols-3 gap-3">
              @foreach($product->variants as $v)
                <button type="button" class="variant-card rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4
                                text-left hover:border-slate-700 transition" data-variant-id="{{ $v->id }}"
                  data-variant-name="{{ $v->name }}" data-variant-price="{{ $v->final_price }}">

                  <div class="text-sm font-medium">{{ $v->name }}</div>
                  <div class="text-xs text-slate-400 mt-0.5">{{ $v->buyer_sku_code }}</div>

                  <div class="mt-3 font-semibold">
                    Rp {{ number_format($v->final_price, 0, ',', '.') }}
                  </div>
                </button>
              @endforeach
            </div>
          </div>

          {{-- ============================= --}}
          {{-- ====== Step 3: Pembayaran ==== --}}
          {{-- ============================= --}}
          <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-5">
            <div class="flex items-center gap-2 text-slate-300">
              <div class="size-6 grid place-items-center rounded-full border border-slate-700">3</div>
              <h2 class="font-medium">Pilih Pembayaran</h2>
            </div>

            <div id="payList" class="mt-3 space-y-2">

              {{-- Saldo Maitri --}}
              {{-- Saldo Maitri --}}
              @auth
                @php
                  $formattedSaldo = 'Rp ' . number_format($walletBalance ?? 0, 0, ',', '.');
                @endphp

                <div class="rounded-xl border border-slate-800/70 p-3 flex items-center justify-between">
                  <div class="text-sm">
                    <div class="flex items-center gap-2">
                      <span>Saldo Maitri</span>
                      <span class="text-xs text-slate-400">({{ $formattedSaldo }})</span>
                    </div>
                    <div id="saldoWarning" class="mt-1 text-xs text-amber-300 hidden">
                      Saldo tidak mencukupi untuk varian yang dipilih.
                    </div>
                  </div>

                  <button type="button" id="paySaldoBtn"
                    class="px-3 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 text-sm">
                    Pilih
                  </button>
                </div>
              @else
                <div class="rounded-xl border border-slate-800/80 bg-slate-900/60 px-4 py-3 text-sm text-slate-300">
                  Silakan login untuk menggunakan Saldo Maitri.
                </div>
              @endauth


              {{-- NON SALDO (Placeholder Future Gateway) --}}
              <div class="rounded-xl border border-slate-800/70 p-3 flex items-center justify-between">
                <div class="text-sm">QRIS</div>
                <button class="pickPay px-3 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 text-sm"
                  data-pay="QRIS">Pilih</button>
              </div>

              <div class="rounded-xl border border-slate-800/70 p-3 flex items-center justify-between">
                <div class="text-sm">Virtual Account</div>
                <button class="pickPay px-3 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 text-sm"
                  data-pay="VA">Pilih</button>
              </div>

              <div class="rounded-xl border border-slate-800/70 p-3 flex items-center justify-between">
                <div class="text-sm">OVO</div>
                <button class="pickPay px-3 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 text-sm"
                  data-pay="OVO">Pilih</button>
              </div>
            </div>
          </div>
          {{-- ============================= --}}
          {{-- ====== Step 4: Ringkasan ===== --}}
          {{-- ============================= --}}
          <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-5">

            <div class="flex items-start md:items-center flex-col md:flex-row 
                          md:justify-between gap-4">

              {{-- Ringkasan --}}
              <div class="text-sm text-slate-300 space-y-1" id="summaryBox">
                <div>
                  <span class="text-slate-400">Produk:</span>
                  <span id="sProd">{{ $product->name }}</span>
                </div>

                <div>
                  <span class="text-slate-400">Varian:</span>
                  <span id="sVar">—</span>
                </div>

                <div>
                  <span class="text-slate-400">Metode:</span>
                  <span id="sPay">—</span>
                </div>

                <div>
                  <span class="text-slate-400">Subtotal:</span>
                  <span id="sSub">Rp 0</span>
                </div>

                <div>
                  <span class="text-slate-400">Biaya Admin:</span>
                  <span id="sFee">Rp 0</span>
                </div>

                <div class="text-lg font-semibold">
                  <span class="text-slate-400">Total:</span>
                  <span id="sTotal">Rp 0</span>
                </div>
              </div>

              {{-- Tombol Checkout --}}
              <button id="btnCheckout" class="w-full md:w-auto px-5 py-3 rounded-2xl bg-violet-600 hover:bg-violet-500
                              disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                Lanjutkan Pembayaran
              </button>
            </div>

          </div>
        </section>
      </div>
    </div>


    {{-- ===================================================== --}}
    {{-- =============== MODAL PIN SALDO MAITRI ============== --}}
    {{-- ===================================================== --}}
    @auth
      <div id="saldoPinModal" class="fixed inset-0 z-40 hidden items-center justify-center bg-black/60">

        <div class="w-full max-w-md rounded-2xl bg-slate-900 border border-violet-500/40 p-5 space-y-4">

          <h2 class="text-lg font-semibold text-slate-50">Konfirmasi PIN Pembayaran</h2>

          <p class="text-sm text-slate-300">
            Masukkan PIN pembayaran Maitri untuk melanjutkan transaksi.
          </p>

          <form method="POST" action="{{ route('checkout.saldo') }}" id="saldoPinForm" class="space-y-3">
            @csrf

            {{-- Hidden inputs diisi JS --}}
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="variant_id" id="modalVariantId">
            <input type="hidden" name="target" id="modalTarget">
            <input type="hidden" name="email" id="modalEmail">

            {{-- PIN --}}
            <div>
              <label class="block text-xs font-medium text-slate-400 mb-1">PIN Pembayaran</label>
              <input type="password" maxlength="6" name="pin" class="h-10 w-full rounded-xl bg-slate-950 border border-slate-700/80
                            px-3 text-sm text-slate-100" placeholder="Masukkan PIN" required>
            </div>

            <div class="flex justify-end gap-2 pt-1">
              <button type="button" id="btnClosePin" class="h-9 px-3 rounded-xl text-sm text-slate-300 hover:bg-slate-800">
                Batal
              </button>

              <button type="submit"
                class="h-9 px-4 rounded-xl bg-violet-500 hover:bg-violet-600 text-sm font-medium text-white">
                Bayar Sekarang
              </button>
            </div>
          </form>

        </div>
      </div>
    @endauth

  </section>
@endsection
@push('body')
  <script>
    document.addEventListener('DOMContentLoaded', () => {

      const rupiah = n => new Intl.NumberFormat('id-ID').format(n);

      let selectedVariant = null;
      let selectedMethod = null;

      // DOM Elements
      const cards = document.querySelectorAll('.variant-card');
      const btnCheckout = document.getElementById('btnCheckout');
      const btnSaldo = document.getElementById('paySaldoBtn');
      const saldoWarning = document.getElementById('saldoWarning');

      const sVar = document.getElementById('sVar');
      const sPay = document.getElementById('sPay');
      const sSub = document.getElementById('sSub');
      const sTotal = document.getElementById('sTotal');

      const targetField = document.getElementById('fTarget');
      const emailField = document.getElementById('fEmail');

      const walletBalance = {{ (int) ($walletBalance ?? 0) }};

      // ============================
      //  SELECT VARIANT
      // ============================
      function selectVariant(card) {
        cards.forEach(c => c.classList.remove('ring-2', 'ring-violet-500/80', 'border-violet-600/70'));
        card.classList.add('ring-2', 'ring-violet-500/80', 'border-violet-600/70');

        selectedVariant = {
          id: card.dataset.variantId,
          name: card.dataset.variantName,
          price: parseInt(card.dataset.variantPrice)
        };

        updateSummary();
        validateSaldo();
      }

      cards.forEach(card => {
        card.addEventListener('click', () => selectVariant(card));
      });

      // Preselect first variant
      if (cards.length) {
        selectVariant(cards[0]);
      }

      // ============================
      //  SELECT PAYMENT METHOD
      // ============================
      document.querySelectorAll('.pickPay').forEach(btn => {
        btn.addEventListener('click', () => {

          // remove highlight
          document.querySelectorAll('.pickPay').forEach(b => {
            b.classList.remove('border-violet-600/70');
          });

          btn.classList.add('border-violet-600/70');

          selectedMethod = btn.dataset.pay;

          // unhighlight saldo
          if (btnSaldo) {
            btnSaldo.classList.remove('border-violet-500', 'bg-violet-500/20');
          }

          updateSummary();
        });
      });

      if (btnSaldo) {
        btnSaldo.addEventListener('click', () => {
          selectedMethod = 'SALDO';

          // highlight saldo
          btnSaldo.classList.add('border-violet-500', 'bg-violet-500/20');

          // remove highlight gateway
          document.querySelectorAll('.pickPay').forEach(b => {
            b.classList.remove('border-violet-600/70');
          });

          updateSummary();
        });
      }

      // ============================
      //  UPDATE SUMMARY
      // ============================
      function updateSummary() {
        sVar.textContent = selectedVariant ? selectedVariant.name : '—';
        sPay.textContent = selectedMethod ?? '—';
        sSub.textContent = selectedVariant ? 'Rp ' + rupiah(selectedVariant.price) : 'Rp 0';
        sTotal.textContent = selectedVariant ? 'Rp ' + rupiah(selectedVariant.price) : 'Rp 0';

        btnCheckout.disabled = !(selectedVariant && selectedMethod);
      }

      // ============================
      //  SALDO VALIDATION
      // ============================
      function validateSaldo() {
        if (!selectedVariant) return;

        const insufficient = selectedVariant.price > walletBalance;
        if (insufficient) {
          saldoWarning.classList.remove('hidden');
        } else {
          saldoWarning.classList.add('hidden');
        }
      }

      // ============================
      //  CHECKOUT BUTTON HANDLING
      // ============================
      const saldoPinModal = document.getElementById('saldoPinModal');
      const btnClosePin = document.getElementById('btnClosePin');

      const modalVariantId = document.getElementById('modalVariantId');
      const modalTarget = document.getElementById('modalTarget');
      const modalEmail = document.getElementById('modalEmail');

      btnCheckout.addEventListener('click', () => {

        if (!selectedVariant) {
          alert('Pilih nominal terlebih dahulu');
          return;
        }

        const target = targetField.value.trim();
        if (!target) {
          alert('Isi User ID / Nomor tujuan terlebih dahulu');
          return;
        }

        // Jika metode = Saldo → buka modal PIN
        if (selectedMethod === 'SALDO') {

          if (selectedVariant.price > walletBalance) {
            alert('Saldo tidak mencukupi');
            return;
          }

          modalVariantId.value = selectedVariant.id;
          modalTarget.value = target;
          modalEmail.value = emailField?.value ?? '';

          saldoPinModal.classList.remove('hidden');
          saldoPinModal.classList.add('flex');
        } else {

          // Payment gateway (belum aktif)
          alert('Untuk saat ini pembayaran selain Saldo Maitri belum tersedia.');
        }
      });

      btnClosePin.addEventListener('click', () => {
        saldoPinModal.classList.add('hidden');
        saldoPinModal.classList.remove('flex');
      });

    });
  </script>
@endpush