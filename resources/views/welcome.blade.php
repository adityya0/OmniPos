<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OmniPOS &mdash; Platform POS Kasir Toko & Ekosistem Dropship Terintegrasi</title>
    <meta name="description" content="OmniPOS — Platform B2B Multi-Supplier & Fulfillment Dropship. Hubungkan toko fisik dengan jaringan dropshipper dalam satu ekosistem realtime.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-800 bg-white selection:bg-green-500 selection:text-white">

    <!-- Background Decorative Glow -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-gradient-to-tr from-green-200/40 via-emerald-100/30 to-transparent blur-3xl rounded-full"></div>
        <div class="absolute top-1/2 -right-40 w-[400px] h-[400px] bg-gradient-to-bl from-green-100/20 to-transparent blur-3xl rounded-full"></div>
    </div>

    <!-- Header Navigation -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo-omnipos.png') }}" alt="OmniPOS Logo"
                         class="h-9 w-9 object-contain group-hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-bold tracking-tight text-slate-900">Omni<span class="text-green-500">POS</span></span>
                        <span class="hidden sm:inline-block px-2.5 py-0.5 text-xs font-semibold bg-green-50 text-green-700 rounded-full border border-green-200">B2B Platform</span>
                    </div>
                </a>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-slate-600">
                    <a href="#fitur" class="hover:text-green-600 transition">Fitur Utama</a>
                    <a href="#solusi" class="hover:text-green-600 transition">Ekosistem Suplai</a>
                    <a href="#alur" class="hover:text-green-600 transition">Cara Kerja</a>
                </nav>

                <!-- Auth Actions -->
                <div class="flex items-center gap-2.5">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-500 rounded-xl shadow-sm shadow-green-500/25 transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-3.5 py-2 text-sm font-medium text-slate-600 hover:text-green-600 transition">
                            Masuk / Login
                        </a>
                        <div class="flex items-center gap-2 border-l border-slate-200 pl-2.5">
                            <a href="{{ route('register.supplier') }}"
                               class="px-3 py-1.5 text-xs font-semibold text-white bg-green-600 hover:bg-green-500 rounded-lg shadow-sm shadow-green-500/20 transition">
                                Daftar Supplier
                            </a>
                            <a href="{{ route('register.dropshipper') }}"
                               class="px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition">
                                Daftar Dropshipper
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main>
        <!-- HERO SECTION -->
        <section class="pt-14 pb-16 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto text-center">
            <!-- Logo Hero -->
            <div class="flex justify-center mb-6">
                <div class="relative">
                    <div class="absolute inset-0 bg-green-400/20 rounded-3xl blur-2xl scale-110"></div>
                    <img src="{{ asset('images/logo-omnipos.png') }}" alt="OmniPOS"
                         class="relative w-20 h-20 object-contain drop-shadow-lg">
                </div>
            </div>

            <!-- Pill Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 mb-5 text-xs font-semibold text-green-800 bg-green-50 border border-green-200 rounded-full shadow-sm">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                Platform B2B Multi-Supplier & Fulfillment Dropship
            </div>

            <!-- Headline -->
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 tracking-tight leading-[1.18]">
                Satu Sistem untuk
                <span class="text-green-500">POS Kasir Toko</span>
                &amp;
                <br class="hidden sm:block">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-emerald-500">Jaringan Dropship</span>
            </h1>

            <!-- Subheading -->
            <p class="mt-5 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Hubungkan stok gudang toko fisik Anda dengan dropshipper aktif. Penjualan, fulfillment pesanan, dan split saldo berjalan otomatis realtime.
            </p>

            <!-- CTA Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('register.supplier') }}"
                   class="w-full sm:w-auto px-7 py-3 text-sm font-semibold text-white bg-green-600 hover:bg-green-500 rounded-xl shadow-md shadow-green-500/20 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Daftar Sebagai Supplier / Toko
                </a>
                <a href="{{ route('register.dropshipper') }}"
                   class="w-full sm:w-auto px-7 py-3 text-sm font-semibold text-green-700 bg-white hover:bg-green-50 border border-green-300 rounded-xl transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Daftar Sebagai Dropshipper
                </a>
            </div>

            <!-- Stats Bar -->
            <div class="mt-10 pt-8 border-t border-slate-200/70 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                <div>
                    <div class="text-xl sm:text-2xl font-bold text-green-600">100%</div>
                    <div class="text-xs text-slate-500 font-medium mt-0.5">Realtime Sync</div>
                </div>
                <div>
                    <div class="text-xl sm:text-2xl font-bold text-slate-900">Multi-Toko</div>
                    <div class="text-xs text-slate-500 font-medium mt-0.5">Kemitraan Fleksibel</div>
                </div>
                <div>
                    <div class="text-xl sm:text-2xl font-bold text-green-600">Auto</div>
                    <div class="text-xs text-slate-500 font-medium mt-0.5">Fulfillment & Label Resi</div>
                </div>
                <div>
                    <div class="text-xl sm:text-2xl font-bold text-slate-900">Multi</div>
                    <div class="text-xs text-slate-500 font-medium mt-0.5">Wallet & Split Margin</div>
                </div>
            </div>
        </section>

        <!-- FITUR UTAMA -->
        <section id="fitur" class="py-14 bg-slate-50 border-y border-slate-200/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-xl mx-auto mb-10">
                    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Fitur Utama Tanpa Berbelit</h2>
                    <p class="text-sm text-slate-500 mt-1.5">Semua modul esensial untuk kelancaran transaksi harian.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach([
                        ['icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'title' => 'POS Kasir Toko', 'desc' => 'Penjualan offline toko fisik cepat, scanner barcode, diskon instan, dan cetak struk kasir bluetooth.'],
                        ['icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'title' => 'Katalog Multi-Supplier', 'desc' => 'Dropshipper bebas memilih hingga 5 supplier untuk menjual ratusan produk siap kirim tanpa modal stok.'],
                        ['icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4', 'title' => 'Auto Fulfillment', 'desc' => 'Pesanan dropship otomatis masuk ke antrean tim packing dengan cetak label resi siap kirim.'],
                        ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'title' => 'Dompet & Saldo Transparan', 'desc' => 'Saldo dompet dropshipper per supplier terpisah rapi dengan riwayat mutasi otomatis.'],
                    ] as $f)
                    <div class="p-5 rounded-2xl bg-white border border-slate-200 hover:border-green-300 hover:shadow-lg hover:shadow-green-500/5 transition-all duration-200 hover:-translate-y-0.5 group">
                        <div class="w-11 h-11 rounded-xl bg-green-50 text-green-600 border border-green-100 flex items-center justify-center mb-4 group-hover:bg-green-500 group-hover:text-white group-hover:border-green-500 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/></svg>
                        </div>
                        <h3 class="text-base font-semibold text-slate-900">{{ $f['title'] }}</h3>
                        <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">{{ $f['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- SOLUSI ROLE -->
        <section id="solusi" class="py-14 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Ekosistem Suplai yang Saling Menguntungkan</h2>
                <p class="text-sm text-slate-500 mt-1.5">Dirancang spesifik untuk kebutuhan masing-masing peran bisnis.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Supplier -->
                <div class="bg-white p-7 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden hover:shadow-md transition">
                    <div class="absolute left-0 top-0 h-full w-1 bg-green-500"></div>
                    <span class="inline-block text-xs font-semibold uppercase tracking-wider text-green-700 bg-green-50 border border-green-100 px-2.5 py-1 rounded-lg mb-3">Untuk Pemilik Toko & Supplier</span>
                    <h3 class="text-lg sm:text-xl font-bold text-slate-900">Tingkatkan Omset Tanpa Buka Cabang Baru</h3>
                    <ul class="mt-4 space-y-2.5 text-sm text-slate-600">
                        @foreach(['Produk toko Anda otomatis dipasarkan oleh puluhan dropshipper aktif.', 'Stok toko offline dan penjualan online tersinkron otomatis dalam 1 database.', 'Tim packing tinggal scan resi dan paket langsung siap dikirim.'] as $item)
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    <div class="mt-5">
                        <a href="{{ route('register.supplier') }}" class="inline-block text-sm font-semibold text-green-600 hover:text-green-700 transition">
                            Daftar sebagai Supplier
                        </a>
                    </div>
                </div>

                <!-- Dropshipper -->
                <div class="bg-white p-7 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden hover:shadow-md transition">
                    <div class="absolute left-0 top-0 h-full w-1 bg-emerald-400"></div>
                    <span class="inline-block text-xs font-semibold uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded-lg mb-3">Untuk Dropshipper & Reseller</span>
                    <h3 class="text-lg sm:text-xl font-bold text-slate-900">Jual Beragam Produk dengan Mitra Terpercaya</h3>
                    <ul class="mt-4 space-y-2.5 text-sm text-slate-600">
                        @foreach(['Terhubung dengan supplier pilihan untuk variasi produk jualan Anda.', 'Tidak perlu modal stok gudang atau repot memikirkan urusan packing.', 'Pengiriman atas nama toko dropshipper Anda (White-label resmi).'] as $item)
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    <div class="mt-5">
                        <a href="{{ route('register.dropshipper') }}" class="inline-block text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition">
                            Daftar sebagai Dropshipper
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ALUR KERJA -->
        <section id="alur" class="py-14 bg-slate-900 text-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <!-- Logo kecil di dark section -->
                    <img src="{{ asset('images/logo-omnipos.png') }}" alt="OmniPOS" class="w-10 h-10 object-contain mx-auto mb-3 opacity-90">
                    <h2 class="text-2xl font-bold text-white tracking-tight">Cara Kerja OmniPOS</h2>
                    <p class="text-sm text-slate-400 mt-1.5">3 langkah dari input stok hingga paket sampai ke pembeli.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach([
                        ['num' => '1', 'color' => 'green', 'title' => 'Supplier Siapkan Stok', 'desc' => 'Stok produk toko fisik langsung terbaca otomatis oleh seluruh dropshipper mitra.'],
                        ['num' => '2', 'color' => 'emerald', 'title' => 'Dropshipper Teruskan Order', 'desc' => 'Order pembeli diteruskan, stok otomatis terpotong, dan resi wajib di-input sebelum proses.'],
                        ['num' => '3', 'color' => 'green', 'title' => 'Gudang Kirim Paket', 'desc' => 'Tim gudang kemas barang, resi terscan, paket dikirim atas nama dropshipper.'],
                    ] as $step)
                    <div class="p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur text-center hover:bg-white/10 transition">
                        <div class="w-11 h-11 mx-auto rounded-xl bg-green-500/20 text-green-400 font-semibold text-base flex items-center justify-center border border-green-400/30 mb-4">{{ $step['num'] }}</div>
                        <h4 class="font-semibold text-white text-sm mb-1.5">{{ $step['title'] }}</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                    @endforeach
                </div>

                <!-- CTA di dark section -->
                <div class="mt-10 text-center">
                    <a href="{{ route('register.supplier') }}"
                       class="inline-block px-7 py-3 text-sm font-semibold text-slate-900 bg-green-400 hover:bg-green-300 rounded-xl shadow-md shadow-green-500/20 transition-all hover:-translate-y-0.5">
                        Mulai Sekarang — Gratis
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="py-6 border-t border-slate-200 bg-white text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo-omnipos.png') }}" alt="OmniPOS" class="h-6 w-6 object-contain opacity-80">
                <span class="font-bold text-slate-700">OmniPOS</span>
                <span class="text-slate-300">·</span>
                <span>Platform POS & Ekosistem Dropship Aktif Realtime</span>
            </div>
            <div>&copy; {{ date('Y') }} OmniPOS. Hak Cipta Dilindungi.</div>
        </div>
    </footer>

</body>
</html>
