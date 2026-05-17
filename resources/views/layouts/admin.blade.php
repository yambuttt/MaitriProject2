<!DOCTYPE html>
<html lang="id" class="h-full bg-[#050810] text-slate-200">

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

        .luxury-sidebar {
            background: rgba(7, 11, 20, 0.85);
            backdrop-filter: blur(25px);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .admin-glass-header {
            background: rgba(5, 8, 16, 0.7);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }

        .nav-active {
            background: linear-gradient(90deg, rgba(139, 92, 246, 0.12) 0%, rgba(236, 72, 153, 0.03) 100%);
            border-left: 3px solid #8b5cf6;
            color: #ffffff !important;
            border-color: rgba(139, 92, 246, 0.2);
        }

        .nav-inactive {
            border-left: 3px solid transparent;
            border-color: transparent;
        }

        .nav-inactive:hover {
            background: rgba(255, 255, 255, 0.02);
            color: #ffffff;
            border-left-color: rgba(139, 92, 246, 0.3);
        }
    </style>
</head>

<body class="h-full scroll-smooth font-medium antialiased text-slate-300" data-page="admin">

    {{-- Shell --}}
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-[270px_1fr]">

        {{-- Sidebar --}}
        <aside id="adminSidebar" class="z-40 luxury-sidebar
                   lg:static fixed inset-y-0 left-0 w-[270px]
                   -translate-x-full lg:translate-x-0
                   transition-transform duration-300 ease-out will-change-transform
                   shadow-2xl shadow-black/80 flex flex-col justify-between">

            <div>
                {{-- Logo branding --}}
                <div class="h-20 flex items-center justify-between gap-2 px-6 border-b border-white/5">
                    <a href="{{ route('landing') }}" class="flex items-center gap-2.5 group">
                        <div class="relative flex items-center justify-center size-9 rounded-xl bg-gradient-to-tr from-violet-600 to-fuchsia-600 shadow-[0_0_15px_rgba(139,92,246,0.3)] group-hover:shadow-[0_0_20px_rgba(139,92,246,0.5)] transition-all duration-300">
                            <svg width="18" height="18" viewBox="0 0 24 24" class="text-white" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 2 7 12 12 22 7 12 2" />
                                <polyline points="2 17 12 22 22 17" />
                                <polyline points="2 12 12 17 22 12" />
                            </svg>
                        </div>
                        <span class="font-extrabold text-base tracking-wider bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">
                            Maitri<span class="text-violet-400 font-bold">Admin</span>
                        </span>
                    </a>

                    <span class="ml-auto lg:hidden">
                        <button id="btnSidebarClose" class="size-9 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 flex items-center justify-center text-white transition-colors">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </span>
                </div>

                {{-- Sidebar Navigation --}}
                <nav class="p-4 space-y-1.5 overflow-y-auto h-[calc(100vh-10rem)] no-scrollbar font-bold text-xs tracking-wider uppercase text-slate-400">
                    
                    @php
                        $isActive = fn($routes) => collect((array) $routes)->contains(fn($route) => request()->routeIs($route));
                        $style = fn($routes) => $isActive($routes) ? 'nav-active' : 'nav-inactive';
                    @endphp

                    <a href="{{ route('admin.dashboard') }}"
                        class="group flex items-center gap-3.5 px-4 h-12 rounded-xl transition-all duration-200 {{ $style('admin.dashboard') }}">
                        <svg class="size-4 shrink-0 transition-colors {{ $isActive('admin.dashboard') ? 'text-violet-400' : 'text-slate-500 group-hover:text-violet-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M3 12h7V3H3v9Zm0 9h7v-7H3v7Zm11 0h7v-9h-7v9Zm0-18v7h7V3h-7Z" />
                        </svg>
                        <span>Overview</span>
                    </a>

                    <a href="{{ route('admin.refunds.index') }}"
                        class="group flex items-center gap-3.5 px-4 h-12 rounded-xl transition-all duration-200 {{ $style('admin.refunds.*') }}">
                        <svg class="size-4 shrink-0 transition-colors {{ $isActive('admin.refunds.*') ? 'text-violet-400' : 'text-slate-500 group-hover:text-violet-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M12 8v13m0-13V3m0 5h9m-9 0H3" />
                        </svg>
                        <span>Refund</span>
                    </a>

                    <a href="{{ route('admin.categories.index') }}"
                        class="group flex items-center gap-3.5 px-4 h-12 rounded-xl transition-all duration-200 {{ $style('admin.categories.*') }}">
                        <svg class="size-4 shrink-0 transition-colors {{ $isActive('admin.categories.*') ? 'text-violet-400' : 'text-slate-500 group-hover:text-violet-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M4 7h16M4 12h16M4 17h10" />
                        </svg>
                        <span>Categories</span>
                    </a>

                    <a href="{{ route('admin.subcategories.index') }}"
                        class="group flex items-center gap-3.5 px-4 h-12 rounded-xl transition-all duration-200 {{ $style('admin.subcategories.*') }}">
                        <svg class="size-4 shrink-0 transition-colors {{ $isActive('admin.subcategories.*') ? 'text-violet-400' : 'text-slate-500 group-hover:text-violet-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M8 6h12M4 12h16M10 18h10" />
                        </svg>
                        <span>Subcategories</span>
                    </a>

                    <a href="{{ route('admin.products.index') }}"
                        class="group flex items-center gap-3.5 px-4 h-12 rounded-xl transition-all duration-200 {{ $style('admin.products.*') }}">
                        <svg class="size-4 shrink-0 transition-colors {{ $isActive('admin.products.*') ? 'text-violet-400' : 'text-slate-500 group-hover:text-violet-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M9 3h6l1 3h4v4l-2 2 2 2v4h-4l-1 3H9l-1-3H4v-4l2-2-2-2V6h4l1-3Z" />
                        </svg>
                        <span>Products</span>
                    </a>

                    <a href="{{ route('admin.digiflazz.index') }}"
                        class="group flex items-center gap-3.5 px-4 h-12 rounded-xl transition-all duration-200 {{ $style('admin.digiflazz.*') }}">
                        <svg class="size-4 shrink-0 transition-colors {{ $isActive('admin.digiflazz.*') ? 'text-violet-400' : 'text-slate-500 group-hover:text-violet-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 8v8M8 12h8" />
                        </svg>
                        <span>Master Variants</span>
                    </a>

                    <a href="{{ route('admin.marketplace.orders.index') }}"
                        class="group flex items-center gap-3.5 px-4 h-12 rounded-xl transition-all duration-200 {{ $style('admin.marketplace.orders.*') }}">
                        <svg class="size-4 shrink-0 transition-colors {{ $isActive('admin.marketplace.orders.*') ? 'text-violet-400' : 'text-slate-500 group-hover:text-violet-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M4 4h16v4H4V4Zm0 6h10v4H4v-4Zm0 6h7v4H4v-4Zm12 0h4v4h-4v-4Z" />
                        </svg>
                        <span>Marketplace Orders</span>
                    </a>

                    <a href="{{ route('admin.marketplace.products.index') }}"
                        class="group flex items-center gap-3.5 px-4 h-12 rounded-xl transition-all duration-200 {{ $style('admin.marketplace.products.*') }}">
                        <svg class="size-4 shrink-0 transition-colors {{ $isActive('admin.marketplace.products.*') ? 'text-violet-400' : 'text-slate-500 group-hover:text-violet-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M4 5h16v4H4V5Zm0 5h10v4H4v-4Zm0 5h7v4H4v-4Zm12 0h4v4h-4v-4Z" />
                        </svg>
                        <span>Marketplace Catalog</span>
                    </a>

                    <a href="{{ route('admin.affiliates.applications') }}"
                        class="group flex items-center gap-3.5 px-4 h-12 rounded-xl transition-all duration-200 {{ $style('admin.affiliates.applications*') }}">
                        <svg class="size-4 shrink-0 transition-colors {{ $isActive('admin.affiliates.applications*') ? 'text-violet-400' : 'text-slate-500 group-hover:text-violet-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M4 7h16M4 12h16M4 17h10" />
                        </svg>
                        <span>Affiliate Applicants</span>
                    </a>

                    <a href="{{ route('admin.affiliates.index') }}"
                        class="group flex items-center gap-3.5 px-4 h-12 rounded-xl transition-all duration-200 {{ $isActive('admin.affiliates.*') && !request()->routeIs('admin.affiliates.applications*') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="size-4 shrink-0 transition-colors {{ $isActive('admin.affiliates.*') && !request()->routeIs('admin.affiliates.applications*') ? 'text-violet-400' : 'text-slate-500 group-hover:text-violet-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="12" cy="7" r="4" />
                            <path d="M4 21v-2a4 4 0 014-4h8a4 4 0 014 4v2" />
                        </svg>
                        <span>Affiliates</span>
                    </a>

                    <a href="{{ route('admin.affiliate-levels.index') }}"
                        class="group flex items-center gap-3.5 px-4 h-12 rounded-xl transition-all duration-200 {{ $style('admin.affiliate-levels.*') }}">
                        <svg class="size-4 shrink-0 transition-colors {{ $isActive('admin.affiliate-levels.*') ? 'text-violet-400' : 'text-slate-500 group-hover:text-violet-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M4 4h16v16H4V4Zm4 12V8m4 8V6m4 10v-6" />
                        </svg>
                        <span>Affiliate Levels</span>
                    </a>

                    <a href="{{ route('admin.point-redeems.index') }}"
                        class="group flex items-center gap-3.5 px-4 h-12 rounded-xl transition-all duration-200 {{ $style('admin.point-redeems.*') }}">
                        <svg class="size-4 shrink-0 transition-colors {{ $isActive('admin.point-redeems.*') ? 'text-violet-400' : 'text-slate-500 group-hover:text-violet-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 8v8M9 12h6" />
                        </svg>
                        <span>Redeem Requests</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}"
                        class="group flex items-center gap-3.5 px-4 h-12 rounded-xl transition-all duration-200 {{ $style('admin.users.*') }}">
                        <svg class="size-4 shrink-0 transition-colors {{ $isActive('admin.users.*') ? 'text-violet-400' : 'text-slate-500 group-hover:text-violet-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m16-10a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Users</span>
                    </a>

                    <a href="#"
                        class="group flex items-center gap-3.5 px-4 h-12 rounded-xl transition-all duration-200 nav-inactive">
                        <svg class="size-4 shrink-0 text-slate-500 group-hover:text-violet-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="12" cy="12" r="3" />
                            <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 005 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
                        </svg>
                        <span>Settings</span>
                    </a>
                </nav>
            </div>

            {{-- Sidebar Footer (Mobile logout helper) --}}
            <div class="p-4 border-t border-white/5 lg:hidden">
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full h-11 flex items-center justify-center rounded-xl bg-rose-600 hover:bg-rose-500 font-extrabold text-xs uppercase tracking-wider text-white transition-all">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Frame --}}
        <div class="min-h-screen flex flex-col overflow-x-hidden">

            {{-- Topbar --}}
            <header class="h-20 flex items-center justify-between gap-4 px-4 md:px-6 lg:px-8 admin-glass-header sticky top-0 z-30">
                
                <div class="flex items-center gap-3">
                    <button id="btnSidebarOpen" class="lg:hidden p-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 transition-colors text-white">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {{-- Search desktop admin --}}
                    <div class="hidden md:block w-72">
                        <div class="relative group">
                            <input type="search" placeholder="Cari menu / data..."
                                class="w-full h-10 rounded-xl bg-black/40 border border-white/10 ps-9 pe-12 outline-none text-xs text-white placeholder:text-slate-500 focus:border-violet-500/50 focus:bg-black/60 focus:ring-0 transition-all duration-300">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 size-3.5 text-slate-500 group-focus-within:text-violet-400 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="11" cy="11" r="8" />
                                <path d="M21 21l-4.3-4.3" />
                            </svg>
                            <kbd class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[9px] font-bold text-slate-500 uppercase tracking-widest">/</kbd>
                        </div>
                    </div>
                </div>

                {{-- User Greetings & Avatar --}}
                <div class="flex items-center gap-3.5 shrink-0">
                    <span class="hidden sm:block text-xs font-bold text-slate-400">Halo, <span class="text-white">{{ auth()->user()->name }}</span></span>
                    
                    <div class="relative flex items-center justify-center size-9 rounded-xl bg-gradient-to-tr from-violet-600 to-fuchsia-600 shadow-[0_0_15px_rgba(139,92,246,0.3)]">
                        <span class="text-xs font-extrabold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>

                    <form method="post" action="{{ route('logout') }}" class="hidden sm:block">
                        @csrf
                        <button class="h-10 px-4 rounded-xl bg-white/5 border border-white/10 hover:border-rose-500/50 hover:bg-rose-600/5 text-xs font-bold text-slate-300 hover:text-white transition-all">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            {{-- Content Frame --}}
            <main class="flex-1 p-4 md:p-6 lg:p-8 bg-[#050810]/50 relative z-10">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Overlay for mobile sidebar --}}
    <div id="sidebarBackdrop" class="fixed inset-0 bg-black/60 backdrop-blur-sm
               opacity-0 hidden lg:hidden z-30
               transition-opacity duration-300">
    </div>

    {{-- Sidebar Drawer JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('adminSidebar');
            const openBtn = document.getElementById('btnSidebarOpen');
            const closeBtn = document.getElementById('btnSidebarClose');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (!sidebar || !openBtn || !backdrop) return;

            const openSidebar = () => {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');

                backdrop.classList.remove('hidden');
                // force reflow
                void backdrop.offsetWidth;
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
            };

            const closeSidebar = () => {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');

                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');

                setTimeout(() => {
                    backdrop.classList.add('hidden');
                }, 300);
            };

            openBtn.addEventListener('click', openSidebar);
            closeBtn?.addEventListener('click', closeSidebar);
            backdrop.addEventListener('click', closeSidebar);
        });
    </script>

    @stack('body')
    @stack('scripts')

</body>

</html>