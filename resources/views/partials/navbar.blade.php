<header id="navbar" class="fixed inset-x-0 top-0 z-50 backdrop-blur-md bg-[#050810]/70 border-b border-white/5 transition-all duration-300">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8">
    <div class="h-20 flex items-center justify-between gap-4">
      
      {{-- Logo --}}
      <a href="{{ route('landing') }}" class="flex items-center gap-2.5 group shrink-0">
        <div class="relative flex items-center justify-center size-10 rounded-xl bg-gradient-to-tr from-violet-600 to-fuchsia-600 shadow-[0_0_15px_rgba(139,92,246,0.3)] group-hover:shadow-[0_0_25px_rgba(139,92,246,0.5)] transition-all duration-300">
          <svg width="20" height="20" viewBox="0 0 24 24" class="text-white" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="12 2 2 7 12 12 22 7 12 2" />
            <polyline points="2 17 12 22 22 17" />
            <polyline points="2 12 12 17 22 12" />
          </svg>
        </div>
        <span class="font-extrabold text-lg tracking-wider bg-gradient-to-r from-white via-slate-100 to-slate-400 bg-clip-text text-transparent group-hover:from-white group-hover:to-violet-300 transition-all">
          Maitri<span class="text-violet-400">Project</span>
        </span>
      </a>

      {{-- Search desktop --}}
      <div class="hidden lg:flex flex-1 max-w-lg mx-6">
        <form action="{{ route('search') }}" method="get" class="relative w-full group">
          <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari game, voucher, e-wallet..."
            class="h-11 w-full rounded-2xl bg-black/40 border border-white/10 ps-11 pe-20 py-2 outline-none text-sm text-white placeholder:text-slate-500 focus:border-violet-500/50 focus:bg-black/60 focus:ring-0 transition-all duration-300"
          >
          <svg class="absolute left-4 top-1/2 -translate-y-1/2 size-4 text-slate-500 group-focus-within:text-violet-400 transition-colors" viewBox="0 0 24 24" fill="none">
            <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>

          <button type="submit"
            class="absolute right-2 top-1/2 -translate-y-1/2 px-4 py-1.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-xs font-bold text-white tracking-wide transition-all shadow-[0_0_10px_rgba(139,92,246,0.2)]">
            Cari
          </button>
        </form>
      </div>

      {{-- Nav desktop --}}
      <nav class="hidden md:flex items-center gap-6 text-sm font-bold tracking-wide">
        <a href="{{ route('catalog') }}" class="text-slate-300 hover:text-white transition-colors relative after:absolute after:bottom-[-6px] after:left-0 after:w-0 after:h-0.5 after:bg-violet-400 hover:after:w-full after:transition-all">Digital Goods</a>
        <a href="{{ route('marketplace.index') }}" class="text-slate-300 hover:text-white transition-colors relative after:absolute after:bottom-[-6px] after:left-0 after:w-0 after:h-0.5 after:bg-violet-400 hover:after:w-full after:transition-all">Marketplace</a>

        @auth
          @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl border border-white/10 hover:border-violet-500/50 hover:bg-violet-600/5 text-slate-300 hover:text-white transition-all">Dashboard</a>
          @else
            <a href="{{ route('user.dashboard') }}" class="px-4 py-2 rounded-xl border border-white/10 hover:border-violet-500/50 hover:bg-violet-600/5 text-slate-300 hover:text-white transition-all">Dashboard</a>
          @endif
          <form method="post" action="{{ route('logout') }}" class="shrink-0">
            @csrf
            <button class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white transition-all shadow-md shadow-rose-950/20">
              Logout
            </button>
          </form>
        @endauth

        @guest
          <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl border border-white/10 hover:border-violet-500/50 hover:bg-violet-600/5 text-slate-300 hover:text-white transition-all">Login</a>
          <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white transition-all shadow-[0_0_15px_rgba(139,92,246,0.3)]">Daftar</a>
        @endguest
      </nav>

      {{-- Mobile toggle --}}
      <button id="mobileBtn" class="md:hidden inline-flex items-center justify-center rounded-xl p-2.5 border border-white/10 bg-white/5 hover:bg-white/10 transition-colors text-white">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
    </div>
  </div>

  {{-- Mobile drawer --}}
  <div id="mobileMenu" class="md:hidden hidden border-t border-white/5 bg-[#070b14]/95 backdrop-blur-xl">
    <div class="px-4 py-4 space-y-3">
      
      {{-- Mobile Search --}}
      <form action="{{ route('search') }}" method="get" class="relative w-full mb-3">
        <input
          type="search"
          name="q"
          value="{{ request('q') }}"
          placeholder="Cari game, voucher..."
          class="h-11 w-full rounded-2xl bg-black/40 border border-white/10 ps-10 pe-16 py-2 outline-none text-sm text-white focus:border-violet-500/50 focus:bg-black/60 transition-all"
        >
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-500" viewBox="0 0 24 24" fill="none">
          <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1 rounded-xl bg-violet-600 text-xs font-bold text-white">
          Go
        </button>
      </form>

      <a href="{{ route('catalog') }}" class="block py-2 text-sm font-bold text-slate-300 hover:text-white transition-colors">Digital Goods</a>
      <a href="{{ route('marketplace.index') }}" class="block py-2 text-sm font-bold text-slate-300 hover:text-white transition-colors">Marketplace</a>

      @auth
        @if(auth()->user()->isAdmin())
          <a href="{{ route('admin.dashboard') }}" class="block py-2 text-sm font-bold text-slate-300 hover:text-white transition-colors">Dashboard</a>
        @else
          <a href="{{ route('user.dashboard') }}" class="block py-2 text-sm font-bold text-slate-300 hover:text-white transition-colors">Dashboard</a>
        @endif
        <form method="post" action="{{ route('logout') }}" class="pt-2">
          @csrf
          <button class="w-full h-11 rounded-xl bg-rose-600 hover:bg-rose-500 text-sm font-bold text-white transition-all">Logout</button>
        </form>
      @endauth

      @guest
        <div class="flex gap-2 pt-2">
          <a href="{{ route('login') }}" class="flex-1 h-11 flex items-center justify-center rounded-xl border border-white/10 text-sm font-bold text-slate-300 hover:text-white hover:bg-white/5 transition-all">Login</a>
          <a href="{{ route('register') }}" class="flex-1 h-11 flex items-center justify-center rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 text-sm font-bold text-white shadow-lg shadow-violet-950/20">Daftar</a>
        </div>
      @endguest
    </div>
  </div>
</header>

<script>
  (function () {
    const btn = document.getElementById('mobileBtn');
    const menu = document.getElementById('mobileMenu');
    if (btn && menu) {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        menu.classList.toggle('hidden');
      });
    }
  })();
</script>
