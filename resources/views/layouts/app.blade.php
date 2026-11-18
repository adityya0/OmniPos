<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F1F5F9]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ !empty($title) ? $title . ' - OmniPOS' : 'OmniPOS' }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo-omnipos.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-omnipos.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] { display: none !important; }
            .no-scrollbar::-webkit-scrollbar { display: none !important; width: 0 !important; height: 0 !important; }
            .no-scrollbar { -ms-overflow-style: none !important; scrollbar-width: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased text-slate-800 bg-[#F1F5F9] h-full overflow-hidden flex flex-col">
        @if(session('impersonating_from_master'))
            <!-- Master Control Impersonate Floating Banner -->
            <div class="bg-indigo-900 border-b border-indigo-700 text-white px-4 py-2 text-xs flex items-center justify-between shrink-0  z-50">
                <div class="flex items-center gap-2">
                    <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Mode Master Aktif: Anda sedang mengontrol akun <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->email }} &bull; Role: {{ Auth::user()->role }})</span>
                </div>
                <a href="{{ route('master.exit') }}" class="px-2.5 py-1 bg-red-600 hover:bg-red-500 text-white font-bold rounded-lg transition text-[11px] ">
                    Kembali ke Master Hub
                </a>
            </div>
        @endif

        <div class="flex-1 flex flex-col lg:flex-row min-h-0 overflow-hidden bg-[#F1F5F9]">
            <!-- Fixed Left Sidebar Navigation -->
            @include('layouts.sidebar')

            <!-- Main Workspace Area (Scrollable Independently) -->
            <div class="flex-1 flex flex-col min-w-0 min-h-0 h-full overflow-hidden">
                @if(!request()->routeIs('admin.pos.*'))
                    <!-- Top Seamless Navbar (Desktop) -->
                    <div class="hidden lg:block shrink-0"
                         x-data="{ topSearchOpen: {{ request('search') ? 'true' : 'false' }} }"
                         @keydown.window.ctrl.k.prevent="topSearchOpen = true; $nextTick(() => $refs.globalTopSearch?.focus())"
                         @keydown.window.meta.k.prevent="topSearchOpen = true; $nextTick(() => $refs.globalTopSearch?.focus())">
                        <div class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between gap-4">
                            <!-- Left: Unified Page Title / Custom Topbar + Optional Inline Filters -->
                            <div class="flex items-center gap-3 min-w-0 flex-1 overflow-x-auto no-scrollbar">
                                @isset($topbar)
                                    {{ $topbar }}
                                @else
                                    <h2 class="font-bold text-base sm:text-lg text-slate-900 truncate shrink-0">
                                        {{ !empty($title) ? $title : 'OmniPOS' }}
                                    </h2>
                                    @stack('topbar-left')
                                @endisset
                            </div>

                            <!-- Right: Expandable Search Icon + Round Icon Buttons (?, ✉, 🔔, Profile Avatar) -->
                            <div class="flex items-center gap-2 shrink-0">
                                <div class="flex items-center gap-1.5" @click.outside="if (!$refs.globalTopSearch?.value) topSearchOpen = false">
                                    <form x-show="topSearchOpen"
                                          x-cloak
                                          x-transition:enter="transition ease-out duration-150"
                                          x-transition:enter-start="opacity-0 scale-95"
                                          x-transition:enter-end="opacity-100 scale-100"
                                          action="{{ (Auth::check() && Auth::user()->role === 'dropshipper') ? url('/dropshipper/catalog') : url('/admin/products') }}"
                                          method="GET"
                                          class="m-0">
                                        <input type="text"
                                               x-ref="globalTopSearch"
                                               name="search"
                                               @keydown.escape="topSearchOpen = false"
                                               placeholder="{{ (Auth::check() && Auth::user()->role === 'dropshipper') ? 'Cari katalog, SKU...' : 'Cari produk, SKU...' }}"
                                               value="{{ request('search') }}"
                                               class="w-48 xl:w-56 px-3.5 py-1.5 text-xs bg-slate-50/80 focus:bg-white text-slate-700 placeholder-slate-400 rounded-full border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition font-normal">
                                    </form>
                                    <button type="button"
                                            @click="topSearchOpen = !topSearchOpen; if (topSearchOpen) $nextTick(() => $refs.globalTopSearch?.focus())"
                                            title="Cari (Ctrl+K)"
                                            class="w-9 h-9 rounded-full border flex items-center justify-center transition cursor-pointer"
                                            :class="topSearchOpen ? 'bg-emerald-50 border-emerald-300 text-emerald-700' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-500 hover:text-slate-800'">
                                        <svg class="w-4 h-4 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </button>
                                </div>
                                <a href="https://wa.me/6281234567890" target="_blank" class="w-9 h-9 rounded-full bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 transition " title="Bantuan & Dukungan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </a>

                                <a href="{{ (Auth::check() && Auth::user()->role === 'dropshipper') ? url('/dropshipper/profile') : url('/admin/dropshippers') }}" class="w-9 h-9 rounded-full bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 transition " title="Pesan & Kemitraan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </a>

                                <div class="relative w-9 h-9 rounded-full bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 transition " title="Notifikasi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                                </div>

                                <div class="flex items-center pl-1 border-l border-slate-200 ml-1">
                                    <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-full bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold text-xs flex items-center justify-center border border-emerald-200/80 transition " title="{{ Auth::user()->name ?? 'Profil' }}">
                                        {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'U' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Page Content (Own scroll container so sidebar stays 100% fixed) -->
                <main class="flex-1 {{ request()->routeIs('admin.pos.*') ? 'overflow-hidden flex flex-col h-full min-h-0 bg-white' : 'overflow-y-auto pt-16 lg:pt-0 pb-0 bg-white outline-workspace' }}" style="view-transition-name: app-main;">
                    <div class="page-transition {{ request()->routeIs('admin.pos.*') ? 'h-full flex-1 flex flex-col min-h-0' : 'space-y-0' }}">
                        @isset($header)
                            <header class="px-5 sm:px-6 py-3.5 border-b border-slate-200 bg-white" style="view-transition-name: app-header;">
                                {{ $header }}
                            </header>
                        @endisset

                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
