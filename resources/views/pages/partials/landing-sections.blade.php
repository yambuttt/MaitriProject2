{{-- Kategori --}}
<section id="kategori" class="py-14">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
    <h2 class="reveal text-2xl md:text-3xl font-semibold">Pilih Kategori</h2>
    <div class="mt-6 flex gap-2 overflow-x-auto no-scrollbar">
      @foreach(['Game','Pulsa','Data','PLN','e-Wallet'] as $i=>$k)
        <a href="{{ route('catalog') }}" class="reveal px-4 py-2 rounded-2xl border border-slate-800/70 bg-[#111826] text-sm hover:border-violet-700/60 {{ $i===0?'ring-2 ring-violet-500/30 border-violet-700/60':'' }}">{{ $k }}</a>
      @endforeach
    </div>
  </div>
</section>

{{-- Produk Populer --}}
<section id="produk" class="py-14">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
    <div class="flex items-end justify-between">
      <h2 class="reveal text-2xl md:text-3xl font-semibold">Lagi Ramai Dibeli</h2>
      <a href="{{ route('catalog') }}" class="reveal text-sm text-slate-400 hover:text-slate-200">Lihat semua</a>
    </div>
    <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      @foreach(range(1,8) as $i)
      <article class="reveal group rounded-3xl border border-slate-800/70 bg-[#111826] p-4 hover:border-violet-700/60 transition">
        <div class="flex items-center gap-3">
          <div class="size-10 rounded-xl bg-slate-800/60"></div>
          <div class="min-w-0">
            <h3 class="font-medium truncate">Produk Populer {{ $i }}</h3>
            <p class="text-xs text-slate-400">Mulai Rp {{ number_format(5000*$i,0,',','.') }}</p>
          </div>
        </div>
        <div class="mt-4 flex items-center justify-between">
          <div class="text-sm text-slate-400">Estimasi ±1–2 menit</div>
          <a href="{{ route('catalog') }}" class="px-3 py-1.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm">Top Up</a>
        </div>
      </article>
      @endforeach
    </div>
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
<section id="bantuan" class="py-14">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
    <h2 class="reveal text-2xl md:text-3xl font-semibold">FAQ</h2>
    <div class="mt-6 space-y-3">
      @foreach([
        ['Apakah resmi terhubung ke Digiflazz?','Ya, kami menggunakan API resmi Digiflazz untuk proses top up.'],
        ['Berapa lama prosesnya?','Umumnya &lt; 2 menit setelah pembayaran terkonfirmasi.'],
        ['Metode pembayaran apa saja?','VA bank, e-wallet, dan QRIS.'],
        ['Bagaimana jika gagal?','Dana dikembalikan/di-refund sesuai kebijakan.'],
      ] as [$q,$a])
      <details class="reveal group rounded-2xl border border-slate-800/70 bg-[#111826] p-4">
        <summary class="list-none cursor-pointer flex items-center justify-between">
          <span class="font-medium">{{ $q }}</span>
          <svg class="size-5 text-slate-400 group-open:rotate-180 transition" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.5"/></svg>
        </summary>
        <p class="mt-2 text-slate-400">{{ $a }}</p>
      </details>
      @endforeach
    </div>
  </div>
</section>

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
