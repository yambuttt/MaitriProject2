@extends('layouts.app')

@section('title', 'Detail Produk — MaitriProject')
@section('meta_description', 'Detail produk, pilihan varian, dan pembayaran.')
@section('page', 'product')

@section('content')

@push('head')
<style>
  .luxury-glass {
    background: rgba(17, 24, 39, 0.4);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.06);
  }
  .step-card {
    background: linear-gradient(180deg, rgba(30, 41, 59, 0.25) 0%, rgba(15, 23, 42, 0.5) 100%);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(139, 92, 246, 0.1);
    border-radius: 2rem;
    padding: 1.5rem;
    transition: all 0.3s ease;
  }
  .step-card:hover {
    border-color: rgba(139, 92, 246, 0.25);
    box-shadow: 0 10px 30px -10px rgba(139, 92, 246, 0.15);
  }
  .variant-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(17, 24, 39, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.05);
  }
  .variant-card:not([disabled]):hover {
    background: rgba(139, 92, 246, 0.05);
    border-color: rgba(139, 92, 246, 0.3);
    transform: translateY(-2px);
  }
  .variant-card.ring-2 {
    background: rgba(139, 92, 246, 0.1) !important;
    border-color: #8B5CF6 !important;
    box-shadow: 0 0 20px rgba(139, 92, 246, 0.25) !important;
  }
  .text-gradient {
    background: linear-gradient(to right, #C4B5FD, #A78BFA);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
</style>
@endpush

<section class="min-h-screen pb-24 pt-8 bg-[#050810] relative overflow-hidden">
  
  {{-- Ambient Lights --}}
  <div class="fixed inset-0 pointer-events-none -z-10">
    <div class="absolute top-[10%] left-[20%] w-[500px] h-[500px] bg-violet-900/10 blur-[130px] rounded-full"></div>
    <div class="absolute bottom-[20%] right-[10%] w-[500px] h-[500px] bg-fuchsia-900/10 blur-[120px] rounded-full"></div>
  </div>

  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">

    {{-- Breadcrumb --}}
    <div class="mb-6 reveal">
      <a href="{{ route('catalog') }}" class="group inline-flex items-center gap-2 text-sm text-slate-400 hover:text-violet-400 transition-colors">
        <svg class="size-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali ke katalog
      </a>
    </div>

    <div class="space-y-6">

      {{-- ==============================
           1. HERO SECTION (PRODUCT INFO)
           ============================== --}}
      <div class="relative overflow-hidden rounded-[2.5rem] p-6 md:p-10 luxury-glass shadow-2xl reveal">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-violet-600/20 blur-[100px] rounded-full pointer-events-none"></div>
        
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 relative z-10">
          {{-- Thumbnail --}}
          <div class="flex-shrink-0">
            <div class="w-36 h-36 md:w-44 md:h-44 rounded-[2rem] border border-white/10 bg-black/40 overflow-hidden shadow-2xl">
              @if(!empty($product->thumbnail))
                <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
              @else
                <div class="w-full h-full flex flex-col items-center justify-center gap-2 text-slate-600">
                  <svg class="size-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  <span class="text-[10px] uppercase font-bold tracking-widest">No Image</span>
                </div>
              @endif
            </div>
          </div>

          {{-- Meta Info --}}
          <div class="flex-1 text-center md:text-left space-y-4">
            <div>
              <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] font-bold tracking-widest text-violet-300 uppercase mb-2">
                <span>{{ $product->category?->name ?? 'Layanan' }}</span>
                @if($product->subcategory)
                  <span class="w-1 h-1 rounded-full bg-slate-600"></span>
                  <span class="text-slate-400">{{ $product->subcategory->name }}</span>
                @endif
              </div>
              <h1 id="pName" class="text-3xl md:text-5xl font-extrabold text-white leading-tight">
                {{ $product->name }}
              </h1>
              @if($product->provider)
                <p id="pMeta" class="mt-1 text-sm text-slate-400 font-medium">
                  Provider Resmi: <span class="text-white">{{ $product->provider }}</span>
                </p>
              @endif
            </div>

            {{-- Badges --}}
            <div class="flex flex-wrap justify-center md:justify-start gap-2 text-[11px] font-semibold text-slate-300">
              <span class="inline-flex items-center gap-1.5 rounded-xl bg-white/5 border border-white/10 px-3 py-1.5 backdrop-blur-md">
                ⚡ <span>Proses Cepat</span>
              </span>
              <span class="inline-flex items-center gap-1.5 rounded-xl bg-white/5 border border-white/10 px-3 py-1.5 backdrop-blur-md">
                💬 <span>Aktif 24/7</span>
              </span>
              <span class="inline-flex items-center gap-1.5 rounded-xl bg-white/5 border border-white/10 px-3 py-1.5 backdrop-blur-md">
                🛡️ <span>Aman & Legal</span>
              </span>
            </div>

            {{-- Description (Desktop only) --}}
            <div id="pDescription" class="text-sm leading-relaxed text-slate-400 hidden md:block max-w-3xl pt-2 border-t border-white/5">
              {!! nl2br(e($product->description)) !!}
            </div>
          </div>
        </div>
      </div>

      {{-- ==============================
           2. MOBILE TABS
           ============================== --}}
      <div class="md:hidden reveal">
        <div class="flex rounded-2xl bg-slate-900/60 border border-white/5 p-1 gap-1">
          <button id="tabTransaksi" type="button" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold transition-all bg-slate-800 text-white shadow-lg">
            Transaksi
          </button>
          <button id="tabKeterangan" type="button" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold text-slate-400 transition-all">
            Keterangan
          </button>
        </div>
      </div>

      {{-- ==============================
           3. MAIN GRID (STEPS & SUMMARY)
           ============================== --}}
      <div class="grid gap-6 lg:grid-cols-[minmax(0,7.2fr)_minmax(0,3.8fr)] items-start">

        {{-- LEFT COLUMN: STEP TRANSAKSI --}}
        <div class="space-y-6" id="panelTransaksi">

          {{-- STEP 1: TARGET --}}
          <div class="step-card reveal">
            <div class="flex items-center gap-3 pb-4 border-b border-white/5">
              <span class="flex items-center justify-center size-8 rounded-xl bg-violet-600/10 border border-violet-500/30 text-violet-300 font-extrabold text-sm shadow-[0_0_15px_rgba(139,92,246,0.15)]">1</span>
              <h2 class="text-lg font-bold text-white">Lengkapi Data Target</h2>
            </div>

            <div class="mt-5 space-y-4">
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">User ID / Nomor Tujuan</label>
                <input id="fTarget" name="target" type="text" placeholder="Masukkan ID game atau nomor HP tujuan..."
                       class="h-12 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-sm text-white placeholder-slate-500 outline-none focus:border-violet-500/50 focus:bg-black/60 focus:ring-0 transition-all">
                <p id="fTargetHelp" class="mt-2 text-xs text-slate-500 leading-normal">
                  Contoh: 12345678(1234) untuk game, atau 081234567890 untuk pulsa/data.
                </p>
              </div>
            </div>
          </div>

          {{-- STEP 2: NOMINAL --}}
          <div class="step-card reveal">
            <div class="flex items-center gap-3 pb-4 border-b border-white/5">
              <span class="flex items-center justify-center size-8 rounded-xl bg-violet-600/10 border border-violet-500/30 text-violet-300 font-extrabold text-sm shadow-[0_0_15px_rgba(139,92,246,0.15)]">2</span>
              <h2 class="text-lg font-bold text-white">Pilih Nominal Varian</h2>
            </div>

            <div id="variantGrid" class="mt-5 grid grid-cols-2 sm:grid-cols-3 gap-3 md:gap-4">
              @foreach($product->variants as $v)
                @php
                  $master = $v->digiflazzVariant;
                  $sellerActive = $master?->seller_product_status ?? true;
                  $statusText = strtolower($master->status ?? '');
                  $startCut = $master->raw['start_cut_off'] ?? null;
                  $endCut = $master->raw['end_cut_off'] ?? null;

                  $now = \Carbon\Carbon::now(config('app.timezone'));
                  $nowMinutes = $now->hour * 60 + $now->minute;
                  $isInCutoff = false;

                  if ($startCut && $endCut) {
                    try {
                      [$sh, $sm] = array_map('intval', explode(':', $startCut));
                      [$eh, $em] = array_map('intval', explode(':', $endCut));
                      $startMinutes = $sh * 60 + $sm;
                      $endMinutes = $eh * 60 + $em;
                      if ($startMinutes <= $endMinutes) {
                        $isInCutoff = $nowMinutes >= $startMinutes && $nowMinutes <= $endMinutes;
                      } else {
                        $isInCutoff = $nowMinutes >= $startMinutes || $nowMinutes <= $endMinutes;
                      }
                    } catch (\Throwable $e) { $isInCutoff = false; }
                  }

                  $isGangguan = !$sellerActive || str_contains($statusText, 'gangguan') || $isInCutoff;
                @endphp

                <button type="button" class="variant-card relative rounded-2xl p-4 text-left overflow-hidden flex flex-col justify-between h-full min-h-[100px]
                     {{ $isGangguan ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}"
                  data-variant-id="{{ $v->id }}" 
                  data-variant-name="{{ $v->name }}"
                  data-variant-price="{{ $v->final_price }}" 
                  @if($isGangguan) data-disabled="1" disabled @endif>
                  
                  <div>
                    <div class="text-xs font-bold text-slate-200 line-clamp-2 leading-snug mb-1">{{ $v->name }}</div>
                    <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">{{ $v->buyer_sku_code }}</div>
                  </div>

                  <div class="mt-4 pt-3 border-t border-white/5 flex items-center justify-between w-full">
                    <span class="text-xs font-extrabold text-white">
                      Rp {{ number_format($v->final_price, 0, ',', '.') }}
                    </span>
                  </div>

                  @if(!$sellerActive)
                    <div class="absolute inset-0 bg-black/80 flex items-center justify-center p-2 text-center text-[9px] font-bold text-rose-400">Nonaktif</div>
                  @elseif($isInCutoff)
                    <div class="absolute inset-0 bg-black/80 flex items-center justify-center p-2 text-center text-[9px] font-bold text-amber-400">Maintenance</div>
                  @elseif(str_contains($statusText, 'gangguan'))
                    <div class="absolute inset-0 bg-black/80 flex items-center justify-center p-2 text-center text-[9px] font-bold text-amber-400">Gangguan</div>
                  @endif
                </button>
              @endforeach
            </div>
          </div>

          {{-- STEP 3: PEMBAYARAN --}}
          <div class="step-card reveal">
            <div class="flex items-center gap-3 pb-4 border-b border-white/5">
              <span class="flex items-center justify-center size-8 rounded-xl bg-violet-600/10 border border-violet-500/30 text-violet-300 font-extrabold text-sm shadow-[0_0_15px_rgba(139,92,246,0.15)]">3</span>
              <h2 class="text-lg font-bold text-white">Metode Pembayaran</h2>
            </div>

            <div id="payList" class="mt-5 space-y-3">
              {{-- Saldo Maitri --}}
              @auth
                @php
                  $formattedSaldo = 'Rp ' . number_format($walletBalance ?? 0, 0, ',', '.');
                @endphp
                <div class="group rounded-2xl border border-white/5 p-4 bg-[#111827]/30 flex items-center justify-between transition-all hover:bg-slate-800/40">
                  <div class="space-y-1">
                    <div class="text-sm font-bold text-slate-200">Saldo Maitri</div>
                    <div class="text-xs text-slate-400">Tersedia: <span class="text-violet-300 font-semibold">{{ $formattedSaldo }}</span></div>
                    <div id="saldoWarning" class="text-[10px] text-rose-400 font-semibold hidden">Saldo Anda tidak mencukupi</div>
                  </div>
                  <button type="button" id="paySaldoBtn" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-xs font-bold text-slate-300 hover:bg-violet-600 hover:border-violet-500 hover:text-white transition-all">
                    Pilih
                  </button>
                </div>
              @else
                <div class="rounded-2xl border border-white/5 bg-slate-900/40 p-4 text-xs font-semibold text-slate-500 text-center">
                  Silakan login terlebih dahulu untuk menggunakan Saldo Maitri.
                </div>
              @endauth

              {{-- Gateway Paydisini Channels --}}
              @foreach([
                ['QRIS', 'QRIS Instan', 'Otomatis aktif & support semua e-wallet'],
                ['VA_MANDIRI', 'Virtual Account Mandiri', 'Pembayaran via Bank Mandiri virtual account'],
                ['ALFAMART', 'Alfamart', 'Bayar tunai di gerai Alfamart terdekat'],
                ['INDOMARET', 'Indomaret', 'Bayar tunai di gerai Indomaret terdekat']
              ] as $ch)
                <div class="group rounded-2xl border border-white/5 p-4 bg-[#111827]/30 flex items-center justify-between transition-all hover:bg-slate-800/40">
                  <div class="space-y-0.5">
                    <div class="text-sm font-bold text-slate-200">{{ $ch[1] }}</div>
                    <div class="text-xs text-slate-500 leading-normal">{{ $ch[2] }}</div>
                  </div>
                  <button type="button" class="pickPay px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-xs font-bold text-slate-300 hover:bg-violet-600 hover:border-violet-500 hover:text-white transition-all" data-pay="{{ $ch[0] }}">
                    Pilih
                  </button>
                </div>
              @endforeach
            </div>
          </div>

          {{-- STEP 4: KONTAK --}}
          <div class="step-card reveal">
            <div class="flex items-center gap-3 pb-4 border-b border-white/5">
              <span class="flex items-center justify-center size-8 rounded-xl bg-violet-600/10 border border-violet-500/30 text-violet-300 font-extrabold text-sm shadow-[0_0_15px_rgba(139,92,246,0.15)]">4</span>
              <h2 class="text-lg font-bold text-white">Detail Kontak</h2>
            </div>

            <div class="mt-5 space-y-4">
              <div class="grid sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Email (Untuk Bukti)</label>
                  <input id="fEmail" name="email" type="email" placeholder="nama@email.com"
                         class="h-12 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-sm text-white placeholder-slate-500 outline-none focus:border-violet-500/50 focus:bg-black/60 focus:ring-0 transition-all">
                </div>
                <div>
                  <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">No. WhatsApp / HP</label>
                  <input id="fPhone" name="phone" type="tel" placeholder="08xxxxxxxxxx"
                         class="h-12 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-sm text-white placeholder-slate-500 outline-none focus:border-violet-500/50 focus:bg-black/60 focus:ring-0 transition-all">
                </div>
              </div>
              <p class="text-xs text-slate-500 leading-normal">
                Detail pemesanan dan bukti pembayaran akan kami koordinasikan melalui WhatsApp dan Email yang Anda masukkan.
              </p>
            </div>
          </div>

        </div>

        {{-- RIGHT COLUMN: SIDEBAR SUMMARY / DETAILS --}}
        <aside class="space-y-6" id="panelKeterangan">

          {{-- Mobile Description Tab Content --}}
          <div class="step-card md:hidden reveal">
            <h2 class="text-sm font-bold text-slate-300 uppercase tracking-wider mb-2">Deskripsi Layanan</h2>
            <div class="text-sm leading-relaxed text-slate-400">
              {!! nl2br(e($product->description)) !!}
            </div>
          </div>

          {{-- CHECKOUT SUMMARY CARD --}}
          <div id="summaryWrapper" class="hidden md:block rounded-[2rem] border border-violet-500/30 bg-gradient-to-b from-[#1E1B4B]/30 to-[#0F172A]/70 p-6 space-y-6 shadow-2xl reveal">
            <div class="pb-4 border-b border-white/5 flex items-center justify-between">
              <h2 class="text-base font-bold text-white">Detail Pembelian</h2>
              <span class="text-[10px] font-bold tracking-widest text-violet-300 uppercase">Verifikasi</span>
            </div>

            <dl class="space-y-3.5 text-sm text-slate-300" id="summaryBox">
              <div class="flex justify-between gap-4">
                <dt class="text-slate-500">Nama Layanan</dt>
                <dd class="text-right font-semibold text-white" id="sProd">{{ $product->name }}</dd>
              </div>
              <div class="flex justify-between gap-4">
                <dt class="text-slate-500">Nominal Varian</dt>
                <dd class="text-right font-semibold text-white truncate max-w-[150px]" id="sVar">—</dd>
              </div>
              <div class="flex justify-between gap-4">
                <dt class="text-slate-500">Metode Pembayaran</dt>
                <dd class="text-right font-semibold text-white" id="sPay">—</dd>
              </div>
              <div class="flex justify-between gap-4">
                <dt class="text-slate-500">Harga Item</dt>
                <dd class="text-right font-semibold text-white" id="sSub">Rp 0</dd>
              </div>
              <div class="flex justify-between gap-4">
                <dt class="text-slate-500">Biaya Administrasi</dt>
                <dd class="text-right font-semibold text-white" id="sFee">Rp 0</dd>
              </div>
              <div class="flex justify-between gap-4 pt-4 border-t border-white/5 mt-2">
                <dt class="text-sm font-bold text-slate-400">Total Pembayaran</dt>
                <dd class="text-lg font-extrabold text-white text-gradient" id="sTotal">Rp 0</dd>
              </div>
            </dl>

            <button id="btnCheckout" class="w-full h-12 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-bold text-white tracking-wide transition-all shadow-[0_0_20px_rgba(139,92,246,0.3)] hover:shadow-[0_0_30px_rgba(139,92,246,0.5)] disabled:opacity-40 disabled:cursor-not-allowed" disabled>
              Lanjutkan Transaksi
            </button>
            
            <p class="text-[10px] text-slate-500 text-center leading-normal">
              Dengan mengklik tombol, Anda menyetujui seluruh ketentuan layanan di platform MaitriProject.
            </p>
          </div>

          {{-- RATINGS CARD --}}
          <div class="step-card reveal">
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4">Ulasan & Reputasi</h2>
            <div class="flex items-center gap-4">
              <div class="flex items-baseline gap-1">
                <span class="text-4xl font-extrabold text-white">4.9</span>
                <span class="text-xs text-slate-500 font-bold">/ 5.0</span>
              </div>
              <div class="space-y-1">
                <div class="flex items-center gap-0.5 text-amber-400 text-base font-bold">
                  ★★★★★
                </div>
                <p class="text-[10px] text-slate-500 font-medium leading-none">Berdasarkan 1.842 review pelanggan</p>
              </div>
            </div>
          </div>

          {{-- SUPPORT CARD --}}
          <div class="step-card flex gap-4 items-start reveal">
            <div class="flex-shrink-0 size-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-300">
              <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
            </div>
            <div class="space-y-1">
              <h2 class="text-sm font-bold text-slate-200">Layanan Bantuan</h2>
              <p class="text-xs text-slate-500 leading-normal">
                Mengalami kendala pemesanan? Layanan CS kami siap siaga 24 jam melalui tautan menu Hubungi Admin.
              </p>
            </div>
          </div>

        </aside>
      </div>

    </div>
  </div>

  {{-- =============== MODAL PIN SALDO MAITRI ============== --}}
  @auth
    <div id="saldoPinModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-md">
      <div class="w-full max-w-md mx-4 rounded-3xl bg-slate-950 border border-white/10 p-6 space-y-5 shadow-2xl">
        <div class="space-y-2">
          <h2 class="text-lg font-bold text-white">Konfirmasi PIN Keamanan</h2>
          <p class="text-xs text-slate-400 leading-normal">
            Masukkan PIN akun Maitri Anda demi menjaga keamanan proses debet saldo pembayaran.
          </p>
        </div>

        <form method="POST" action="{{ route('checkout.saldo') }}" id="saldoPinForm" class="space-y-4">
          @csrf
          <input type="hidden" name="product_id" value="{{ $product->id }}">
          <input type="hidden" name="variant_id" id="modalVariantId">
          <input type="hidden" name="target" id="modalTarget">
          <input type="hidden" name="email" id="modalEmail">
          <input type="hidden" name="phone" id="modalPhone">

          <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">PIN Keamanan</label>
            <input type="password" maxlength="6" name="pin" placeholder="Masukkan 6-Digit PIN Anda..." required
                   class="h-12 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-sm text-white placeholder-slate-600 outline-none focus:border-violet-500/50 text-center tracking-widest font-extrabold">
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button type="button" id="btnClosePin" class="h-10 px-4 rounded-xl text-xs font-bold text-slate-400 hover:bg-white/5 transition-all">
              Batal
            </button>
            <button type="submit" class="h-10 px-5 rounded-xl bg-violet-600 hover:bg-violet-500 text-xs font-bold text-white transition-all shadow-lg">
              Konfirmasi & Bayar
            </button>
          </div>
        </form>
      </div>
    </div>
  @endauth

  {{-- =========== FORM HIDDEN CHECKOUT PAYDISINI =========== --}}
  <form id="paydisiniForm" method="POST" action="{{ route('checkout.paydisini') }}" class="hidden">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="variant_id" id="paydisiniVariantId">
    <input type="hidden" name="target" id="paydisiniTarget">
    <input type="hidden" name="email" id="paydisiniEmail">
    <input type="hidden" name="phone" id="paydisiniPhone">
    <input type="hidden" name="payment_channel" id="paydisiniChannel">
  </form>

  {{-- STICKY CHECKOUT BAR (MOBILE) --}}
  <div id="checkoutBar" class="fixed inset-x-0 bottom-0 z-40 hidden md:hidden bg-[#0c101a]/95 border-t border-white/5 backdrop-blur-xl shadow-2xl">
    <div class="mx-auto max-w-[1280px] px-4 py-3 space-y-3">
      
      {{-- Mobile Summary Box --}}
      <div id="summaryWrapperMobile" class="rounded-2xl border border-violet-500/20 bg-[#111826]/80 px-4 py-3 text-xs text-slate-200 hidden shadow-inner">
        <div class="flex items-center justify-between gap-4 pb-2 border-b border-white/5">
          <div class="min-w-0">
            <p class="font-bold text-white truncate">{{ $product->name }}</p>
            <p id="sHeaderMobile" class="text-[10px] text-slate-400 truncate">Pilih varian & metode pembayaran.</p>
          </div>
          <p id="sTotalShortMobile" class="text-sm font-extrabold text-white text-gradient whitespace-nowrap">Rp 0</p>
        </div>

        <dl class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-[10px]">
          <div class="flex justify-between gap-1">
            <dt class="text-slate-500">Varian</dt>
            <dd id="sVarMobile" class="text-right font-semibold text-slate-300 truncate max-w-[80px]">—</dd>
          </div>
          <div class="flex justify-between gap-1">
            <dt class="text-slate-500">Metode</dt>
            <dd id="sPayMobile" class="text-right font-semibold text-slate-300">—</dd>
          </div>
          <div class="flex justify-between gap-1">
            <dt class="text-slate-500">Subtotal</dt>
            <dd id="sSubMobile" class="text-right font-semibold text-slate-300">Rp 0</dd>
          </div>
          <div class="flex justify-between gap-1">
            <dt class="text-slate-500">Total</dt>
            <dd id="sTotalMobile" class="text-right font-bold text-white">Rp 0</dd>
          </div>
        </dl>
      </div>

      <button id="btnCheckoutMobile" class="w-full h-12 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-bold text-white tracking-wide transition-all shadow-[0_0_20px_rgba(139,92,246,0.3)]">
        Pesan Sekarang!
      </button>
    </div>
  </div>

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
      const btnCheckoutMobile = document.getElementById('btnCheckoutMobile');
      const checkoutBar = document.getElementById('checkoutBar');

      const btnSaldo = document.getElementById('paySaldoBtn');
      const saldoWarning = document.getElementById('saldoWarning');
      const summaryWrapper = document.getElementById('summaryWrapper');

      const payButtons = document.querySelectorAll('.pickPay');

      const sVar = document.getElementById('sVar');
      const sPay = document.getElementById('sPay');
      const sSub = document.getElementById('sSub');
      const sFee = document.getElementById('sFee');   // 👈 baru
      const sTotal = document.getElementById('sTotal');

      // Elemen ringkasan MOBILE
      const summaryWrapperMobile = document.getElementById('summaryWrapperMobile');
      const sHeaderMobile = document.getElementById('sHeaderMobile');
      const sVarMobile = document.getElementById('sVarMobile');
      const sPayMobile = document.getElementById('sPayMobile');
      const sSubMobile = document.getElementById('sSubMobile');
      const sTotalMobile = document.getElementById('sTotalMobile');
      const sTotalShortMobile = document.getElementById('sTotalShortMobile');


      const targetField = document.getElementById('fTarget');
      const emailField = document.getElementById('fEmail');
      const phoneField = document.getElementById('fPhone');

      const walletBalance = {{ (int) ($walletBalance ?? 0) }};

      // Mobile tabs
      const tabTransaksi = document.getElementById('tabTransaksi');
      const tabKeterangan = document.getElementById('tabKeterangan');
      const panelTransaksi = document.getElementById('panelTransaksi');
      const panelKeterangan = document.getElementById('panelKeterangan');

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
          if (card.dataset.disabled === '1') {
            alert('Varian ini sedang gangguan / nonaktif / maintenance. Silakan pilih nominal lain.');
            return;
          }

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

      function calculateAdminFee(method, subtotal) {
        if (!subtotal || subtotal <= 0) return 0;

        switch (method) {
          case 'QRIS':
            // 0.7% dibulatkan ke atas (supaya minimal sama dengan yg dibebankan gateway)
            return Math.ceil(subtotal * 0.007);
          case 'VA_MANDIRI':
          case 'ALFAMART':
          case 'INDOMARET':
            return 2500;
          default:
            return 0;
        }
      }

      // ============================
      //  UPDATE SUMMARY
      // ============================
      function updateSummary() {
        let payText = '—';
        switch (selectedMethod) {
          case 'SALDO': payText = 'Saldo Maitri'; break;
          case 'QRIS': payText = 'QRIS'; break;
          case 'VA_MANDIRI': payText = 'VA Mandiri'; break;
          case 'ALFAMART': payText = 'Alfamart'; break;
          case 'INDOMARET': payText = 'Indomaret'; break;
        }

        const hasVariant = !!selectedVariant;
        const ready = !!(selectedVariant && selectedMethod);

        const subtotal = hasVariant ? selectedVariant.price : 0;

        // Biaya admin hanya untuk gateway Paydisini (QRIS/VA/Alfa/Indo), saldo = 0
        let adminFee = 0;
        if (ready && selectedMethod && selectedMethod !== 'SALDO') {
          adminFee = calculateAdminFee(selectedMethod, subtotal);
        }

        const total = subtotal + adminFee;

        const subText = 'Rp ' + rupiah(subtotal);
        const totalText = 'Rp ' + rupiah(total);

        // --- RINGKASAN DESKTOP (kanan) ---
        if (sVar) sVar.textContent = hasVariant ? selectedVariant.name : '—';
        if (sPay) sPay.textContent = payText;
        if (sSub) sSub.textContent = subText;
        if (sFee) sFee.textContent = 'Rp ' + rupiah(adminFee);
        if (sTotal) sTotal.textContent = totalText;

        // TAMPILKAN summaryWrapper HANYA di desktop (>= 768px)
        if (summaryWrapper) {
          if (ready && window.innerWidth >= 768) {
            summaryWrapper.classList.remove('hidden');
          } else {
            summaryWrapper.classList.add('hidden');
          }
        }

        // --- RINGKASAN MOBILE (sticky bar) ---
        if (sVarMobile) sVarMobile.textContent = hasVariant ? selectedVariant.name : '—';
        if (sPayMobile) sPayMobile.textContent = payText;
        if (sSubMobile) sSubMobile.textContent = subText;
        if (sTotalMobile) sTotalMobile.textContent = totalText;
        if (sTotalShortMobile) sTotalShortMobile.textContent = totalText;
        if (sHeaderMobile) {
          sHeaderMobile.textContent = hasVariant
            ? `Varian: ${selectedVariant.name}` + (payText !== '—' ? ' • ' + payText : '')
            : 'Pilih varian & metode pembayaran dulu.';
        }

        if (summaryWrapperMobile) {
          summaryWrapperMobile.classList.toggle('hidden', !ready);
        }

        // --- TOMBOL CHECKOUT (desktop + sticky mobile) ---
        if (btnCheckout) btnCheckout.disabled = !ready;
        if (btnCheckoutMobile) btnCheckoutMobile.disabled = !ready;

        if (checkoutBar) {
          checkoutBar.classList.toggle('hidden', !ready);
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
      //  CHECKOUT HANDLING (dipakai desktop & mobile)
      // ============================
      function handleCheckout() {

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
      }

      if (btnCheckout) {
        btnCheckout.addEventListener('click', handleCheckout);
      }
      if (btnCheckoutMobile) {
        btnCheckoutMobile.addEventListener('click', handleCheckout);
      }

      // ============================
      //  MODAL PIN CLOSE
      // ============================
      if (btnClosePin) {
        btnClosePin.addEventListener('click', () => {
          saldoPinModal.classList.add('hidden');
          saldoPinModal.classList.remove('flex');
        });
      }

      // ============================
      //  MOBILE TABS LOGIC
      // ============================
      function setTab(which) {
        if (!tabTransaksi || !tabKeterangan || !panelTransaksi || !panelKeterangan) return;
        const isMobile = window.innerWidth < 768;
        if (!isMobile) return;

        const isTransaksi = which === 'transaksi';

        panelTransaksi.classList.toggle('hidden', !isTransaksi);
        panelKeterangan.classList.toggle('hidden', isTransaksi);

        tabTransaksi.classList.toggle('bg-slate-800', isTransaksi);
        tabTransaksi.classList.toggle('text-slate-50', isTransaksi);
        tabTransaksi.classList.toggle('text-slate-400', !isTransaksi);

        tabKeterangan.classList.toggle('bg-slate-800', !isTransaksi);
        tabKeterangan.classList.toggle('text-slate-50', !isTransaksi);
        tabKeterangan.classList.toggle('text-slate-400', isTransaksi);
      }

      if (tabTransaksi && tabKeterangan) {
        tabTransaksi.addEventListener('click', () => setTab('transaksi'));
        tabKeterangan.addEventListener('click', () => setTab('keterangan'));

        // initial state mobile
        setTab('transaksi');

        // kalau resize ke desktop, pastikan dua panel muncul lagi
        window.addEventListener('resize', () => {
          if (window.innerWidth >= 768) {
            panelTransaksi.classList.remove('hidden');
            panelKeterangan.classList.remove('hidden');
          } else {
            setTab('transaksi');
          }
        });
      }
    });
  </script>
@endpush