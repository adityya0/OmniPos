<x-app-layout title="Produk & Stok">
    <x-slot name="topbar">
        <div class="flex items-center justify-between gap-4 w-full pr-2">
            <div class="flex items-baseline gap-2.5 min-w-0">
                <h2 class="font-bold text-base sm:text-lg text-slate-900 truncate">
                    Inventori & Manajemen Produk
                </h2>
                <span class="text-xs text-slate-400 hidden xl:inline truncate">
                    &bull; Toko: <strong class="font-semibold text-slate-700">{{ $supplier->store_name }}</strong>
                </span>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <!-- Tombol Edit Kategori Level Massal (Bubble Outline) -->
                <button type="button"
                        onclick="window.dispatchEvent(new CustomEvent('open-tier-modal'))"
                        @click="window.dispatchEvent(new CustomEvent('open-tier-modal'))"
                        class="px-3.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 hover:text-emerald-700 border border-slate-200 hover:border-emerald-300 font-semibold text-xs rounded-full transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span>Edit Kategori Level</span>
                </button>

                <!-- Tombol Import Excel / CSV (Bubble Outline) -->
                <button type="button"
                        onclick="window.dispatchEvent(new CustomEvent('open-import-modal'))"
                        @click="window.dispatchEvent(new CustomEvent('open-import-modal'))"
                        class="px-3.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 hover:text-emerald-700 border border-slate-200 hover:border-emerald-300 font-semibold text-xs rounded-full transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Import Excel / CSV</span>
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Header Fallback untuk Layar Mobile (< lg) -->
    <div class="lg:hidden px-4 py-3 border-b border-slate-200 bg-white flex flex-wrap items-center justify-between gap-2">
        <h2 class="font-bold text-sm text-slate-900">Inventori & Manajemen Produk</h2>
        <div class="flex items-center gap-1.5">
            <button type="button"
                    onclick="window.dispatchEvent(new CustomEvent('open-tier-modal'))"
                    class="px-2.5 py-1.5 bg-white border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg">
                Edit Level
            </button>
            <button type="button"
                    onclick="window.dispatchEvent(new CustomEvent('open-import-modal'))"
                    class="px-2.5 py-1.5 bg-white border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg">
                Import CSV
            </button>
        </div>
    </div>

    <div class="py-0" x-data="productInventory()" @open-import-modal.window="showImportModal = true" @open-tier-modal.window="showTierModal = true">
        <div class="w-full space-y-6">

            <!-- Flash Alert -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs space-y-1">
                    <div class="font-bold">Terjadi kesalahan:</div>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- KPI Metric Cards: Simple, Tanpa Icon, Teks Judul Lebih Besar -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 rounded-2xl bg-white border border-slate-200/90 flex flex-col justify-between">
                    <span class="text-sm font-semibold text-slate-600">Total Macam Stok</span>
                    <h3 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-2">{{ number_format($stats['total_products']) }}</h3>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-slate-200/90 flex flex-col justify-between">
                    <span class="text-sm font-semibold text-emerald-700">Stok di Gudang</span>
                    <h3 class="text-2xl sm:text-3xl font-bold text-emerald-700 mt-2">{{ number_format($stats['total_stock']) }} <span class="text-xs font-medium text-slate-400">Unit</span></h3>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-slate-200/90 flex flex-col justify-between">
                    <span class="text-sm font-semibold text-sky-700">Stok Dibooking</span>
                    <h3 class="text-2xl sm:text-3xl font-bold text-sky-700 mt-2">{{ number_format($stats['total_pending_dropship']) }} <span class="text-xs font-medium text-slate-400">Unit</span></h3>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-slate-200/90 flex flex-col justify-between">
                    <span class="text-sm font-semibold text-amber-700">Stok Menipis</span>
                    <h3 class="text-2xl sm:text-3xl font-bold text-amber-600 mt-2">{{ number_format($stats['low_stock']) }}</h3>
                </div>
            </div>

            <!-- Bulk Action Bar (Muncul jika ada produk dipilih) -->
            <div x-show="selectedIds.length > 0"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200 flex flex-col sm:flex-row items-center justify-between gap-3 ">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">
                        <span x-text="selectedIds.length"></span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-800 text-xs"><span x-text="selectedIds.length"></span> Produk Terpilih</span>
                        <p class="text-[11px] text-slate-500">Aksi massal untuk semua produk yang dicentang.</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button"
                            @click="selectedIds = []"
                            class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition">
                        Batal Pilihan
                    </button>

                    <!-- Tombol Restock Massal -->
                    <!-- Tombol Restock Massal -->
                    <button type="button"
                            @click="openBulkRestock()"
                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold transition ">
                        Restock Massal (<span x-text="selectedIds.length"></span>)
                    </button>

                    <!-- Tombol Hapus Massal -->
                    <form method="POST"
                          action="{{ route('admin.products.bulk-delete') }}"
                          @submit="return confirm('Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' produk terpilih dari inventori? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        <template x-for="id in selectedIds" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <button type="submit"
                                class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white text-xs font-bold transition ">
                            Hapus Massal
                        </button>
                    </form>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200  overflow-hidden">
                <!-- Search & Filter Header -->
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <form method="GET" action="{{ route('admin.products.index') }}" class="flex-1 flex flex-col sm:flex-row gap-2 w-full">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari nama produk atau SKU..."
                               class="text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 flex-1">

                        <select name="category_id" class="text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                            Filter
                        </button>

                        @if (request()->hasAny(['search', 'category_id', 'filter']))
                            <a href="{{ route('admin.products.index') }}" class="px-3 py-2 text-xs text-red-500 hover:underline flex items-center">
                                Reset
                            </a>
                        @endif
                    </form>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('admin.products.index', ['filter' => 'low_stock']) }}" class="text-xs font-semibold px-3.5 py-2 rounded-full border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 transition whitespace-nowrap">
                            Lihat Stok Menipis
                        </a>

                        <!-- Tombol Icon Bubble Tambah Produk Baru di Paling Kanan -->
                        <a href="{{ route('admin.products.create') }}"
                           title="Tambah Produk Baru"
                           class="w-9 h-9 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white transition flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="sr-only">Tambah Produk Baru</span>
                        </a>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="overflow-x-auto min-h-[320px]">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-100">
                            <tr>
                                <th class="w-10 px-4 py-3 text-center">
                                    <input type="checkbox"
                                           @change="toggleSelectAll($event)"
                                           :checked="isAllSelected()"
                                           title="Pilih Semua Produk di Halaman Ini"
                                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </th>
                                <th class="px-4 py-3 whitespace-nowrap">Produk</th>
                                <th class="px-4 py-3 whitespace-nowrap">Kategori</th>
                                <th class="px-4 py-3 whitespace-nowrap">Dimensi & Berat</th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">HPP Modal</th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <span>Harga Jual POS</span>
                                        <button type="button"
                                                @click="showTierModal = true"
                                                title="Edit Kategori Level Harga Massal untuk Semua Produk"
                                                class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-md bg-green-100 hover:bg-green-200 text-green-800 text-[9px] font-bold lowercase normal-case tracking-normal transition cursor-pointer">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            <span>Edit Level</span>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">Modal Dropship</th>
                                <th class="px-4 py-3 text-center whitespace-nowrap">Sisa Stok</th>
                                <th class="px-4 py-3 text-center whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($products as $product)
                                <tr class="hover:bg-slate-50/70 transition" :class="selectedIds.includes({{ $product->id }}) ? 'bg-indigo-50/40' : ''">
                                    <td class="w-10 px-4 py-3 text-center">
                                        <input type="checkbox"
                                               :value="{{ $product->id }}"
                                               x-model.number="selectedIds"
                                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-11 h-11 rounded-lg object-cover bg-slate-100 shrink-0 border border-slate-200">
                                            <div>
                                                <div class="font-bold text-slate-900 text-xs">{{ $product->name }}</div>
                                                <span class="inline-block mt-0.5 font-mono text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">
                                                    {{ $product->sku }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="font-medium text-slate-600">{{ $product->category?->name ?? '-' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-[11px] text-slate-500 whitespace-nowrap">
                                        <div>{{ $product->length }} &times; {{ $product->width }} &times; {{ $product->height }} cm</div>
                                        <div class="text-[10px] text-slate-400">{{ $product->weight }} gram</div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-slate-600 whitespace-nowrap">
                                        Rp {{ number_format($product->cost_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <div class="font-semibold text-slate-900 text-sm whitespace-nowrap">
                                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                        </div>
                                        @if ($product->tiers && $product->tiers->count() > 1)
                                            <div class="flex flex-col items-end gap-1 mt-1">
                                                @foreach ($product->tiers as $t)
                                                    <span class="inline-flex items-center gap-1 text-[11px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200 whitespace-nowrap" title="{{ $t->name }} (Isi {{ $t->unit_multiplier }} pcs)">
                                                        <span class="font-medium text-slate-800">{{ $t->name }}:</span>
                                                        <span class="text-green-700 font-semibold">Rp {{ number_format($t->price, 0, ',', '.') }}</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-emerald-700 text-sm whitespace-nowrap">
                                        Rp {{ number_format($product->dropship_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button"
                                                @click="openRestock({ id: {{ $product->id }}, name: '{{ addslashes($product->name) }}', sku: '{{ $product->sku }}', stock: {{ $product->stock }}, safety_stock: {{ $product->safety_stock ?? 5 }} })"
                                                title="Klik untuk Tambah Stok / Atur Cadangan Cepat"
                                                class="inline-block px-3 py-1 rounded-full text-xs font-semibold transition hover:scale-105 cursor-pointer "
                                                :class="(productStocks[{{ $product->id }}] !== undefined ? productStocks[{{ $product->id }}] : {{ $product->stock }}) > 5 
                                                    ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' 
                                                    : ((productStocks[{{ $product->id }}] !== undefined ? productStocks[{{ $product->id }}] : {{ $product->stock }}) > 0 
                                                        ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' 
                                                        : 'bg-red-50 text-red-700 hover:bg-red-100 border border-red-200')">
                                            <span x-text="productStocks[{{ $product->id }}] !== undefined ? productStocks[{{ $product->id }}] : {{ $product->stock }}"></span>
                                        </button>
                                        @if($product->pending_dropship_qty > 0)
                                            <div class="mt-1">
                                                <span class="inline-flex items-center gap-0.5 text-xs font-medium text-sky-700 bg-sky-50 px-1.5 py-0.5 rounded border border-sky-200"
                                                      title="Ada {{ $product->pending_dropship_qty }} unit dibooking pesanan online dropship (menunggu approval/packing)">
                                                    📦 {{ $product->pending_dropship_qty }} booked
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center" x-data="{ menuOpen: false }">
                                        <div class="relative inline-block text-left">
                                            <!-- Tombol Titik Tiga Vertikal -->
                                            <button type="button"
                                                    @click="menuOpen = !menuOpen"
                                                    @click.outside="menuOpen = false"
                                                    title="Menu Aksi"
                                                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                </svg>
                                            </button>

                                            <!-- Dropdown Menu Aksi CRUD -->
                                            <div x-show="menuOpen"
                                                 x-cloak
                                                 @click.outside="menuOpen = false"
                                                 x-transition:enter="transition ease-out duration-100"
                                                 x-transition:enter-start="transform opacity-0 scale-95"
                                                 x-transition:enter-end="transform opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-75"
                                                 x-transition:leave-start="transform opacity-100 scale-100"
                                                 x-transition:leave-end="transform opacity-0 scale-95"
                                                 class="absolute right-0 {{ ($loop->count > 2 && $loop->remaining < 2) ? 'bottom-full mb-1 origin-bottom-right' : 'top-full mt-1 origin-top-right' }} w-36 bg-white rounded-xl border border-slate-200-xl border border-slate-200/90 py-1.5 z-40 text-left">
                                                
                                                <!-- Tambah Stok -->
                                                <button type="button"
                                                        @click="openRestock({ id: {{ $product->id }}, name: '{{ addslashes($product->name) }}', sku: '{{ $product->sku }}', stock: {{ $product->stock }} }); menuOpen = false"
                                                        class="w-full block px-3.5 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 transition text-left">
                                                    Tambah Stok
                                                </button>

                                                <!-- Edit Produk -->
                                                <a href="{{ route('admin.products.edit', $product) }}"
                                                   class="w-full block px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition text-left">
                                                    Edit Produk
                                                </a>

                                                <div class="my-1 border-t border-slate-100"></div>

                                                <!-- Hapus Produk -->
                                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini dari inventori?');" class="block w-full">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="w-full block px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition text-left">
                                                        Hapus Produk
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-12 text-center text-slate-400">
                                        Belum ada produk di toko ini. Klik <a href="{{ route('admin.products.create') }}" class="text-indigo-600 underline font-semibold">Tambah Produk Baru</a> atau gunakan <button type="button" @click="showImportModal = true" class="text-emerald-600 underline font-semibold">Import Excel / CSV</button>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- ================= MODAL EDIT KATEGORI LEVEL HARGA MASSAL ================= -->
        <template x-teleport="body">
            <div x-show="showTierModal"
                 x-cloak
                 class="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-4"
                 aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <!-- Backdrop -->
                <div x-show="showTierModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                     @click="showTierModal = false"></div>

                <div class="relative z-10 w-full max-w-2xl my-auto">
                    <div x-show="showTierModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="transform overflow-hidden rounded-3xl bg-white text-left border border-slate-200-2xl transition-all w-full border border-slate-200">

                        <!-- Modal Header -->
                        <div class="bg-gradient-to-r from-slate-900 to-slate-800 p-6 text-white flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-green-500/20 text-green-400 border border-green-500/30 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-black text-base text-white">Kelola & Edit Kategori Level Harga</h3>
                                    <p class="text-xs text-slate-300 mt-0.5">
                                        Ubah nama level atau satuan sekali saja, otomatis terupdate ke <strong>seluruh {{ $stats['total_products'] }} produk</strong>!
                                    </p>
                                </div>
                            </div>

                            <button type="button" @click="showTierModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Modal Form -->
                        <form action="{{ route('admin.products.tiers.bulk-update') }}" method="POST">
                            @csrf
                            <div class="p-6 space-y-4 max-h-[65vh] overflow-y-auto">
                                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-2.5">
                                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="leading-relaxed">
                                        <strong>Sinkronisasi Massal:</strong> Mengubah nama kategori level di sini (misal: <em>Eceran</em>, <em>Grosir Lusin</em>, <em>Grosir Dus</em>) akan langsung memperbarui label dan filter di semua halaman kasir POS dan daftar inventori produk.
                                    </div>
                                </div>

                                <!-- List Tingkatan Level -->
                                <div class="space-y-3">
                                    <template x-for="(tier, index) in storeTiers" :key="index">
                                        <div class="p-4 rounded-2xl border transition duration-150 bg-slate-50/70 border-slate-200/90 hover:border-slate-300 relative group">
                                            <div class="flex items-center justify-between gap-3 mb-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-6 h-6 rounded-lg font-black text-xs flex items-center justify-center text-white"
                                                          :class="index === 0 ? 'bg-indigo-600' : (index === 1 ? 'bg-green-600' : (index === 2 ? 'bg-amber-600' : 'bg-slate-700'))"
                                                          x-text="index + 1"></span>
                                                    <span class="font-extrabold text-xs text-slate-800"
                                                          x-text="index === 0 ? 'Level 1 (Harga Dasar / Eceran)' : ('Level ' + (index + 1) + ' (Grosir / Multi-Pack)')"></span>
                                                </div>

                                                <!-- Tombol Hapus Level (Hanya untuk Level > 1) -->
                                                <template x-if="index > 0">
                                                    <button type="button"
                                                            @click="removeTierLevel(index)"
                                                            title="Hapus tingkatan level ini dari semua produk"
                                                            class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 p-1 rounded-lg text-xs font-semibold transition flex items-center gap-1 cursor-pointer">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        <span class="text-[11px]">Hapus</span>
                                                    </button>
                                                </template>
                                            </div>

                                            <input type="hidden" :name="'tiers[' + index + '][tier_level]'" :value="index + 1">

                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                <!-- Nama Level -->
                                                <div class="sm:col-span-2">
                                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">
                                                        Nama Kategori Level *
                                                    </label>
                                                    <input type="text"
                                                           :name="'tiers[' + index + '][name]'"
                                                           x-model="tier.name"
                                                           required
                                                           placeholder="Contoh: Grosir (Lusin)"
                                                           class="w-full text-xs rounded-xl border-slate-300 focus:border-green-500 focus:ring-green-500 bg-white">
                                                </div>

                                                <!-- Isi Satuan Multiplier -->
                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">
                                                        Jumlah Satuan (Pcs) *
                                                    </label>
                                                    <div class="relative">
                                                        <input type="number"
                                                               :name="'tiers[' + index + '][unit_multiplier]'"
                                                               x-model.number="tier.unit_multiplier"
                                                               min="1"
                                                               :readonly="index === 0"
                                                               :class="index === 0 ? 'bg-slate-100 text-slate-500' : 'bg-white'"
                                                               required
                                                               class="w-full text-xs rounded-xl border-slate-300 focus:border-green-500 focus:ring-green-500 pr-10">
                                                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-[10px] text-slate-400 pointer-events-none">
                                                            pcs
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Tombol Tambah Level Baru -->
                                <div class="pt-2">
                                    <button type="button"
                                            @click="addTierLevel()"
                                            class="w-full py-2.5 px-4 rounded-2xl border-2 border-dashed border-slate-300 hover:border-green-500 hover:bg-green-50/50 text-slate-600 hover:text-green-700 text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>+ Tambah Tingkatan Level Baru</span>
                                    </button>
                                </div>

                                <!-- Checkbox Opsi Rekalkulasi Harga -->
                                <div class="pt-3 border-t border-slate-200">
                                    <label class="flex items-start gap-2.5 cursor-pointer select-none">
                                        <input type="checkbox"
                                               name="recalculate_prices"
                                               value="1"
                                               class="w-4 h-4 text-green-600 rounded border-slate-300 focus:ring-green-500 mt-0.5">
                                        <span class="text-xs text-slate-600 leading-snug">
                                            <strong>Sesuaikan harga proporsional</strong> jika jumlah satuan (pcs) diubah (menghitung ulang harga tier berdasarkan perubahan isi satuan).
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="bg-slate-50 px-6 py-4 rounded-b-3xl border-t border-slate-100 flex items-center justify-between">
                                <button type="button"
                                        @click="showTierModal = false"
                                        class="px-4 py-2 rounded-xl border border-slate-300 bg-white text-slate-700 hover:bg-slate-100 font-semibold text-xs transition cursor-pointer">
                                    Batal
                                </button>

                                <button type="submit"
                                        class="px-5 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold text-xs  transition flex items-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Simpan & Terapkan ke Semua Produk</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        <!-- ================= MODAL IMPORT PRODUK EXCEL / CSV ================= -->
        <template x-teleport="body">
            <div x-show="showImportModal"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                <div @click.outside="showImportModal = false"
                     class="bg-white rounded-2xl max-w-lg w-full p-6 border border-slate-200-2xl border border-slate-200 text-slate-800 space-y-4 my-auto">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-base text-slate-900">Import Produk dari Excel / CSV</h3>
                                <p class="text-xs text-slate-500">Unggah banyak produk sekaligus tanpa ribet input satu per satu.</p>
                            </div>
                        </div>
                        <button type="button" @click="showImportModal = false" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center font-bold text-lg transition">&times;</button>
                    </div>

                    <!-- Step 1: Download Template -->
                    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between gap-3">
                        <div>
                            <div class="font-bold text-xs text-emerald-900">Langkah 1: Unduh Format Template</div>
                            <p class="text-[11px] text-emerald-700 mt-0.5">Format file sudah disesuaikan agar langsung bisa dibuka dan diedit di Microsoft Excel.</p>
                        </div>
                        <a href="{{ route('admin.products.template') }}"
                           class="shrink-0 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition ">
                            Unduh Template
                        </a>
                    </div>

                    <!-- Step 2: Upload Form -->
                    <form method="POST" action="{{ route('admin.products.import') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block font-bold text-xs text-slate-800 mb-1">
                                Langkah 2: Pilih File Excel / CSV yang Telah Diisi
                            </label>
                            <input type="file"
                                   name="file"
                                   accept=".csv,text/csv,text/plain"
                                   required
                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-300 rounded-xl p-1.5 bg-slate-50/50">
                            <p class="text-[10px] text-slate-400 mt-1">
                                Format yang didukung: File <strong>.csv</strong> (Maksimal 5MB).
                            </p>
                        </div>

                        <!-- Guidance note -->
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600 space-y-1">
                            <div class="font-bold text-slate-800 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Informasi Penting:</span>
                            </div>
                            <ul class="list-disc pl-4 space-y-0.5 text-slate-500">
                                <li><strong>Gambar produk opsional:</strong> Anda dapat mengunggah ratusan produk tanpa gambar terlebih dahulu, foto dapat dilengkapi kapan saja melalui tombol Edit.</li>
                                <li><strong>SKU Otomatis:</strong> Sistem akan mengkombinasikan nama produk dan nomor urutan secara otomatis.</li>
                                <li><strong>Kategori:</strong> Jika kategori yang Anda tulis di file belum ada, sistem akan otomatis membuatnya untuk Anda.</li>
                            </ul>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                            <button type="button" @click="showImportModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-bold transition ">
                                Mulai Import Produk
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </template>

        <!-- ================= MODAL QUICK RESTOCK (TAMBAH STOK CEPAT) ================= -->
        <template x-teleport="body">
            <div x-show="showRestockModal"
                 x-cloak
                 class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                <div @click.outside="showRestockModal = false"
                     x-show="showRestockModal"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="bg-white rounded-2xl max-w-sm w-full p-5 border border-slate-200-2xl border border-slate-200 space-y-4 my-auto">

                    <!-- Header Modal -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-sm text-slate-900">Tambah Stok Cepat</h3>
                                <p class="text-[11px] text-slate-400">Update unit stok fisik toko langsung</p>
                            </div>
                        </div>
                        <button type="button" @click="showRestockModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                    </div>

                    <!-- Informasi Produk (Read-Only) -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Produk:</span>
                            <div class="text-xs font-bold text-slate-900 truncate" x-text="restockProduct.name"></div>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-slate-200">
                            <div>
                                <span class="text-[10px] text-slate-400 block">SKU:</span>
                                <span class="font-mono text-xs text-slate-700 font-semibold" x-text="restockProduct.sku"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 block">Stok Saat Ini:</span>
                                <span class="text-xs font-black text-slate-900" x-text="restockProduct.stock + ' unit'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Input Jumlah Tambahan Stok -->
                    <form @submit.prevent="submitRestock()" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1">
                                Jumlah Tambahan Stok
                            </label>
                            <input type="number"
                                   min="1"
                                   step="1"
                                   required
                                   x-ref="additionalStockInput"
                                   x-model.number="additionalStock"
                                   class="w-full text-sm font-bold text-slate-900 rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                            <p class="text-[10px] text-slate-400 mt-1">
                                Masukkan jumlah unit barang masuk yang baru diterima.
                            </p>
                        </div>

                        <!-- Cadangan Offline / Safety Stock -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-slate-800">
                                    Cadangan Minimum Toko Fisik (Safety Stock)
                                </label>
                                <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">
                                    Etalase Offline
                                </span>
                            </div>
                            <input type="number"
                                   min="0"
                                   step="1"
                                   x-model.number="safetyStock"
                                   class="w-full text-xs font-semibold text-slate-900 rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                            <p class="text-[10px] text-slate-400 mt-1">
                                Batas cadangan stok yang dipertahankan di toko fisik agar tidak habis terpesan dropshipper online.
                            </p>
                        </div>

                        <!-- Prediksi Stok Baru -->
                        <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-200 text-xs">
                            <div class="text-slate-600 flex items-center justify-between">
                                <span>Prediksi stok baru:</span>
                                <span class="font-bold text-emerald-800">
                                    <span x-text="restockProduct.stock"></span> + 
                                    <span x-text="parseInt(additionalStock || 0, 10)"></span> = 
                                    <span class="text-sm font-black text-emerald-900" x-text="(parseInt(restockProduct.stock || 0, 10) + parseInt(additionalStock || 0, 10)) + ' unit'"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                            <button type="button"
                                    @click="showRestockModal = false"
                                    :disabled="isSubmittingRestock"
                                    class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                                Batal
                            </button>
                            <button type="submit"
                                    :disabled="isSubmittingRestock"
                                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 transition flex items-center gap-1.5 ">
                                <span x-show="!isSubmittingRestock">Simpan Tambah Stok</span>
                                <span x-show="isSubmittingRestock">Menyimpan...</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </template>

        <!-- ================= MODAL RESTOCK MASSAL (BULK RESTOCK) ================= -->
        <template x-teleport="body">
            <div x-show="showBulkRestockModal"
                 x-cloak
                 class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                <div @click.outside="showBulkRestockModal = false"
                     x-show="showBulkRestockModal"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="bg-white rounded-2xl max-w-sm w-full p-5 border border-slate-200-2xl border border-slate-200 space-y-4 my-auto">

                    <!-- Header Modal -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                                <span x-text="selectedIds.length"></span>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-sm text-slate-900">Restock Massal</h3>
                                <p class="text-[11px] text-slate-400">Tambah stok sekaligus untuk produk terpilih</p>
                            </div>
                        </div>
                        <button type="button" @click="showBulkRestockModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                    </div>

                    <!-- Informasi Ringkasan Pilihan -->
                    <div class="p-3.5 rounded-xl bg-indigo-50/70 border border-indigo-200/80 text-xs">
                        <div class="font-bold text-indigo-900 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><span x-text="selectedIds.length"></span> Produk Sedang Dicentang</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Jumlah tambahan di bawah ini akan <strong>ditambahkan ke stok masing-masing produk</strong> yang Anda centang secara serentak.
                        </p>
                    </div>

                    <!-- Form Input Jumlah Tambahan Stok Massal -->
                    <form @submit.prevent="submitBulkRestock()" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1">
                                Jumlah Tambahan per Produk (+ Unit)
                            </label>
                            <input type="number"
                                   min="1"
                                   step="1"
                                   required
                                   x-ref="bulkAdditionalStockInput"
                                   x-model.number="bulkAdditionalStock"
                                   class="w-full text-sm font-bold text-slate-900 rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                            <p class="text-[10px] text-slate-400 mt-1">
                                Contoh: jika diisi <strong>10</strong>, maka seluruh <span x-text="selectedIds.length"></span> produk akan bertambah masing-masing +10 unit.
                            </p>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                            <button type="button"
                                    @click="showBulkRestockModal = false"
                                    :disabled="isSubmittingBulkRestock"
                                    class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                                Batal
                            </button>
                            <button type="submit"
                                    :disabled="isSubmittingBulkRestock"
                                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 transition flex items-center gap-1.5 ">
                                <span x-show="!isSubmittingBulkRestock">Simpan Restock (<span x-text="selectedIds.length"></span>)</span>
                                <span x-show="isSubmittingBulkRestock">Memproses...</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </template>

        <!-- Toast Notifikasi Sukses Restock -->
        <template x-teleport="body">
            <div x-show="restockSuccessMsg"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="fixed bottom-6 right-6 z-[100] bg-slate-900 text-white px-4 py-3 rounded-2xl border border-slate-200-2xl border border-slate-700 flex items-center gap-2.5 text-xs font-medium">
                <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span x-text="restockSuccessMsg"></span>
            </div>
        </template>
    </div>

    @push('scripts')
<script>
function productInventory() {
    return {
        selectedIds: [],
        showImportModal: false,
        showTierModal: false,
        storeTiers: @json($configuredTiers),
        pageProductIds: {!! json_encode($products->pluck('id')) !!},

        addTierLevel() {
            const nextLevel = this.storeTiers.length + 1;
            this.storeTiers.push({
                tier_level: nextLevel,
                name: 'Grosir Level ' + nextLevel,
                unit_multiplier: nextLevel * 12
            });
        },

        removeTierLevel(index) {
            if (this.storeTiers.length <= 1) {
                alert('Minimal harus ada 1 level (Level 1 Eceran).');
                return;
            }
            if (confirm('Hapus tingkatan level ini dari semua produk toko?')) {
                this.storeTiers.splice(index, 1);
                this.storeTiers.forEach((t, idx) => {
                    t.tier_level = idx + 1;
                });
            }
        },
        
        // Quick Restock State
        showRestockModal: false,
        restockProduct: { id: null, name: '', sku: '', stock: 0, safety_stock: 5 },
        additionalStock: 1,
        safetyStock: 5,
        isSubmittingRestock: false,
        restockSuccessMsg: '',
        productStocks: {!! json_encode($products->pluck('stock', 'id')) !!},

        // Bulk Restock State
        showBulkRestockModal: false,
        bulkAdditionalStock: 10,
        isSubmittingBulkRestock: false,

        openRestock(prod) {
            this.restockProduct = {
                id: prod.id,
                name: prod.name,
                sku: prod.sku,
                stock: this.productStocks[prod.id] !== undefined ? this.productStocks[prod.id] : prod.stock,
                safety_stock: prod.safety_stock !== undefined ? prod.safety_stock : 5
            };
            this.additionalStock = 1;
            this.safetyStock = this.restockProduct.safety_stock;
            this.showRestockModal = true;
            this.$nextTick(() => {
                if (this.$refs.additionalStockInput) {
                    this.$refs.additionalStockInput.focus();
                    this.$refs.additionalStockInput.select();
                }
            });
        },

        openBulkRestock() {
            if (this.selectedIds.length === 0) return;
            this.bulkAdditionalStock = 10;
            this.showBulkRestockModal = true;
            this.$nextTick(() => {
                if (this.$refs.bulkAdditionalStockInput) {
                    this.$refs.bulkAdditionalStockInput.focus();
                    this.$refs.bulkAdditionalStockInput.select();
                }
            });
        },

        async submitBulkRestock() {
            const add = parseInt(this.bulkAdditionalStock, 10);
            if (!add || add < 1) {
                alert('Jumlah tambahan stok minimal 1 unit.');
                return;
            }

            this.isSubmittingBulkRestock = true;
            try {
                const res = await fetch("{{ route('admin.products.bulk-restock') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ids: this.selectedIds,
                        additional_stock: add
                    })
                });

                const data = await res.json();
                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'Gagal menambah stok massal.');
                }

                if (data.updated_stocks) {
                    Object.keys(data.updated_stocks).forEach(id => {
                        this.productStocks[id] = data.updated_stocks[id];
                    });
                }

                this.restockSuccessMsg = data.message;
                this.showBulkRestockModal = false;
                this.selectedIds = [];

                setTimeout(() => {
                    this.restockSuccessMsg = '';
                }, 4000);
            } catch (err) {
                alert(err.message);
            } finally {
                this.isSubmittingBulkRestock = false;
            }
        },

        async submitRestock() {
            const add = parseInt(this.additionalStock, 10);
            if (!add || add < 1) {
                alert('Jumlah tambahan stok minimal 1 unit.');
                return;
            }

            this.isSubmittingRestock = true;
            try {
                const url = '/admin/products/' + this.restockProduct.id + '/restock';
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ 
                        additional_stock: add,
                        safety_stock: this.safetyStock
                    })
                });

                const data = await res.json();
                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'Gagal menambah stok.');
                }

                this.productStocks[this.restockProduct.id] = data.new_stock;
                this.restockSuccessMsg = data.message;
                this.showRestockModal = false;

                setTimeout(() => {
                    this.restockSuccessMsg = '';
                }, 4000);
            } catch (err) {
                alert(err.message);
            } finally {
                this.isSubmittingRestock = false;
            }
        },

        toggleSelectAll(e) {
            if (e.target.checked) {
                this.selectedIds = [...this.pageProductIds];
            } else {
                this.selectedIds = [];
            }
        },
        isAllSelected() {
            return this.pageProductIds.length > 0 && this.selectedIds.length === this.pageProductIds.length;
        }
    };
}
</script>
@endpush
</x-app-layout>
