<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MaitriProject — Top Up & Digital Goods</title>
  <meta name="description" content="Top up pulsa, data, game, PLN, dan e-wallet cepat & aman. Terhubung Digiflazz.">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    /* micro-tweaks tanpa mengganggu tailwind */
    .reveal{opacity:0;transform:translateY(16px);transition:opacity .4s ease,transform .4s ease}
    .reveal-in{opacity:1;transform:translateY(0)}
    ::selection{background:#6D28D9;color:white}
  </style>
</head>
<body class="bg-[#0B0F17] text-slate-200 antialiased">
  <!-- Navbar -->
  <header id="navbar" class="fixed inset-x-0 top-0 z-50 backdrop-blur supports-[backdrop-filter]:bg-black/30 bg-black/20 border-b border-slate-800/60">
    <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
      <div class="h-16 flex items-center justify-between">
        <!-- Logo -->
        <a href="{{ route('landing') }}" class="flex items-center gap-2 group">
          <svg width="28" height="28" viewBox="0 0 24 24" aria-hidden="true"
               class="text-violet-400 group-hover:text-violet-300 transition">
            <defs>
              <linearGradient id="g" x1="0" x2="1" y1="0" y2="1">
                <stop offset="0%" stop-color="#7C3AED"/><stop offset="100%" stop-color="#6D28D9"/>
              </linearGradient>
            </defs>
            <rect rx="6" width="24" height="24" fill="url(#g)"/>
            <path d="M6.5 15.5L9.5 8h2l3 7.5h-2l-.6-1.6H9.2L8.6 15.5h-2zm3.2-3.5h2.1L11 8.9 9.7 12z"
                  fill="white"/>
          </svg>
          <span class="font-semibold tracking-wide">MaitriProject</span>
        </a>

        <!-- Search (desktop) -->
        <div class="hidden md:flex flex-1 max-w-xl mx-6">
          <div class="relative w-full">
            <input type="search" placeholder="Cari game, pulsa, e-wallet…"
                   class="w-full rounded-2xl bg-[#111826] border border-slate-800/70 ps-10 pe-12 py-2.5 outline-none text-sm placeholder:text-slate-500 focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30 transition">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-5 text-slate-500" viewBox="0 0 24 24" fill="none">
              <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="1.5"/>
            </svg>
            <kbd class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] text-slate-500">⌘K</kbd>
          </div>
        </div>

        <!-- Nav actions -->
        <nav class="hidden md:flex items-center gap-6 text-sm">
          <a href="#kategori" class="hover:text-slate-50 transition">Katalog</a>
          <a href="#produk" class="hover:text-slate-50 transition">Produk</a>
          <a href="#bantuan" class="hover:text-slate-50 transition">Bantuan</a>
          <button class="px-4 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 transition">Masuk</button>
          <a href="#kategori" class="px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 transition shadow-md shadow-violet-900/30">Daftar</a>
        </nav>

        <!-- Mobile menu button -->
        <button id="mobileBtn" class="md:hidden inline-flex items-center justify-center rounded-xl p-2 border border-slate-800/70">
          <svg class="size-5" viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.5"/></svg>
        </button>
      </div>
    </div>
    <!-- Mobile drawer -->
    <div id="mobileMenu" class="md:hidden hidden border-t border-slate-800/70 bg-[#0D1320]">
      <div class="px-4 py-3 space-y-2">
        <a href="#kategori" class="block py-2">Katalog</a>
        <a href="#produk" class="block py-2">Produk</a>
        <a href="#bantuan" class="block py-2">Bantuan</a>
        <div class="flex gap-2 pt-2">
          <button class="flex-1 px-4 py-2 rounded-xl border border-slate-800/70">Masuk</button>
          <a href="#kategori" class="flex-1 px-4 py-2 text-center rounded-xl bg-violet-600">Daftar</a>
        </div>
      </div>
    </div>
  </header>

  <main class="pt-16">
    <!-- Hero -->
    <section class="relative overflow-hidden">
      <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-10 items-center min-h-[70svh]">
          <div class="reveal">
            <h1 class="text-3xl md:text-5xl font-semibold leading-tight tracking-tight">
              Top Up & Digital Goods <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-violet-200">secepat kilat</span>
            </h1>
            <p class="mt-4 text-slate-400 max-w-prose">
              Pulsa, data, game, dan e-wallet resmi. Bayar aman. Masuk instan. Terhubung langsung ke Digiflazz.
            </p>
            <div class="mt-6 flex items-center gap-3">
              <a href="#kategori" class="px-5 py-3 rounded-2xl bg-violet-600 hover:bg-violet-500 transition shadow-lg shadow-violet-900/30">
                Mulai Top Up
              </a>
              <a href="#produk" class="px-5 py-3 rounded-2xl border border-slate-800/70 hover:border-slate-700 transition">Lihat Harga</a>
            </div>
            <div class="mt-6 flex items-center gap-4 text-slate-400 text-sm">
              <div class="flex items-center gap-2"><span class="inline-block size-2 rounded-full bg-emerald-500"></span> Proses &lt; 2 menit</div>
              <div class="flex items-center gap-2"><span class="inline-block size-2 rounded-full bg-violet-500"></span> 24/7</div>
            </div>
          </div>

          <!-- Visual hero -->
          <div class="relative">
            <div data-parallax class="relative isolate">
              <div class="absolute -top-10 -right-10 w-72 h-72 rounded-full blur-3xl bg-violet-600/20"></div>
              <div class="absolute -bottom-10 -left-10 w-72 h-72 rounded-full blur-3xl bg-indigo-500/10"></div>
              <div class="reveal relative rounded-3xl border border-slate-800/70 bg-[#111826] p-4 shadow-2xl">
                <!-- mockup daftar produk -->
                <div class="flex items-center justify-between p-3 rounded-2xl bg-[#0E1524] border border-slate-800/70">
                  <div class="flex items-center gap-3">
                    <div class="size-9 rounded-xl bg-violet-600/20 grid place-items-center text-violet-300">
                      <!-- bolt icon -->
                      <svg class="size-5" viewBox="0 0 24 24" fill="none"><path d="M13 3L4 14h7l-1 7 9-11h-7l1-7Z" stroke="currentColor" stroke-width="1.5"/></svg>
                    </div>
                    <div>
                      <div class="font-medium">Pulsa Telkomsel 20K</div>
                      <div class="text-xs text-slate-400">Mulai Rp 22.000</div>
                    </div>
                  </div>
                  <button class="px-3 py-1.5 text-sm rounded-xl bg-violet-600 hover:bg-violet-500">Top Up</button>
                </div>
                <div class="mt-3 grid sm:grid-cols-2 gap-3">
                  @foreach([['Diamond ML','Mulai Rp 5.000'],['Voucher FF','Mulai Rp 5.000'],['Token PLN','Mulai Rp 20.000'],['Saldo e-Wallet','Mulai Rp 10.000']] as [$name,$price])
                  <div class="p-3 rounded-2xl bg-[#0E1524] border border-slate-800/70 hover:border-violet-700/60 transition">
                    <div class="flex items-center gap-3">
                      <div class="size-9 rounded-xl bg-slate-800/60"></div>
                      <div class="min-w-0">
                        <div class="truncate font-medium">{{ $name }}</div>
                        <div class="text-xs text-slate-400">{{ $price }}</div>
                      </div>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- subtle grid overlay -->
      <div class="pointer-events-none absolute inset-0 [background-image:linear-gradient(rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.03)_1px,transparent_1px)] [background-size:48px_48px] [mask-image:radial-gradient(closest-side,black,transparent)]"></div>
    </section>

    <!-- Trust bar -->
    <section class="py-10">
      <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
          @foreach([['Terhubung Digiflazz','link'],['Transaksi Aman','shield-check'],['&lt; 2 Menit','bolt'],['Layanan 24/7','clock']] as [$label,$icon])
          <div class="reveal flex items-center gap-3 p-4 rounded-2xl bg-[#111826] border border-slate-800/70 hover:border-slate-700 transition">
            <div class="size-10 grid place-items-center rounded-xl bg-slate-800/60">
              @if($icon==='link')
              <svg class="size-5" viewBox="0 0 24 24" fill="none"><path d="M10 14a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1M14 10a5 5 0 0 1 0 7l-1 1a5 5 0 1 1-7-7l1-1" stroke="currentColor" stroke-width="1.5"/></svg>
              @elseif($icon==='shield-check')
              <svg class="size-5" viewBox="0 0 24 24" fill="none"><path d="M12 3l7 3v6c0 5-3.5 7.5-7 9-3.5-1.5-7-4-7-9V6l7-3Z" stroke="currentColor" stroke-width="1.5"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.5"/></svg>
              @elseif($icon==='bolt')
              <svg class="size-5" viewBox="0 0 24 24" fill="none"><path d="M13 3 4 14h7l-1 7 9-11h-7l1-7Z" stroke="currentColor" stroke-width="1.5"/></svg>
              @else
              <svg class="size-5" viewBox="0 0 24 24" fill="none"><path d="M12 7v5l3 2M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20Z" stroke="currentColor" stroke-width="1.5"/></svg>
              @endif
            </div>
            <div class="font-medium">{{ $label }}</div>
          </div>
          @endforeach
        </div>
      </div>
    </section>

    <!-- Kategori -->
    <section id="kategori" class="py-14">
      <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
        <h2 class="reveal text-2xl md:text-3xl font-semibold">Pilih Kategori</h2>
        <div class="mt-6 flex gap-2 overflow-x-auto no-scrollbar">
          @foreach(['Game','Pulsa','Data','PLN','e-Wallet'] as $i=>$k)
            <button class="reveal px-4 py-2 rounded-2xl border border-slate-800/70 bg-[#111826] text-sm hover:border-violet-700/60 {{ $i===0?'ring-2 ring-violet-500/30 border-violet-700/60':'' }}">
              {{ $k }}
            </button>
          @endforeach
        </div>

        <!-- Kartu kategori -->
        <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
          @foreach([
            ['Game','Diamond & voucher resmi.'],
            ['Pulsa','Semua operator, harga bersahabat.'],
            ['Data','Kuota termurah, langsung aktif.'],
            ['PLN','Token & tagihan mudah.'],
            ['e-Wallet','Isi saldo cepat.'],
            ['Voucher','Belanja & hiburan.'],
          ] as [$title,$desc])
          <article class="reveal p-5 rounded-3xl bg-[#111826] border border-slate-800/70 hover:border-violet-700/60 transition group">
            <div class="flex items-center gap-3">
              <div class="size-10 rounded-xl bg-slate-800/60"></div>
              <div class="min-w-0">
                <h3 class="font-medium">{{ $title }}</h3>
                <p class="text-sm text-slate-400 line-clamp-2">{{ $desc }}</p>
              </div>
            </div>
            <div class="mt-4">
              <a href="#produk" class="inline-flex items-center gap-2 text-violet-300 group-hover:text-violet-200">
                Jelajahi
                <svg class="size-4" viewBox="0 0 24 24" fill="none"><path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>
              </a>
            </div>
          </article>
          @endforeach
        </div>
      </div>
    </section>

    <!-- Produk Populer -->
    <section id="produk" class="py-14">
      <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
        <div class="flex items-end justify-between">
          <h2 class="reveal text-2xl md:text-3xl font-semibold">Lagi Ramai Dibeli</h2>
          <a href="#kategori" class="reveal text-sm text-slate-400 hover:text-slate-200">Lihat semua</a>
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
              <button class="px-3 py-1.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm">Top Up</button>
            </div>
          </article>
          @endforeach
        </div>
      </div>
    </section>

    <!-- Cara Kerja -->
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

    <!-- Promo Carousel -->
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

    <!-- Testimoni -->
    <section class="py-14">
      <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
        <h2 class="reveal text-2xl md:text-3xl font-semibold">Apa kata mereka</h2>
        <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
          @foreach([
            ['A***','Cepat banget, 1 menit langsung masuk!'],
            ['B***','Harga bersahabat dan proses aman.'],
            ['C***','UI-nya enak dipakai.'],
          ] as [$name,$text])
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

    <!-- FAQ -->
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
              <svg class="size-5 text-slate-400 group-open:rotate-180 transition" viewBox="0 0 24 24" fill="none">
                <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </summary>
            <p class="mt-2 text-slate-400">{{ $a }}</p>
          </details>
          @endforeach
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="py-16">
      <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
        <div class="reveal relative overflow-hidden rounded-3xl border border-slate-800/70 bg-gradient-to-br from-violet-700/20 to-indigo-700/10 p-8">
          <h3 class="text-xl md:text-2xl font-semibold">Mulai top up sekarang.</h3>
          <p class="mt-1 text-slate-300">Pulsa, data, game, PLN, e-Wallet—instan & aman.</p>
          <a href="#kategori" class="mt-4 inline-flex px-5 py-3 rounded-2xl bg-violet-600 hover:bg-violet-500">Buka Katalog</a>
          <div class="pointer-events-none absolute -right-20 -bottom-20 w-[320px] h-[320px] rounded-full blur-3xl bg-violet-600/20"></div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="border-t border-slate-800/70">
    <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8 py-10">
      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div>
          <div class="flex items-center gap-2">
            <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><rect rx="6" width="24" height="24" fill="#7C3AED"/></svg>
            <span class="font-semibold">MaitriProject</span>
          </div>
          <p class="mt-3 text-sm text-slate-400">Top up digital goods cepat & aman.</p>
        </div>
        <div>
          <h4 class="font-medium">Produk</h4>
          <ul class="mt-3 space-y-2 text-sm text-slate-400">
            <li>Game</li><li>Pulsa</li><li>Data</li><li>PLN</li><li>e-Wallet</li>
          </ul>
        </div>
        <div>
          <h4 class="font-medium">Bantuan</h4>
          <ul class="mt-3 space-y-2 text-sm text-slate-400">
            <li>FAQ</li><li>Kontak</li><li>S&K</li>
          </ul>
        </div>
        <div>
          <h4 class="font-medium">Ikuti</h4>
          <ul class="mt-3 space-y-2 text-sm text-slate-400">
            <li>Instagram</li><li>TikTok</li><li>X</li>
          </ul>
        </div>
      </div>
      <div class="mt-8 text-xs text-slate-500">© {{ date('Y') }} MaitriProject. All rights reserved.</div>
    </div>
  </footer>

  <!-- Inline minimal JS for demo; logic utama di resources/js/landing.js -->
  <script>
    // Mobile drawer
    const mobileBtn=document.getElementById('mobileBtn');
    const mobileMenu=document.getElementById('mobileMenu');
    if(mobileBtn&&mobileMenu){mobileBtn.addEventListener('click',()=>mobileMenu.classList.toggle('hidden'))}
  </script>
</body>
</html>
