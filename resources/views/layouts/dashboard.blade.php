<!doctype html>
<html lang="id" class="h-full">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Dashboard — Maitri')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('head')
</head>

<body class="bg-[#050816] text-slate-200 antialiased">

  <div class="min-h-screen flex">

    {{-- Sidebar DESKTOP --}}
    <aside class="hidden md:flex w-64 flex-col border-r border-slate-800 bg-[#070b15]">
      <div class="px-5 py-4 border-b border-slate-800">
        <div class="text-lg font-semibold">MaitriProject</div>
        <div class="text-xs text-slate-400">Member Dashboard</div>
      </div>

      <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
        <a href="{{ route('user.dashboard') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-800/60 @if(request()->routeIs('user.dashboard')) bg-slate-800 @endif">
          <span>Overview</span>
        </a>

        <a href="{{ route('dashboard.wallet') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-800/60 @if(request()->routeIs('dashboard.wallet')) bg-slate-800 @endif">
          <span>Saldo &amp; Topup</span>
        </a>

        <a href="{{ route('dashboard.orders') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-800/60 @if(request()->routeIs('dashboard.orders')) bg-slate-800 @endif">
          <span>Riwayat Produk</span>
        </a>

        <a href="{{ route('dashboard.marketplace.orders') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-800/60 @if(request()->routeIs('dashboard.marketplace.orders')) bg-slate-800 @endif">
          <span>Pesanan Marketplace</span>
        </a>
        <a href="{{ route('dashboard.affiliate') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-800/60 @if(request()->routeIs('dashboard.affiliate')) bg-slate-800 @endif">
          <span>Affiliate</span>
        </a>
        
      </nav>

      <div class="px-4 py-4 border-t border-slate-800 text-xs text-slate-500">
        &copy; {{ date('Y') }} MaitriProject
      </div>
    </aside>

    {{-- Sidebar MOBILE (drawer) --}}
    <aside id="dashboardSidebarMobile"
      class="fixed inset-y-0 left-0 z-40 w-64 flex-col border-r border-slate-800 bg-[#070b15] transform -translate-x-full md:hidden transition-transform duration-200">
      <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
        <div>
          <div class="text-lg font-semibold">MaitriProject</div>
          <div class="text-xs text-slate-400">Member Dashboard</div>
        </div>
        <button id="dashboardSidebarClose"
          class="inline-flex items-center justify-center rounded-xl p-2 border border-slate-800/70">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
            <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
          </svg>
        </button>
      </div>

      <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
        <a href="{{ route('user.dashboard') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-800/60 @if(request()->routeIs('user.dashboard')) bg-slate-800 @endif">
          <span>Overview</span>
        </a>

        <a href="{{ route('dashboard.wallet') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-800/60 @if(request()->routeIs('dashboard.wallet')) bg-slate-800 @endif">
          <span>Saldo &amp; Topup</span>
        </a>

        <a href="{{ route('dashboard.orders') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-800/60 @if(request()->routeIs('dashboard.orders')) bg-slate-800 @endif">
          <span>Riwayat Produk</span>
        </a>

        <a href="{{ route('dashboard.marketplace.orders') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-800/60 @if(request()->routeIs('dashboard.marketplace.orders')) bg-slate-800 @endif">
          <span>Pesanan Marketplace</span>
        </a>
      </nav>

      <div class="px-4 py-4 border-t border-slate-800 text-xs text-slate-500">
        &copy; {{ date('Y') }} MaitriProject
      </div>
    </aside>

    {{-- Overlay mobile --}}
    <div id="dashboardSidebarOverlay"
      class="fixed inset-0 z-30 bg-black/60 opacity-0 pointer-events-none md:hidden transition-opacity duration-200">
    </div>

    {{-- Main --}}
    <div class="flex-1 flex flex-col">
      <header
        class="h-14 border-b border-slate-800 flex items-center justify-between px-4 md:px-6 bg-[#050816]/80 backdrop-blur">
        <div class="flex items-center gap-3">
          {{-- tombol sidebar mobile --}}
          <button id="dashboardSidebarBtn"
            class="md:hidden inline-flex items-center justify-center rounded-xl p-2 border border-slate-800/70">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
              <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
          </button>
          <div class="text-sm text-slate-400">
            @yield('breadcrumb', 'Dashboard')
          </div>
        </div>

        <div class="flex items-center gap-3">
          <span class="hidden sm:inline text-sm text-slate-300">
            {{ auth()->user()->name }}
          </span>
          <form method="post" action="{{ route('logout') }}">
            @csrf
            <button class="text-xs px-3 py-1 rounded-lg border border-slate-700 hover:bg-slate-800">
              Logout
            </button>
          </form>
        </div>
      </header>

      <main class="flex-1 p-4 md:p-8">
        @yield('content')
      </main>
    </div>
  </div>

  {{-- Script toggle sidebar mobile --}}
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
      close.addEventListener('click', closeSidebar);
      overlay.addEventListener('click', closeSidebar);

      // ESC untuk tutup
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
          closeSidebar();
        }
      });
    });
  </script>

  @stack('body')
</body>

</html>