@extends('layouts.app')

@section('title', 'Detail Produk — MaitriProject')
@section('meta_description', 'Detail produk, pilihan varian, dan pembayaran.')
@section('page', 'product')

@section('content')
<section class="py-8" id="productPage" data-slug="{{ $product->slug }}">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
    <a href="{{ route('catalog') }}" class="text-sm text-slate-400 hover:text-slate-200">← Kembali ke kataloggg</a>

    <div class="mt-4 grid lg:grid-cols-12 gap-6">
      {{-- Kiri: Info produk & Form akun --}}
      <aside class="lg:col-span-5 space-y-4">
        <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-5">
          <div class="flex items-center gap-3">
            <div class="size-10 rounded-xl bg-violet-600/20 grid place-items-center text-violet-300">
              <svg class="size-5" viewBox="0 0 24 24" fill="none">
                <path d="M13 3 4 14h7l-1 7 9-11h-7l1-7Z" stroke="currentColor" stroke-width="1.5" />
              </svg>
            </div>
            <div class="min-w-0">
              <h1 id="pName" class="text-xl font-semibold truncate">{{ $product->name }}</h1>
              <p id="pMeta" class="text-sm text-slate-400">
                {{ $product->category?->name ?? '—' }}
                @if($product->subcategory) • {{ $product->subcategory->name }} @endif
                @if($product->provider) • {{ $product->provider }} @endif
              </p>
            </div>
          </div>

          <div class="mt-4 space-y-3 text-sm leading-relaxed text-slate-300" id="pDescription">
            {{ $product->description ?? '—' }}
          </div>
        </div>

        <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-5">
          <div class="flex items-center gap-2 text-slate-300">
            <div class="size-6 grid place-items-center rounded-full border border-slate-700">1</div>
            <h2 class="font-medium">Masukkan Data Akun</h2>
          </div>

          <div class="mt-4 space-y-3">
            <div>
              <label class="text-sm text-slate-400">User ID / Nomor Tujuan</label>
              <input id="fTarget" type="text" placeholder="Masukkan User ID atau Nomor"
                class="mt-1 w-full rounded-xl bg-[#0E1524] border border-slate-800/70 px-3 py-2 text-sm outline-none focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
              <p id="fTargetHelp" class="mt-1 text-xs text-slate-500">Contoh: 081234567890 atau 12345678(1234).</p>
            </div>
            <div>
              <label class="text-sm text-slate-400">Email (untuk bukti pembayaran)</label>
              <input id="fEmail" type="email" placeholder="nama@email.com"
                class="mt-1 w-full rounded-xl bg-[#0E1524] border border-slate-800/70 px-3 py-2 text-sm outline-none focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
            </div>
          </div>
        </div>
      </aside>

      {{-- Kanan: Varian & Pembayaran --}}
      <section class="lg:col-span-7 space-y-4">
        {{-- PILIH NOMINAL --}}
        <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-5">
          <div class="flex items-center gap-2 text-slate-300">
            <div class="size-6 grid place-items-center rounded-full border border-slate-700">2</div>
            <h2 class="font-medium">Pilih Nominal</h2>
          </div>

          {{-- Tabs varian group (sederhana: hanya "Umum" untuk sekarang) --}}
          <div id="variantTabs" class="mt-3 flex flex-wrap gap-2">
            <button type="button"
              class="px-3 py-1.5 rounded-full border border-violet-600/70 bg-violet-600/10 text-violet-300 text-sm cursor-default">
              Umum
            </button>
          </div>

          {{-- Grid varian --}}
          {{-- Grid varian (selectable cards) --}}
          <div id="variantGrid" class="mt-4 grid sm:grid-cols-2 xl:grid-cols-3 gap-3">
            @foreach($product->variants as $v)
              <button type="button"
                class="variant-card rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4 text-left hover:border-slate-700 transition"
                data-variant-id="{{ $v->id }}" data-variant-name="{{ $v->name }}"
                data-variant-sku="{{ $v->buyer_sku_code }}" data-variant-price="{{ $v->final_price }}">
                <div class="text-sm font-medium">{{ $v->name }}</div>
                <div class="text-xs text-slate-400 mt-0.5">{{ $v->buyer_sku_code }}</div>

                <div class="mt-3 font-semibold">
                  Rp {{ number_format($v->final_price, 0, ',', '.') }}
                </div>
              </button>
            @endforeach
          </div>


        </div>

        {{-- METODE BAYAR (placeholder; bisa diisi dinamis nanti) --}}
        <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-5">
          <div class="flex items-center gap-2 text-slate-300">
            <div class="size-6 grid place-items-center rounded-full border border-slate-700">3</div>
            <h2 class="font-medium">Pilih Pembayaran</h2>
          </div>

          <div id="payList" class="mt-3 space-y-2">
            {{-- contoh statis dulu; ganti dengan accordion/metode asli nanti --}}
            <div class="rounded-xl border border-slate-800/70 p-3 flex items-center justify-between">
              <div class="text-sm">QRIS</div>
              <button class="px-3 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 text-sm pickPay"
                data-pay="QRIS">Pilih</button>
            </div>
            <div class="rounded-xl border border-slate-800/70 p-3 flex items-center justify-between">
              <div class="text-sm">Virtual Account</div>
              <button class="px-3 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 text-sm pickPay"
                data-pay="VA">Pilih</button>
            </div>
            <div class="rounded-xl border border-slate-800/70 p-3 flex items-center justify-between">
              <div class="text-sm">OVO</div>
              <button class="px-3 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 text-sm pickPay"
                data-pay="OVO">Pilih</button>
            </div>
          </div>
        </div>

        {{-- RINGKASAN --}}
        <div class="rounded-3xl border border-slate-800/70 bg-[#111826] p-5">
          <div class="flex items-start md:items-center flex-col md:flex-row md:justify-between gap-4">
            <div class="text-sm text-slate-300 space-y-1" id="summaryBox">
              <div><span class="text-slate-400">Produk:</span> <span id="sProd">{{ $product->name }}</span></div>
              <div><span class="text-slate-400">Varian:</span> <span id="sVar">—</span></div>
              <div><span class="text-slate-400">Metode:</span> <span id="sPay">—</span></div>
              <div><span class="text-slate-400">Subtotal:</span> <span id="sSub">Rp 0</span></div>
              <div><span class="text-slate-400">Biaya Admin:</span> <span id="sFee">Rp 0</span></div>
              <div class="text-lg font-semibold"><span class="text-slate-400">Total:</span> <span id="sTotal">Rp
                  0</span></div>
            </div>
            <button id="btnCheckout"
              class="w-full md:w-auto px-5 py-3 rounded-2xl bg-violet-600 hover:bg-violet-500 disabled:opacity-50 disabled:cursor-not-allowed"
              disabled>
              Lanjutkan Pembayaran
            </button>
          </div>
        </div>
      </section>
    </div>
  </div>
</section>

{{-- Inisialisasi ringan (optional) --}}
@push('body')
  <script>
    (function () {
      const rupiah = n => new Intl.NumberFormat('id-ID').format(n);
      let chosen = { varId: null, varName: '', varPrice: 0, pay: '' };

      function refreshSummary() {
        document.getElementById('sVar').textContent = chosen.varName || '—';
        document.getElementById('sPay').textContent = chosen.pay || '—';
        document.getElementById('sSub').textContent = 'Rp ' + rupiah(chosen.varPrice || 0);
        document.getElementById('sFee').textContent = 'Rp ' + rupiah(0);
        document.getElementById('sTotal').textContent = 'Rp ' + rupiah(chosen.varPrice || 0);
        document.getElementById('btnCheckout').disabled = !(chosen.varId && chosen.pay);
      }

      // --- Variant selection (card style)
      const cards = Array.from(document.querySelectorAll('.variant-card'));
      function selectCard(card) {
        cards.forEach(c => c.classList.remove('ring-2', 'ring-violet-500/70', 'border-violet-600/70'));
        card.classList.add('ring-2', 'ring-violet-500/70', 'border-violet-600/70');

        chosen.varId = card.dataset.variantId;
        chosen.varName = card.dataset.variantName;
        chosen.varPrice = parseInt(card.dataset.variantPrice || '0', 10);
        refreshSummary();
      }
      cards.forEach(c => c.addEventListener('click', () => selectCard(c)));

      // Preselect first variant (jika ada)
      if (cards.length) selectCard(cards[0]);

      // --- Payment selection (pakai tombol .pickPay yang sudah ada)
      document.querySelectorAll('.pickPay').forEach(btn => {
        btn.addEventListener('click', () => {
          // visual hint sederhana
          document.querySelectorAll('.pickPay').forEach(b => b.classList.remove('border-violet-600/70'));
          btn.classList.add('border-violet-600/70');

          chosen.pay = btn.dataset.pay || btn.textContent.trim();
          refreshSummary();
        });
      });
    })();
  </script>
@endpush