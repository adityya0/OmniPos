<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <!-- Judul Utama: Besar, Tebal (Bold 700), Tegas -->
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                    {{ Auth::user()->isDropshipper() ? ('Halo, ' . Auth::user()->name) : ($supplier->store_name ?? 'Cita Laksana Toys') }}
                </h1>
                <!-- Sub-teks: Abu-abu (Medium/Regular 400), Lega -->
                <p class="text-xs sm:text-sm text-slate-400 font-normal mt-1">
                    {{ Auth::user()->isDropshipper() 
                        ? 'Kelola kemitraan toko, pantau katalog produk, dan proses order dropship Anda.' 
                        : 'Dashboard analitik operasional & monitoring penjualan kasir, antrean packing SLA, dan inventori.' }}
                </p>
            </div>

            <!-- Action Pills & Tombol di Sebelah Kanan -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Tanggal Hari Ini Pill -->
                <div class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200/70 text-xs font-medium text-slate-500 shadow-[0_2px_8px_rgba(0,0,0,0.02)]">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>

                <!-- Tombol Live Sync Minimalis Membulat -->
                <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200/70 text-slate-600 text-xs font-medium shadow-[0_2px_8px_rgba(0,0,0,0.02)]">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Live Sync</span>
                </span>

                @if(!Auth::user()->isDropshipper())
                    <a href="{{ route('admin.pos.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white text-xs font-semibold shadow-sm shadow-emerald-600/20 transition duration-150">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Buka Kasir POS</span>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    @if (Auth::user()->isDropshipper())
        <!-- ================= TAMPILAN MITRA DROPSHIPPER ================= -->
        <div class="space-y-6">
            <!-- Welcome Banner -->
            <div class="bg-white rounded-[20px] p-6 sm:p-7 border border-slate-200/60 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Portal Mitra Dropshipper Resmi
                    </span>
                    <h2 class="text-xl sm:text-2xl font-bold mt-2.5 text-slate-900 tracking-tight">
                        Mulai Jual Produk Tanpa Modal Stok Gudang
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                        Hubungkan dengan supplier pilihan, gunakan katalog produk berstok realtime, dan masukkan resi marketplace Anda untuk langsung diproses tim gudang.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <a href="{{ route('dropshipper.suppliers.index') }}"
                       class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-sm shadow-emerald-600/20 transition">
                        Pilih Penyuplai
                    </a>
                    <a href="{{ route('dropshipper.catalog.index') }}"
                       class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium border border-slate-200/80 transition">
                        Buka Katalog
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- ================= TAMPILAN ADMIN SUPPLIER & OWNER ================= -->
        <div class="space-y-6">
            <!-- ================= 1. GRID 4 CARD RINGKASAN ATAS ================= -->
            <!-- Seragam ukurannya, angka data besar & tebal di kiri, ikon bulat warna lembut di kanan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Card 1: Penjualan Hari Ini -->
                <div class="bg-white rounded-[20px] p-6 border border-slate-200/60 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            Penjualan Hari Ini
                        </span>
                        <!-- Lencana "Rp" hijau muda bulat -->
                        <div class="w-11 h-11 rounded-full bg-emerald-50 text-emerald-600 font-bold text-sm flex items-center justify-center border border-emerald-100/80">
                            Rp
                        </div>
                    </div>
                    <!-- Angka Utama: Besar & Tebal (Visual Anchor) -->
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-3">
                        Rp {{ number_format($todayPosSales, 0, ',', '.') }}
                    </div>
                    <!-- Sub-teks kecil dengan warna yang sesuai di bagian bawah -->
                    <div class="mt-3.5 flex items-center gap-1.5 text-xs text-emerald-600 font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>POS Kasir Offline &bull; Hari ini</span>
                    </div>
                </div>

                <!-- Card 2: Menunggu Packing -->
                <div class="bg-white rounded-[20px] p-6 border border-slate-200/60 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            Menunggu Packing
                        </span>
                        <!-- Ikon box oranye bulat berwarna lembut -->
                        <div class="w-11 h-11 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center border border-amber-100/80">
                            <svg class="w-5 h-5 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                    <!-- Angka Utama -->
                    <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mt-3">
                        {{ number_format($pendingPackingCount) }}
                    </div>
                    <!-- Sub-teks -->
                    <div class="mt-3.5 text-xs font-medium text-slate-400">
                        @if ($pendingPackingCount > 0)
                            <span class="text-amber-600 font-semibold">Perlu dikemas segera</span>
                        @else
                            <span>✓ Semua pesanan terkemas</span>
                        @endif
                    </div>
                </div>

                <!-- Card 3: Alert Stok Kritis -->
                <div class="bg-white rounded-[20px] p-6 border border-slate-200/60 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            Alert Stok Kritis
                        </span>
                        <!-- Ikon warning merah bulat berwarna lembut -->
                        <div class="w-11 h-11 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center border border-rose-100/80">
                            <svg class="w-5 h-5 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                    <!-- Angka Utama -->
                    <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mt-3">
                        {{ number_format($lowStockCount) }}
                    </div>
                    <!-- Sub-teks -->
                    <div class="mt-3.5 text-xs font-medium text-slate-400">
                        @if ($lowStockCount > 0)
                            <span class="text-rose-600 font-semibold">⚠️ Stok &le; safety stock</span>
                        @else
                            <span class="text-emerald-600 font-medium">✓ Stok inventori aman</span>
                        @endif
                    </div>
                </div>

                <!-- Card 4: Mitra Dropshipper -->
                <div class="bg-white rounded-[20px] p-6 border border-slate-200/60 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            Mitra Dropshipper
                        </span>
                        <!-- Ikon orang hijau bulat berwarna lembut -->
                        <div class="w-11 h-11 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100/80">
                            <svg class="w-5 h-5 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <!-- Angka Utama -->
                    <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mt-3">
                        {{ number_format($activeDropshippersCount) }}
                    </div>
                    <!-- Sub-teks -->
                    <div class="mt-3.5 text-xs font-medium text-slate-400">
                        <span>Terkoneksi aktif ke toko</span>
                    </div>
                </div>
            </div>

            <!-- ================= 2. SECTION 2 KOLOM: PESANAN MENDESAK & PRODUK STOK MENIPIS ================= -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
                <!-- Card "Pesanan Mendesak (Deadline)": Whitespace luas di dalamnya, ilustrasi tipis & soft -->
                <div class="bg-white rounded-[20px] p-6 border border-slate-200/60 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div>
                                <h3 class="font-bold text-sm sm:text-base text-slate-900">Pesanan Mendesak (Deadline)</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Urutan prioritas pengiriman marketplace</p>
                            </div>
                            <!-- Aksen tombol "Semua" sangat minimalis & membulat -->
                            <a href="{{ route('admin.packing.index') }}"
                               class="px-3.5 py-1 rounded-full text-xs font-medium text-slate-500 hover:text-slate-800 bg-slate-50 hover:bg-slate-100 border border-slate-200/60 transition">
                                Semua
                            </a>
                        </div>

                        @if($urgentOrders->isEmpty())
                            <!-- Whitespace luas di dalamnya dengan ikon ilustrasi tipis/soft -->
                            <div class="py-16 sm:py-20 flex flex-col items-center justify-center text-center">
                                <div class="w-14 h-14 rounded-full bg-slate-50 border border-slate-100/80 flex items-center justify-center text-slate-300 mb-3">
                                    <svg class="w-6 h-6 stroke-[1.3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="font-semibold text-sm text-slate-700">
                                    Tidak ada pesanan mendesak saat ini.
                                </div>
                                <p class="text-xs text-slate-400 mt-1 max-w-xs">
                                    Semua pesanan dropshipper telah dikemas dan diserahkan ke kurir pengiriman.
                                </p>
                            </div>
                        @else
                            <div class="divide-y divide-slate-100 mt-2" x-data="{ copiedResi: null }">
                                @foreach($urgentOrders as $order)
                                    <div class="py-3.5 flex items-center justify-between text-xs hover:bg-slate-50/50 rounded-xl px-2 transition">
                                        <div class="min-w-0 pr-2">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-semibold text-xs text-slate-900">
                                                    {{ $order->order_code ?: $order->invoice_number }}
                                                </span>
                                                @if($order->shipping_receipt_number)
                                                    <button type="button"
                                                            @click="navigator.clipboard.writeText('{{ $order->shipping_receipt_number }}'); copiedResi = '{{ $order->shipping_receipt_number }}'; setTimeout(() => copiedResi = null, 2000)"
                                                            title="Salin No Resi"
                                                            class="inline-flex items-center gap-1 text-[11px] font-mono text-slate-500 hover:text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md transition cursor-pointer">
                                                        <span>{{ $order->shipping_receipt_number }}</span>
                                                        <span x-show="copiedResi === '{{ $order->shipping_receipt_number }}'" class="text-emerald-600 font-bold">✓</span>
                                                    </button>
                                                @endif
                                            </div>
                                            <div class="text-slate-400 text-xs mt-1 truncate">
                                                {{ $order->customer_name }} &bull; <span class="text-slate-600 font-medium">{{ $order->dropshipper?->name ?? 'Mitra Dropshipper' }}</span>
                                            </div>
                                        </div>

                                        <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200/70 text-xs font-medium shrink-0">
                                            Siap Kemas
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card "Produk Stok Menipis": Daftar produk sangat rapi, font semi-bold/medium, pill stok pastel red -->
                <div class="bg-white rounded-[20px] p-6 border border-slate-200/60 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div>
                                <h3 class="font-bold text-sm sm:text-base text-slate-900">Produk Stok Menipis</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Perlu restock segera</p>
                            </div>
                            <!-- Aksen tombol "Restock" sangat minimalis & membulat -->
                            <a href="{{ route('admin.products.index') }}"
                               class="px-3.5 py-1 rounded-full text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/80 transition">
                                Restock
                            </a>
                        </div>

                        @if($lowStockProducts->isEmpty())
                            <div class="py-16 text-center text-xs text-slate-400">
                                Semua stok produk berada di atas batas safety stock.
                            </div>
                        @else
                            <div class="space-y-3.5 mt-4">
                                @foreach($lowStockProducts as $prod)
                                    <div class="flex items-center justify-between text-xs py-1 px-1">
                                        <div class="truncate max-w-[260px] sm:max-w-xs">
                                            <!-- Nama produk menggunakan font semi-bold/medium (500-600) -->
                                            <div class="font-medium text-slate-800 text-sm truncate" title="{{ $prod->name }}">
                                                {{ $prod->name }}
                                            </div>
                                            <!-- Teks SKU kecil berwarna abu-abu di bawahnya -->
                                            <div class="text-xs text-slate-400 font-mono mt-0.5">
                                                SKU: {{ $prod->sku ?: '-' }}
                                            </div>
                                        </div>

                                        <!-- Pill stok: sangat kecil, membulat, pastel red lembut & teks merah tua tidak mencolok -->
                                        <span class="px-3 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-600 border border-rose-100/80 shrink-0">
                                            Sisa: {{ $prod->stock }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ================= 3. SECTION BAWAH: RIWAYAT TRANSAKSI KASIR ================= -->
            <div class="bg-white rounded-[20px] p-6 border border-slate-200/60 shadow-[0_2px_12px_rgba(0,0,0,0.03)]">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-sm sm:text-base text-slate-900">Riwayat Transaksi Kasir Offline Hari Ini</h3>
                        <p class="text-xs text-slate-400 mt-0.5">5 transaksi penjualan kasir POS toko terbaru</p>
                    </div>

                    <a href="{{ route('admin.reports.index') }}"
                       class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">
                        Buka Laporan Akumulasi &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto mt-2">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="text-slate-400 uppercase tracking-wider text-[11px] border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-3 font-semibold">No. Invoice</th>
                                <th class="py-3 px-3 font-semibold">Waktu</th>
                                <th class="py-3 px-3 font-semibold">Kasir</th>
                                <th class="py-3 px-3 font-semibold">Pelanggan</th>
                                <th class="py-3 px-3 font-semibold">Item Produk</th>
                                <th class="py-3 px-3 font-semibold">Metode</th>
                                <th class="py-3 px-3 font-semibold text-right">Total Belanja</th>
                                <th class="py-3 px-3 font-semibold text-right">Laba Bersih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100/80">
                            @forelse($recentPosSales as $sale)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3.5 px-3 font-mono font-medium text-slate-900">
                                        {{ $sale->invoice_number }}
                                    </td>
                                    <td class="py-3.5 px-3 text-slate-500">
                                        {{ $sale->created_at->format('H:i') }} WIB
                                    </td>
                                    <td class="py-3.5 px-3 font-medium text-slate-800">
                                        {{ $sale->user?->name ?? 'Kasir' }}
                                    </td>
                                    <td class="py-3.5 px-3 text-slate-600">
                                        {{ $sale->customer_name ?: 'Pelanggan Offline' }}
                                    </td>
                                    <td class="py-3.5 px-3 text-slate-500 max-w-[200px] truncate">
                                        {{ $sale->items->map(fn($it) => $it->quantity . 'x ' . $it->product_name)->implode(', ') ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium uppercase bg-slate-100 text-slate-600 border border-slate-200/60">
                                            {{ $sale->payment_method }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3 text-right font-semibold text-slate-900">
                                        Rp {{ number_format($sale->final_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right font-semibold text-emerald-600">
                                        +Rp {{ number_format($sale->net_profit, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400">
                                        Belum ada transaksi kasir tercatat hari ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
