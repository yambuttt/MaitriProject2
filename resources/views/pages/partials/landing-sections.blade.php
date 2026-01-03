{{-- Kategori --}}


{{-- Produk Populer --}}
{{-- Produk Populer --}}
@php
  use Illuminate\Support\Facades\Storage;

  $popularDigital = $popularDigital ?? collect();
  $popularMarketplace = $popularMarketplace ?? collect();
@endphp

<section id="produk" class="py-14">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4">
      <div>
        <h2 class="reveal text-2xl md:text-3xl font-semibold">Lagi Ramai Dibeli</h2>
        <p class="reveal mt-1 text-sm text-slate-400">Produk yang paling sering dibeli dalam 14 hari terakhir.</p>
      </div>

      <div class="reveal flex items-center gap-2">
        <a href="{{ route('catalog') }}"
           class="hidden sm:inline-flex px-4 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 transition text-sm">
          Lihat katalog top up
        </a>
        <a href="{{ route('marketplace.index') }}"
           class="hidden sm:inline-flex px-4 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 transition text-sm">
          Buka marketplace
        </a>
      </div>
    </div>

    {{-- Tabs --}}
    <div class="mt-6 reveal">
      <div class="inline-flex p-1 rounded-2xl bg-[#0E1524] border border-slate-800/70 text-xs" data-popular-tabs>
        <button type="button"
          class="popular-tab px-3 py-1.5 rounded-xl font-medium bg-[#111826] text-slate-100"
          data-tab="digital">
          Digital Goods
        </button>
        <button type="button"
          class="popular-tab px-3 py-1.5 rounded-xl font-medium text-slate-400"
          data-tab="marketplace">
          Marketplace
        </button>
      </div>
    </div>

    {{-- Panel: Digital --}}
    <div class="mt-6" data-popular-panel="digital">
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($popularDigital as $item)
          <a href="{{ $item->url }}"
             class="reveal group rounded-3xl border border-slate-800/70 bg-[#111826] p-4 hover:border-violet-700/60 transition">
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-center gap-3 min-w-0">
                <div class="size-10 rounded-xl bg-slate-800/60 overflow-hidden shrink-0">
                  @if(!empty($item->thumbnail))
                    <img src="{{ Storage::url($item->thumbnail) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                  @else
                    <div class="w-full h-full grid place-items-center text-[10px] text-slate-500">IMG</div>
                  @endif
                </div>
                <div class="min-w-0">
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-violet-600/15 text-violet-300 border border-violet-700/30">
                      {{ $item->label }}
                    </span>
                    @if(!is_null($item->total))
                      <span class="text-[10px] text-slate-500">{{ $item->total }} trx</span>
                    @endif
                  </div>
                  <h3 class="mt-1 font-medium truncate">{{ $item->name }}</h3>
                  @if(!empty($item->min_price))
                    <p class="text-xs text-slate-400">Mulai Rp {{ number_format($item->min_price,0,',','.') }}</p>
                  @endif
                </div>
              </div>

              <div class="size-9 rounded-xl bg-violet-600/15 text-violet-200 grid place-items-center shrink-0">
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                  <path d="M13 3L4 14h7l-1 7 9-11h-7l1-7Z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </div>
            </div>

            <div class="mt-4 flex items-center justify-between">
              <div class="text-sm text-slate-400">Estimasi ±1–2 menit</div>
              <span class="px-3 py-1.5 rounded-xl bg-violet-600 group-hover:bg-violet-500 text-sm">
                Top Up
              </span>
            </div>
          </a>
        @empty
          <div class="text-sm text-slate-400">Belum ada produk yang ramai dibeli.</div>
        @endforelse
      </div>
    </div>

    {{-- Panel: Marketplace --}}
    <div class="mt-6 hidden" data-popular-panel="marketplace">
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($popularMarketplace as $item)
          <a href="{{ $item->url }}"
             class="reveal group rounded-3xl border border-slate-800/70 bg-[#111826] p-4 hover:border-violet-700/60 transition">
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-center gap-3 min-w-0">
                <div class="size-10 rounded-xl bg-slate-800/60 overflow-hidden shrink-0">
                  @if(!empty($item->thumbnail))
                    <img src="{{ Storage::url($item->thumbnail) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                  @else
                    <div class="w-full h-full grid place-items-center text-[10px] text-slate-500">IMG</div>
                  @endif
                </div>
                <div class="min-w-0">
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-700/40 text-slate-200 border border-slate-700/60">
                      {{ $item->label }}
                    </span>
                    @if(!is_null($item->total))
                      <span class="text-[10px] text-slate-500">{{ $item->total }} trx</span>
                    @endif
                  </div>
                  <h3 class="mt-1 font-medium truncate">{{ $item->name }}</h3>
                  @if(!empty($item->min_price))
                    <p class="text-xs text-slate-400">Mulai Rp {{ number_format($item->min_price,0,',','.') }}</p>
                  @endif
                </div>
              </div>

              <div class="size-9 rounded-xl bg-slate-700/30 text-slate-100 grid place-items-center shrink-0">
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                  <path d="M7 7h14l-1 7H8L7 7Z" stroke="currentColor" stroke-width="1.5"/>
                  <path d="M7 7 6 4H3" stroke="currentColor" stroke-width="1.5"/>
                  <path d="M9 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2ZM18 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </div>
            </div>

            <div class="mt-4 flex items-center justify-between">
              <div class="text-sm text-slate-400">Produk premium</div>
              <span class="px-3 py-1.5 rounded-xl bg-violet-600 group-hover:bg-violet-500 text-sm">
                Lihat
              </span>
            </div>
          </a>
        @empty
          <div class="text-sm text-slate-400">Belum ada produk marketplace yang ramai dibeli.</div>
        @endforelse
      </div>
    </div>

    {{-- Tabs JS (mini) --}}
    <script>
      (function () {
        const wrap = document.querySelector('[data-popular-tabs]');
        if (!wrap) return;

        const btns = wrap.querySelectorAll('.popular-tab[data-tab]');
        const panels = document.querySelectorAll('[data-popular-panel]');

        const setTab = (tab) => {
          btns.forEach(b => {
            const active = b.getAttribute('data-tab') === tab;
            b.classList.toggle('bg-[#111826]', active);
            b.classList.toggle('text-slate-100', active);
            b.classList.toggle('text-slate-400', !active);
          });

          panels.forEach(p => {
            const active = p.getAttribute('data-popular-panel') === tab;
            p.classList.toggle('hidden', !active);
          });
        };

        btns.forEach(b => b.addEventListener('click', () => setTab(b.getAttribute('data-tab'))));
        setTab('digital');
      })();
    </script>
  </div>
</section>


{{-- Cara Kerja --}}
<section class="py-14">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
    <h2 class="reveal text-2xl md:text-3xl font-semibold">Cara Kerja</h2>
    <ol class="mt-6 grid md:grid-cols-3 gap-4">
      @foreach([
        ['Masukkan Tujuan','Nomor HP atau Game ID + Server.'],
        ['Pilih Nominal','Pilih denominasi sesuai kebutuhan.'],
        ['Bayar & Selesai','Pembayaran aman, instan masuk.'],
      ] as $idx => [$title,$desc])
      <li class="reveal p-5 rounded-3xl bg-[#111826] border border-slate-800/70">
        <div class="flex items-center gap-3">
          <div class="size-10 rounded-xl bg-violet-600/20 grid place-items-center text-violet-300 font-semibold">{{ $idx+1 }}</div>
          <div>
            <div class="font-medium">{{ $title }}</div>
            <p class="text-sm text-slate-400">{{ $desc }}</p>
          </div>
        </div>
      </li>
      @endforeach
    </ol>
  </div>
</section>

{{-- Promo --}}
<section class="py-14">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
    <h2 class="reveal text-2xl md:text-3xl font-semibold">Promo & Kupon</h2>
    <div class="mt-6 overflow-x-auto no-scrollbar">
      <div class="flex gap-4 snap-x snap-mandatory">
        @foreach([
          ['Diskon Pulsa 10%', 'Sampai 31 Okt'],
          ['Bonus Diamond', 'Khusus ML/FF'],
          ['Cashback e-Wallet', 'Syarat & ketentuan berlaku'],
        ] as [$title,$time])
        <article class="reveal min-w-[280px] md:min-w-[360px] snap-start rounded-3xl border border-slate-800/70 bg-gradient-to-br from-[#111826] to-[#0E1524] p-5">
          <div class="text-sm text-violet-300">Promo</div>
          <h3 class="mt-2 text-lg font-semibold">{{ $title }}</h3>
          <p class="text-sm text-slate-400">{{ $time }}</p>
          <button class="mt-4 px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm">Klaim</button>
        </article>
        @endforeach
      </div>
    </div>
  </div>
</section>

{{-- Testimoni --}}
<section class="py-14">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
    <h2 class="reveal text-2xl md:text-3xl font-semibold">Apa kata mereka</h2>
    <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach([['A***','Cepat banget, 1 menit langsung masuk!'],['B***','Harga bersahabat dan proses aman.'],['C***','UI-nya enak dipakai.']] as [$name,$text])
      <article class="reveal p-5 rounded-3xl bg-[#111826] border border-slate-800/70 hover:translate-y-[-2px] transition">
        <div class="flex items-center gap-3">
          <div class="size-10 rounded-full bg-slate-800/60 grid place-items-center">{{ substr($name,0,1) }}</div>
          <div class="font-medium">{{ $name }}</div>
        </div>
        <p class="mt-3 text-slate-300">{{ $text }}</p>
        <div class="mt-2 text-amber-400">★★★★★</div>
      </article>
      @endforeach
    </div>
  </div>
</section>

{{-- FAQ --}}


{{-- CTA Penutup --}}
<section class="py-16">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
    <div class="reveal relative overflow-hidden rounded-3xl border border-slate-800/70 bg-gradient-to-br from-violet-700/20 to-indigo-700/10 p-8">
      <h3 class="text-xl md:text-2xl font-semibold">Mulai top up sekarang.</h3>
      <p class="mt-1 text-slate-300">Pulsa, data, game, PLN, e-Wallet—instan & aman.</p>
      <a href="{{ route('catalog') }}" class="mt-4 inline-flex px-5 py-3 rounded-2xl bg-violet-600 hover:bg-violet-500">Buka Katalog</a>
      <div class="pointer-events-none absolute -right-20 -bottom-20 w-[320px] h-[320px] rounded-full blur-3xl bg-violet-600/20"></div>
    </div>
  </div>
</section>
