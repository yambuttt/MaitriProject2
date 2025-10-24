<header id="navbar" class="fixed inset-x-0 top-0 z-50 backdrop-blur supports-[backdrop-filter]:bg-black/30 bg-black/20 border-b border-slate-800/60">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
    <div class="h-16 flex items-center justify-between">
      {{-- Logo --}}
      <a href="{{ route('landing') }}" class="flex items-center gap-2 group">
        <svg width="28" height="28" viewBox="0 0 24 24" class="text-violet-400 group-hover:text-violet-300 transition" aria-hidden="true">
          <defs><linearGradient id="g" x1="0" x2="1" y1="0" y2="1"><stop offset="0%" stop-color="#7C3AED"/><stop offset="100%" stop-color="#6D28D9"/></linearGradient></defs>
          <rect rx="6" width="24" height="24" fill="url(#g)"/>
          <path d="M6.5 15.5L9.5 8h2l3 7.5h-2l-.6-1.6H9.2L8.6 15.5h-2zm3.2-3.5h2.1L11 8.9 9.7 12z" fill="white"/>
        </svg>
        <span class="font-semibold tracking-wide">MaitriProject</span>
      </a>

      {{-- Search desktop --}}
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

      {{-- Nav desktop --}}
      <nav class="hidden md:flex items-center gap-6 text-sm">
        <a href="{{ route('catalog') }}" class="hover:text-slate-50 transition">Katalog</a>
        <a href="{{ route('catalog') }}#produk" class="hover:text-slate-50 transition">Produk</a>
        <a href="{{ route('landing') }}#bantuan" class="hover:text-slate-50 transition">Bantuan</a>

        @auth
          @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 transition">Dashboard</a>
          @else
            <a href="{{ route('user.dashboard') }}" class="px-4 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 transition">Dashboard</a>
          @endif
          <form method="post" action="{{ route('logout') }}">
            @csrf
            <button class="px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 transition shadow-md shadow-violet-900/30">
              Logout
            </button>
          </form>
        @endauth

        @guest
          <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 transition">Masuk</a>
          <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 transition shadow-md shadow-violet-900/30">Daftar</a>
        @endguest
      </nav>

      {{-- Mobile toggle --}}
      <button id="mobileBtn" class="md:hidden inline-flex items-center justify-center rounded-xl p-2 border border-slate-800/70">
        <svg class="size-5" viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.5"/></svg>
      </button>
    </div>
  </div>

  {{-- Mobile drawer --}}
  <div id="mobileMenu" class="md:hidden hidden border-t border-slate-800/70 bg-[#0D1320]">
    <div class="px-4 py-3 space-y-2">
      <a href="{{ route('catalog') }}" class="block py-2">Katalog</a>
      <a href="{{ route('catalog') }}#produk" class="block py-2">Produk</a>
      <a href="{{ route('landing') }}#bantuan" class="block py-2">Bantuan</a>

      @auth
        @if(auth()->user()->isAdmin())
          <a href="{{ route('admin.dashboard') }}" class="block py-2">Dashboard</a>
        @else
          <a href="{{ route('user.dashboard') }}" class="block py-2">Dashboard</a>
        @endif
        <form method="post" action="{{ route('logout') }}" class="pt-2">
          @csrf
          <button class="w-full px-4 py-2 rounded-xl bg-violet-600">Logout</button>
        </form>
      @endauth

      @guest
        <div class="flex gap-2 pt-2">
          <a href="{{ route('login') }}" class="flex-1 px-4 py-2 text-center rounded-xl border border-slate-800/70">Masuk</a>
          <a href="{{ route('register') }}" class="flex-1 px-4 py-2 text-center rounded-xl bg-violet-600">Daftar</a>
        </div>
      @endguest
    </div>
  </div>
</header>
