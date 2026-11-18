<x-app-layout title="Kasir POS">
    <div x-data="posApp()" class="h-full flex-1 flex overflow-hidden relative min-h-0">
        <!-- Floating Hover Bubble Nama Produk Keranjang (Fixed z-[9999] Paling Atas & Tidak Pernah Terpotong) -->
        <div x-show="cartTooltipText"
             x-cloak
             :style="`top: ${cartTooltipTop}px; left: ${cartTooltipLeft}px; transform: translateY(-100%);`"
             class="fixed z-[9999] pointer-events-none max-w-[235px] px-2.5 py-1.5 rounded-xl bg-white border border-emerald-300/90 ring-1 ring-slate-200/70 text-[11px] font-medium text-slate-700 leading-snug whitespace-normal break-words">
            <span x-text="cartTooltipText"></span>
        </div>

        <!-- Floating Warning Toast (Stok Habis / Peringatan) -->
        <div x-show="warningToast"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
             class="fixed top-5 left-1/2 -translate-x-1/2 z-50 max-w-md w-full px-4 pointer-events-none">
            <div class="bg-rose-600 text-white px-4 py-3 rounded-2xl flex items-center gap-3 border border-rose-500">
                <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-[10px] font-black uppercase tracking-wider text-rose-200">Perhatian: Stok Habis</div>
                    <div class="text-xs font-bold leading-tight" x-text="warningToastMessage"></div>
                </div>
            </div>
        </div>

        <!-- ================= KOLOM KIRI: NAVBAR ATAS (OTOMATIS MENGIKUTI LEBAR) + KATALOG PRODUK ================= -->
        <div class="flex-1 flex flex-col min-w-0 min-h-0 h-full bg-white overflow-hidden">
            <!-- Top Navbar Kasir (Tanpa Judul/Subjudul, Otomatis Mengecil Saat Sidebar Kanan Terbuka) -->
            <div class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 shrink-0 flex items-center justify-between gap-3">
                <!-- Kiri: Kategori Pills -->
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar flex-1 min-w-0 text-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1 hidden sm:inline">Kategori:</span>
                    <button type="button"
                            @click="selectedCategory = ''"
                            :class="selectedCategory === '' ? 'bg-green-600 text-white font-semibold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition shrink-0 cursor-pointer">
                        Semua
                    </button>
                    @foreach ($categories as $cat)
                        <button type="button"
                                @click="selectedCategory = '{{ $cat->id }}'"
                                :class="selectedCategory === '{{ $cat->id }}' ? 'bg-green-600 text-white font-semibold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                                class="px-3.5 py-1.5 rounded-xl whitespace-nowrap transition shrink-0 cursor-pointer">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>

                <!-- Kanan: Filter Harga + Search Expandable + Tombol Keranjang -->
                <div class="flex items-center gap-2 shrink-0">
                    <!-- Filter Tingkatan Harga Dropdown -->
                    <div x-show="posPricingMode === 'global_filter'"
                         x-cloak
                         class="relative shrink-0"
                         x-data="{ openTierDropdown: false }">
                        <button type="button"
                                @click="openTierDropdown = !openTierDropdown"
                                class="flex items-center gap-2 px-3 py-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 transition active:scale-95 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-[10px] uppercase font-bold text-slate-400 hidden md:inline">Filter Harga:</span>
                            <span class="text-green-700 font-extrabold max-w-[110px] truncate"
                                  x-text="(availableTiers.find(t => t.level === selectedTierLevel)?.name || 'Eceran')">
                            </span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                                 :class="openTierDropdown ? 'rotate-180 text-green-600' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Menu Dropdown Pilihan Level Harga -->
                        <div x-show="openTierDropdown"
                             x-cloak
                             @click.outside="openTierDropdown = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                             class="absolute right-0 mt-1.5 w-60 bg-white rounded-2xl border border-slate-200 py-1.5 z-40">
                            <div class="px-3 py-1.5 border-b border-slate-100 text-[10px] font-black uppercase text-slate-400 tracking-wider">
                                Pilih Tingkatan Harga
                            </div>
                            <template x-for="tierOpt in availableTiers" :key="tierOpt.level">
                                <button type="button"
                                        @click="selectedTierLevel = tierOpt.level; openTierDropdown = false"
                                        class="w-full text-left px-3.5 py-2 text-xs flex items-center justify-between transition hover:bg-green-50/70 cursor-pointer"
                                        :class="selectedTierLevel === tierOpt.level ? 'text-green-700 font-black bg-green-50/60' : 'text-slate-700 font-semibold'">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                              :class="selectedTierLevel === tierOpt.level ? 'bg-green-600' : 'bg-slate-300'"></span>
                                        <span x-text="tierOpt.name"></span>
                                    </div>
                                    <span class="text-[10px] font-mono px-1.5 py-0.5 rounded-md"
                                          :class="selectedTierLevel === tierOpt.level ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-500'"
                                          x-show="tierOpt.multiplier > 1"
                                          x-text="tierOpt.multiplier + ' pcs'"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Expandable Search Bar (Icon Only by Default) -->
                    <div class="flex items-center gap-2" @click.outside="if (!search) searchOpen = false">
                        <div x-show="searchOpen"
                             x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="relative w-48 sm:w-64">
                            <input type="text"
                                   x-ref="searchInput"
                                   x-model="search"
                                   @keydown.enter.prevent="handleBarcodeScan()"
                                   @keydown.escape="searchOpen = false"
                                   placeholder="Scan barcode / cari SKU atau nama..."
                                   class="w-full pl-3.5 pr-8 py-2 text-xs rounded-2xl border border-slate-200 focus:border-green-500 focus:ring-green-500 bg-slate-50/70">
                            <button type="button"
                                    x-show="search"
                                    @click="search = ''; $refs.searchInput.focus()"
                                    title="Bersihkan pencarian"
                                    class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <button type="button"
                                @click="searchOpen = !searchOpen; if (searchOpen) $nextTick(() => $refs.searchInput.focus())"
                                title="Cari Produk / Scan Barcode"
                                class="relative p-2.5 rounded-2xl border transition duration-150 focus:outline-none flex items-center justify-center shrink-0 cursor-pointer"
                                :class="(searchOpen || search)
                                    ? 'bg-green-50 border-green-500 text-green-700 hover:bg-green-100'
                                    : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span x-show="search && !searchOpen" x-cloak class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-green-600 ring-2 ring-white"></span>
                        </button>
                    </div>

                    <!-- Tombol Ikon Keranjang dengan Badge Counter -->
                    <button type="button"
                            @click="cartOpen = !cartOpen"
                            title="Keranjang Belanja Kasir"
                            class="relative p-2.5 rounded-2xl transition duration-150 focus:outline-none flex items-center justify-center shrink-0 cursor-pointer"
                            :class="cartOpen 
                                ? 'bg-green-600 text-white hover:bg-green-500' 
                                : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>

                        <span class="absolute -top-1.5 -right-1.5 min-w-[20px] h-5 px-1 rounded-full text-[10px] font-black flex items-center justify-center transition"
                              :class="totalCartItems() > 0 
                                  ? (cartOpen ? 'bg-amber-400 text-amber-950 ring-2 ring-white' : 'bg-green-600 text-white') 
                                  : 'bg-slate-200 text-slate-500'"
                              x-text="totalCartItems()">
                        </span>
                    </button>
                </div>
            </div>

            <!-- Scrollable Katalog Produk -->
            <div class="flex-1 overflow-y-auto bg-white min-h-0">

                <!-- Product Grid -->
                <!-- Product Grid (Dua Mode: 1. Filter Tingkatan Global (Default) | 2. Tombol di Setiap Card) -->
                <div class="grid gap-0 transition-all duration-300 bg-white"
                     :class="cartOpen
                        ? 'grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4'
                        : 'grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6'">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="posPricingMode === 'global_filter' && product.stock >= (getCurrentTier(product).unit_multiplier || 1) ? addToCart(product, getCurrentTier(product)) : null"
                             :class="product.stock <= 0
                                ? 'bg-slate-50 border-slate-200 opacity-60 grayscale cursor-not-allowed select-none'
                                : 'bg-white border-slate-200 hover:bg-emerald-50/20 cursor-pointer'"
                             class="border-b border-r border-slate-200 transition-colors duration-150 flex flex-col justify-between group p-4 relative h-full">
                            
                            <div>
                                <!-- Foto Produk Berbingkai Dalam (Padded Frame dengan Sudut Membulat Rapi) -->
                                <div class="aspect-[4/3] w-full rounded-2xl bg-slate-100 overflow-hidden relative shrink-0">
                                    <template x-if="product.image_url">
                                        <img :src="product.image_url"
                                             :alt="product.name"
                                             loading="lazy"
                                             class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                                    </template>
                                    <template x-if="!product.image_url">
                                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100/90 p-3 text-center">
                                            <div class="w-10 h-10 rounded-2xl bg-white  border border-slate-200 flex items-center justify-center text-slate-400 group-hover:text-green-600 group-hover:scale-110 transition duration-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                            <span class="text-[10px] font-bold text-slate-400 mt-1.5 truncate max-w-full" x-text="product.name"></span>
                                        </div>
                                    </template>

                                    <!-- Indikator Buffer Stok Online Mengambang (Hanya jika ada antrean pesanan online) -->
                                    <template x-if="product.pending_online_qty > 0">
                                        <div class="absolute top-2 right-2 z-10 pointer-events-none">
                                            <span class="inline-flex items-center gap-1 text-[9px] font-black px-2 py-0.5 rounded-lg bg-sky-600/95 backdrop-blur-xs text-white "
                                                  :title="'Ada ' + product.pending_online_qty + ' unit dibooking pesanan dropship online (menunggu packing)'">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                <span><span x-text="product.pending_online_qty"></span> Dropship</span>
                                            </span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Body Card Produk: Nama Jelas & Lengkap, SKU Berbingkai Kotak -->
                                <div class="mt-3 space-y-2">
                                    <!-- Nama Produk: Jelas, Lengkap & Terbaca Utuh (Tanpa Truncate Kasar) -->
                                    <h4 class="text-xs sm:text-sm font-semibold text-slate-900 leading-snug line-clamp-2 min-h-[2.5rem] break-words group-hover:text-green-700 transition"
                                        x-text="product.name"
                                        :title="product.name"></h4>

                                    <!-- SKU & Badges dengan Bingkai Kotak Bersekeliling (Sesuai Referensi) -->
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <!-- Kotak SKU Bersekeliling -->
                                        <span class="px-2 py-0.5 rounded-md border border-slate-200 bg-slate-50/70 font-mono text-xs font-medium text-slate-600 tracking-tight"
                                              x-text="'#' + (product.sku || 'NO-SKU')">
                                        </span>

                                        <!-- Kotak Tingkatan Level (Mode Global Filter, Bersih Tanpa Tanda Kurung) -->
                                        <template x-if="posPricingMode === 'global_filter'">
                                            <span class="px-2 py-0.5 rounded-md border border-slate-200 bg-slate-50/70 text-xs font-medium text-slate-600"
                                                  :title="cleanTierName(getCurrentTier(product).name)">
                                                <span x-text="cleanTierName(getCurrentTier(product).name)"></span>
                                            </span>
                                        </template>

                                        <!-- Kotak Sisa Stok Multi-Kanal (Kasir & Fisik) -->
                                        <span class="px-2 py-0.5 rounded-md border text-xs font-medium inline-flex items-center gap-1"
                                              :title="product.pending_online_qty > 0 ? ('Stok Kasir: ' + product.stock + ' unit | Booking Dropship: ' + product.pending_online_qty + ' unit | Total Fisik di Toko: ' + (product.stock + product.pending_online_qty) + ' unit') : ('Stok Kasir: ' + product.stock + ' unit')"
                                              :class="product.stock > 10 ? 'border-emerald-200 bg-emerald-50/50 text-emerald-700' : (product.stock > 0 ? 'border-amber-200 bg-amber-50/50 text-amber-700' : 'border-rose-200 bg-rose-50/50 text-rose-700')">
                                            <span class="w-1.5 h-1.5 rounded-full shrink-0"
                                                  :class="product.stock > 10 ? 'bg-emerald-500' : (product.stock > 0 ? 'bg-amber-500' : 'bg-rose-500')"></span>
                                            <span x-text="product.stock > 0 ? product.stock + ' unit' : 'Habis'"></span>
                                        </span>

                                        <template x-if="product.pending_online_qty > 0">
                                            <span class="px-1.5 py-0.5 rounded-md border border-sky-200 bg-sky-50 text-xs font-medium text-sky-700 inline-flex items-center gap-0.5"
                                                  :title="'Fisik di rak toko: ' + (product.stock + product.pending_online_qty) + ' unit (' + product.pending_online_qty + ' unit pesanan dropshipper)'">
                                                <span>Fisik:</span>
                                                <span x-text="product.stock + product.pending_online_qty"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Baris Bawah: Harga Jelas Sejajar dengan Tombol Add to Cart (Ikon Cart Bulat Simple) -->
                            <div class="mt-3.5 pt-2.5 border-t border-slate-100">
                                <!-- Mode 1: Global Filter (Default) -> Harga Jelas di Kiri, Tombol Cart Bulat Simple di Kanan Sejajar -->
                                <template x-if="posPricingMode === 'global_filter'">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <span class="text-xs font-medium uppercase tracking-wider text-slate-500 block leading-none">Harga</span>
                                            <div class="text-sm sm:text-base font-bold text-slate-900 mt-0.5 leading-tight tracking-tight whitespace-nowrap"
                                                 x-text="formatRupiah(getCurrentTier(product).price)">
                                            </div>
                                        </div>

                                        <button type="button"
                                                @click.stop="addToCart(product, getCurrentTier(product))"
                                                :disabled="product.stock < (getCurrentTier(product).unit_multiplier || 1)"
                                                :title="'Tambah ' + cleanTierName(getCurrentTier(product).name) + ' ke keranjang'"
                                                :class="product.stock < (getCurrentTier(product).unit_multiplier || 1)
                                                    ? 'bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200 border border-slate-200-none'
                                                    : 'bg-green-600 hover:bg-green-500 active:scale-90 text-white  hover:border-slate-300 '"
                                                class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 transition duration-150 focus:outline-none">
                                            <!-- Ikon Cart Bulat Simple Sesuai Permintaan -->
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>

                                <!-- Mode 2: Tombol Tingkatan Harga di Setiap Card -->
                                <template x-if="posPricingMode === 'card_buttons'">
                                    <div class="space-y-1.5">
                                        <template x-for="tier in (product.tiers && product.tiers.length ? product.tiers : [{ name: 'Eceran', unit_multiplier: 1, price: product.selling_price }])" :key="tier.id || tier.name">
                                            <button type="button"
                                                    @click.stop="addToCart(product, tier)"
                                                    :disabled="product.stock < (tier.unit_multiplier || 1)"
                                                    :title="'Beli ' + tier.name + ' (' + (tier.unit_multiplier || 1) + ' pcs) - ' + formatRupiah(tier.price)"
                                                    :class="product.stock < (tier.unit_multiplier || 1)
                                                        ? 'opacity-40 bg-slate-100 text-slate-400 cursor-not-allowed border-slate-200'
                                                        : 'bg-green-50/80 hover:bg-green-600 hover:text-white text-green-950 border-green-200/90 active:scale-95 '"
                                                    class="w-full flex items-center justify-between px-3 py-2 rounded-xl border text-[11px] font-semibold transition duration-150 group/tier">
                                                <div class="flex items-center gap-1.5 min-w-0">
                                                    <svg class="w-3.5 h-3.5 shrink-0 opacity-70 group-hover/tier:opacity-100" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                    <span class="font-extrabold truncate" x-text="cleanTierName(tier.name)"></span>
                                                    <span class="text-[9.5px] opacity-75 font-normal" x-show="tier.unit_multiplier > 1" x-text="'(' + tier.unit_multiplier + ' pcs)'"></span>
                                                </div>
                                                <div class="font-black shrink-0 ml-1.5" x-text="formatRupiah(tier.price)"></div>
                                            </button>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <div x-show="filteredProducts.length === 0" class="col-span-full p-12 text-center bg-white rounded-2xl border border-slate-200 text-slate-400">
                        <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-xs font-medium">Tidak ada produk yang cocok dengan pencarian.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= KOLOM KANAN: SIDEBAR KERANJANG FULL KE ATAS (OTOMATIS MENGECILKAN NAVBAR KIRI) ================= -->
        <aside x-show="cartOpen"
               x-transition:enter="transition ease-in-out duration-300"
               x-transition:enter-start="translate-x-full opacity-0"
               x-transition:enter-end="translate-x-0 opacity-100"
               x-transition:leave="transition ease-in-out duration-300"
               x-transition:leave-start="translate-x-0 opacity-100"
               x-transition:leave-end="translate-x-full opacity-0"
               class="w-full sm:w-[320px] md:w-[335px] lg:w-[345px] bg-white border-l border-slate-200 flex flex-col justify-between shrink-0 h-full z-20">

            <!-- Header Keranjang (h-16 Sejajar Penuh dengan Topbar Kiri) -->
            <div class="h-16 px-4 border-b border-slate-200 flex items-center justify-between shrink-0 bg-white">
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-sm text-slate-900">Keranjang</h3>
                    <span class="min-w-[22px] h-5 px-1.5 rounded-full bg-green-100 text-green-700 text-xs font-bold inline-flex items-center justify-center" x-text="totalCartItems()"></span>
                </div>

                <div class="flex items-center gap-1">
                    <button type="button"
                            @click="clearCart()"
                            x-show="cart.length > 0"
                            title="Kosongkan Keranjang"
                            class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                    <button type="button" @click="cartOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer" title="Tutup Keranjang">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Scrollable Item List (Flush Outline, 1 Harga Total, Nama 3 Kata + Bubble Hover, Tombol Hapus Icon) -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-200 bg-white" @scroll="hideCartTooltip()">
                <template x-for="(item, index) in cart" :key="item.cart_key || index">
                    <div @mouseenter="showCartTooltip($event, item.name)"
                         @mouseleave="hideCartTooltip()"
                         class="px-3.5 py-3 bg-white hover:bg-emerald-50/25 transition flex items-center gap-2.5 group/item relative">
                        <!-- Mini Thumbnail Foto Produk -->
                        <div class="w-9 h-9 rounded-lg bg-slate-50 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center">
                            <img :src="item.image_url || 'https://placehold.co/100x100/e2e8f0/475569?text=' + encodeURIComponent(item.name.charAt(0))"
                                 :alt="item.name"
                                 class="w-full h-full object-cover">
                        </div>

                        <!-- Detail Nama (3 Kata Pertama + Hover Bubble Nama Lengkap di Layer Paling Atas) & 1 Harga (Total Harga Item) -->
                        <div class="flex-1 min-w-0 relative">
                            <h5 class="text-xs font-semibold text-slate-800 group-hover/item:text-emerald-800 transition-colors leading-tight truncate cursor-default"
                                x-text="shortProductName(item.name)"></h5>

                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="text-[10px] font-medium px-1.5 py-0.2 rounded bg-green-50 text-green-700 border border-green-200/70" x-text="item.tier_name || 'Eceran'"></span>
                                <span class="text-xs font-bold text-slate-900" x-text="formatRupiah(item.selling_price * item.quantity)"></span>
                            </div>
                        </div>

                        <!-- Stepper Kuantitas [-] [Qty] [+] -->
                        <div class="flex items-center bg-white border border-slate-200 rounded-lg shrink-0 overflow-hidden">
                            <button type="button" @click="decreaseQty(index)" class="w-6 h-6 hover:bg-slate-100 text-slate-600 font-semibold flex items-center justify-center text-xs transition active:scale-95 cursor-pointer">-</button>
                            <span class="w-6 text-center font-semibold text-xs text-slate-800" x-text="item.quantity"></span>
                            <button type="button" @click="increaseQty(index)" class="w-6 h-6 hover:bg-slate-100 text-slate-600 font-semibold flex items-center justify-center text-xs transition active:scale-95 cursor-pointer">+</button>
                        </div>

                        <!-- Tombol Hapus Icon Saja di Kanan -->
                        <button type="button"
                                @click="removeItem(index)"
                                title="Hapus produk"
                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition shrink-0 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </template>

                <div x-show="cart.length === 0" class="py-16 text-center text-slate-400 text-xs">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-200 text-slate-400 flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <span class="font-semibold text-slate-600">Keranjang masih kosong</span>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pilih produk di katalog sebelah kiri untuk belanja.</p>
                </div>
            </div>

                <!-- Bottom Summary & Checkout Area (Flush Outline Menyatu, Hemat Ruang) -->
                <div class="border-t border-slate-200 bg-white divide-y divide-slate-200 text-xs shrink-0">

                    <!-- Baris 1: Subtotal & Tombol Pop-up Diskon -->
                    <div class="px-3.5 py-2.5 flex items-center justify-between gap-2">
                        <div>
                            <span class="text-slate-500 text-[11px]">Subtotal</span>
                            <div class="font-bold text-slate-900 text-xs mt-0.5" x-text="formatRupiah(cartSubtotal())"></div>
                        </div>

                        <!-- Tombol Buka Pop-up Diskon -->
                        <button type="button"
                                @click="showDiscountModal = true"
                                :class="actualDiscount() > 0
                                    ? 'border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold'
                                    : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold'"
                                class="px-2.5 py-1.5 rounded-lg border text-xs flex items-center gap-1.5 transition cursor-pointer shrink-0">
                            <svg class="w-3.5 h-3.5" :class="actualDiscount() > 0 ? 'text-rose-600' : 'text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span x-show="actualDiscount() === 0">Diskon</span>
                            <span x-show="actualDiscount() > 0" x-text="'-' + formatRupiah(actualDiscount())"></span>
                        </button>
                    </div>

                    <!-- Baris 2: Total Tagihan & Pilihan Metode Pembayaran -->
                    <div class="px-3.5 py-2.5 flex items-center justify-between gap-2">
                        <div>
                            <span class="text-[10px] font-semibold uppercase text-slate-400 block tracking-wider">Total Tagihan</span>
                            <div class="text-base sm:text-lg font-bold text-green-700 leading-tight mt-0.5" x-text="formatRupiah(cartTotal())"></div>
                        </div>

                        <!-- Segmented Tab Metode Pembayaran -->
                        <div class="inline-flex rounded-lg bg-slate-100 border border-slate-200 p-0.5 text-xs font-semibold shrink-0">
                            <button type="button"
                                    @click="paymentMethod = 'cash'"
                                    :class="paymentMethod === 'cash' ? 'bg-green-600 text-white' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                                Tunai
                            </button>
                            <button type="button"
                                    @click="paymentMethod = 'qris'"
                                    :class="paymentMethod === 'qris' ? 'bg-green-600 text-white' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                                QRIS
                            </button>
                            <button type="button"
                                    @click="paymentMethod = 'transfer'"
                                    :class="paymentMethod === 'transfer' ? 'bg-green-600 text-white' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                                Transfer
                            </button>
                        </div>
                    </div>

                    <!-- Baris 3: Uang Diterima & Kembalian (Flush Outline Tanpa Kotak Card) -->
                    <div x-show="paymentMethod === 'cash'" class="px-3.5 py-2.5 bg-slate-50/40 space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-slate-700 shrink-0">Uang Diterima:</span>
                            <input type="text"
                                   :value="formatThousand(cashAmount)"
                                   @input="cashAmount = parseThousand($event.target.value)"
                                   placeholder="0"
                                   class="w-28 text-right text-xs py-1 px-2.5 rounded-lg bg-white border border-slate-200 focus:border-green-500 focus:ring-green-500 font-semibold text-slate-900">
                        </div>

                        <!-- Quick Cash Pills & Kembalian -->
                        <div class="flex items-center justify-between gap-1">
                            <div class="flex flex-wrap gap-1 text-[11px]">
                                <button type="button" @click="cashAmount = cartTotal()" class="px-2 py-0.5 rounded bg-white hover:bg-slate-100 text-slate-700 font-medium border border-slate-200 cursor-pointer">Pas</button>
                                <button type="button" @click="cashAmount = roundUpCash(cartTotal(), 10000)" class="px-2 py-0.5 rounded bg-white hover:bg-slate-100 text-slate-700 font-medium border border-slate-200 cursor-pointer">+10rb</button>
                                <button type="button" @click="cashAmount = roundUpCash(cartTotal(), 50000)" class="px-2 py-0.5 rounded bg-white hover:bg-slate-100 text-slate-700 font-medium border border-slate-200 cursor-pointer">+50rb</button>
                                <button type="button" @click="cashAmount = roundUpCash(cartTotal(), 100000)" class="px-2 py-0.5 rounded bg-white hover:bg-slate-100 text-slate-700 font-medium border border-slate-200 cursor-pointer">+100rb</button>
                            </div>
                            <div class="text-xs font-semibold text-right shrink-0">
                                <span class="text-slate-500">Kembali: </span>
                                <span :class="calculateChange() >= 0 ? 'text-emerald-600 font-semibold' : 'text-rose-500 font-semibold'" x-text="formatRupiah(Math.max(0, calculateChange()))"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Baris 4: Tombol Bayar & Cetak Struk -->
                    <div class="p-3 bg-white">
                        <button type="button"
                                @click="processCheckout()"
                                :disabled="isProcessing || cart.length === 0"
                                :class="cart.length === 0 || isProcessing ? 'opacity-50 cursor-not-allowed bg-green-400' : 'bg-green-600 hover:bg-green-700 active:bg-green-800 cursor-pointer'"
                                class="w-full py-2.5 rounded-xl text-white font-semibold text-xs sm:text-sm transition duration-150 text-center">
                            <span x-show="!isProcessing">Bayar & Cetak Struk</span>
                            <span x-show="isProcessing">Memproses Transaksi...</span>
                        </button>
                    </div>
                </div>
            </aside>

        <!-- ================= POP-UP MODAL DISKON DI TENGAH LAYAR ================= -->
        <div x-show="showDiscountModal"
             x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showDiscountModal = false"
                 class="bg-white rounded-2xl max-w-sm w-full border border-slate-200 overflow-hidden text-slate-800">
                <!-- Header Modal -->
                <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="font-bold text-sm text-slate-900">Tambah Diskon Transaksi</h3>
                    <button type="button" @click="showDiscountModal = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body Modal -->
                <div class="p-5 space-y-4 text-xs">
                    <!-- Pilihan Tipe Diskon: Persen (%) vs Rupiah (Rp) -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Jenis Potongan</label>
                        <div class="grid grid-cols-2 rounded-xl bg-slate-100 p-1 border border-slate-200 font-bold">
                            <button type="button"
                                    @click="discountType = 'percent'; discountValue = 0; if (paymentMethod === 'cash') cashAmount = cartTotal();"
                                    :class="discountType === 'percent' ? 'bg-green-600 text-white' : 'text-slate-600 hover:text-slate-900'"
                                    class="py-1.5 rounded-lg transition cursor-pointer">
                                Persentase (%)
                            </button>
                            <button type="button"
                                    @click="discountType = 'rupiah'; discountValue = 0; if (paymentMethod === 'cash') cashAmount = cartTotal();"
                                    :class="discountType === 'rupiah' ? 'bg-green-600 text-white' : 'text-slate-600 hover:text-slate-900'"
                                    class="py-1.5 rounded-lg transition cursor-pointer">
                                Nominal (Rp)
                            </button>
                        </div>
                    </div>

                    <!-- Mode Persen (%) -->
                    <template x-if="discountType === 'percent'">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Pilih Cepat</label>
                                <div class="grid grid-cols-4 gap-2">
                                    <button type="button" @click="setDiscountPercent(5)" class="py-1.5 rounded-lg font-bold border transition cursor-pointer" :class="discountValue == 5 ? 'bg-green-600 text-white border-green-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">5%</button>
                                    <button type="button" @click="setDiscountPercent(10)" class="py-1.5 rounded-lg font-bold border transition cursor-pointer" :class="discountValue == 10 ? 'bg-green-600 text-white border-green-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">10%</button>
                                    <button type="button" @click="setDiscountPercent(20)" class="py-1.5 rounded-lg font-bold border transition cursor-pointer" :class="discountValue == 20 ? 'bg-green-600 text-white border-green-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">20%</button>
                                    <button type="button" @click="setDiscountPercent(50)" class="py-1.5 rounded-lg font-bold border transition cursor-pointer" :class="discountValue == 50 ? 'bg-green-600 text-white border-green-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">50%</button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Persentase Diskon</label>
                                <div class="relative">
                                    <input type="number"
                                           min="0"
                                           max="100"
                                           x-model.number="discountValue"
                                           @input="if (paymentMethod === 'cash') cashAmount = cartTotal();"
                                           placeholder="0"
                                           class="w-full text-sm py-2 pr-8 pl-3 rounded-xl border border-slate-200 focus:border-green-500 focus:ring-green-500 font-bold text-slate-900">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm pointer-events-none">%</span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Mode Rupiah (Rp) -->
                    <template x-if="discountType === 'rupiah'">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Pilih Cepat</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button type="button" @click="discountValue = 5000; if (paymentMethod === 'cash') cashAmount = cartTotal();" class="py-1.5 rounded-lg font-bold border transition cursor-pointer" :class="discountValue == 5000 ? 'bg-green-600 text-white border-green-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">5rb</button>
                                    <button type="button" @click="discountValue = 10000; if (paymentMethod === 'cash') cashAmount = cartTotal();" class="py-1.5 rounded-lg font-bold border transition cursor-pointer" :class="discountValue == 10000 ? 'bg-green-600 text-white border-green-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">10rb</button>
                                    <button type="button" @click="discountValue = 25000; if (paymentMethod === 'cash') cashAmount = cartTotal();" class="py-1.5 rounded-lg font-bold border transition cursor-pointer" :class="discountValue == 25000 ? 'bg-green-600 text-white border-green-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">25rb</button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nominal Potongan (Rp)</label>
                                <input type="text"
                                       :value="formatThousand(discountValue)"
                                       @input="discountValue = parseThousand($event.target.value); if (paymentMethod === 'cash') cashAmount = cartTotal();"
                                       placeholder="Rp 0"
                                       class="w-full text-sm py-2 px-3 rounded-xl border border-slate-200 focus:border-green-500 focus:ring-green-500 font-bold text-slate-900">
                            </div>
                        </div>
                    </template>

                    <!-- Ringkasan Potongan -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                        <div class="flex justify-between text-slate-500">
                            <span>Potongan Harga:</span>
                            <span class="font-bold text-rose-600" x-text="'-' + formatRupiah(actualDiscount())"></span>
                        </div>
                        <div class="flex justify-between text-slate-900 font-bold pt-1 border-t border-slate-200">
                            <span>Total Setelah Diskon:</span>
                            <span class="text-green-700" x-text="formatRupiah(cartTotal())"></span>
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50/50 flex items-center justify-between gap-2">
                    <button type="button"
                            @click="discountValue = 0; if (paymentMethod === 'cash') cashAmount = cartTotal(); showDiscountModal = false"
                            class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-rose-50 hover:border-rose-200 text-slate-600 hover:text-rose-600 font-semibold text-xs transition cursor-pointer">
                        Reset Diskon
                    </button>
                    <button type="button"
                            @click="showDiscountModal = false"
                            class="px-4 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold text-xs transition cursor-pointer">
                        Simpan Diskon
                    </button>
                </div>
            </div>
        </div>

        <!-- ================= MODAL STRUK SUKSES (TIDAK TERPOTONG, PREVIEW STRUK MODERN) ================= -->
        <div x-show="showReceiptModal"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.outside="closeReceiptModal()"
                 class="bg-white rounded-[28px] max-w-md w-full max-h-[92vh] flex flex-col border border-slate-200 overflow-hidden text-slate-800 relative">

                <!-- Tombol Close Pojok Kanan Atas -->
                <button type="button"
                        @click="closeReceiptModal()"
                        title="Tutup"
                        class="absolute top-4 right-4 p-1.5 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Top Header (Fixed, Tidak Ikut Terpotong) -->
                <div class="pt-5 pb-3 px-6 text-center shrink-0">
                    <div class="w-14 h-14 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-2.5">
                        <div class="w-9 h-9 bg-green-600 text-white rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.8" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="font-extrabold text-lg sm:text-xl text-slate-900 tracking-tight">Pembayaran Berhasil!</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Transaksi telah berhasil tersimpan.</p>
                </div>

                <!-- Scrollable Receipt Preview Frame -->
                <div class="flex-1 overflow-y-auto px-4 sm:px-5 py-1 min-h-0">
                    <div class="p-3 rounded-2xl bg-slate-100/90 border border-slate-200/80">
                        <div id="receipt-print-area" class="bg-white rounded-xl p-4 sm:p-5 text-xs font-mono text-slate-800 border border-slate-200/70 w-full mx-auto">
                            <!-- Logo Bulat Hijau Tua di Atas Struk -->
                            @if ($supplier->receipt_show_logo)
                                <div class="text-center mb-2.5">
                                    @if ($supplier->receipt_logo)
                                        <div class="w-12 h-12 rounded-full overflow-hidden border border-slate-300 mx-auto">
                                            <img src="{{ asset('storage/' . $supplier->receipt_logo) }}" alt="Logo" class="w-full h-full object-cover">
                                        </div>
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-emerald-950 flex items-center justify-center mx-auto p-2.5">
                                            <img src="{{ asset('images/logo-omnipos.png') }}" alt="OmniPOS" class="w-full h-full object-contain">
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Nama Toko & Alamat -->
                            <div class="text-center pb-3 border-b border-dashed border-slate-300 space-y-0.5">
                                @if ($supplier->receipt_show_header)
                                    <div class="font-extrabold text-sm uppercase tracking-wide text-slate-900">{{ $supplier->receipt_header_title ?: $supplier->store_name }}</div>
                                @endif
                                @if ($supplier->receipt_show_address && $supplier->warehouse_address)
                                    <div class="text-[10px] text-slate-500 leading-tight">{{ $supplier->warehouse_address }}</div>
                                @endif
                                @if ($supplier->receipt_show_phone && $supplier->contact_phone)
                                    <div class="text-[10px] text-slate-500">Telp: {{ $supplier->contact_phone }}</div>
                                @endif
                            </div>

                            <!-- Meta Transaksi (ID Trans, Tanggal, Waktu) -->
                            <div class="py-3 border-b border-dashed border-slate-300 space-y-1.5 text-[11px]">
                                @if ($supplier->receipt_show_invoice)
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">ID Trans</span>
                                        <span class="font-bold text-slate-900" x-text="'#' + (completedOrder?.invoice_number || '')"></span>
                                    </div>
                                @endif
                                @if ($supplier->receipt_show_date)
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Tanggal</span>
                                        <span class="font-bold text-slate-900">{{ date('d/m/Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Waktu</span>
                                        <span class="font-bold text-slate-900">{{ date('H:i') }}</span>
                                    </div>
                                @endif
                                @if ($supplier->receipt_show_cashier)
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Kasir</span>
                                        <span class="font-bold text-slate-900">{{ Auth::user()->name }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Header Tabel Item (ITEM & TOTAL) -->
                            <div class="py-3 space-y-2.5 text-[11px]">
                                <div class="flex justify-between font-extrabold text-slate-900 uppercase tracking-wider">
                                    <span>ITEM</span>
                                    <span>TOTAL</span>
                                </div>

                                <template x-for="item in completedOrder?.items" :key="item.id">
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-slate-900 uppercase leading-snug" x-text="item.product_name"></div>
                                        <div class="flex justify-between items-center text-slate-500">
                                            <span x-text="item.quantity + ' x ' + formatRupiah(item.unit_price || (item.subtotal / item.quantity))"></span>
                                            <span class="font-bold text-slate-900" x-text="formatRupiah(item.subtotal)"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Subtotal & Diskon (Jika Ada Diskon) -->
                            <div x-show="completedOrder?.discount > 0" class="pt-2 border-t border-dashed border-slate-300 space-y-1 text-[11px]">
                                <div class="flex justify-between text-slate-500">
                                    <span>Subtotal</span>
                                    <span class="font-bold text-slate-800" x-text="formatRupiah(completedOrder?.subtotal)"></span>
                                </div>
                                <div class="flex justify-between text-rose-600">
                                    <span>Diskon</span>
                                    <span class="font-bold" x-text="'-' + formatRupiah(completedOrder?.discount)"></span>
                                </div>
                            </div>

                            <!-- Baris TOTAL dengan Garis Putus-Putus Ganda Tegas -->
                            <div class="border-y-2 border-dashed border-slate-800 py-2.5 my-2.5 flex items-center justify-between text-sm font-extrabold text-slate-900">
                                <span>TOTAL</span>
                                <span x-text="formatRupiah(completedOrder?.final_amount)"></span>
                            </div>

                            <!-- Rincian Metode Pembayaran, Tunai/Bayar, & Kembalian -->
                            <div class="py-2.5 border-b border-dashed border-slate-300 space-y-1.5 text-[11px]">
                                @if ($supplier->receipt_show_payment_method)
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Metode Pembayaran</span>
                                        <span class="font-bold text-slate-900" x-text="completedOrder?.payment_method === 'cash' ? 'Tunai' : (completedOrder?.payment_method || '').toUpperCase()"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Tunai / Bayar</span>
                                        <span class="font-bold text-slate-900" x-text="formatRupiah(completedOrder?.cash_amount)"></span>
                                    </div>
                                @endif
                                @if ($supplier->receipt_show_change)
                                    <div class="flex justify-between font-bold text-emerald-700">
                                        <span>Kembalian</span>
                                        <span x-text="formatRupiah(completedOrder?.change_amount || 0)"></span>
                                    </div>
                                @endif
                            </div>

                            <!-- Footer Ucapan Terima Kasih -->
                            <div class="text-center pt-3.5 space-y-1">
                                <div class="font-extrabold text-xs text-slate-900 uppercase tracking-wider">TERIMA KASIH</div>
                                @if ($supplier->receipt_show_footer)
                                    <div class="text-[10px] font-bold text-slate-700 uppercase whitespace-pre-line leading-relaxed">
                                        {{ $supplier->receipt_footer_notes ?: 'TERIMA KASIH ATAS KUNJUNGAN ANDA!' }}
                                    </div>
                                @endif
                                <div class="text-[9px] italic text-slate-400 uppercase pt-1">
                                    DIHASILKAN OLEH SISTEM OMNI POS
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Bluetooth jika terhubung -->
                <div x-show="bluetoothStatus" x-cloak class="mx-5 mt-2 p-2 rounded-xl bg-green-50 border border-green-200 text-[11px] text-green-800 text-center font-medium shrink-0" x-text="bluetoothStatus"></div>

                <!-- Bottom Action Buttons (Fixed di Bawah Modal, Tidak Terpotong) -->
                <div class="p-4 sm:px-5 bg-white border-t border-slate-100 grid grid-cols-2 gap-2.5 shrink-0">
                    <!-- Tombol Cetak -->
                    <button type="button"
                            @click="printReceipt()"
                            class="py-2.5 px-4 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak</span>
                    </button>

                    <!-- Tombol Bluetooth / Selesai -->
                    <button type="button"
                            @click="closeReceiptModal()"
                            class="py-2.5 px-4 rounded-2xl bg-green-600 hover:bg-green-700 text-white text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Selesai</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Media Styling untuk Kertas Kasir 58mm / 80mm & Semua Printer -->
    <style>
        @media print {
            body * {
                visibility: hidden !important;
            }
            #receipt-print-area, #receipt-print-area * {
                visibility: visible !important;
            }
            #receipt-print-area {
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                max-width: {{ $supplier->receipt_paper_size === '80mm' ? '80mm' : '58mm' }} !important;
                margin: 0 auto !important;
                padding: 4px !important;
                background: #ffffff !important;
                color: #000000 !important;
                border: none !important;
                font-size: {{ $supplier->receipt_paper_size === '80mm' ? '12px' : '11px' }} !important;
                line-height: 1.3 !important;
                font-family: 'Courier New', Courier, monospace !important;
                box-border border-slate-200: none !important;
            }
            @page {
                size: {{ $supplier->receipt_paper_size === '80mm' ? '80mm' : '58mm' }} auto;
                margin: 0;
            }
        }
    </style>

    @push('scripts')
    <script>
        function posApp() {
            return {
                products: @json($products),
                search: '',
                searchOpen: false,
                selectedCategory: '',
                posPricingMode: @json($supplier->pos_pricing_mode ?? 'global_filter'),
                selectedTierLevel: 1,
                cart: [],
                customerName: 'Pelanggan Offline',
                paymentMethod: 'cash',
                discountType: 'percent',
                discountValue: 0,
                showDiscountModal: false,
                cashAmount: 0,
                cartOpen: false,
                isProcessing: false,
                showReceiptModal: false,
                completedOrder: null,
                bluetoothDevice: null,
                bluetoothCharacteristic: null,
                isConnectingBluetooth: false,
                bluetoothStatus: '',
                warningToast: false,
                warningToastMessage: '',
                warningToastTimer: null,
                cartTooltipText: '',
                cartTooltipTop: 0,
                cartTooltipLeft: 0,

                showCartTooltip(event, name) {
                    const rect = event.currentTarget.getBoundingClientRect();
                    this.cartTooltipText = name;
                    this.cartTooltipTop = Math.max(40, rect.top - 6);
                    this.cartTooltipLeft = rect.left + 44;
                },

                hideCartTooltip() {
                    this.cartTooltipText = '';
                },

                cleanTierName(name) {
                    if (!name) return 'Eceran';
                    return name.replace(/\s*\([^)]*\)/g, '').trim();
                },

                shortProductName(name) {
                    if (!name) return '';
                    const words = name.trim().split(/\s+/);
                    if (words.length <= 3) return name;
                    return words.slice(0, 3).join(' ') + '...';
                },

                get availableTiers() {
                    const map = new Map();
                    this.products.forEach(p => {
                        if (p.tiers && p.tiers.length) {
                            p.tiers.forEach(t => {
                                const lvl = parseInt(t.tier_level) || 1;
                                if (!map.has(lvl)) {
                                    map.set(lvl, {
                                        level: lvl,
                                        name: this.cleanTierName(t.name) || ('Level ' + lvl),
                                        multiplier: parseInt(t.unit_multiplier) || 1
                                    });
                                }
                            });
                        }
                    });

                    if (map.size === 0) {
                        return [
                            { level: 1, name: 'Eceran', multiplier: 1 },
                            { level: 2, name: 'Grosir', multiplier: 12 },
                            { level: 3, name: 'Grosir Besar', multiplier: 72 }
                        ];
                    }
                    return Array.from(map.values()).sort((a, b) => a.level - b.level);
                },

                getCurrentTier(product) {
                    if (!product.tiers || product.tiers.length === 0) {
                        return {
                            tier_level: 1,
                            name: 'Eceran',
                            unit_multiplier: 1,
                            price: parseFloat(product.selling_price)
                        };
                    }
                    const match = product.tiers.find(t => parseInt(t.tier_level) === parseInt(this.selectedTierLevel));
                    if (match) return match;
                    return product.tiers[0];
                },

                setPricingMode(newMode) {
                    this.posPricingMode = newMode;
                    try {
                        localStorage.setItem('pos_pricing_mode', newMode);
                    } catch(e) {}

                    fetch('{{ route('admin.pos.pricing-mode') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ pos_pricing_mode: newMode })
                    }).catch(err => console.error('Gagal menyimpan preferensi kasir:', err));
                },

                showWarningToast(message) {
                    this.warningToastMessage = message;
                    this.warningToast = true;
                    if (this.warningToastTimer) clearTimeout(this.warningToastTimer);
                    this.warningToastTimer = setTimeout(() => {
                        this.warningToast = false;
                    }, 3500);
                },

                actualDiscount() {
                    const subtotal = this.cartSubtotal();
                    if (this.discountType === 'percent') {
                        const pct = Math.min(100, Math.max(0, parseFloat(this.discountValue) || 0));
                        return Math.round((subtotal * pct) / 100);
                    }
                    return Math.min(subtotal, Math.max(0, parseFloat(this.discountValue) || 0));
                },

                setDiscountPercent(pct) {
                    this.discountType = 'percent';
                    this.discountValue = pct;
                    if (this.paymentMethod === 'cash') {
                        this.cashAmount = this.cartTotal();
                    }
                },

                get filteredProducts() {
                    return this.products.filter(p => {
                        const matchNameOrSku = !this.search ||
                            p.name.toLowerCase().includes(this.search.toLowerCase()) ||
                            (p.sku && p.sku.toLowerCase().includes(this.search.toLowerCase()));
                        const matchCategory = !this.selectedCategory || p.category_id == this.selectedCategory;
                        return matchNameOrSku && matchCategory;
                    });
                },

                handleBarcodeScan() {
                    const query = (this.search || '').trim();
                    if (!query) return;

                    // 1. Cari produk berdasarkan SKU yang cocok persis (case-insensitive)
                    const exactSkuMatch = this.products.find(p => p.sku && p.sku.toUpperCase() === query.toUpperCase());

                    if (exactSkuMatch) {
                        const tier = this.posPricingMode === 'global_filter' ? this.getCurrentTier(exactSkuMatch) : null;
                        this.addToCart(exactSkuMatch, tier);
                        this.search = '';
                        this.$nextTick(() => {
                            if (this.$refs.searchInput) {
                                this.$refs.searchInput.focus();
                            }
                        });
                        return;
                    }

                    // 2. Jika hanya ada tepat 1 produk pada filteredProducts
                    if (this.filteredProducts.length === 1) {
                        const prod = this.filteredProducts[0];
                        const tier = this.posPricingMode === 'global_filter' ? this.getCurrentTier(prod) : null;
                        this.addToCart(prod, tier);
                        this.search = '';
                        this.$nextTick(() => {
                            if (this.$refs.searchInput) {
                                this.$refs.searchInput.focus();
                            }
                        });
                        return;
                    }

                    // 3. Jika tidak ditemukan kecocokan persis
                    this.showWarningToast('Produk dengan SKU / Barcode "' + query + '" tidak ditemukan.');
                },

                addToCart(product, tier = null) {
                    const defaultTier = (product.tiers && product.tiers.length) ? product.tiers[0] : null;
                    const activeTier = tier || defaultTier;
                    const tierName = activeTier ? this.cleanTierName(activeTier.name) : 'Eceran';
                    const multiplier = activeTier ? parseInt(activeTier.unit_multiplier || 1) : 1;
                    const price = activeTier ? parseFloat(activeTier.price) : parseFloat(product.selling_price);

                    // Hitung total unit fisik produk ini yang SUDAH ADA di keranjang saat ini
                    const currentlyCommitted = this.cart
                        .filter(i => (i.product_id || i.id) === product.id)
                        .reduce((sum, i) => sum + (i.quantity * (i.unit_multiplier || 1)), 0);

                    if (currentlyCommitted + multiplier > product.stock) {
                        this.showWarningToast('Stok fisik "' + product.name + '" tidak mencukupi! Sisa stok fisik: ' + product.stock + ' pcs.');
                        return;
                    }

                    // Buka keranjang otomatis jika sedang tertutup
                    this.cartOpen = true;

                    const cartKey = product.id + '-' + tierName;
                    const existingIndex = this.cart.findIndex(i => i.cart_key === cartKey);

                    if (existingIndex > -1) {
                        this.cart[existingIndex].quantity++;
                    } else {
                        this.cart.push({
                            cart_key: cartKey,
                            id: product.id,
                            product_id: product.id,
                            name: product.name,
                            sku: product.sku,
                            tier_name: tierName,
                            unit_multiplier: multiplier,
                            image_url: product.image_url,
                            selling_price: price,
                            cost_price: parseFloat(product.cost_price),
                            stock: product.stock,
                            quantity: 1
                        });
                    }

                    if (this.paymentMethod === 'cash') {
                        this.cashAmount = this.cartTotal();
                    }
                },

                increaseQty(index) {
                    const item = this.cart[index];
                    const product = this.products.find(p => p.id === (item.product_id || item.id));
                    if (!product) return;

                    const multiplier = item.unit_multiplier || 1;
                    const currentlyCommitted = this.cart
                        .filter(i => (i.product_id || i.id) === product.id)
                        .reduce((sum, i) => sum + (i.quantity * (i.unit_multiplier || 1)), 0);

                    if (currentlyCommitted + multiplier > product.stock) {
                        this.showWarningToast('Kuantitas melebihi sisa stok fisik yang tersedia (' + product.stock + ' pcs)');
                        return;
                    }
                    item.quantity++;
                    if (this.paymentMethod === 'cash') {
                        this.cashAmount = this.cartTotal();
                    }
                },

                decreaseQty(index) {
                    if (this.cart[index].quantity > 1) {
                        this.cart[index].quantity--;
                    } else {
                        this.removeItem(index);
                    }
                    if (this.paymentMethod === 'cash') {
                        this.cashAmount = this.cartTotal();
                    }
                },

                removeItem(index) {
                    this.cart.splice(index, 1);
                    if (this.paymentMethod === 'cash') {
                        this.cashAmount = this.cartTotal();
                    }
                },

                clearCart() {
                    this.cart = [];
                    this.discountValue = 0;
                    this.cashAmount = 0;
                },

                totalCartItems() {
                    return this.cart.reduce((sum, item) => sum + item.quantity, 0);
                },

                cartSubtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.selling_price * item.quantity), 0);
                },

                cartTotal() {
                    return Math.max(0, this.cartSubtotal() - this.actualDiscount());
                },

                calculateChange() {
                    return (parseFloat(this.cashAmount) || 0) - this.cartTotal();
                },

                roundUpCash(amount, step) {
                    return Math.ceil(amount / step) * step;
                },

                // Helper memformat angka dengan pemisah ribuan titik (1.000)
                formatThousand(value) {
                    if (value === null || value === undefined || value === '') return '';
                    const num = parseInt(String(value).replace(/\D/g, ''), 10);
                    if (isNaN(num)) return '';
                    return new Intl.NumberFormat('id-ID').format(num);
                },

                // Helper mengubah format titik kembali ke angka numerik
                parseThousand(val) {
                    if (!val) return 0;
                    const clean = String(val).replace(/\D/g, '');
                    return parseInt(clean, 10) || 0;
                },

                formatRupiah(number) {
                    return 'Rp ' + Number(number || 0).toLocaleString('id-ID');
                },

                async processCheckout() {
                    if (this.cart.length === 0) return;

                    const total = this.cartTotal();
                    if (this.paymentMethod === 'cash' && this.cashAmount < total) {
                        alert('Nominal uang tunai kurang dari total belanja.');
                        return;
                    }

                    this.isProcessing = true;

                    try {
                        const payload = {
                            customer_name: this.customerName || 'Pelanggan Offline',
                            payment_method: this.paymentMethod,
                            discount: this.actualDiscount(),
                            cash_amount: this.paymentMethod === 'cash' ? this.cashAmount : total,
                            items: this.cart.map(i => ({
                                product_id: i.product_id || i.id,
                                quantity: i.quantity,
                                tier_name: i.tier_name || 'Eceran',
                                unit_multiplier: i.unit_multiplier || 1,
                                unit_price: i.selling_price
                            }))
                        };

                        const response = await fetch("{{ route('admin.pos.checkout') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            throw new Error(result.message || 'Gagal memproses transaksi.');
                        }

                        // Kurangi stok di memory secara realtime sesuai unit fisik
                        this.cart.forEach(cartItem => {
                            const p = this.products.find(prod => prod.id === (cartItem.product_id || cartItem.id));
                            if (p) {
                                p.stock -= (cartItem.quantity * (cartItem.unit_multiplier || 1));
                            }
                        });

                        this.completedOrder = result.order;
                        this.showReceiptModal = true;
                        this.clearCart();
                    } catch (err) {
                        alert(err.message);
                    } finally {
                        this.isProcessing = false;
                    }
                },

                printReceipt() {
                    window.print();
                },

                // Menghubungkan & Mencetak langsung ke Printer Thermal Bluetooth (ESC/POS)
                async printBluetoothReceipt() {
                    if (!navigator.bluetooth) {
                        alert('Browser perangkat ini belum mendukung Web Bluetooth API secara langsung. Anda tetap dapat menggunakan tombol "Cetak Struk" untuk mencetak ke semua printer (Bluetooth, USB, WiFi, AirPrint, Mopria).');
                        this.printReceipt();
                        return;
                    }

                    if (!this.completedOrder) {
                        alert('Tidak ada data transaksi yang dapat dicetak.');
                        return;
                    }

                    this.isConnectingBluetooth = true;
                    this.bluetoothStatus = 'Mencari printer Bluetooth...';

                    try {
                        // Request Bluetooth Device dengan service umum ESC/POS Printer
                        if (!this.bluetoothCharacteristic) {
                            this.bluetoothDevice = await navigator.bluetooth.requestDevice({
                                filters: [
                                    { services: ['000018f0-0000-1000-8000-00805f9b34fb'] },
                                    { services: ['e7810a71-73ae-499d-8c15-faa9aef0c3f2'] },
                                    { services: ['49535343-fe7d-4ae5-8fa9-9fafd205e455'] },
                                    { namePrefix: 'RPP' },
                                    { namePrefix: 'MPT' },
                                    { namePrefix: 'POS' },
                                    { namePrefix: 'MTP' },
                                    { namePrefix: 'Printer' },
                                    { namePrefix: 'Thermal' }
                                ],
                                optionalServices: [
                                    '000018f0-0000-1000-8000-00805f9b34fb',
                                    'e7810a71-73ae-499d-8c15-faa9aef0c3f2',
                                    '49535343-fe7d-4ae5-8fa9-9fafd205e455',
                                    '0000ff00-0000-1000-8000-00805f9b34fb'
                                ]
                            });

                            this.bluetoothStatus = 'Menghubungkan ke ' + this.bluetoothDevice.name + '...';
                            const server = await this.bluetoothDevice.gatt.connect();

                            // Cari primary service yang aktif
                            const services = await server.getPrimaryServices();
                            if (services.length === 0) {
                                throw new Error('Service printer tidak ditemukan.');
                            }

                            const service = services[0];
                            const characteristics = await service.getCharacteristics();
                            
                            // Cari characteristic yang mendukung 'write' atau 'writeWithoutResponse'
                            const writeChar = characteristics.find(c => c.properties.write || c.properties.writeWithoutResponse);
                            if (!writeChar) {
                                throw new Error('Characteristic komunikasi printer tidak tersedia.');
                            }

                            this.bluetoothCharacteristic = writeChar;
                        }

                        this.bluetoothStatus = 'Mengirim data struk ke printer...';

                        // Buat bytes ESC/POS untuk dicetak
                        const escPosBytes = this.generateEscPosBytes(this.completedOrder);

                        // Kirim data secara berurutan dalam chunk 100 bytes (agar tidak overflow)
                        const chunkSize = 100;
                        for (let i = 0; i < escPosBytes.length; i += chunkSize) {
                            const chunk = escPosBytes.slice(i, i + chunkSize);
                            if (this.bluetoothCharacteristic.writeValueWithoutResponse) {
                                await this.bluetoothCharacteristic.writeValueWithoutResponse(chunk);
                            } else {
                                await this.bluetoothCharacteristic.writeValue(chunk);
                            }
                            await new Promise(r => setTimeout(r, 20));
                        }

                        this.bluetoothStatus = 'Struk berhasil dicetak via Bluetooth!';
                        setTimeout(() => { this.bluetoothStatus = ''; }, 4000);
                    } catch (error) {
                        console.error('Bluetooth Print Error:', error);
                        this.bluetoothStatus = 'Koneksi Bluetooth batal / gagal: ' + error.message;
                        // Fallback ke window.print universal
                        if (confirm('Koneksi Bluetooth dibatalkan. Buka jendela cetak sistem/browser?')) {
                            this.printReceipt();
                        }
                    } finally {
                        this.isConnectingBluetooth = false;
                    }
                },

                // Generate perintah ESC/POS sesuai konfigurasi struk toko
                generateEscPosBytes(order) {
                    const ESC = 0x1B;
                    const GS = 0x1D;
                    const LF = 0x0A;
                    const lineWidth = {{ $supplier->receipt_paper_size === '80mm' ? 42 : 32 }};

                    let commands = [];
                    const encoder = new TextEncoder();

                    function addText(str) {
                        const bytes = encoder.encode(str);
                        for (let i = 0; i < bytes.length; i++) commands.push(bytes[i]);
                    }

                    // Reset / Initialize Printer (ESC @)
                    commands.push(ESC, 0x40);

                    // Center Align (ESC a 1)
                    commands.push(ESC, 0x61, 1);

                    @if ($supplier->receipt_show_header)
                        // Bold On (ESC E 1)
                        commands.push(ESC, 0x45, 1);
                        addText(@json(($supplier->receipt_header_title ?: $supplier->store_name) . "\n"));
                        commands.push(ESC, 0x45, 0);
                    @endif

                    @if ($supplier->receipt_show_address && $supplier->warehouse_address)
                        addText(@json($supplier->warehouse_address . "\n"));
                    @endif

                    @if ($supplier->receipt_show_phone && $supplier->contact_phone)
                        addText(@json("Telp: " . $supplier->contact_phone . "\n"));
                    @endif

                    @if ($supplier->receipt_show_email && $supplier->email)
                        addText(@json("Email: " . $supplier->email . "\n"));
                    @endif

                    addText("-".repeat(lineWidth) + "\n");

                    // Left Align (ESC a 0)
                    commands.push(ESC, 0x61, 0);

                    @if ($supplier->receipt_show_invoice)
                        addText("Inv : " + (order.invoice_number || '') + "\n");
                    @endif

                    @if ($supplier->receipt_show_date)
                        addText("Wkt : " + new Date().toLocaleString('id-ID') + "\n");
                    @endif

                    @if ($supplier->receipt_show_cashier)
                        addText(@json("Ksr : " . Auth::user()->name . "\n"));
                    @endif

                    @if ($supplier->receipt_show_customer)
                        addText("Plg : " + (order.customer_name || 'Pelanggan Offline') + "\n");
                    @endif

                    addText("-".repeat(lineWidth) + "\n");

                    // Items list
                    if (order.items && order.items.length) {
                        order.items.forEach(item => {
                            addText(item.product_name + "\n");
                            if (item.product_sku) {
                                addText("  #" + item.product_sku + "\n");
                            }
                            const qtyPrice = item.quantity + " x " + this.formatRupiah(item.unit_price || (item.subtotal / item.quantity));
                            const subtotal = this.formatRupiah(item.subtotal);
                            const spaces = Math.max(1, lineWidth - qtyPrice.length - subtotal.length);
                            addText(qtyPrice + " ".repeat(spaces) + subtotal + "\n");
                        });
                    }

                    addText("-".repeat(lineWidth) + "\n");

                    // Totals
                    @if ($supplier->receipt_show_subtotal)
                        const subtotalText = "Subtotal";
                        const subtotalVal = this.formatRupiah(order.subtotal);
                        addText(subtotalText + " ".repeat(Math.max(1, lineWidth - subtotalText.length - subtotalVal.length)) + subtotalVal + "\n");
                    @endif

                    @if ($supplier->receipt_show_discount)
                        if (order.discount > 0) {
                            const discText = "Diskon";
                            const discVal = "-" + this.formatRupiah(order.discount);
                            addText(discText + " ".repeat(Math.max(1, lineWidth - discText.length - discVal.length)) + discVal + "\n");
                        }
                    @endif

                    // Bold On for Total
                    commands.push(ESC, 0x45, 1);
                    const totalText = "TOTAL";
                    const totalVal = this.formatRupiah(order.final_amount);
                    addText(totalText + " ".repeat(Math.max(1, lineWidth - totalText.length - totalVal.length)) + totalVal + "\n");
                    commands.push(ESC, 0x45, 0);

                    @if ($supplier->receipt_show_payment_method)
                        const payText = "Bayar (" + (order.payment_method || 'CASH').toUpperCase() + ")";
                        const payVal = this.formatRupiah(order.cash_amount);
                        addText(payText + " ".repeat(Math.max(1, lineWidth - payText.length - payVal.length)) + payVal + "\n");
                    @endif

                    @if ($supplier->receipt_show_change)
                        if (order.change_amount > 0) {
                            const chgText = "Kembalian";
                            const chgVal = this.formatRupiah(order.change_amount);
                            addText(chgText + " ".repeat(Math.max(1, lineWidth - chgText.length - chgVal.length)) + chgVal + "\n");
                        }
                    @endif

                    @if ($supplier->receipt_show_footer && $supplier->receipt_footer_notes)
                        addText("-".repeat(lineWidth) + "\n");
                        commands.push(ESC, 0x61, 1);
                        addText(@json($supplier->receipt_footer_notes . "\n"));
                    @endif

                    addText("\n\n\n\n");

                    // Cut Paper (GS V 66 0)
                    commands.push(GS, 0x56, 66, 0);

                    return new Uint8Array(commands);
                },

                closeReceiptModal() {
                    this.showReceiptModal = false;
                    this.completedOrder = null;
                    this.bluetoothStatus = '';
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
