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

    {{-- Sidebar --}}
    <aside class="hidden md:flex w-64 flex-col border-r border-slate-800 bg-[#070b15]">
      <div class="px-5 py-4 border-b border-slate-800">
        <div class="text-lg font-semibold">MaitriProject</div>
        <div class="text-xs text-slate-400">Member Dashboard</div>
      </div>

      <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
        <a href="{{ route('user.dashboard') }}"
           class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-800/60 @if(request()->routeIs('dashboard.index')) bg-slate-800 @endif">
          <span>Overview</span>
        </a>
        <a href="{{ route('dashboard.wallet') }}"
           class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-800/60 @if(request()->routeIs('dashboard.wallet')) bg-slate-800 @endif">
          <span>Saldo &amp; Topup</span>
        </a>
        {{-- nanti bisa tambah: riwayat, profil, dll --}}
      </nav>

      <div class="px-4 py-4 border-t border-slate-800 text-xs text-slate-500">
        &copy; {{ date('Y') }} MaitriProject
      </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col">
      <header class="h-14 border-b border-slate-800 flex items-center justify-between px-4 md:px-6 bg-[#050816]/80 backdrop-blur">
        <div class="text-sm text-slate-400">
          @yield('breadcrumb', 'Dashboard')
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

  @stack('body')
</body>
</html>
