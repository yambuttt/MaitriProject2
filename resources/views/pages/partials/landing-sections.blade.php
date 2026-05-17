@php
  use Illuminate\Support\Facades\Storage;

  $popularDigital = $popularDigital ?? collect();
  $popularMarketplace = $popularMarketplace ?? collect();
@endphp

{{-- POPULAR PRODUCTS SECTION --}}
<section id="produk" class="py-16 bg-[#050810] relative">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8 relative z-10">
    
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-white/5">
      <div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] font-bold tracking-widest text-violet-300 uppercase mb-2">
          🔥 Terlaris Minggu Ini
        </div>
        <h2 class="reveal text-3xl font-extrabold text-white tracking-tight">Lagi Ramai Dibeli</h2>
        <p class="reveal mt-1 text-sm text-slate-400 font-medium">Rekomendasi layanan terpopuler berdasarkan data pembelian 14 hari terakhir.</p>
      </div>

      <div class="reveal flex flex-wrap items-center gap-3">
        <a href="{{ route('catalog') }}"
           class="h-11 inline-flex items-center px-5 rounded-xl border border-white/10 hover:border-violet-500/50 hover:bg-violet-600/5 text-xs font-bold text-slate-300 hover:text-white transition-all">
          Lihat Katalog Top Up
        </a>
        <a href="{{ route('marketplace.index') }}"
           class="h-11 inline-flex items-center px-5 rounded-xl border border-white/10 hover:border-violet-500/50 hover:bg-violet-600/5 text-xs font-bold text-slate-300 hover:text-white transition-all">
          Buka Marketplace
        </a>
      </div>
    </div>

    {{-- Tabs Control --}}
    <div class="mt-8 reveal">
      <div class="inline-flex p-1 rounded-2xl bg-black/40 border border-white/5 text-xs font-bold" data-popular-tabs>
        <button type="button" class="popular-tab px-4 py-2 rounded-xl font-bold transition-all bg-[#111826] text-white" data-tab="digital">
          Digital Goods
        </button>
        <button type="button" class="popular-tab px-4 py-2 rounded-xl font-bold transition-all text-slate-400" data-tab="marketplace">
          Marketplace Product
        </button>
      </div>
    </div>

    {{-- Panel: Digital --}}
    <div class="mt-6" data-popular-panel="digital">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-5">
        @forelse($popularDigital as $item)
          <a href="{{ $item->url }}" class="reveal group glass-card p-5 rounded-[2rem] flex flex-col justify-between h-full min-h-[180px]">
            
            <div class="flex items-start justify-between gap-4">
              <div class="flex items-center gap-3.5 min-w-0">
                <div class="size-12 rounded-xl bg-black/40 overflow-hidden shrink-0 border border-white/5 shadow-inner">
                  @if(!empty($item->thumbnail))
                    <img src="{{ Storage::url($item->thumbnail) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                  @else
                    <div class="w-full h-full grid place-items-center text-[10px] font-bold text-slate-600 uppercase tracking-widest bg-slate-900">IMG</div>
                  @endif
                </div>
                <div class="min-w-0">
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-violet-600/10 text-violet-300 border border-violet-500/20">
                      {{ $item->label }}
                    </span>
                    @if(!is_null($item->total))
                      <span class="text-[10px] text-slate-500 font-semibold">{{ $item->total }} trx</span>
                    @endif
                  </div>
                  <h3 class="mt-1.5 font-bold text-white text-sm truncate leading-snug group-hover:text-violet-300 transition-colors">{{ $item->name }}</h3>
                  @if(!empty($item->min_price))
                    <p class="text-xs text-slate-400 mt-0.5">Mulai <span class="text-white font-semibold">Rp {{ number_format($item->min_price,0,',','.') }}</span></p>
                  @endif
                </div>
              </div>

              <div class="size-9 rounded-lg bg-violet-600/10 border border-violet-500/20 text-violet-400 flex items-center justify-center shrink-0 transition-colors group-hover:bg-violet-600 group-hover:text-white">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
            </div>

            <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-between">
              <div class="text-xs text-slate-500 font-medium">±1-2 Menit Proses</div>
              <span class="px-4 py-2 rounded-xl bg-violet-600 group-hover:bg-violet-500 text-xs font-bold text-white transition-all shadow-[0_0_10px_rgba(139,92,246,0.2)]">
                Top Up
              </span>
            </div>
          </a>
        @empty
          <div class="col-span-full py-12 text-center text-sm font-semibold text-slate-500">Belum ada produk digital yang ramai dibeli.</div>
        @endforelse
      </div>
    </div>

    {{-- Panel: Marketplace --}}
    <div class="mt-6 hidden" data-popular-panel="marketplace">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-5">
        @forelse($popularMarketplace as $item)
          <a href="{{ $item->url }}" class="reveal group glass-card p-5 rounded-[2rem] flex flex-col justify-between h-full min-h-[180px]">
            
            <div class="flex items-start justify-between gap-4">
              <div class="flex items-center gap-3.5 min-w-0">
                <div class="size-12 rounded-xl bg-black/40 overflow-hidden shrink-0 border border-white/5 shadow-inner">
                  @if(!empty($item->thumbnail))
                    <img src="{{ Storage::url($item->thumbnail) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                  @else
                    <div class="w-full h-full grid place-items-center text-[10px] font-bold text-slate-600 uppercase tracking-widest bg-slate-900">IMG</div>
                  @endif
                </div>
                <div class="min-w-0">
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-fuchsia-600/10 text-fuchsia-300 border border-fuchsia-500/20">
                      {{ $item->label }}
                    </span>
                    @if(!is_null($item->total))
                      <span class="text-[10px] text-slate-500 font-semibold">{{ $item->total }} trx</span>
                    @endif
                  </div>
                  <h3 class="mt-1.5 font-bold text-white text-sm truncate leading-snug group-hover:text-fuchsia-300 transition-colors">{{ $item->name }}</h3>
                  @if(!empty($item->min_price))
                    <p class="text-xs text-slate-400 mt-0.5">Mulai <span class="text-white font-semibold">Rp {{ number_format($item->min_price,0,',','.') }}</span></p>
                  @endif
                </div>
              </div>

              <div class="size-9 rounded-lg bg-fuchsia-600/10 border border-fuchsia-500/20 text-fuchsia-400 flex items-center justify-center shrink-0 transition-colors group-hover:bg-fuchsia-600 group-hover:text-white">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
              </div>
            </div>

            <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-between">
              <div class="text-xs text-slate-500 font-medium">Kualitas Premium</div>
              <span class="px-4 py-2 rounded-xl bg-violet-600 group-hover:bg-violet-500 text-xs font-bold text-white transition-all shadow-[0_0_10px_rgba(139,92,246,0.2)]">
                Buka
              </span>
            </div>
          </a>
        @empty
          <div class="col-span-full py-12 text-center text-sm font-semibold text-slate-500">Belum ada produk marketplace yang ramai dibeli.</div>
        @endforelse
      </div>
    </div>

    <script>
      (function () {
        const wrap = document.querySelector('[data-popular-tabs]');
        if (!wrap) return;

        const btns = wrap.querySelectorAll('.popular-tab[data-tab]');
        const panels = document.querySelectorAll('[data-popular-panel]');

        const setTab = (tab) => {
          btns.forEach(b => {
            const active = b.getAttribute('data-tab') === tab;
            b.classList.toggle('bg-violet-600', active);
            b.classList.toggle('text-white', active);
            b.classList.toggle('bg-transparent', !active);
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


{{-- WORKFLOW SECTION --}}
<section class="py-16 bg-[#050810]">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto mb-10">
      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] font-bold tracking-widest text-violet-300 uppercase mb-2">
        🛠️ 3 Langkah Mudah
      </div>
      <h2 class="reveal text-3xl font-extrabold text-white tracking-tight">Cara Kerja Transaksi</h2>
      <p class="reveal mt-1 text-sm text-slate-400 font-medium">Beli produk digital atau marketplace dengan proses yang sangat praktis.</p>
    </div>

    <ol class="grid grid-cols-1 md:grid-cols-3 gap-6">
      @foreach([
        ['Masukkan Tujuan', 'Isi nomor HP, User ID Game lengkap beserta kode server tujuan secara presisi.'],
        ['Pilih Nominal Varian', 'Tentukan paket topup, diamond game, atau nominal voucher produk yang Anda butuhkan.'],
        ['Bayar & Selesai', 'Selesaikan pembayaran menggunakan QRIS, VA, atau Saldo. Produk langsung terkirim otomatis.']
      ] as $idx => $step)
      <li class="reveal p-6 rounded-[2rem] bg-white/[0.02] border border-white/5 hover:border-violet-500/20 hover:bg-[#111827]/10 transition-all duration-300 flex flex-col justify-between h-full min-h-[160px] group">
        <div class="flex items-start gap-4">
          <div class="size-11 rounded-xl bg-violet-600/10 border border-violet-500/20 flex items-center justify-center text-violet-400 font-extrabold text-sm shrink-0 shadow-[0_0_15px_rgba(139,92,246,0.15)] group-hover:bg-violet-600 group-hover:text-white transition-all duration-300">
            0{{ $idx + 1 }}
          </div>
          <div class="space-y-1">
            <h3 class="font-bold text-white text-base group-hover:text-violet-300 transition-colors">{{ $step[0] }}</h3>
            <p class="text-xs text-slate-500 leading-normal font-medium">{{ $step[1] }}</p>
          </div>
        </div>
      </li>
      @endforeach
    </ol>
  </div>
</section>


{{-- COUPONS & OFFERS SECTION --}}
<section class="py-16 bg-[#050810]">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-white/5">
      <div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] font-bold tracking-widest text-violet-300 uppercase mb-2">
          🎁 Penawaran Spesial
        </div>
        <h2 class="reveal text-3xl font-extrabold text-white tracking-tight">Kupon Promo Terbatas</h2>
        <p class="reveal mt-1 text-sm text-slate-400 font-medium">Gunakan kupon promo khusus untuk mendapatkan penawaran harga terbaik.</p>
      </div>
    </div>

    <div class="mt-8 overflow-x-auto no-scrollbar pb-2">
      <div class="flex gap-5 snap-x snap-mandatory">
        @foreach([
          ['Kupon Diskon Pulsa 10%', 'Berlaku s/d 31 Desember', 'Potongan langsung untuk semua operator pulsa.'],
          ['Bonus Diamond Ekstra', 'Khusus Game MLBB & FF', 'Dapatkan ekstra bonus diamond instan di setiap topup.'],
          ['Cashback Saldo Maitri', 'Syarat & Ketentuan berlaku', 'Dapatkan cashback langsung setelah menyelesaikan pesanan.']
        ] as $promo)
        <article class="reveal min-w-[290px] md:min-w-[380px] snap-start rounded-[2rem] border border-dashed border-white/15 bg-gradient-to-br from-[#1E1B4B]/20 to-[#0F172A]/80 p-6 flex flex-col justify-between relative overflow-hidden group hover:border-violet-500/40 transition-all duration-300">
          <div class="absolute -right-16 -top-16 w-32 h-32 rounded-full blur-2xl bg-violet-600/10 pointer-events-none group-hover:bg-violet-600/20 transition-all"></div>
          
          <div>
            <div class="inline-flex items-center px-2.5 py-1 rounded-full bg-violet-500/10 border border-violet-500/20 text-[9px] font-extrabold uppercase tracking-widest text-violet-300">
              Kupon Aktif
            </div>
            <h3 class="mt-3 text-lg font-bold text-white leading-snug group-hover:text-violet-300 transition-colors">{{ $promo[0] }}</h3>
            <p class="text-xs text-slate-400 mt-1 font-medium leading-relaxed">{{ $promo[2] }}</p>
          </div>

          <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-between">
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">{{ $promo[1] }}</span>
            <button class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 hover:border-violet-500/50 hover:bg-violet-600 hover:text-white text-xs font-bold text-slate-300 transition-all">
              Klaim
            </button>
          </div>
        </article>
        @endforeach
      </div>
    </div>
  </div>
</section>


{{-- TESTIMONIALS SECTION --}}
<section class="py-16 bg-[#050810]">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto mb-10">
      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] font-bold tracking-widest text-violet-300 uppercase mb-2">
        💬 Testimoni
      </div>
      <h2 class="reveal text-3xl font-extrabold text-white tracking-tight">Kepuasan Pelanggan</h2>
      <p class="reveal mt-1 text-sm text-slate-400 font-medium">Ulasan asli dari ribuan pengguna setia platform MaitriProject.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach([
        ['Adrian Arifin', 'Prosesnya luar biasa cepat! Baru pencet tombol bayar, gak sampai 1 menit Diamond ML langsung masuk.', 'M***'],
        ['Budi Santoso', 'Harga vouchernya benar-benar murah meriah dibanding lapak sebelah. Pembayaran QRIS juga berjalan lancar.', 'B***'],
        ['Clarissa Putri', 'Tampilan antarmuka website sangat elegan dan bersih. Diakses lewat handphone sangat responsif dan mudah.', 'C***']
      ] as $testi)
      <article class="reveal p-6 rounded-[2rem] bg-white/[0.02] border border-white/5 hover:border-violet-500/20 hover:bg-[#111827]/10 hover:translate-y-[-4px] transition-all duration-300 flex flex-col justify-between min-h-[180px]">
        <div>
          <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-3">
              <div class="size-10 rounded-full bg-gradient-to-tr from-violet-600 to-fuchsia-600 flex items-center justify-center font-extrabold text-white text-sm shadow-inner shrink-0">
                {{ substr($testi[0], 0, 1) }}
              </div>
              <div>
                <h4 class="font-bold text-sm text-white leading-none">{{ $testi[0] }}</h4>
                <span class="text-[10px] text-slate-500 font-bold mt-1 inline-block">{{ $testi[2] }}</span>
              </div>
            </div>
            <div class="text-amber-400 text-xs tracking-tighter">★★★★★</div>
          </div>
          <p class="mt-4 text-xs text-slate-400 leading-relaxed font-medium italic">"{{ $testi[1] }}"</p>
        </div>
      </article>
      @endforeach
    </div>
  </div>
</section>


{{-- CLOSING CALL-TO-ACTION (CTA) SECTION --}}
<section class="py-20 bg-[#050810] relative overflow-hidden">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8 relative z-10">
    <div class="reveal relative overflow-hidden rounded-[2.5rem] p-8 md:p-14 border border-violet-500/30 bg-gradient-to-br from-[#1E1B4B]/30 to-[#0F172A]/70 shadow-2xl">
      <div class="absolute -right-24 -bottom-24 w-80 h-80 rounded-full blur-[100px] bg-violet-600/25 pointer-events-none"></div>
      
      <div class="max-w-2xl space-y-5 relative z-10">
        <h3 class="text-3xl md:text-5xl font-extrabold text-white tracking-tight leading-tight">
          Mulai Top Up Game <br/>
          Favorit Anda Sekarang!
        </h3>
        <p class="text-sm md:text-base text-slate-300 leading-relaxed font-medium">
          Dapatkan pulsa, paket data, voucher, diamond game, e-wallet, dan produk marketplace dengan harga bersahabat, terjamin aman, legal, serta masuk otomatis 1 detik.
        </p>
        <div class="pt-2 flex flex-col sm:flex-row gap-4">
          <a href="{{ route('catalog') }}" class="px-8 h-12 inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-sm font-bold text-white transition-all shadow-[0_0_15px_rgba(139,92,246,0.3)]">
            Buka Katalog Layanan
          </a>
          <a href="{{ route('marketplace.index') }}" class="px-8 h-12 inline-flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 hover:border-violet-500/50 hover:bg-violet-600/5 text-sm font-bold text-slate-300 hover:text-white transition-all">
            Belanja Marketplace
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
