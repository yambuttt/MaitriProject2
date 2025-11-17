<!DOCTYPE html>
<html lang="id" class="h-full bg-[#0B1120] text-slate-200">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin — MaitriProject')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .scroll-smooth {
            scroll-behavior: smooth
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none
        }
    </style>
</head>

<body class="h-full scroll-smooth" data-page="admin">

    {{-- Shell --}}
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-[260px_1fr]">

        {{-- Sidebar --}}
        <aside id="adminSidebar"
            class="z-40 border-r border-slate-800/70 bg-[#0E1524] lg:static fixed inset-y-0 left-0 w-[260px] translate-x-[-100%] lg:translate-x-0 transition-transform duration-200 will-change-transform">
            <div class="h-16 flex items-center gap-2 px-4 border-b border-slate-800/70">
                <a href="{{ route('landing') }}" class="flex items-center gap-2">
                    <svg width="28" height="28" viewBox="0 0 24 24" class="text-violet-400">
                        <defs>
                            <linearGradient id="g" x1="0" x2="1" y1="0" y2="1">
                                <stop offset="0%" stop-color="#7C3AED" />
                                <stop offset="100%" stop-color="#6D28D9" />
                            </linearGradient>
                        </defs>
                        <rect rx="6" width="24" height="24" fill="url(#g)" />
                    </svg>
                    <span class="font-semibold">MaitriProject</span>
                </a>
                <span class="ml-auto lg:hidden">
                    <button id="btnSidebarClose" class="p-2 rounded-lg border border-slate-800/70">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none">
                            <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" />
                        </svg>
                    </button>
                </span>
            </div>

            <nav class="p-3 space-y-2 overflow-y-auto h-[calc(100vh-4rem)] no-scrollbar">
                @php $r = fn($name) => request()->routeIs($name) ? 'bg-[#121a2b] text-white border-violet-700/60' : 'border-slate-800/70 hover:border-slate-700'; @endphp

                <a href="{{ route('admin.dashboard') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl border transition {{ $r('admin.dashboard') }}">
                    <svg class="size-5 text-slate-400 group-hover:text-slate-300" viewBox="0 0 24 24" fill="none">
                        <path d="M3 12h7V3H3v9Zm0 9h7v-7H3v7Zm11 0h7v-9h-7v9Zm0-18v7h7V3h-7Z" stroke="currentColor"
                            stroke-width="1.5" />
                    </svg>
                    <span>Overview</span>
                </a>

                <a href="{{ route('admin.categories.index') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl border transition {{ request()->routeIs('admin.categories.*') ? 'bg-[#121a2b] text-white border-violet-700/60' : 'border-slate-800/70 hover:border-slate-700' }}">
                    <svg class="size-5 text-slate-400 group-hover:text-slate-300" viewBox="0 0 24 24" fill="none">
                        <path d="M4 7h16M4 12h16M4 17h10" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" />
                    </svg>
                    <span>Categories</span>
                </a>
                <a href="{{ route('admin.subcategories.index') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl border transition {{ request()->routeIs('admin.subcategories.*') ? 'bg-[#121a2b] text-white border-violet-700/60' : 'border-slate-800/70 hover:border-slate-700' }}">
                    <svg class="size-5 text-slate-400 group-hover:text-slate-300" viewBox="0 0 24 24" fill="none">
                        <path d="M8 6h12M4 12h16M10 18h10" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" />
                    </svg>
                    <span>Subcategories</span>
                </a>
                <a href="{{ route('admin.products.index') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl border transition {{ request()->routeIs('admin.products.*') ? 'bg-[#121a2b] text-white border-violet-700/60' : 'border-slate-800/70 hover:border-slate-700' }}">
                    <svg class="size-5 text-slate-400 group-hover:text-slate-300" viewBox="0 0 24 24" fill="none">
                        <path d="M9 3h6l1 3h4v4l-2 2 2 2v4h-4l-1 3H9l-1-3H4v-4l2-2-2-2V6h4l1-3Z" stroke="currentColor"
                            stroke-width="1.5" />
                    </svg>
                    <span>Products</span>
                </a>
                <!-- <li>
                    <a href="{{ route('admin.digiflazz.index') }}"
                        class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm
              {{ request()->routeIs('admin.digiflazz.*') ? 'bg-slate-800 text-slate-50' : 'text-slate-300 hover:bg-slate-800/70' }}">
                        <span>Digiflazz</span>
                    </a>
                </li> -->
                <a href="{{ route('admin.digiflazz.index') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl border transition {{ request()->routeIs('admin.digiflazz.*') ? 'bg-[#121a2b] text-white border-violet-700/60' : 'border-slate-800/70 hover:border-slate-700' }}">
                    <svg class="size-5 text-slate-400 group-hover:text-slate-300" viewBox="0 0 24 24" fill="none">
                        <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10ZM3 21a9 9 0 1 1 18 0" stroke="currentColor"
                            stroke-width="1.5" />
                    </svg>
                    <span>Master Variants</span>
                </a>


                <a href="{{ route('admin.users.index') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl border transition {{ request()->routeIs('admin.users.*') ? 'bg-[#121a2b] text-white border-violet-700/60' : 'border-slate-800/70 hover:border-slate-700' }}">
                    <svg class="size-5 text-slate-400 group-hover:text-slate-300" viewBox="0 0 24 24" fill="none">
                        <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10ZM3 21a9 9 0 1 1 18 0" stroke="currentColor"
                            stroke-width="1.5" />
                    </svg>
                    <span>Users</span>
                </a>

                <a href="#"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl border transition border-slate-800/70 hover:border-slate-700">
                    <svg class="size-5 text-slate-400 group-hover:text-slate-300" viewBox="0 0 24 24" fill="none">
                        <path d="M12 15v3m0-12v3m-6 6h3m6 0h3" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                    <span>Settings</span>
                </a>
            </nav>
        </aside>

        {{-- Main --}}
        <div class="min-h-screen flex flex-col">
            {{-- Topbar --}}
            <header
                class="h-16 flex items-center gap-3 px-4 border-b border-slate-800/70 bg-[#0B1120]/80 backdrop-blur">
                <button id="btnSidebarOpen" class="lg:hidden p-2 rounded-lg border border-slate-800/70">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                </button>

                <div class="hidden md:block flex-1 max-w-xl">
                    <div class="relative">
                        <input type="search" placeholder="Cari di admin…"
                            class="w-full h-10 rounded-xl bg-[#0E1524] border border-slate-800/70 ps-9 pe-12 outline-none text-sm placeholder:text-slate-500 focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-5 text-slate-500" viewBox="0 0 24 24"
                            fill="none">
                            <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor"
                                stroke-width="1.5" />
                        </svg>
                        <kbd class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] text-slate-500">/</kbd>
                    </div>
                </div>

                <div class="ml-auto flex items-center gap-3">
                    <span class="hidden sm:block text-sm text-slate-400">Halo, {{ auth()->user()->name }}</span>
                    <div class="size-9 rounded-full bg-violet-600/30 grid place-items-center text-violet-200">
                        <span class="text-sm font-semibold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                    <form method="post" action="{{ route('logout') }}" class="hidden sm:block">
                        @csrf
                        <button
                            class="px-3 py-2 rounded-xl border border-slate-800/70 hover:border-slate-700 text-sm">Logout</button>
                    </form>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-4 md:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Overlay for mobile sidebar --}}
    <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden lg:hidden"></div>

    {{-- Minimal JS: open/close sidebar + persist state --}}
    <script>
        (function () {
            const sb = document.getElementById('adminSidebar');
            const openBtn = document.getElementById('btnSidebarOpen');
            const closeBtn = document.getElementById('btnSidebarClose');
            const backdrop = document.getElementById('sidebarBackdrop');

            function open() { sb.style.transform = 'translateX(0)'; backdrop.classList.remove('hidden'); }
            function close() { sb.style.transform = 'translateX(-100%)'; backdrop.classList.add('hidden'); }

            openBtn?.addEventListener('click', open);
            closeBtn?.addEventListener('click', close);
            backdrop?.addEventListener('click', close);
        })();
    </script>

    @stack('body')
</body>

</html>