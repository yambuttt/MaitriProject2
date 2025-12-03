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

      <div class="mt-4 space-y-6">

        {{-- ========================= --}}
        {{-- HERO: gambar + info produk --}}
        {{-- ========================= --}}
        <div
          class="rounded-3xl border border-slate-800/80 bg-gradient-to-b from-slate-900/80 to-slate-950/90 shadow-xl shadow-slate-950/50">
          <div class="grid gap-6 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,2fr)] items-center p-5 sm:p-6 lg:p-8">

            {{-- Thumbnail --}}
            <div class="flex justify-center lg:justify-start">
              <div
                class="w-40 h-40 sm:w-48 sm:h-48 rounded-3xl border border-slate-800/80 bg-slate-900/80 overflow-hidden shadow-lg shadow-slate-950/40">
                @if(!empty($product->thumbnail))
                  <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}"
                    class="w-full h-full object-cover">
                @else
                  <div class="w-full h-full flex flex-col items-center justify-center gap-2 text-slate-500 text-xs">
                    <div class="size-10 rounded-xl bg-violet-600/15 grid place-items-center text-violet-300">
                      <svg class="size-5" viewBox="0 0 24 24" fill="none">
                        <path d="M13 3 4 14h7l-1 7 9-11h-7l1-7Z" stroke="currentColor" stroke-width="1.5" />
                      </svg>
                    </div>
                    <span>Gambar produk belum diatur</span>
                  </div>
                @endif
              </div>
            </div>

            {{-- Info utama --}}
            <div class="space-y-4">
              <div>
                <p class="text-[11px] font-medium tracking-[.15em] uppercase text-slate-500">
                  Produk Digiflazz
                </p>
                <h1 id="pName" class="mt-1 text-2xl sm:text-3xl font-semibold text-slate-50">
                  {{ $product->name }}
                </h1>
                <p id="pMeta" class="mt-1 text-sm text-slate-400">
                  {{ $product->category?->name ?? '—' }}
                  @if($product->subcategory) • {{ $product->subcategory->name }} @endif
                  @if($product->provider) • {{ $product->provider }} @endif
                </p>
              </div>

              {{-- “Badge” kecil seperti referensi --}}
              <div class="flex flex-wrap gap-2 text-[11px] text-slate-200">
                <span class="inline-flex items-center gap-1 rounded-full bg-slate-800/80 px-3 py-1">
                  ⚡ <span>Proses cepat</span>
                </span>
                <span class="inline-flex items-center gap-1 rounded-full bg-slate-800/80 px-3 py-1">
                  💬 <span>Layanan chat 24/7</span>
                </span>
                <span class="inline-flex items-center gap-1 rounded-full bg-slate-800/80 px-3 py-1">
                  🛡️ <span>Pembayaran aman</span>
                </span>
              </div>

              <div id="pDescription" class="text-sm leading-relaxed text-slate-300">
                {!! nl2br(e($product->description)) !!}
              </div>
            </div>
          </div>
        </div>

        {{-- ============================= --}}
        {{-- MAIN GRID: step kiri + info kanan --}}
        {{-- ============================= --}}
        <div class="grid gap-6 lg:grid-cols-[minmax(0,7fr)_minmax(0,4fr)] items-start">

          {{-- ========== KIRI: STEP ========= --}}
          <div class="space-y-4">

            {{-- Step 1: Target --}}
            <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-5">
              <div class="flex items-center gap-2 text-slate-300">
                <div class="size-6 grid place-items-center rounded-full border border-slate-700 text-xs">1</div>
                <h2 class="font-medium">Target</h2>
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
              </div>
            </div>

            {{-- Step 2: Pilih Nominal --}}
            <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-5">
              <div class="flex items-center gap-2 text-slate-300">
                <div class="size-6 grid place-items-center rounded-full border border-slate-700 text-xs">2</div>
                <h2 class="font-medium">Pilih Nominal</h2>
              </div>

              {{-- Tabs (untuk future kategori) --}}
              <!-- <div id="variantTabs" class="mt-3 flex flex-wrap gap-2">
                            <button type="button" class="px-3 py-1.5 rounded-full border border-violet-600/70 bg-violet-600/10
                                       text-violet-300 text-sm cursor-default">
                              Umum
                            </button>
                          </div> -->

              {{-- Grid Varian --}}
              <div id="variantGrid" class="mt-4 grid sm:grid-cols-2 xl:grid-cols-3 gap-3">
                @foreach($product->variants as $v)
                  <button type="button" class="variant-card rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4 text-left
                                                       hover:border-slate-700 transition" data-variant-id="{{ $v->id }}"
                    data-variant-name="{{ $v->name }}" data-variant-price="{{ $v->final_price }}">
                    <div class="text-sm font-medium text-slate-100">{{ $v->name }}</div>
                    <div class="text-xs text-slate-400 mt-0.5">{{ $v->buyer_sku_code }}</div>

                    <div class="mt-3 font-semibold text-slate-50">
                      Rp {{ number_format($v->final_price, 0, ',', '.') }}
                    </div>
                  </button>
                @endforeach
              </div>
            </div>

            {{-- Step 3: Pilih Pembayaran --}}
            <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-5">
              <div class="flex items-center gap-2 text-slate-300">
                <div class="size-6 grid place-items-center rounded-full border border-slate-700 text-xs">3</div>
                <h2 class="font-medium">Pilih Pembayaran</h2>
              </div>

              <div id="payList" class="mt-3 space-y-2">

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

                {{-- Channel Paydisini (Gateway) --}}
                <div class="rounded-xl border border-slate-800/70 p-3 flex items-center justify-between">
                  <div class="text-sm">QRIS</div>
                  <button type="button"
                    class="pickPay px-3 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 text-sm"
                    data-pay="QRIS">
                    Pilih
                  </button>
                </div>

                <div class="rounded-xl border border-slate-800/70 p-3 flex items-center justify-between">
                  <div class="text-sm">Virtual Account Mandiri</div>
                  <button type="button"
                    class="pickPay px-3 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 text-sm"
                    data-pay="VA_MANDIRI">
                    Pilih
                  </button>
                </div>

                <div class="rounded-xl border border-slate-800/70 p-3 flex items-center justify-between">
                  <div class="text-sm">Alfamart</div>
                  <button type="button"
                    class="pickPay px-3 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 text-sm"
                    data-pay="ALFAMART">
                    Pilih
                  </button>
                </div>

                <div class="rounded-xl border border-slate-800/70 p-3 flex items-center justify-between">
                  <div class="text-sm">Indomaret</div>
                  <button type="button"
                    class="pickPay px-3 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 text-sm"
                    data-pay="INDOMARET">
                    Pilih
                  </button>
                </div>
              </div>
            </div>

            {{-- Step 4: Detail Kontak --}}
            {{-- Step 4: Detail Kontak --}}
            <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-5">
              <div class="flex items-center gap-2 text-slate-300">
                <div class="size-6 grid place-items-center rounded-full border border-slate-700 text-xs">4</div>
                <h2 class="font-medium">Detail Kontak</h2>
              </div>

              <div class="mt-4 space-y-3">
                {{-- Email --}}
                <div>
                  <label class="text-sm text-slate-400">Email (untuk bukti pembayaran)</label>
                  <input id="fEmail" name="email" type="email" placeholder="nama@email.com" class="mt-1 w-full rounded-xl bg-[#0E1524] border border-slate-800/70
                         px-3 py-2 text-sm outline-none
                         focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
                  <p class="mt-1 text-[11px] text-slate-500">
                    Bukti transaksi dan info pesanan bisa kami kirim ke email ini.
                  </p>
                </div>

                {{-- Nomor HP / WhatsApp --}}
                <div>
                  <label class="text-sm text-slate-400">No. WhatsApp / HP</label>
                  <input id="fPhone" name="phone" type="tel" placeholder="08xxxxxxxxxx" class="mt-1 w-full rounded-xl bg-[#0E1524] border border-slate-800/70
                         px-3 py-2 text-sm outline-none
                         focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
                  <p class="mt-1 text-[11px] text-slate-500">
                    Nomor ini akan dihubungi jika terjadi masalah pada pesanan.
                  </p>
                </div>
              </div>
            </div>


          </div>

          {{-- ========== KANAN: RATING + SUMMARY ========= --}}
          <aside class="space-y-4">

            {{-- Rating (dummy UI, bisa disambungkan ke data nanti) --}}
            <div class="rounded-3xl border border-slate-800/80 bg-slate-900/80 p-5">
              <h2 class="text-sm font-semibold text-slate-100">Ulasan dan rating</h2>

              <div class="mt-3 flex items-center gap-3">
                <div class="flex items-baseline gap-1">
                  <span class="text-3xl font-semibold text-slate-50">4.9</span>
                  <span class="text-xs text-slate-400">/ 5.0</span>
                </div>
                <div class="flex items-center gap-0.5 text-amber-300 text-lg">
                  ★★★★★
                </div>
              </div>

              <p class="mt-1 text-[11px] text-slate-500">
                Contoh tampilan rating — bisa dihubungkan ke sistem review kalau sudah siap.
              </p>
            </div>

            {{-- Bantuan --}}
            <div class="rounded-3xl border border-slate-800/80 bg-slate-900/80 p-5 flex gap-3">
              <div
                class="mt-0.5 h-9 w-9 flex items-center justify-center rounded-full border border-slate-700 text-slate-300">
                ☎
              </div>
              <div class="space-y-1 text-sm">
                <h2 class="font-semibold text-slate-100">Butuh Bantuan?</h2>
                <p class="text-xs text-slate-400">
                  Jika terjadi masalah pada pesanan, hubungi admin melalui WhatsApp atau menu Bantuan di atas.
                </p>
              </div>
            </div>

            {{-- Detail biaya + tombol checkout (muncul hanya jika siap) --}}
            <div id="summaryWrapper" class="rounded-3xl border border-violet-700/70 bg-[#111826] p-5 space-y-4 hidden">

              <div class="flex items-center justify-between gap-2">
                <h2 class="text-sm font-semibold text-slate-100">Detail biaya</h2>
                <span class="text-[11px] text-violet-300">Cek kembali sebelum bayar</span>
              </div>

              <dl class="space-y-2 text-sm text-slate-200" id="summaryBox">
                <div class="flex justify-between gap-4">
                  <dt class="text-slate-400">Produk</dt>
                  <dd class="text-right" id="sProd">{{ $product->name }}</dd>
                </div>

                <div class="flex justify-between gap-4">
                  <dt class="text-slate-400">Varian</dt>
                  <dd class="text-right" id="sVar">—</dd>
                </div>

                <div class="flex justify-between gap-4">
                  <dt class="text-slate-400">Metode</dt>
                  <dd class="text-right" id="sPay">—</dd>
                </div>

                <div class="flex justify-between gap-4">
                  <dt class="text-slate-400">Subtotal</dt>
                  <dd class="text-right" id="sSub">Rp 0</dd>
                </div>

                <div class="flex justify-between gap-4">
                  <dt class="text-slate-400">Biaya Admin</dt>
                  <dd class="text-right" id="sFee">Rp 0</dd>
                </div>

                <div class="flex justify-between gap-4 pt-2 border-t border-slate-800 mt-1">
                  <dt class="text-slate-400">Total Bayar</dt>
                  <dd class="text-right font-semibold text-slate-50" id="sTotal">Rp 0</dd>
                </div>
              </dl>

              <button id="btnCheckout" class="w-full mt-2 px-5 py-3 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-medium
                                     text-white disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                Lanjutkan Pembayaran
              </button>

              <p class="text-[11px] text-slate-500">
                Setelah pembayaran berhasil, detail transaksi lengkap akan muncul di halaman invoice.
              </p>
            </div>
          </aside>
        </div>
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
            <input type="hidden" name="phone" id="modalPhone">

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

    {{-- ===================================================== --}}
    {{-- =========== FORM HIDDEN CHECKOUT PAYDISINI =========== --}}
    {{-- ===================================================== --}}
    <form id="paydisiniForm" method="POST" action="{{ route('checkout.paydisini') }}" class="hidden">
      @csrf
      <input type="hidden" name="product_id" value="{{ $product->id }}">
      <input type="hidden" name="variant_id" id="paydisiniVariantId">
      <input type="hidden" name="target" id="paydisiniTarget">
      <input type="hidden" name="email" id="paydisiniEmail">
      <input type="hidden" name="phone" id="paydisiniPhone">
      <input type="hidden" name="payment_channel" id="paydisiniChannel">
    </form>

  </section>
@endsection

@push('body')
  <script>
    document.addEventListener('DOMContentLoaded', () => {

      const rupiah = n => new Intl.NumberFormat('id-ID').format(n);

      let selectedVariant = null;
      let selectedMethod = null; // 'SALDO', 'QRIS', 'VA_MANDIRI', 'ALFAMART', 'INDOMARET'

      // DOM Elements
      const cards = document.querySelectorAll('.variant-card');
      const btnCheckout = document.getElementById('btnCheckout');
      const btnSaldo = document.getElementById('paySaldoBtn');
      const saldoWarning = document.getElementById('saldoWarning');
      const summaryWrapper = document.getElementById('summaryWrapper');

      const payButtons = document.querySelectorAll('.pickPay');

      const sVar = document.getElementById('sVar');
      const sPay = document.getElementById('sPay');
      const sSub = document.getElementById('sSub');
      const sTotal = document.getElementById('sTotal');

      const targetField = document.getElementById('fTarget');
      const emailField = document.getElementById('fEmail');
      const phoneField = document.getElementById('fPhone');

      const walletBalance = {{ (int) ($walletBalance ?? 0) }};

      // Modal saldo
      const saldoPinModal = document.getElementById('saldoPinModal');
      const btnClosePin = document.getElementById('btnClosePin');
      const modalVariantId = document.getElementById('modalVariantId');
      const modalTarget = document.getElementById('modalTarget');
      const modalEmail = document.getElementById('modalEmail');
      const modalPhone = document.getElementById('modalPhone');

      // Form Paydisini
      const paydisiniForm = document.getElementById('paydisiniForm');
      const payVariantIdInput = document.getElementById('paydisiniVariantId');
      const payTargetInput = document.getElementById('paydisiniTarget');
      const payEmailInput = document.getElementById('paydisiniEmail');
      const payPhoneInput = document.getElementById('paydisiniPhone');
      const payChannelInput = document.getElementById('paydisiniChannel');

      // ============================
      //  SELECT VARIANT
      // ============================
      function selectVariant(card) {
        cards.forEach(c =>
          c.classList.remove('ring-2', 'ring-violet-500/80', 'border-violet-600/70')
        );
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
        card.addEventListener('click', () => {
          selectVariant(card);
        });
      });

      // ============================
      //  PILIH METODE NON-SALDO
      // ============================
      payButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          const payName = btn.dataset.pay; // 'QRIS', 'VA_MANDIRI', 'ALFAMART', 'INDOMARET'
          selectedMethod = payName;

          // highlight tombol gateway
          payButtons.forEach(b => b.classList.remove('border-violet-600/70'));
          btn.classList.add('border-violet-600/70');

          // hapus highlight saldo
          if (btnSaldo) {
            btnSaldo.classList.remove('border-violet-500', 'bg-violet-500/20');
          }

          updateSummary();
        });
      });

      // ============================
      //  PILIH SALDO MAITRI
      // ============================
      if (btnSaldo) {
        btnSaldo.addEventListener('click', () => {
          selectedMethod = 'SALDO';

          // highlight saldo
          btnSaldo.classList.add('border-violet-500', 'bg-violet-500/20');

          // hapus highlight gateway
          payButtons.forEach(b => b.classList.remove('border-violet-600/70'));

          updateSummary();
        });
      }

      // ============================
      //  UPDATE SUMMARY
      // ============================
      function updateSummary() {
        sVar.textContent = selectedVariant ? selectedVariant.name : '—';

        let payText = '—';
        switch (selectedMethod) {
          case 'SALDO': payText = 'Saldo Maitri'; break;
          case 'QRIS': payText = 'QRIS'; break;
          case 'VA_MANDIRI': payText = 'VA Mandiri'; break;
          case 'ALFAMART': payText = 'Alfamart'; break;
          case 'INDOMARET': payText = 'Indomaret'; break;
        }

        sPay.textContent = payText;
        sSub.textContent = selectedVariant ? 'Rp ' + rupiah(selectedVariant.price) : 'Rp 0';
        sTotal.textContent = selectedVariant ? 'Rp ' + rupiah(selectedVariant.price) : 'Rp 0';

        const ready = !!(selectedVariant && selectedMethod);
        btnCheckout.disabled = !ready;

        if (summaryWrapper) {
          if (ready) {
            summaryWrapper.classList.remove('hidden');
          } else {
            summaryWrapper.classList.add('hidden');
          }
        }
      }

      // ============================
      //  SALDO VALIDATION
      // ============================
      function validateSaldo() {
        if (!selectedVariant || !saldoWarning) return;

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
        const phone = phoneField?.value.trim() || '';
        if (!phone) {
          alert('Isi No. WhatsApp / HP terlebih dahulu');
          return;
        }

        // ==================== SALDO ====================
        if (selectedMethod === 'SALDO') {

          @if(Auth::check())
            // isi hidden saldo form
            modalVariantId.value = selectedVariant.id;
            modalTarget.value = target;
            modalEmail.value = emailField?.value ?? '';
            modalPhone.value = phone;

            saldoPinModal.classList.remove('hidden');
            saldoPinModal.classList.add('flex');
          @else
            alert('Silakan login untuk menggunakan Saldo Maitri.');
          @endif

                      // =================== PAYDISINI ==================
                      } else {

          const channelMap = {
            'QRIS': 'qris',
            'VA_MANDIRI': 'va_mandiri',
            'ALFAMART': 'alfamart',
            'INDOMARET': 'indomaret',
          };

          const channel = channelMap[selectedMethod];
          if (!channel) {
            alert('Metode pembayaran ini belum tersedia.');
            return;
          }

          // isi hidden form Paydisini
          payVariantIdInput.value = selectedVariant.id;
          payTargetInput.value = target;
          payEmailInput.value = emailField?.value ?? '';
          payPhoneInput.value = phone;
          payChannelInput.value = channel;

          paydisiniForm.submit();
        }
      });

      if (btnClosePin) {
        btnClosePin.addEventListener('click', () => {
          saldoPinModal.classList.add('hidden');
          saldoPinModal.classList.remove('flex');
        });
      }
    });
  </script>
@endpush