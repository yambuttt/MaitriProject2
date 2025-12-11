@extends('layouts.app')

@section('title', 'MaitriProject — Top Up & Digital Goods')
@section('meta_description', 'Pulsa, data, game, PLN, dan e-wallet resmi. Bayar aman. Masuk instan. Terhubung Digiflazz.')
@section('page', 'landing')
@php
  use Illuminate\Support\Facades\Storage;
@endphp

@section('content')


  {{-- HERO --}}
  <section class="relative overflow-hidden">
    <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
      <div class="grid lg:grid-cols-2 gap-10 items-center min-h-[70svh]">
        <div class="reveal">
          <h1 class="text-3xl md:text-5xl font-semibold leading-tight tracking-tight">
            Top Up & Digital Goods <span
              class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-violet-200">secepat kilat</span>
          </h1>
          <p class="mt-4 text-slate-400 max-w-prose">
            Pulsa, data, game, dan e-wallet resmi. Bayar aman. Masuk instan.
          </p>
          <div class="mt-6 flex items-center gap-3">
            {{-- Tetap ke katalog top up (Digiflazz) --}}
            <a href="{{ route('catalog') }}"
              class="px-5 py-3 rounded-2xl bg-violet-600 hover:bg-violet-500 transition shadow-lg shadow-violet-900/30">
              Mulai Top Up
            </a>

            {{-- Ganti: sekarang ke halaman produk marketplace --}}
            <a href="{{ route('marketplace.index') }}"
              class="px-5 py-3 rounded-2xl border border-slate-800/70 hover:border-slate-700 transition">
              Product Marketplace
            </a>
          </div>

          <div class="mt-6 flex items-center gap-4 text-slate-400 text-sm">
            <div class="flex items-center gap-2"><span class="inline-block size-2 rounded-full bg-emerald-500"></span>
              Proses &lt; 2 menit</div>
            <div class="flex items-center gap-2"><span class="inline-block size-2 rounded-full bg-violet-500"></span> 24/7
            </div>
          </div>
        </div>

        <!-- Visual hero -->
        <div class="relative">
          <div data-parallax class="relative isolate">
            <div class="absolute -top-10 -right-10 w-72 h-72 rounded-full blur-3xl bg-violet-600/20"></div>
            <div class="absolute -bottom-10 -left-10 w-72 h-72 rounded-full blur-3xl bg-indigo-500/10"></div>

            @php
              $heroProducts = ($heroProducts ?? collect());
              $mainHeroProduct = $heroProducts->first();
              $extraHeroProducts = $heroProducts->slice(1)->take(4);

              $heroMarketplaceProducts = ($heroMarketplaceProducts ?? collect());
            @endphp

            <div class="reveal relative rounded-3xl border border-slate-800/70 bg-[#111826] p-4 shadow-2xl"
              data-hero-tabs>
              {{-- TAB BUTTONS --}}
              <div class="flex items-center justify-between mb-3">
                <div class="inline-flex p-1 rounded-2xl bg-[#0E1524] border border-slate-800/70 text-xs">
                  <button type="button" class="hero-tab-btn px-3 py-1.5 rounded-xl font-medium" data-tab="digital">
                    Top Up Digital
                  </button>
                  <button type="button" class="hero-tab-btn px-3 py-1.5 rounded-xl font-medium text-slate-400"
                    data-tab="marketplace">
                    Produk Marketplace
                  </button>
                </div>
                <p class="hidden md:block text-[11px] text-slate-500">
                  Pilih tab untuk lihat contoh produk
                </p>
              </div>

              {{-- PANEL: TOP UP DIGITAL (Digiflazz) --}}
              <div class="hero-tab-panel space-y-3" data-tab-panel="digital">
                @if($mainHeroProduct)
                  @php
                    $minPrice = $mainHeroProduct->variants->min(fn($v) => $v->final_price);
                  @endphp

                  <div class="flex items-center justify-between p-3 rounded-2xl bg-[#0E1524] border border-slate-800/70">
                    <div class="flex items-center gap-3">
                      <div class="size-9 rounded-xl bg-violet-600/20 grid place-items-center text-violet-300">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none">
                          <path d="M13 3L4 14h7l-1 7 9-11h-7l1-7Z" stroke="currentColor" stroke-width="1.5" />
                        </svg>
                      </div>
                      <div>
                        <div class="font-medium truncate">{{ $mainHeroProduct->name }}</div>
                        <div class="text-xs text-slate-400">
                          Mulai Rp {{ number_format($minPrice, 0, ',', '.') }}
                        </div>
                      </div>
                    </div>
                    <a href="{{ route('catalog.product.show', $mainHeroProduct->slug) }}"
                      class="px-3 py-1.5 text-sm rounded-xl bg-violet-600 hover:bg-violet-500">
                      Top Up
                    </a>
                  </div>

                  <div class="grid sm:grid-cols-2 gap-3">
                    @forelse($extraHeroProducts as $p)
                      @php
                        $minPrice = $p->variants->min(fn($v) => $v->final_price);
                      @endphp
                      <a href="{{ route('catalog.product.show', $p->slug) }}"
                        class="p-3 rounded-2xl bg-[#0E1524] border border-slate-800/70 hover:border-violet-700/60 transition">
                        <div class="flex items-center gap-3">
                          <div class="size-9 rounded-xl bg-slate-800/60 overflow-hidden">
                            @if($p->thumbnail)
                              <img src="{{ Storage::url($p->thumbnail) }}" alt="{{ $p->name }}"
                                class="w-full h-full object-cover">
                            @else
                              <div class="w-full h-full flex items-center justify-center text-[10px] text-slate-500">
                                Gambar
                              </div>
                            @endif
                          </div>
                          <div class="min-w-0">
                            <div class="truncate font-medium">{{ $p->name }}</div>
                            <div class="text-xs text-slate-400">
                              Mulai Rp {{ number_format($minPrice, 0, ',', '.') }}
                            </div>
                          </div>
                        </div>
                      </a>
                    @empty
                    @endforelse
                  </div>
                @else
                  {{-- fallback kalau belum ada produk digiflazz sama sekali --}}
                  <p class="text-sm text-slate-400">Belum ada produk top up yang aktif.</p>
                @endif
              </div>

              {{-- PANEL: PRODUK MARKETPLACE --}}
              <div class="hero-tab-panel space-y-3 hidden" data-tab-panel="marketplace">
                @if($heroMarketplaceProducts->isNotEmpty())
                  @php
                    $mainMarketplace = $heroMarketplaceProducts->first();
                    $extraMarketplace = $heroMarketplaceProducts->slice(1);
                    $mainMarketplacePrice = optional(
                      $mainMarketplace->variants->where('is_active', true)
                    )->min('price');
                  @endphp

                  <div class="flex items-center justify-between p-3 rounded-2xl bg-[#0E1524] border border-slate-800/70">
                    <div class="flex items-center gap-3">
                      <div class="size-9 rounded-xl bg-violet-600/20 grid place-items-center text-violet-300">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none">
                          <path d="M12 3l7 3v6c0 5-3.5 7.5-7 9-3.5-1.5-7-4-7-9V6l7-3Z" stroke="currentColor"
                            stroke-width="1.5" />
                          <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.5" />
                        </svg>
                      </div>
                      <div>
                        <div class="font-medium truncate">{{ $mainMarketplace->name }}</div>
                        @if($mainMarketplacePrice)
                          <div class="text-xs text-slate-400">
                            Mulai Rp {{ number_format($mainMarketplacePrice, 0, ',', '.') }}
                          </div>
                        @endif
                      </div>
                    </div>
                    <a href="{{ route('marketplace.product.show', $mainMarketplace->slug) }}"
                      class="px-3 py-1.5 text-sm rounded-xl bg-violet-600 hover:bg-violet-500">
                      Lihat Produk
                    </a>
                  </div>

                  <div class="grid sm:grid-cols-2 gap-3">
                    @foreach($extraMarketplace as $p)
                      @php
                        $minPrice = $p->variants
                          ->where('is_active', true)
                          ->min('price');
                      @endphp
                      <a href="{{ route('marketplace.product.show', $p->slug) }}"
                        class="p-3 rounded-2xl bg-[#0E1524] border border-slate-800/70 hover:border-violet-700/60 transition">
                        <div class="flex items-center gap-3">
                          <div class="size-9 rounded-xl bg-slate-800/60 overflow-hidden">
                            @if($p->thumbnail)
                              <img src="{{ Storage::url($p->thumbnail) }}" alt="{{ $p->name }}"
                                class="w-full h-full object-cover">
                            @else
                              <div class="w-full h-full flex items-center justify-center text-[10px] text-slate-500">
                                Gambar
                              </div>
                            @endif
                          </div>
                          <div class="min-w-0">
                            <div class="truncate font-medium">{{ $p->name }}</div>
                            @if($minPrice)
                              <div class="text-xs text-slate-400">
                                Mulai Rp {{ number_format($minPrice, 0, ',', '.') }}
                              </div>
                            @endif
                          </div>
                        </div>
                      </a>
                    @endforeach
                  </div>
                @else
                  <p class="text-sm text-slate-400">Belum ada produk marketplace aktif.</p>
                @endif
              </div>
            </div>

          </div>
        </div>


        <div
          class="pointer-events-none absolute inset-0 [background-image:linear-gradient(rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.03)_1px,transparent_1px)] [background-size:48px_48px] [mask-image:radial-gradient(closest-side,black,transparent)]">
        </div>
  </section>

  {{-- TRUST BAR --}}
  <section class="py-10">
    <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach([['Transaksi Aman', 'shield-check'], ['Transaksi Cepat; 2 Menit', 'bolt'], ['Layanan 24/7', 'clock']] as [$label, $icon])
          <div
            class="reveal flex items-center gap-3 p-4 rounded-2xl bg-[#111826] border border-slate-800/70 hover:border-slate-700 transition">
            <div class="size-10 grid place-items-center rounded-xl bg-slate-800/60">
              @if($icon === 'link')
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                  <path d="M10 14a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1M14 10a5 5 0 0 1 0 7l-1 1a5 5 0 1 1-7-7l1-1"
                    stroke="currentColor" stroke-width="1.5" />
                </svg>
              @elseif($icon === 'shield-check')
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                  <path d="M12 3l7 3v6c0 5-3.5 7.5-7 9-3.5-1.5-7-4-7-9V6l7-3Z" stroke="currentColor" stroke-width="1.5" />
                  <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.5" />
                </svg>
              @elseif($icon === 'bolt')
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                  <path d="M13 3 4 14h7l-1 7 9-11h-7l1-7Z" stroke="currentColor" stroke-width="1.5" />
                </svg>
              @else
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                  <path d="M12 7v5l3 2M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20Z" stroke="currentColor" stroke-width="1.5" />
                </svg>
              @endif
            </div>
            <div class="font-medium">{{ $label }}</div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <script>
    // Hero tabs: Top Up Digital vs Produk Marketplace
    (function () {
      const root = document.querySelector('[data-hero-tabs]');
      if (!root) return;

      const buttons = root.querySelectorAll('.hero-tab-btn[data-tab]');
      const panels = root.querySelectorAll('.hero-tab-panel[data-tab-panel]');

      const setActive = (tab) => {
        buttons.forEach((btn) => {
          const active = btn.getAttribute('data-tab') === tab;
          btn.classList.toggle('bg-[#0E1524]', active);
          btn.classList.toggle('text-slate-200', active);
          btn.classList.toggle('text-slate-400', !active);
        });

        panels.forEach((panel) => {
          const active = panel.getAttribute('data-tab-panel') === tab;
          panel.classList.toggle('hidden', !active);
        });
      };

      buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
          setActive(btn.getAttribute('data-tab'));
        });
      });

      // default: tab top up digital
      setActive('digital');
    })();

  </script>
  {{-- SECTIONS LAIN (kategori, produk, cara kerja, promo, testimoni, faq, cta) --}}
  @include('pages.partials.landing-sections')
@endsection