<!-- Desktop & Mobile Sidebar Container with Collapse Feature (Default: Icon-Only, Opens Only on >> Click) -->
<div x-data="{
        mobileOpen: false,
        collapsed: true,
        sidebarTipText: '',
        sidebarTipTop: 0,
        sidebarTipLeft: 0,
        toggleCollapse() {
            this.collapsed = !this.collapsed;
            this.sidebarTipText = '';
        },
        showTip(event, label) {
            if (!this.collapsed) return;
            const rect = event.currentTarget.getBoundingClientRect();
            this.sidebarTipText = label;
            this.sidebarTipTop = rect.top + (rect.height / 2);
            this.sidebarTipLeft = rect.right + 10;
        },
        hideTip() {
            this.sidebarTipText = '';
        }
     }"
     class="shrink-0 flex flex-col">

    <!-- Floating Hover Bubble Menu Sidebar (Putih Outline Abu-Hijau, Paling Atas z-[9999]) -->
    <div x-show="collapsed && sidebarTipText"
         x-cloak
         :style="`top: ${sidebarTipTop}px; left: ${sidebarTipLeft}px; transform: translateY(-50%);`"
         class="hidden lg:block fixed z-[9999] pointer-events-none px-3 py-1.5 rounded-xl bg-white border border-emerald-300/90 ring-1 ring-slate-200/70 text-xs font-semibold text-slate-700 whitespace-nowrap">
        <span x-text="sidebarTipText"></span>
    </div>

    <!-- Mobile Floating Rounded Top Navigation Bar -->
    <div class="lg:hidden fixed top-3 inset-x-3 z-50 flex items-center justify-between bg-white/95 backdrop-blur-md text-slate-800 px-4 py-2.5 border border-slate-200  rounded-[22px] shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo-omnipos.png') }}" alt="OmniPOS" class="w-7 h-7 object-contain">
            <span class="font-bold text-sm tracking-tight text-slate-900">Omni<span class="text-emerald-500">POS</span></span>
        </a>

        <div class="flex items-center gap-1">
            <div class="relative w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-white"></span>
            </div>

            <a href="{{ route('profile.edit') }}" class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center border border-emerald-200/80 mx-1">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>

            <button @click="mobileOpen = !mobileOpen" class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none" title="Buka Navigasi">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Backdrop -->
    <div x-show="mobileOpen"
         x-cloak
         @click="mobileOpen = false"
         class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-xs lg:hidden transition-opacity"></div>

    <!-- Sidebar Panel: Detached, Pure White Card with Soft Elevation & Rounded Corners -->
    <aside :class="{
               'translate-x-0': mobileOpen,
               '-translate-x-full lg:translate-x-0': !mobileOpen,
               'lg:w-[250px]': !collapsed,
               'lg:w-[76px]': collapsed
           }"
           style="view-transition-name: app-sidebar;"
           class="fixed inset-y-0 left-0 z-50 bg-white text-slate-700 flex flex-col justify-between transition-all duration-300 ease-in-out lg:static lg:h-full border-r border-slate-200 overflow-hidden w-64 lg:w-[76px]">

        <!-- Top Header & Menu -->
        <div class="flex flex-col flex-1 overflow-y-auto overflow-x-hidden no-scrollbar">

            <!-- Brand Logo (saat terbuka) / Tombol Buka Sidebar >> (saat tertutup) -->
            <div class="h-16 border-b border-slate-200 flex items-center justify-center px-3 shrink-0 transition-all duration-300"
                 :class="collapsed ? 'lg:px-3 lg:justify-center px-5 justify-between' : 'px-5 justify-between'">
                <!-- Logo Brand: Hanya tampil saat sidebar dibuka (atau pada menu mobile) -->
                <a href="{{ route('dashboard') }}"
                   :class="collapsed ? 'flex lg:hidden' : 'flex'"
                   class="items-center gap-3 overflow-hidden group">
                    <img src="{{ asset('images/logo-omnipos.png') }}" alt="OmniPOS" class="w-8 h-8 object-contain shrink-0 group-hover:scale-105 transition-transform duration-200">
                    <span class="font-bold text-base tracking-tight text-slate-900 whitespace-nowrap">
                        Omni<span class="text-emerald-500">POS</span>
                    </span>
                </a>

                <!-- Tombol Buka Sidebar (>>) menggantikan logo saat sidebar ditutup -->
                <button type="button"
                        @click="toggleCollapse()"
                        @mouseenter="showTip($event, 'Buka Sidebar')"
                        @mouseleave="hideTip()"
                        x-show="collapsed"
                        class="hidden lg:flex p-2 rounded-xl text-slate-500 hover:text-emerald-700 hover:bg-emerald-50/50 transition focus:outline-none cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Tombol Tutup Sidebar (<<) saat sidebar dibuka -->
                <button type="button"
                        @click="toggleCollapse()"
                        title="Tutup Sidebar"
                        x-show="!collapsed"
                        x-cloak
                        class="hidden lg:flex p-1.5 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition focus:outline-none cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>

                <!-- Mobile Close Button -->
                <button @click="mobileOpen = false" class="lg:hidden text-slate-400 hover:text-slate-700 p-1.5 rounded-xl hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="p-3 space-y-1 text-xs">
                <!-- Header Kategori: MENU UTAMA -->
                <div x-show="!collapsed" x-cloak class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 px-3 pt-2 pb-1.5">
                    Menu Utama
                </div>

                <!-- Dashboard (Active Menu: Green Pill Rounded) -->
                <a href="{{ route('dashboard') }}"
                   @mouseenter="showTip($event, 'Dashboard')"
                   @mouseleave="hideTip()"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition duration-150 group {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white font-medium ' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal' }}">
                    <div class="w-5 flex justify-center shrink-0">
                        <svg class="w-4 h-4 transition {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <span x-show="!collapsed" x-cloak
                          x-transition:enter="transition ease-out duration-150"
                          x-transition:enter-start="opacity-0"
                          x-transition:enter-end="opacity-100"
                          class="whitespace-nowrap">Dashboard</span>
                </a>

                @if (Auth::user()->activeSupplier())
                    @if (!Auth::user()->isTimPacking())
                        <!-- Header Kategori: KASIR & GUDANG -->
                        <div x-show="!collapsed" x-cloak class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 px-3 pt-4 pb-1.5">
                            Kasir & Gudang
                        </div>

                        <!-- POS Kasir -->
                        <a href="{{ route('admin.pos.index') }}"
                           @mouseenter="showTip($event, 'POS Kasir Toko')"
                           @mouseleave="hideTip()"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition duration-150 group {{ request()->routeIs('admin.pos.*') ? 'bg-emerald-600 text-white font-medium ' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal' }}">
                            <div class="w-5 flex justify-center shrink-0">
                                <svg class="w-4 h-4 transition {{ request()->routeIs('admin.pos.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span x-show="!collapsed" x-cloak
                                  x-transition:enter="transition ease-out duration-150"
                                  x-transition:enter-start="opacity-0"
                                  x-transition:enter-end="opacity-100"
                                  class="whitespace-nowrap">POS Kasir Toko</span>
                        </a>

                        <!-- Produk & Stok -->
                        <a href="{{ route('admin.products.index') }}"
                           @mouseenter="showTip($event, 'Produk & Stok')"
                           @mouseleave="hideTip()"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition duration-150 group {{ request()->routeIs('admin.products.*') ? 'bg-emerald-600 text-white font-medium ' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal' }}">
                            <div class="w-5 flex justify-center shrink-0">
                                <svg class="w-4 h-4 transition {{ request()->routeIs('admin.products.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <span x-show="!collapsed" x-cloak
                                  x-transition:enter="transition ease-out duration-150"
                                  x-transition:enter-start="opacity-0"
                                  x-transition:enter-end="opacity-100"
                                  class="whitespace-nowrap">Produk & Stok</span>
                        </a>

                        <!-- Analisis & Prediksi Stok -->
                        <a href="{{ route('admin.stock-analytics.index') }}"
                           @mouseenter="showTip($event, 'Analisis Stok')"
                           @mouseleave="hideTip()"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition duration-150 group {{ request()->routeIs('admin.stock-analytics.*') ? 'bg-emerald-600 text-white font-medium ' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal' }}">
                            <div class="w-5 flex justify-center shrink-0">
                                <svg class="w-4 h-4 transition {{ request()->routeIs('admin.stock-analytics.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <span x-show="!collapsed" x-cloak
                                  x-transition:enter="transition ease-out duration-150"
                                  x-transition:enter-start="opacity-0"
                                  x-transition:enter-end="opacity-100"
                                  class="whitespace-nowrap">Analisis Stok</span>
                        </a>

                        <!-- Laporan Penjualan -->
                        <a href="{{ route('admin.reports.index') }}"
                           @mouseenter="showTip($event, 'Laporan Penjualan')"
                           @mouseleave="hideTip()"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition duration-150 group {{ request()->routeIs('admin.reports.*') ? 'bg-emerald-600 text-white font-medium ' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal' }}">
                            <div class="w-5 flex justify-center shrink-0">
                                <svg class="w-4 h-4 transition {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <span x-show="!collapsed" x-cloak
                                  x-transition:enter="transition ease-out duration-150"
                                  x-transition:enter-start="opacity-0"
                                  x-transition:enter-end="opacity-100"
                                  class="whitespace-nowrap">Laporan Penjualan</span>
                        </a>
                    @endif

                    <!-- Header Kategori: KEMITRAAN & DROPSHIP -->
                    <div x-show="!collapsed" x-cloak class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 px-3 pt-4 pb-1.5">
                        {{ Auth::user()->isTimPacking() ? 'Operasional Gudang' : 'Kemitraan & Dropship' }}
                    </div>

                    @php
                        $packingBadgeQuery = \App\Models\Order::where('supplier_id', Auth::user()->activeSupplier()->id)
                            ->where('transaction_type', 'dropship');
                        if (Auth::user()->isTimPacking()) {
                            $packingBadgeQuery->where('status', 'menunggu_packing');
                        } else {
                            $packingBadgeQuery->whereIn('status', ['menunggu_approval', 'menunggu_packing']);
                        }
                        $pendingPackingBadge = $packingBadgeQuery->count();
                    @endphp

                    <!-- Antrean Packing -->
                    <a href="{{ route('admin.packing.index') }}"
                       @mouseenter="showTip($event, 'Antrean Packing')"
                       @mouseleave="hideTip()"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl transition duration-150 group {{ request()->routeIs('admin.packing.*') ? 'bg-emerald-600 text-white font-medium ' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-5 flex justify-center shrink-0">
                                <svg class="w-4 h-4 transition {{ request()->routeIs('admin.packing.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <span x-show="!collapsed" x-cloak
                                  x-transition:enter="transition ease-out duration-150"
                                  x-transition:enter-start="opacity-0"
                                  x-transition:enter-end="opacity-100"
                                  class="whitespace-nowrap">
                                {{ Auth::user()->isTimPacking() ? 'Antrean Packing' : 'Antrean Packing' }}
                            </span>
                        </div>
                        @if ($pendingPackingBadge > 0)
                            <span x-show="!collapsed" x-cloak
                                  class="px-2 py-0.5 rounded-full text-xs font-semibold {{ request()->routeIs('admin.packing.*') ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }}">
                                {{ $pendingPackingBadge }}
                            </span>
                        @endif
                    </a>

                    @if (!Auth::user()->isTimPacking())
                        <!-- Mitra Dropshipper -->
                        <a href="{{ route('admin.dropshippers.index') }}"
                           @mouseenter="showTip($event, 'Mitra Dropshipper')"
                           @mouseleave="hideTip()"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition duration-150 group {{ request()->routeIs('admin.dropshippers.*') ? 'bg-emerald-600 text-white font-medium ' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal' }}">
                            <div class="w-5 flex justify-center shrink-0">
                                <svg class="w-4 h-4 transition {{ request()->routeIs('admin.dropshippers.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span x-show="!collapsed" x-cloak
                                  x-transition:enter="transition ease-out duration-150"
                                  x-transition:enter-start="opacity-0"
                                  x-transition:enter-end="opacity-100"
                                  class="whitespace-nowrap">Mitra Dropshipper</span>
                        </a>

                        <!-- Keuangan & Saldo Dropship -->
                        <a href="{{ route('admin.dropship-finances.index') }}"
                           @mouseenter="showTip($event, 'Keuangan Dropship')"
                           @mouseleave="hideTip()"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition duration-150 group {{ request()->routeIs('admin.dropship-finances.*') ? 'bg-emerald-600 text-white font-medium ' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal' }}">
                            <div class="w-5 flex justify-center shrink-0">
                                <svg class="w-4 h-4 transition {{ request()->routeIs('admin.dropship-finances.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <span x-show="!collapsed" x-cloak
                                  x-transition:enter="transition ease-out duration-150"
                                  x-transition:enter-start="opacity-0"
                                  x-transition:enter-end="opacity-100"
                                  class="whitespace-nowrap">Keuangan Dropship</span>
                        </a>

                        <!-- Kelola Staf Toko -->
                        <a href="{{ route('admin.staff.index') }}"
                           @mouseenter="showTip($event, 'Kelola Staf Toko')"
                           @mouseleave="hideTip()"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition duration-150 group {{ request()->routeIs('admin.staff.*') ? 'bg-emerald-600 text-white font-medium ' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal' }}">
                            <div class="w-5 flex justify-center shrink-0">
                                <svg class="w-4 h-4 transition {{ request()->routeIs('admin.staff.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                            <span x-show="!collapsed" x-cloak
                                  x-transition:enter="transition ease-out duration-150"
                                  x-transition:enter-start="opacity-0"
                                  x-transition:enter-end="opacity-100"
                                  class="whitespace-nowrap">Kelola Staf Toko</span>
                        </a>

                        <!-- Header Kategori: PERANGKAT -->
                        <div x-show="!collapsed" x-cloak class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 px-3 pt-4 pb-1.5">
                            Perangkat
                        </div>

                        <!-- Pengaturan Printer & Struk -->
                        <a href="{{ route('admin.printer.index') }}"
                           @mouseenter="showTip($event, 'Pengaturan Struk')"
                           @mouseleave="hideTip()"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition duration-150 group {{ request()->routeIs('admin.printer.*') ? 'bg-emerald-600 text-white font-medium ' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal' }}">
                            <div class="w-5 flex justify-center shrink-0">
                                <svg class="w-4 h-4 transition {{ request()->routeIs('admin.printer.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                            </div>
                            <span x-show="!collapsed" x-cloak
                                  x-transition:enter="transition ease-out duration-150"
                                  x-transition:enter-start="opacity-0"
                                  x-transition:enter-end="opacity-100"
                                  class="whitespace-nowrap">Pengaturan Struk</span>
                        </a>
                    @endif
                @endif

                @if (Auth::user()->isDropshipper())
                    <!-- Header Kategori: DROPSHIP -->
                    <div x-show="!collapsed" x-cloak class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 px-3 pt-4 pb-1.5">
                        Kemitraan Dropship
                    </div>

                    <!-- Pilih Toko Penyuplai -->
                    <a href="{{ route('dropshipper.suppliers.index') }}"
                       @mouseenter="showTip($event, 'Pilih Penyuplai')"
                       @mouseleave="hideTip()"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition duration-150 group {{ request()->routeIs('dropshipper.suppliers.*') ? 'bg-emerald-600 text-white font-medium ' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal' }}">
                        <div class="w-5 flex justify-center shrink-0">
                            <svg class="w-4 h-4 transition {{ request()->routeIs('dropshipper.suppliers.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span x-show="!collapsed" x-cloak
                              x-transition:enter="transition ease-out duration-150"
                              x-transition:enter-start="opacity-0"
                              x-transition:enter-end="opacity-100"
                              class="whitespace-nowrap">Pilih Penyuplai</span>
                    </a>

                    <!-- Katalog Realtime -->
                    <a href="{{ route('dropshipper.catalog.index') }}"
                       @mouseenter="showTip($event, 'Katalog Produk')"
                       @mouseleave="hideTip()"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition duration-150 group {{ request()->routeIs('dropshipper.catalog.*') ? 'bg-emerald-600 text-white font-medium ' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal' }}">
                        <div class="w-5 flex justify-center shrink-0">
                            <svg class="w-4 h-4 transition {{ request()->routeIs('dropshipper.catalog.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </div>
                        <span x-show="!collapsed" x-cloak
                              x-transition:enter="transition ease-out duration-150"
                              x-transition:enter-start="opacity-0"
                              x-transition:enter-end="opacity-100"
                              class="whitespace-nowrap">Katalog Produk</span>
                    </a>

                    <!-- Pesanan Dropship Saya -->
                    <a href="{{ route('dropshipper.orders.index') }}"
                       @mouseenter="showTip($event, 'Pesanan Saya')"
                       @mouseleave="hideTip()"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition duration-150 group {{ request()->routeIs('dropshipper.orders.*') ? 'bg-emerald-600 text-white font-medium ' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50/40 font-normal' }}">
                        <div class="w-5 flex justify-center shrink-0">
                            <svg class="w-4 h-4 transition {{ request()->routeIs('dropshipper.orders.*') ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <span x-show="!collapsed" x-cloak
                              x-transition:enter="transition ease-out duration-150"
                              x-transition:enter-start="opacity-0"
                              x-transition:enter-end="opacity-100"
                              class="whitespace-nowrap">Pesanan Saya</span>
                    </a>
                @endif
            </nav>
        </div>

        <!-- Pengaturan di bagian paling bawah sidebar (tanpa info akun login) -->
        @php
            $settingsUrl = Auth::user()->isAdminSupplier()
                ? route('admin.settings.index')
                : (Auth::user()->isDropshipper() ? route('dropshipper.profile') : route('profile.edit'));
            $settingsActive = request()->routeIs('admin.settings.*') || request()->routeIs('profile.*') || request()->routeIs('dropshipper.profile');
        @endphp

        <div class="p-3 border-t border-slate-100 bg-white">
            <a href="{{ $settingsUrl }}"
               @mouseenter="showTip($event, 'Pengaturan')"
               @mouseleave="hideTip()"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs transition duration-150 group {{ $settingsActive ? 'bg-emerald-600 text-white font-medium' : 'text-slate-600 hover:text-emerald-800 hover:bg-emerald-50/40 font-medium' }}"
               :class="collapsed ? 'justify-center px-0' : ''">
                <div class="w-5 flex justify-center shrink-0">
                    <svg class="w-4 h-4 transition {{ $settingsActive ? 'text-white' : 'text-slate-400 group-hover:text-emerald-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <span x-show="!collapsed" x-cloak
                      x-transition:enter="transition ease-out duration-150"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      class="whitespace-nowrap">Pengaturan</span>
            </a>
        </div>
    </aside>
</div>
