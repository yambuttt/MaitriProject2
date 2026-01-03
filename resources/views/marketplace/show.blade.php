@extends('layouts.app')

@section('title', $product->name.' — Marketplace')
@section('page','marketplace-product')

@php
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Str;

  $images = $product->images ?? collect();

  // ambil min price (buat badge "Mulai dari")
  $minPrice = null;
  try {
    $minPrice = $product->variants?->where('is_active', true)->min('price') ?? $product->variants?->min('price');
  } catch (\Throwable $e) {
    $minPrice = null;
  }

  $catName = $product->category?->name ?? 'Marketplace';
@endphp

@section('content')
<section class="relative overflow-hidden py-8">
  {{-- BG glow --}}
  <div class="pointer-events-none absolute inset-0">
    <div class="absolute -top-24 -right-24 w-[520px] h-[520px] rounded-full blur-3xl bg-violet-600/15"></div>
    <div class="absolute -bottom-24 -left-24 w-[560px] h-[560px] rounded-full blur-3xl bg-indigo-500/10"></div>
    <div class="absolute inset-0 [background-image:linear-gradient(rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.03)_1px,transparent_1px)] [background-size:48px_48px] [mask-image:radial-gradient(closest-side,black,transparent)] opacity-70"></div>
  </div>

  <div class="relative mx-auto max-w-[1120px] px-4 md:px-6 lg:px-8">

    {{-- Breadcrumb + actions --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="text-xs text-slate-500 flex items-center gap-1 min-w-0">
        <a href="{{ route('landing') }}" class="hover:text-slate-200">Beranda</a>
        <span>/</span>
        <a href="{{ route('marketplace.index') }}" class="hover:text-slate-200">Marketplace</a>
        <span>/</span>
        <span class="text-slate-400 line-clamp-1">{{ $product->name }}</span>
      </div>

      <a href="{{ route('marketplace.index') }}"
         class="h-9 px-3 rounded-2xl border border-slate-800/70 bg-slate-950/30 hover:bg-slate-900/40 text-xs text-slate-200 transition">
        ← Kembali
      </a>
    </div>

    {{-- HERO --}}
    <div class="mt-5 rounded-3xl border border-slate-800/70 bg-slate-900/45 p-5 md:p-7">
      <div class="flex flex-col gap-3">
        <div class="flex flex-wrap items-center gap-2">
          <span class="inline-flex items-center gap-2 rounded-full border border-slate-800/70 bg-slate-950/30 px-3 py-1 text-[11px] text-slate-300">
            <span class="size-1.5 rounded-full bg-violet-400"></span>
            {{ $catName }}
          </span>

          @if($minPrice)
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-800/70 bg-slate-950/30 px-3 py-1 text-[11px] text-slate-300">
              Mulai Rp <b class="text-slate-100">{{ number_format((int)$minPrice, 0, ',', '.') }}</b>
            </span>
          @endif

          <span class="inline-flex items-center gap-2 rounded-full border border-emerald-500/25 bg-emerald-500/10 px-3 py-1 text-[11px] text-emerald-200">
            <span class="size-1.5 rounded-full bg-emerald-400"></span>
            Transaksi aman
          </span>
        </div>

        <h1 class="text-2xl md:text-3xl font-semibold tracking-tight text-slate-50">
          {{ $product->name }}
        </h1>

        <p class="text-sm text-slate-400 max-w-prose">
          Pilih varian di panel kanan, lalu lanjutkan checkout. Produk akan diproses setelah pembayaran berhasil.
        </p>

        <div class="mt-1 flex flex-wrap gap-2 text-xs text-slate-400">
          <span class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/70 bg-slate-950/30 px-3 py-2">
            <span class="size-2 rounded-full bg-emerald-500"></span> Admin fee gratis
          </span>
          <span class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/70 bg-slate-950/30 px-3 py-2">
            <span class="size-2 rounded-full bg-violet-500"></span> Kirim manual oleh admin
          </span>
          <span class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/70 bg-slate-950/30 px-3 py-2">
            <span class="size-2 rounded-full bg-sky-500"></span> Support via WhatsApp/Email
          </span>
        </div>
      </div>
    </div>

    {{-- MAIN --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)] items-start">

      {{-- LEFT: Gallery + Description --}}
      <div class="space-y-5">

        {{-- Gallery card --}}
        <div class="rounded-3xl border border-slate-800/70 bg-slate-900/45 p-4 md:p-5">
          @if($images->count())
            <div class="space-y-3">
              <div id="mpSlider"
                   class="relative overflow-hidden rounded-2xl bg-slate-950/40 border border-slate-800/70">
                @foreach($images as $idx => $img)
                  <div class="mp-slide {{ $idx === 0 ? '' : 'hidden' }}">
                    <img
                      src="{{ asset('storage/'.$img->path) }}"
                      alt="{{ $product->name }}"
                      class="w-full aspect-[16/10] md:aspect-[16/9] object-cover"
                      loading="lazy"
                    >
                  </div>
                @endforeach

                @if($images->count() > 1)
                  <button type="button" id="mpPrev"
                    class="absolute left-3 top-1/2 -translate-y-1/2 h-10 w-10 rounded-full bg-black/40 backdrop-blur border border-white/10 flex items-center justify-center text-slate-100 hover:bg-black/55 transition">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none">
                      <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </button>

                  <button type="button" id="mpNext"
                    class="absolute right-3 top-1/2 -translate-y-1/2 h-10 w-10 rounded-full bg-black/40 backdrop-blur border border-white/10 flex items-center justify-center text-slate-100 hover:bg-black/55 transition">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none">
                      <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </button>
                @endif
              </div>

              @if($images->count() > 1)
                <div class="flex gap-2 overflow-x-auto pb-1">
                  @foreach($images as $idx => $img)
                    <button type="button"
                      class="mp-thumb shrink-0 rounded-2xl border border-slate-800/70 overflow-hidden {{ $idx === 0 ? 'ring-2 ring-violet-500' : '' }}"
                      data-index="{{ $idx }}">
                      <img
                        src="{{ asset('storage/'.$img->path) }}"
                        alt="{{ $product->name }}"
                        class="h-16 w-16 md:h-20 md:w-20 object-cover"
                        loading="lazy"
                      >
                    </button>
                  @endforeach
                </div>
              @endif
            </div>
          @else
            <div class="aspect-[16/10] rounded-2xl border border-dashed border-slate-800/70 bg-slate-950/30 flex items-center justify-center">
              <p class="text-xs text-slate-500">Belum ada gambar produk</p>
            </div>
          @endif
        </div>

        {{-- Description card --}}
        <div class="rounded-3xl border border-slate-800/70 bg-slate-900/45 p-5 md:p-6">
          <h2 class="text-base font-semibold text-slate-100">Deskripsi</h2>
          <div class="mt-3 prose prose-invert prose-sm max-w-none prose-p:text-slate-300 prose-a:text-violet-300">
            {!! nl2br(e($product->description)) !!}
          </div>

          <div class="mt-5 grid gap-3 sm:grid-cols-2">
            <div class="rounded-2xl border border-slate-800/70 bg-slate-950/25 p-4">
              <div class="text-xs font-semibold text-slate-200">Cara kerja</div>
              <ul class="mt-2 text-xs text-slate-400 space-y-1">
                <li>• Pilih varian → checkout</li>
                <li>• Isi email/WA dengan benar</li>
                <li>• Admin kirim detail setelah pembayaran</li>
              </ul>
            </div>

            <div class="rounded-2xl border border-slate-800/70 bg-slate-950/25 p-4">
              <div class="text-xs font-semibold text-slate-200">Catatan penting</div>
              <ul class="mt-2 text-xs text-slate-400 space-y-1">
                <li>• Proses manual oleh admin</li>
                <li>• Pastikan kontak aktif</li>
                <li>• Simpan invoice setelah order</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      {{-- RIGHT: Sticky purchase panel --}}
      <aside class="space-y-4 lg:sticky lg:top-6">
        <div class="rounded-3xl border border-slate-800/70 bg-slate-900/55 p-4 md:p-5 space-y-4">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <p class="text-xs text-slate-500">Produk marketplace</p>
              <h2 class="text-lg font-semibold text-slate-50 truncate">{{ $product->name }}</h2>
              <p class="mt-1 text-xs text-slate-400">Pilih varian untuk melihat total.</p>
            </div>
          </div>

          <div class="border-t border-slate-800/70 pt-3">
            <p class="text-xs font-semibold text-slate-300 mb-2">Pilih Varian</p>

            <form method="POST"
                  action="{{ route('marketplace.checkout.create', $product) }}"
                  id="variantForm"
                  class="space-y-3">
              @csrf

              <input type="hidden" name="variant_id" id="variantIdInput">

              <div class="space-y-2 max-h-[360px] overflow-auto pr-1">
                @foreach($product->variants as $variant)
                  @php
                    $isActive = isset($variant->is_active) ? (bool)$variant->is_active : true;
                  @endphp

                  <button type="button"
                    class="variantItem w-full text-left rounded-2xl border border-slate-800/70 px-3.5 py-3 text-sm flex items-center justify-between
                           hover:border-violet-500/60 hover:bg-slate-950/25 transition focus:outline-none"
                    data-id="{{ $variant->id }}"
                    data-price="{{ $variant->price }}"
                    @if(!$isActive) disabled @endif
                  >
                    <div class="min-w-0">
                      <div class="font-medium text-slate-50 truncate">{{ $variant->name }}</div>
                      <div class="mt-1 flex flex-wrap items-center gap-2">
                        @if($variant->duration_days)
                          <span class="text-[11px] px-2 py-0.5 rounded-full border border-slate-800/70 bg-slate-950/30 text-slate-400">
                            {{ $variant->duration_days }} hari
                          </span>
                        @endif
                        @if(!$isActive)
                          <span class="text-[11px] px-2 py-0.5 rounded-full border border-rose-500/25 bg-rose-500/10 text-rose-200">
                            Tidak tersedia
                          </span>
                        @endif
                      </div>
                    </div>

                    <div class="text-right shrink-0 ps-3">
                      <div class="font-semibold text-slate-50 text-sm">
                        Rp {{ number_format($variant->price, 0, ',', '.') }}
                      </div>
                      <div class="text-[11px] text-slate-500">Tap untuk pilih</div>
                    </div>
                  </button>
                @endforeach
              </div>

              {{-- total --}}
              <div class="pt-3 border-t border-slate-800/70 flex items-center justify-between">
                <span class="text-sm text-slate-400">Total</span>
                <span id="selectedPrice" class="text-base font-semibold text-slate-50">
                  Pilih varian dulu
                </span>
              </div>

              <button type="submit"
                id="btnContinue"
                disabled
                class="mt-2 w-full h-11 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-medium text-white
                       disabled:opacity-50 disabled:cursor-not-allowed transition shadow-lg shadow-violet-900/25">
                Lanjutkan ke checkout
              </button>

              <p class="text-[11px] text-slate-500">
                Dengan klik tombol di atas, kamu akan diarahkan ke halaman checkout.
              </p>
            </form>
          </div>
        </div>

        {{-- Extra info card --}}
        <div class="rounded-3xl border border-slate-800/70 bg-slate-900/45 p-4 text-xs text-slate-400 space-y-1.5">
          <p>• Akun akan dikirim manual oleh admin setelah pembayaran berhasil.</p>
          <p>• Pastikan email & nomor WhatsApp yang kamu masukkan sudah benar.</p>
          <p>• Jika ada kendala, hubungi admin melalui menu Bantuan.</p>
        </div>
      </aside>
    </div>
  </div>

  {{-- Mobile Sticky Bottom Bar (muncul di HP) --}}
  <div class="lg:hidden fixed bottom-0 inset-x-0 z-40">
    <div class="mx-auto max-w-[1120px] px-4 pb-4">
      <div class="rounded-3xl border border-slate-800/70 bg-slate-950/60 backdrop-blur p-3 flex items-center justify-between gap-3">
        <div class="min-w-0">
          <div class="text-[11px] text-slate-400">Total</div>
          <div id="selectedPriceMobile" class="text-sm font-semibold text-slate-100 truncate">
            Pilih varian dulu
          </div>
        </div>
        <button type="button" id="btnContinueMobile"
          class="h-11 px-4 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-medium text-white disabled:opacity-50 disabled:cursor-not-allowed transition"
          disabled>
          Checkout
        </button>
      </div>
    </div>
  </div>

  {{-- SCRIPT: pilih varian + slider gallery --}}
  <script>
    (function () {
      // VARIANT
      const items   = document.querySelectorAll('.variantItem');
      const input   = document.getElementById('variantIdInput');
      const btn     = document.getElementById('btnContinue');
      const priceEl = document.getElementById('selectedPrice');

      const priceMobile = document.getElementById('selectedPriceMobile');
      const btnMobile   = document.getElementById('btnContinueMobile');
      const form        = document.getElementById('variantForm');

      function formatIDR(n) {
        try { return 'Rp ' + Number(n).toLocaleString('id-ID'); }
        catch(e){ return 'Rp ' + n; }
      }

      items.forEach(item => {
        item.addEventListener('click', () => {
          if (item.disabled) return;

          const id    = item.dataset.id;
          const price = item.dataset.price;

          input.value = id;

          items.forEach(i => {
            i.classList.remove('border-violet-500', 'bg-slate-950/30', 'ring-1', 'ring-violet-500/30');
          });
          item.classList.add('border-violet-500', 'bg-slate-950/30', 'ring-1', 'ring-violet-500/30');

          if (priceEl && price) priceEl.textContent = formatIDR(price);
          if (priceMobile && price) priceMobile.textContent = formatIDR(price);

          btn.disabled = false;
          if (btnMobile) btnMobile.disabled = false;
        });
      });

      // Mobile button triggers same form submit
      btnMobile && btnMobile.addEventListener('click', () => {
        if (!input.value) return;
        form.submit();
      });

      // SLIDER
      const slider  = document.getElementById('mpSlider');
      if (!slider) return;

      const slides  = slider.querySelectorAll('.mp-slide');
      const thumbs  = document.querySelectorAll('.mp-thumb');
      const prevBtn = document.getElementById('mpPrev');
      const nextBtn = document.getElementById('mpNext');
      let current   = 0;

      function showSlide(index) {
        if (!slides.length) return;
        current = (index + slides.length) % slides.length;

        slides.forEach((s, i) => {
          if (i === current) s.classList.remove('hidden');
          else s.classList.add('hidden');
        });

        thumbs.forEach((t, i) => {
          if (i === current) t.classList.add('ring-2', 'ring-violet-500');
          else t.classList.remove('ring-2', 'ring-violet-500');
        });
      }

      prevBtn && prevBtn.addEventListener('click', () => showSlide(current - 1));
      nextBtn && nextBtn.addEventListener('click', () => showSlide(current + 1));

      thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
          const idx = parseInt(thumb.dataset.index, 10);
          showSlide(idx);
        });
      });
    })();
  </script>
</section>
@endsection
