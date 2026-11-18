<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    Tambah Produk Baru
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Toko: <span class="font-semibold text-slate-700">{{ $supplier->store_name }}</span> &bull; Lengkapi detail produk, dimensi, foto dan tingkatan harga jual
                </p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-0" x-data="productForm()">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Section 1: Informasi Utama & SKU -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200  space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-green-900 border-b border-slate-100 pb-2">
                        1. Informasi Utama Produk & SKU
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama Produk -->
                        <div>
                            <x-input-label for="name" :value="__('Nama Produk *')" />
                            <x-text-input id="name"
                                          x-model="name"
                                          class="block mt-1 w-full text-sm"
                                          type="text"
                                          name="name"
                                          :value="old('name')"
                                          required
                                          placeholder="Contoh: Lego Robot Mini Transformer" />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <!-- Kategori Produk (Wajib Ada, Bisa Buat Baru Langsung) -->
                        <div>
                            <div class="flex items-center justify-between">
                                <x-input-label for="category_id" :value="__('Kategori Produk *')" />
                                <button type="button"
                                        @click="showNewCategory = !showNewCategory"
                                        class="text-xs font-semibold text-green-600 hover:text-green-800 focus:outline-none">
                                    <span x-text="showNewCategory ? 'Batal Tambah' : '+ Buat Kategori Baru'"></span>
                                </button>
                            </div>

                            <!-- Dropdown Kategori yang Sudah Ada -->
                            <div x-show="!showNewCategory">
                                <select id="category_id"
                                        x-model="categoryId"
                                        name="category_id"
                                        class="block mt-1 w-full text-sm rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500 bg-white">
                                    <option value="">Pilih Kategori Produk</option>
                                    <template x-for="cat in categoryList" :key="cat.id">
                                        <option :value="cat.id" x-text="cat.name" :selected="cat.id == categoryId"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Input Inline Buat Kategori Baru -->
                            <div x-show="showNewCategory" x-cloak class="mt-1">
                                <div class="flex items-center gap-2">
                                    <input type="text"
                                           x-model="newCategoryName"
                                           name="new_category_name"
                                           placeholder="Ketik nama kategori baru..."
                                           class="text-sm rounded-lg border-green-300 focus:border-green-500 focus:ring-green-500 bg-green-50/30 flex-1">
                                    <button type="button"
                                            @click="saveNewCategory()"
                                            :disabled="!newCategoryName || isSavingCategory"
                                            class="px-3 py-2 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white text-xs font-bold rounded-lg transition whitespace-nowrap">
                                        <span x-show="!isSavingCategory">Simpan</span>
                                        <span x-show="isSavingCategory">Menyimpan...</span>
                                    </button>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1">
                                    Kategori baru akan langsung tersimpan dan otomatis terpilih untuk produk ini.
                                </p>
                            </div>

                            <x-input-error :messages="$errors->get('category_id')" class="mt-1" />
                            <x-input-error :messages="$errors->get('new_category_name')" class="mt-1" />
                        </div>
                    </div>

                    <!-- SKU dengan Auto Generator -->
                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label for="sku" :value="__('Kode SKU (Harus Unik) *')" />
                            <button type="button"
                                    @click="generateSku()"
                                    :disabled="isGeneratingSku"
                                    class="text-xs font-semibold text-green-600 hover:text-green-800 underline focus:outline-none">
                                <span x-show="!isGeneratingSku">Generate SKU Otomatis</span>
                                <span x-show="isGeneratingSku">Membuat SKU...</span>
                            </button>
                        </div>
                        <x-text-input id="sku"
                                      x-model="skuVal"
                                      class="block mt-1 w-full text-sm font-mono uppercase tracking-wider"
                                      type="text"
                                      name="sku"
                                      :value="old('sku')"
                                      required
                                      placeholder="Contoh: TOY-LEG-103" />
                        <x-input-error :messages="$errors->get('sku')" class="mt-1" />
                        <p class="text-[11px] text-slate-400 mt-1">
                            SKU otomatis dikombinasikan dari nama produk, kategori, dan angka urutan unik.
                        </p>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <x-input-label for="description" :value="__('Deskripsi Produk')" />
                        <textarea id="description"
                                  name="description"
                                  rows="3"
                                  class="block mt-1 w-full text-sm rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500"
                                  placeholder="Tuliskan spesifikasi, bahan, dan keunggulan produk...">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>
                </div>

                <!-- Section 2: Dimensi & Berat Pengiriman -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200  space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-green-900 border-b border-slate-100 pb-2">
                        2. Ukuran Dimensi & Berat (Logistik)
                    </h3>
                    <p class="text-xs text-slate-500 -mt-2">
                        Data ini digunakan oleh tim packing dan dropshipper untuk perhitungan ongkos kirim.
                    </p>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <!-- Panjang -->
                        <div>
                            <x-input-label for="length" :value="__('Panjang (cm)')" />
                            <x-text-input id="length" class="block mt-1 w-full text-sm placeholder:text-slate-300 placeholder:font-normal" type="number" step="0.1" name="length" :value="old('length')" placeholder="Contoh: 15" required />
                            <x-input-error :messages="$errors->get('length')" class="mt-1" />
                        </div>

                        <!-- Lebar -->
                        <div>
                            <x-input-label for="width" :value="__('Lebar (cm)')" />
                            <x-text-input id="width" class="block mt-1 w-full text-sm placeholder:text-slate-300 placeholder:font-normal" type="number" step="0.1" name="width" :value="old('width')" placeholder="Contoh: 10" required />
                            <x-input-error :messages="$errors->get('width')" class="mt-1" />
                        </div>

                        <!-- Tinggi -->
                        <div>
                            <x-input-label for="height" :value="__('Tinggi (cm)')" />
                            <x-text-input id="height" class="block mt-1 w-full text-sm placeholder:text-slate-300 placeholder:font-normal" type="number" step="0.1" name="height" :value="old('height')" placeholder="Contoh: 5" required />
                            <x-input-error :messages="$errors->get('height')" class="mt-1" />
                        </div>

                        <!-- Berat -->
                        <div>
                            <x-input-label for="weight" :value="__('Berat (gram)')" />
                            <x-text-input id="weight" class="block mt-1 w-full text-sm placeholder:text-slate-300 placeholder:font-normal" type="number" step="1" name="weight" :value="old('weight')" placeholder="Contoh: 250" required />
                            <x-input-error :messages="$errors->get('weight')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <!-- Section 3: Modal, Stok, & Tingkatan Harga (Multi-Level Grosir/Eceran) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200  space-y-6">
                    <div class="border-b border-slate-100 pb-2">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-green-900">
                            3. Modal, Stok, & Tingkatan Harga Grosir / Eceran (Multi-Level)
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Sistem ritel multi-level: Dukung penjualan eceran per satuan, grosir lusin, hingga grosir partai karton secara fleksibel.
                        </p>
                    </div>

                    <!-- Baris 1: HPP Modal, Modal Dropship, dan Total Stok Gudang -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- HPP Modal -->
                        <div>
                            <x-input-label for="cost_price" :value="__('HPP Modal Toko (Rp) *')" />
                            <input type="hidden" name="cost_price" :value="costPrice">
                            <input type="text"
                                   id="cost_price"
                                   :value="formatThousand(costPrice)"
                                   @input="costPrice = parseThousand($event.target.value)"
                                   class="block mt-1 w-full text-sm font-semibold rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500 placeholder:text-slate-300 placeholder:font-normal"
                                   required
                                   placeholder="Contoh: 25.000" />
                            <x-input-error :messages="$errors->get('cost_price')" class="mt-1" />
                            <span class="text-[10px] text-slate-400">Dasar hitung laba bersih toko</span>
                        </div>

                        <!-- Modal Dropship (Harga Modal Mitra) -->
                        <div>
                            <x-input-label for="dropship_price" :value="__('Modal Dropship (Rp) *')" />
                            <input type="hidden" name="dropship_price" :value="dropshipPrice">
                            <input type="text"
                                   id="dropship_price"
                                   :value="formatThousand(dropshipPrice)"
                                   @input="dropshipPrice = parseThousand($event.target.value)"
                                   class="block mt-1 w-full text-sm font-bold text-emerald-700 rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500 placeholder:text-slate-300 placeholder:font-normal"
                                   required
                                   placeholder="Contoh: 38.000" />
                            <x-input-error :messages="$errors->get('dropship_price')" class="mt-1" />
                            <span class="text-[10px] text-slate-400">Harga modal saldo dropshipper</span>
                        </div>

                        <!-- Total Stok Fisik Toko -->
                        <div>
                            <x-input-label for="stock" :value="__('Jumlah Total Stok Fisik Toko *')" />
                            <x-text-input id="stock" class="block mt-1 w-full text-sm font-bold placeholder:text-slate-300 placeholder:font-normal" type="number" min="0" name="stock" :value="old('stock')" placeholder="Contoh: 100" required />
                            <x-input-error :messages="$errors->get('stock')" class="mt-1" />
                            <span class="text-[10px] text-slate-400">Total unit fisik master di gudang</span>
                        </div>
                    </div>

                    <!-- Baris 2: Multi-Level Form (Side-by-Side dengan Tombol Tambah Level) -->
                    <div class="pt-4 border-t border-slate-100">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-xs uppercase tracking-wider text-slate-800">Form Tingkatan Harga & Satuan Jual (Side by Side)</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-green-100 text-green-800" x-text="tiers.length + ' Level Aktif'"></span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Form bertambah ke samping secara side-by-side dari Level 1 (kiri) ke Level berikutnya (kanan).
                                </p>
                            </div>

                            <!-- Tombol Tambah Level -->
                            <button type="button"
                                    @click="addTier()"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white text-xs font-bold rounded-xl transition  shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>+ Tambah Level</span>
                            </button>
                        </div>

                        <!-- Hidden Inputs for Form Submission -->
                        <template x-for="(tier, idx) in tiers" :key="'input-' + idx">
                            <div>
                                <input type="hidden" :name="'tiers[' + idx + '][level]'" :value="idx + 1">
                                <input type="hidden" :name="'tiers[' + idx + '][name]'" :value="tier.name">
                                <input type="hidden" :name="'tiers[' + idx + '][multiplier]'" :value="tier.multiplier">
                                <input type="hidden" :name="'tiers[' + idx + '][price]'" :value="tier.price">
                            </div>
                        </template>
                        <input type="hidden" name="selling_price" :value="tiers[0] ? tiers[0].price : ''">

                        <!-- Container SIDE BY SIDE (Scroll Horizontal halus jika melebihi lebar layar) -->
                        <div class="flex items-stretch gap-4 overflow-x-auto pb-4 pt-1 no-scrollbar sm:scroll-smooth">
                            <template x-for="(tier, idx) in tiers" :key="idx">
                                <div class="min-w-[270px] sm:min-w-[300px] flex-1 max-w-[340px] bg-slate-50/80 rounded-2xl border-2 transition duration-200 p-4 flex flex-col justify-between"
                                     :class="idx === 0 ? 'border-green-500/80 bg-green-50/20 ' : 'border-slate-200 hover:border-slate-300'">
                                    
                                    <div>
                                        <!-- Card Top Header -->
                                        <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200">
                                            <div class="flex items-center gap-2">
                                                <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-black"
                                                      :class="idx === 0 ? 'bg-green-600 text-white' : 'bg-slate-700 text-white'"
                                                      x-text="idx + 1"></span>
                                                <span class="font-extrabold text-xs text-slate-800" x-text="'Level ' + (idx + 1)"></span>
                                                <span x-show="idx === 0" class="text-[9.5px] px-1.5 py-0.5 rounded bg-green-100 text-green-700 font-bold">Dasar / POS</span>
                                            </div>

                                            <!-- Tombol Hapus Level (Hanya jika > Level 1) -->
                                            <button type="button"
                                                    x-show="tiers.length > 1 && idx > 0"
                                                    @click="removeTier(idx)"
                                                    title="Hapus Level Ini"
                                                    class="text-slate-400 hover:text-rose-600 p-1 rounded-lg hover:bg-rose-50 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Input 1: Nama Level (Custom) -->
                                        <div class="space-y-1 mb-3">
                                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">
                                                Nama Level / Satuan
                                            </label>
                                            <input type="text"
                                                   x-model="tier.name"
                                                   placeholder="Misal: Eceran, Lusin, Karton..."
                                                   class="w-full text-xs font-bold rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500 bg-white">
                                        </div>

                                        <!-- Input 2: Konversi Stok / Pengali Unit (Isi Pcs) -->
                                        <div class="space-y-1 mb-3">
                                            <div class="flex items-center justify-between">
                                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">
                                                    Isi Satuan (Potong Stok)
                                                </label>
                                                <span class="text-[10px] text-slate-500 font-medium" x-text="tier.multiplier + ' Pcs'"></span>
                                            </div>
                                            <div class="relative">
                                                <input type="number"
                                                       min="1"
                                                       x-model.number="tier.multiplier"
                                                       placeholder="1"
                                                       class="w-full text-xs font-bold pr-12 rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500 bg-white">
                                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400 pointer-events-none">Pcs</span>
                                            </div>
                                            <p class="text-[9.5px] text-slate-400 leading-tight">
                                                Tiap 1 <span class="font-semibold text-slate-600" x-text="tier.name || 'unit'"></span> memotong <span class="font-semibold text-slate-600" x-text="tier.multiplier"></span> stok fisik toko.
                                            </p>
                                        </div>

                                        <!-- Input 3: Harga Jual POS untuk Level Ini -->
                                        <div class="space-y-1 mb-2">
                                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600">
                                                Harga Jual Level (Rp)
                                            </label>
                                            <input type="text"
                                                   :value="formatThousand(tier.price)"
                                                   @input="tier.price = parseThousand($event.target.value); if (idx === 0) sellingPrice = tier.price;"
                                                   placeholder="Rp 0"
                                                   class="w-full text-sm font-black text-green-700 rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500 bg-white">
                                        </div>
                                    </div>

                                    <!-- Info Perhitungan Harga Rata-Rata per Pcs -->
                                    <div class="mt-2 pt-2 border-t border-slate-200 bg-white/70 p-2 rounded-xl text-[10.5px] flex items-center justify-between">
                                        <span class="text-slate-500">Harga per pcs:</span>
                                        <span class="font-extrabold text-slate-800">
                                            Rp <span x-text="formatThousand(Math.round((tier.price || 0) / (tier.multiplier || 1)))"></span><span class="text-[9.5px] font-normal text-slate-400">/pcs</span>
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Foto Produk -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200  space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-green-900 border-b border-slate-100 pb-2">
                        4. Foto Sampul Produk
                    </h3>

                    <div>
                        <input type="file"
                               name="image"
                               accept="image/jpeg,image/png,image/jpg,image/webp"
                               @change="previewImage($event)"
                               class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer" />
                        <x-input-error :messages="$errors->get('image')" class="mt-1" />

                        <!-- Preview Container -->
                        <div x-show="imageUrl" class="mt-3">
                            <span class="text-[11px] text-slate-500 block mb-1">Pratinjau Foto:</span>
                            <img :src="imageUrl" class="w-32 h-32 rounded-xl object-cover border border-slate-200 ">
                        </div>
                    </div>
                </div>

                <!-- Section 5: Bank Materi Konten & Foto HD (Google Photos / Drive Link) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200  space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 border border-sky-200 flex items-center justify-center font-bold text-xs">
                                ☁️
                            </span>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800">
                                5. Link Google Photos / Google Drive (Materi Promosi)
                            </h3>
                        </div>
                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Hemat Memori Server
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            URL Tautan Album Google Photos atau Folder Google Drive
                        </label>
                        <input type="url"
                               name="marketing_assets_url"
                               value="{{ old('marketing_assets_url') }}"
                               placeholder="Contoh: https://photos.app.goo.gl/... atau https://drive.google.com/drive/folders/..."
                               class="w-full text-xs rounded-xl border-slate-300 focus:border-green-500 focus:ring-green-500 bg-slate-50/50" />
                        <x-input-error :messages="$errors->get('marketing_assets_url')" class="mt-1" />
                        <p class="text-[11px] text-slate-500 mt-1.5 leading-relaxed">
                            💡 <strong>Tips:</strong> Simpan foto resolusi tinggi (HD) dan video unboxing produk di Google Photos / Drive supplier. Dropshipper dapat langsung mengunduh aset tersebut melalui tombol khusus di katalog mereka.
                        </p>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-bold text-sm rounded-xl  transition">
                        Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function productForm() {
            return {
                name: @json(old('name', '')),
                categoryId: @json(old('category_id', '')),
                categoryList: @json($categories),
                showNewCategory: false,
                newCategoryName: '',
                isSavingCategory: false,
                skuVal: @json(old('sku', '')),
                costPrice: @json(old('cost_price', '')),
                sellingPrice: @json(old('selling_price', '')),
                dropshipPrice: @json(old('dropship_price', '')),
                isGeneratingSku: false,
                imageUrl: null,

                // Inisialisasi 3 Level Default (Eceran, Grosir, Grosir Besar)
                tiers: [
                    { name: 'Eceran', multiplier: 1, price: {{ old('selling_price') ? old('selling_price') : 50000 }} },
                    { name: 'Grosir', multiplier: 12, price: {{ old('selling_price') ? old('selling_price') * 12 * 0.9 : 500000 }} },
                    { name: 'Grosir Besar', multiplier: 72, price: {{ old('selling_price') ? old('selling_price') * 72 * 0.8 : 2500000 }} }
                ],

                addTier() {
                    const nextLevel = this.tiers.length + 1;
                    let defaultName = 'Level ' + nextLevel;
                    let defaultMultiplier = 1;
                    let defaultPrice = 0;

                    if (nextLevel === 2) {
                        defaultName = 'Grosir';
                        defaultMultiplier = 12;
                        defaultPrice = (this.tiers[0]?.price || 50000) * 12 * 0.9;
                    } else if (nextLevel === 3) {
                        defaultName = 'Grosir Besar';
                        defaultMultiplier = 72;
                        defaultPrice = (this.tiers[0]?.price || 50000) * 72 * 0.8;
                    } else if (nextLevel === 4) {
                        defaultName = 'Grosir Karton';
                        defaultMultiplier = 144;
                        defaultPrice = (this.tiers[0]?.price || 50000) * 144 * 0.75;
                    } else {
                        defaultMultiplier = (this.tiers[this.tiers.length - 1]?.multiplier || 1) * 2;
                        defaultPrice = (this.tiers[this.tiers.length - 1]?.price || 0) * 1.8;
                    }

                    this.tiers.push({
                        name: defaultName,
                        multiplier: defaultMultiplier,
                        price: Math.round(defaultPrice)
                    });
                },

                removeTier(index) {
                    if (this.tiers.length > 1) {
                        this.tiers.splice(index, 1);
                    }
                },

                async saveNewCategory() {
                    if (!this.newCategoryName.trim()) return;
                    this.isSavingCategory = true;
                    try {
                        const res = await fetch("{{ route('admin.categories.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ name: this.newCategoryName })
                        });
                        const data = await res.json();
                        if (!res.ok || !data.success) {
                            throw new Error(data.message || 'Gagal membuat kategori.');
                        }
                        this.categoryList.push(data.category);
                        this.categoryId = data.category.id;
                        this.newCategoryName = '';
                        this.showNewCategory = false;
                        if (this.name) {
                            this.generateSku();
                        }
                    } catch (err) {
                        alert(err.message);
                    } finally {
                        this.isSavingCategory = false;
                    }
                },

                formatThousand(value) {
                    if (value === null || value === undefined || value === '') return '';
                    const num = parseInt(String(value).replace(/\D/g, ''), 10);
                    if (isNaN(num)) return '';
                    return new Intl.NumberFormat('id-ID').format(num);
                },

                parseThousand(val) {
                    if (!val) return 0;
                    const clean = String(val).replace(/\D/g, '');
                    return parseInt(clean, 10) || 0;
                },

                async generateSku() {
                    this.isGeneratingSku = true;
                    try {
                        const url = new URL("{{ route('admin.products.generate-sku') }}", window.location.origin);
                        url.searchParams.set('name', this.name);
                        if (this.categoryId) {
                            url.searchParams.set('category_id', this.categoryId);
                        }

                        const res = await fetch(url.toString(), {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        if (data.sku) {
                            this.skuVal = data.sku;
                        }
                    } catch (e) {
                        console.error('Gagal generate SKU:', e);
                    } finally {
                        this.isGeneratingSku = false;
                    }
                },

                previewImage(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.imageUrl = URL.createObjectURL(file);
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>