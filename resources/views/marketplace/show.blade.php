@extends('layouts.app')

@section('title', $product->name.' — Marketplace')
@section('page','marketplace-product')

@section('content')
  <section class="py-8">
    <div class="mx-auto max-w-[1120px] px-4 md:px-6 lg:px-8">

      {{-- breadcrumb sederhana --}}
      <div class="text-xs text-slate-500 flex items-center gap-1">
        <a href="{{ route('landing') }}" class="hover:text-slate-200">Beranda</a>
        <span>/</span>
        <a href="{{ route('marketplace.index') }}" class="hover:text-slate-200">Marketplace</a>
        <span>/</span>
        <span class="text-slate-400 line-clamp-1">{{ $product->name }}</span>
      </div>

      <div class="mt-5 grid gap-8 lg:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)] items-start">
        {{-- KIRI: GALLERY + DESKRIPSI --}}
        <div class="space-y-5 lg:pr-4">

          {{-- Gallery utama --}}
          <div class="rounded-3xl border border-slate-800/80 bg-slate-900/70 p-4">
            @php
              $images = $product->images ?? collect();
            @endphp

            @if($images->count())
              <div class="space-y-3">
                {{-- gambar utama / slider --}}
                <div id="mpSlider"
                     class="relative overflow-hidden rounded-2xl bg-slate-950/70 border border-slate-800/80">
                  @foreach($images as $idx => $img)
                    <div class="mp-slide {{ $idx === 0 ? '' : 'hidden' }}">
                      <img
                        src="{{ asset('storage/'.$img->path) }}"
                        alt="{{ $product->name }}"
                        class="w-full max-h-[480px] object-cover"
                      >
                    </div>
                  @endforeach

                  @if($images->count() > 1)
                    <button type="button" id="mpPrev"
                            class="absolute left-3 top-1/2 -translate-y-1/2 h-9 w-9 rounded-full bg-black/40 backdrop-blur flex items-center justify-center text-lg text-slate-100">
                      ‹
                    </button>
                    <button type="button" id="mpNext"
                            class="absolute right-3 top-1/2 -translate-y-1/2 h-9 w-9 rounded-full bg-black/40 backdrop-blur flex items-center justify-center text-lg text-slate-100">
                      ›
                    </button>
                  @endif
                </div>

                {{-- thumbnails --}}
                @if($images->count() > 1)
                  <div class="flex gap-2 overflow-x-auto pb-1">
                    @foreach($images as $idx => $img)
                      <button type="button"
                              class="mp-thumb shrink-0 rounded-2xl border border-slate-800/80 overflow-hidden {{ $idx === 0 ? 'ring-2 ring-violet-500' : '' }}"
                              data-index="{{ $idx }}">
                        <img
                          src="{{ asset('storage/'.$img->path) }}"
                          alt="{{ $product->name }}"
                          class="h-16 w-16 md:h-20 md:w-20 object-cover"
                        >
                      </button>
                    @endforeach
                  </div>
                @endif
              </div>
            @else
              {{-- fallback kalau belum ada gambar --}}
              <div class="aspect-[4/3] rounded-2xl border border-dashed border-slate-800/80 bg-slate-950/60 flex items-center justify-center">
                <p class="text-xs text-slate-500">Belum ada gambar produk</p>
              </div>
            @endif
          </div>

          {{-- Info teks & deskripsi --}}
          <div class="space-y-2">
            <p class="text-xs uppercase tracking-wide text-slate-500">
              {{ $product->category?->name ?? 'Marketplace' }}
            </p>
            <h1 class="text-2xl md:text-3xl font-semibold text-slate-50">
              {{ $product->name }}
            </h1>

            {{-- kalau mau bisa tambahkan info “terjual sekian / rating” di sini nanti --}}

            <div class="mt-3 text-sm leading-relaxed text-slate-300">
              {!! nl2br(e($product->description)) !!}
            </div>
          </div>
        </div>

        {{-- KANAN: PANEL VARIAN + CTA --}}
        <div class="space-y-4 lg:pl-2">
          <div class="rounded-3xl border border-slate-800/80 bg-slate-900/70 p-4 md:p-5 space-y-4">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-xs text-slate-500 mb-1">Produk marketplace</p>
                <h2 class="text-lg font-semibold text-slate-50">{{ $product->name }}</h2>
              </div>
            </div>

            <div class="border-t border-slate-800/70 pt-3">
              <p class="text-xs font-semibold text-slate-400 mb-2">Pilih Varian</p>

              <form method="POST"
                    action="{{ route('marketplace.checkout.create', $product) }}"
                    id="variantForm"
                    class="space-y-3">
                @csrf
                <input type="hidden" name="variant_id" id="variantIdInput">

                <div class="space-y-2">
                  @foreach($product->variants as $variant)
                    <button type="button"
                            class="variantItem w-full text-left rounded-2xl border border-slate-800/70 px-3.5 py-3 text-sm flex items-center justify-between hover:border-violet-500/60 transition"
                            data-id="{{ $variant->id }}"
                            data-price="{{ $variant->price }}">
                      <div>
                        <div class="font-medium text-slate-50">{{ $variant->name }}</div>
                        @if($variant->duration_days)
                          <div class="text-[11px] text-slate-400">{{ $variant->duration_days }} hari</div>
                        @endif
                      </div>
                      <div class="text-right">
                        <div class="font-semibold text-slate-50 text-sm">
                          Rp {{ number_format($variant->price, 0, ',', '.') }}
                        </div>
                      </div>
                    </button>
                  @endforeach
                </div>

                {{-- harga aktif --}}
                <div class="pt-3 border-t border-slate-800/70 flex items-center justify-between text-sm">
                  <span class="text-slate-400">Total</span>
                  <span id="selectedPrice" class="text-base font-semibold text-slate-50">
                    Pilih varian dulu
                  </span>
                </div>

                <button type="submit"
                        id="btnContinue"
                        class="mt-4 w-full h-11 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                  Lanjutkan ke checkout
                </button>
              </form>
            </div>
          </div>

          {{-- info kecil tambahan --}}
          <div class="rounded-2xl border border-slate-800/80 bg-slate-900/70 p-3 text-xs text-slate-400 space-y-1.5">
            <p>• Akun akan dikirim manual oleh admin setelah pembayaran berhasil.</p>
            <p>• Pastikan email & nomor WhatsApp yang kamu masukkan sudah benar.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- SCRIPT: pilih varian + slider gallery --}}
  <script>
    (function () {
      // VARIAN
      const items  = document.querySelectorAll('.variantItem');
      const input  = document.getElementById('variantIdInput');
      const btn    = document.getElementById('btnContinue');
      const priceEl = document.getElementById('selectedPrice');

      items.forEach(item => {
        item.addEventListener('click', () => {
          const id    = item.dataset.id;
          const price = item.dataset.price;

          input.value = id;

          items.forEach(i => i.classList.remove('border-violet-500', 'bg-slate-900'));
          item.classList.add('border-violet-500', 'bg-slate-900');

          if (priceEl && price) {
            priceEl.textContent = 'Rp ' + Number(price).toLocaleString('id-ID');
          }

          btn.disabled = false;
        });
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
          if (i === current) {
            t.classList.add('ring-2', 'ring-violet-500');
          } else {
            t.classList.remove('ring-2', 'ring-violet-500');
          }
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
@endsection
