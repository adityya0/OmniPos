<x-app-layout title="Katalog Produk">
    <x-slot name="topbar">
        <div class="flex items-center justify-between gap-4 w-full pr-2">
            <h2 class="font-bold text-base sm:text-lg text-slate-900 truncate">
                Katalog Produk
            </h2>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('dropshipper.suppliers.index') }}" class="px-3.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition">
                    Pilih Penyuplai ({{ $connectedCount }}/5)
                </a>
                <a href="{{ route('dropshipper.orders.index') }}" class="px-3.5 py-1.5 rounded-full bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-700 hover:border-emerald-300 transition">
                    Pesanan Saya
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-0" x-data="{
        orderModalOpen: false,
        selectedProduct: { id: '', name: '', sku: '', stock: 0, price: 0, dropshipPrice: 0, store: '' },
        orderQty: 1,
        openOrder(prod) {
            this.selectedProduct = prod;
            this.orderQty = 1;
            this.orderModalOpen = true;
        }
    }">
        <div class="w-full p-5 sm:p-6 space-y-5 bg-[#F8FAFC] min-h-full">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2.5 ">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2.5 ">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Filter & Search Bar --}}
            <div class="bg-white p-4 rounded-2xl border border-slate-200">
                <form method="GET" action="{{ route('dropshipper.catalog.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-5 relative">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari nama produk atau SKU..."
                               class="w-full text-xs rounded-full px-4 border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50/50 placeholder-slate-400">
                    </div>

                    <div class="sm:col-span-3">
                        <select name="supplier_id" class="w-full text-xs rounded-full px-4 border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                            <option value="">Semua Toko Penyuplai</option>
                            @foreach ($suppliers as $sup)
                                <option value="{{ $sup->id }}" @selected(request('supplier_id') == $sup->id)>
                                    {{ $sup->store_name }} ({{ $sup->products_count }} item)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-3">
                        <select name="category_id" class="w-full text-xs rounded-full px-4 border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-1 flex items-center gap-2">
                        <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-full transition cursor-pointer">
                            Cari
                        </button>
                    </div>
                </form>
            </div>

            {{-- Product Cards Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 keep-card-gap">
                @forelse ($products as $product)
                    @php
                        $potentialProfit = max(0, $product->selling_price - $product->dropship_price);
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-200  hover:border-slate-300 transition overflow-hidden flex flex-col justify-between group">
                        <div>
                            {{-- Thumbnail & Live Stock Badge --}}
                            <div class="aspect-square w-full bg-slate-50 relative overflow-hidden">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">

                                {{-- Real-time Stock Pill --}}
                                <div class="absolute top-2.5 right-2.5">
                                    <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-lg  {{ $product->stock > 5 ? 'bg-emerald-600 text-white' : ($product->stock > 0 ? 'bg-amber-500 text-white' : 'bg-rose-600 text-white') }}">
                                        Stok: {{ $product->stock }}
                                    </span>
                                </div>

                                {{-- Supplier Store Chip --}}
                                <div class="absolute bottom-2.5 left-2.5">
                                    <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-900/80 text-white backdrop-blur-xs">
                                        {{ $product->supplier?->store_name }}
                                    </span>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-4 space-y-2">
                                <div class="flex items-center justify-between text-[10px] font-mono text-slate-400">
                                    <span>{{ $product->sku }}</span>
                                    <span>{{ $product->category?->name }}</span>
                                </div>

                                <h4 class="font-semibold text-slate-900 text-sm leading-snug line-clamp-2" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </h4>

                                {{-- Dimensi & Berat --}}
                                <div class="p-2 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-500 flex justify-between">
                                    <span>Dimensi: {{ $product->length }}&times;{{ $product->width }}&times;{{ $product->height }} cm</span>
                                    <span>Berat: {{ $product->weight }} gr</span>
                                </div>

                                {{-- Price Structure --}}
                                <div class="pt-2 border-t border-slate-100">
                                    <div class="flex items-baseline justify-between">
                                        <span class="text-xs text-slate-500 font-medium">Harga Dropship:</span>
                                        <span class="text-sm font-semibold text-slate-900">
                                            Rp {{ number_format($product->dropship_price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-slate-500 mt-0.5">
                                        <span>Rekomendasi POS:</span>
                                        <span class="line-through">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs font-semibold text-green-700 bg-green-50 px-2 py-1 rounded-lg mt-1.5 border border-green-200/60">
                                        <span>Potensi Margin:</span>
                                        <span>+Rp {{ number_format($potentialProfit, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card Actions --}}
                        <div class="p-4 pt-0 space-y-2">
                            @if (!empty($product->marketing_assets_url))
                                <a href="{{ $product->marketing_assets_url }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="w-full py-2 px-3 rounded-xl border border-sky-200 bg-sky-50/60 hover:bg-sky-100 text-sky-700 font-bold text-xs flex items-center justify-center gap-1.5 transition">
                                    <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>Aset Konten (Google Photos/Drive)</span>
                                </a>
                            @endif

                            @if ($product->stock > 0)
                                <button type="button"
                                        @click="openOrder({
                                            id: '{{ $product->id }}',
                                            name: '{{ addslashes($product->name) }}',
                                            sku: '{{ $product->sku }}',
                                            stock: {{ $product->stock }},
                                            price: {{ $product->dropship_price }},
                                            store: '{{ addslashes($product->supplier?->store_name ?? 'Toko') }}'
                                        })"
                                        class="w-full py-2.5 rounded-xl font-bold text-xs bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] text-white  transition flex items-center justify-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span>Buat Pesanan Cepat</span>
                                </button>
                            @else
                                <button type="button" disabled class="w-full py-2.5 rounded-xl font-bold text-xs bg-slate-100 text-slate-400 cursor-not-allowed text-center">
                                    Stok Habis
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white p-12 text-center rounded-3xl border border-slate-200 text-slate-400">
                        <p class="text-xs font-semibold text-slate-600">Belum ada produk aktif yang tersedia di katalog saat ini.</p>
                    </div>
                @endforelse
            </div>

            @if ($products->hasPages())
                <div class="p-4 bg-white rounded-2xl border border-slate-200">
                    {{ $products->links() }}
                </div>
            @endif

        </div>

        {{-- MODAL POP-UP PESANAN DROPSHIP (MODERN OUTLINE 3-STEP) --}}
        <template x-teleport="body">
            <div x-show="orderModalOpen" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
                
                <div @click.outside="orderModalOpen = false"
                     class="bg-white rounded-3xl max-w-lg w-full border border-slate-200-2xl border border-slate-200 flex flex-col max-h-[92vh] overflow-hidden my-auto">
                    
                    {{-- Header Modal (Sticky Top) --}}
                    <div class="p-4 sm:p-5 border-b border-slate-100 bg-white shrink-0 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center font-semibold">
                                📦
                            </div>
                            <div>
                                <h3 class="font-semibold text-sm sm:text-base text-slate-900">Buat Pesanan Dropship</h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Pemasok: <strong class="text-emerald-700 font-semibold" x-text="selectedProduct.store"></strong>
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="orderModalOpen = false" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center font-bold text-lg transition cursor-pointer">
                            &times;
                        </button>
                    </div>

                    {{-- Form Body (Scrollable Inside) --}}
                    <form method="POST" action="{{ route('dropshipper.orders.store') }}" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
                        @csrf
                        <input type="hidden" name="product_id" :value="selectedProduct.id">

                        <div class="p-5 overflow-y-auto space-y-4 flex-1">

                            {{-- Step 1: Produk & Jumlah --}}
                            <div class="p-3.5 bg-slate-50/80 border border-slate-200 rounded-2xl flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Langkah 1: Produk Terpilih</span>
                                    <h4 class="font-semibold text-xs sm:text-sm text-slate-900 truncate mt-0.5" x-text="selectedProduct.name"></h4>
                                    <div class="text-xs text-slate-500 font-mono" x-text="'SKU: #' + selectedProduct.sku + ' • Sisa Stok: ' + selectedProduct.stock + ' unit'"></div>
                                    <div class="text-xs font-semibold text-emerald-700 mt-0.5" x-text="'Rp ' + Number(selectedProduct.price).toLocaleString('id-ID') + ' / unit'"></div>
                                </div>
                                
                                {{-- Stepper Qty --}}
                                <div class="flex items-center gap-1.5 shrink-0 bg-white p-1 rounded-xl border border-slate-200 ">
                                    <button type="button" @click="if(orderQty > 1) orderQty--"
                                            class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm flex items-center justify-center transition cursor-pointer">
                                        -
                                    </button>
                                    <input type="number" name="quantity" x-model.number="orderQty" min="1" :max="selectedProduct.stock" required
                                           class="w-12 text-center font-semibold text-sm border-0 p-0 focus:ring-0 text-slate-900">
                                    <button type="button" @click="if(orderQty < selectedProduct.stock) orderQty++"
                                            class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm flex items-center justify-center transition cursor-pointer">
                                        +
                                    </button>
                                </div>
                            </div>

                            {{-- Step 2: Resi & Kurir Marketplace (Wajib) --}}
                            <div class="p-4 bg-white border border-slate-200 rounded-2xl space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-5 h-5 rounded-md bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold">2</span>
                                        <span class="text-xs font-bold text-slate-800">Kurir &amp; Resi Marketplace</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">Wajib Resi</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Pilih Kurir *</label>
                                        <select name="courier_name" required class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50/50">
                                            <option value="J&T Express">J&amp;T Express</option>
                                            <option value="Shopee Xpress (SPX)">Shopee Xpress (SPX)</option>
                                            <option value="TikTok Shop Kurir">TikTok Shop Kurir</option>
                                            <option value="SiCepat">SiCepat Ekspres</option>
                                            <option value="JNE Express">JNE Express</option>
                                            <option value="GoSend / Grab">Instant / Sameday</option>
                                            <option value="Lainnya">Ekspedisi Lainnya</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Nomor Resi Marketplace *</label>
                                        <input type="text" name="shipping_receipt_number" required placeholder="Contoh: JP28391029302"
                                               class="w-full text-xs font-mono font-bold rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50/50 placeholder-slate-400">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Upload Label Resi (PDF / Gambar - Opsional)</label>
                                    <input type="file" name="shipping_label_file" accept=".pdf,.jpg,.jpeg,.png,.webp"
                                           class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 transition cursor-pointer border border-slate-200 rounded-xl p-1 bg-slate-50/30">
                                </div>
                            </div>

                            {{-- Step 3: Data Pembeli & Catatan --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pembeli *</label>
                                    <input type="text" name="customer_name" required placeholder="Nama pembeli di marketplace"
                                           class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-white placeholder-slate-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">No. HP Pembeli (Opsional)</label>
                                    <input type="text" name="customer_phone" placeholder="08xxxxxxxxxx"
                                           class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-white placeholder-slate-400">
                                </div>
                            </div>

                            {{-- Catatan Tambahan --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                                <input type="text" name="notes" placeholder="Contoh: Tolong packing bubble wrap tebal..."
                                       class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-white placeholder-slate-400">
                            </div>

                        </div>

                        {{-- Footer Modal (Sticky Bottom) --}}
                        <div class="p-4 sm:p-5 border-t border-slate-100 bg-white shrink-0 flex items-center justify-between gap-3">
                            <div>
                                <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Tagihan:</div>
                                <div class="text-base sm:text-lg font-bold text-slate-900" x-text="'Rp ' + (selectedProduct.price * (orderQty || 1)).toLocaleString('id-ID')"></div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" @click="orderModalOpen = false"
                                        class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-95  transition flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    <span>Kirim Pesanan ke Pemasok</span>
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </template>

    </div>
</x-app-layout>
