<!doctype html>
<html lang="id" class="h-full">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Dashboard — Maitri')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('head')
</head>

<body class="min-h-screen bg-[#050816] text-slate-200 antialiased">
  {{-- Background glow --}}
  <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
    <div class="absolute -top-40 left-1/2 h-[520px] w-[520px] -translate-x-1/2 rounded-full bg-violet-600/15 blur-3xl"></div>
    <div class="absolute -bottom-48 -left-24 h-[520px] w-[520px] rounded-full bg-indigo-600/10 blur-3xl"></div>
    <div class="absolute inset-0 bg-[radial-gradient(900px_circle_at_20%_10%,rgba(124,58,237,.12),transparent_60%),radial-gradient(900px_circle_at_80%_30%,rgba(99,102,241,.10),transparent_55%)]"></div>
  </div>

  <div class="min-h-screen flex">

    {{-- Sidebar DESKTOP --}}
    <aside class="hidden md:flex w-[280px] flex-col border-r border-slate-800/70 bg-[#070b15]/80 backdrop-blur">
      <div class="px-5 py-4 border-b border-slate-800/70">
        <div class="flex items-center gap-3">
          <div class="size-10 rounded-2xl bg-gradient-to-br from-violet-600 to-indigo-600 grid place-items-center shadow-lg shadow-violet-900/30">
            <svg class="size-5" viewBox="0 0 24 24" fill="none">
              <path d="M7 13l3 3 7-8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div>
            <div class="text-base font-semibold tracking-wide">MaitriProject</div>
            <div class="text-xs text-slate-400">Member Dashboard</div>
          </div>
        </div>
      </div>

      <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
        @php
          $nav = [
            ['label'=>'Overview', 'route'=>'user.dashboard'],
            ['label'=>'Saldo & Topup', 'route'=>'dashboard.wallet'],
            ['label'=>'Riwayat Produk', 'route'=>'dashboard.orders'],
            ['label'=>'Pesanan Marketplace', 'route'=>'dashboard.marketplace.orders'],
            ['label'=>'Affiliate', 'route'=>'dashboard.affiliate'],
          ];
        @endphp

        @foreach($nav as $item)
          <a href="{{ route($item['route']) }}"
             class="group relative flex items-center justify-between px-3 py-2.5 rounded-2xl border border-transparent hover:border-slate-700/60 hover:bg-slate-900/40 transition
                    @if(request()->routeIs($item['route'])) bg-slate-900/60 border-slate-700/60 @endif">
            <span class="flex items-center gap-3">
              <span class="size-2 rounded-full bg-slate-600/60 group-hover:bg-violet-400/70 transition
                @if(request()->routeIs($item['route'])) bg-violet-400 @endif"></span>
              <span class="font-medium">{{ $item['label'] }}</span>
            </span>

            @if(request()->routeIs($item['route']))
              <span class="text-[11px] px-2 py-0.5 rounded-full bg-violet-500/10 text-violet-300 border border-violet-500/20">aktif</span>
            @endif
          </a>
        @endforeach
      </nav>

      <div class="px-4 py-4 border-t border-slate-800/70">
        <div class="flex items-center gap-3 rounded-2xl border border-slate-800/70 bg-slate-950/40 p-3">
          <div class="size-10 rounded-2xl bg-slate-800/50 grid place-items-center">
            <span class="text-sm font-semibold">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
          </div>
          <div class="min-w-0">
            <div class="text-sm font-medium truncate">{{ auth()->user()->name }}</div>
            <div class="text-xs text-slate-400 truncate">{{ auth()->user()->email ?? '' }}</div>
          </div>
        </div>

        <form method="post" action="{{ route('logout') }}" class="mt-3">
          @csrf
          <button class="w-full h-10 rounded-2xl border border-slate-800/70 hover:border-slate-700 hover:bg-slate-900/40 text-sm transition">
            Logout
          </button>
        </form>

        <div class="mt-3 text-[11px] text-slate-500">&copy; {{ date('Y') }} MaitriProject</div>
      </div>
    </aside>

    {{-- Sidebar MOBILE (drawer) --}}
    <aside id="dashboardSidebarMobile"
      class="fixed inset-y-0 left-0 z-40 w-[280px] flex-col border-r border-slate-800/70 bg-[#070b15]/95 backdrop-blur transform -translate-x-full md:hidden transition-transform duration-200">
      <div class="px-5 py-4 border-b border-slate-800/70 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="size-9 rounded-2xl bg-gradient-to-br from-violet-600 to-indigo-600 grid place-items-center">
            <svg class="size-5" viewBox="0 0 24 24" fill="none">
              <path d="M7 13l3 3 7-8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div>
            <div class="text-base font-semibold">MaitriProject</div>
            <div class="text-xs text-slate-400">Member Dashboard</div>
          </div>
        </div>

        <button id="dashboardSidebarClose"
          class="inline-flex items-center justify-center rounded-2xl p-2 border border-slate-800/70 hover:bg-slate-900/40">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
            <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
          </svg>
        </button>
      </div>

      <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
        @foreach($nav as $item)
          <a href="{{ route($item['route']) }}"
             class="flex items-center gap-3 px-3 py-2.5 rounded-2xl hover:bg-slate-900/40 transition
                    @if(request()->routeIs($item['route'])) bg-slate-900/60 border border-slate-700/60 @endif">
            <span class="size-2 rounded-full
              @if(request()->routeIs($item['route'])) bg-violet-400 @else bg-slate-600/60 @endif"></span>
            <span class="font-medium">{{ $item['label'] }}</span>
          </a>
        @endforeach
      </nav>

      <div class="px-4 py-4 border-t border-slate-800/70 text-xs text-slate-500">
        &copy; {{ date('Y') }} MaitriProject
      </div>
    </aside>

    {{-- Overlay mobile --}}
    <div id="dashboardSidebarOverlay"
      class="fixed inset-0 z-30 bg-black/60 opacity-0 pointer-events-none md:hidden transition-opacity duration-200">
    </div>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">
      <header class="sticky top-0 z-20 h-14 border-b border-slate-800/70 bg-[#050816]/70 backdrop-blur">
        <div class="h-full flex items-center justify-between px-4 md:px-6">
          <div class="flex items-center gap-3 min-w-0">
            <button id="dashboardSidebarBtn"
              class="md:hidden inline-flex items-center justify-center rounded-2xl p-2 border border-slate-800/70 hover:bg-slate-900/40">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
              </svg>
            </button>

            <div class="min-w-0">
              <div class="text-xs text-slate-400">@yield('breadcrumb', 'Dashboard')</div>
              <div class="text-sm font-semibold truncate">@yield('title', 'Dashboard')</div>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <span class="hidden sm:inline text-sm text-slate-300 truncate max-w-[220px]">
              {{ auth()->user()->name }}
            </span>
            <form method="post" action="{{ route('logout') }}">
              @csrf
              <button class="text-xs px-3 py-1.5 rounded-xl border border-slate-800/70 hover:bg-slate-900/40 transition">
                Logout
              </button>
            </form>
          </div>
        </div>
      </header>

      <main class="flex-1 p-4 md:p-8 min-w-0">
        <div class="mx-auto w-full max-w-6xl">
          @yield('content')
        </div>
      </main>
    </div>
  </div>

  {{-- Script toggle sidebar mobile (tetap seperti versi kamu) --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const btn = document.getElementById('dashboardSidebarBtn');
      const close = document.getElementById('dashboardSidebarClose');
      const sidebar = document.getElementById('dashboardSidebarMobile');
      const overlay = document.getElementById('dashboardSidebarOverlay');

      if (!btn || !sidebar || !overlay) return;

      function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('pointer-events-none');
        overlay.classList.add('opacity-100');
      }

      function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('pointer-events-none');
        overlay.classList.remove('opacity-100');
      }

      btn.addEventListener('click', openSidebar);
      close?.addEventListener('click', closeSidebar);
      overlay.addEventListener('click', closeSidebar);

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSidebar();
      });
    });
  </script>

  @stack('body')
</body>
</html>
