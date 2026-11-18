{{-- Panel Kanan Bersama untuk Halaman Auth Split (Rating & Testimoni Mitra) --}}
<div
    x-data="{
        active: 0,
        items: [
            {
                quote: 'OmniPOS bikin semua pesanan Shopee saya langsung dipacking gudang tanpa perlu nyetok barang. Saldo komisi juga cepat cair ke rekening.',
                name: 'Rina Kartika',
                role: 'Mitra Dropshipper • Bandung',
                initial: 'RK'
            },
            {
                quote: 'Kasir toko offline tetap jalan cepat, stok fisik langsung sinkron ke puluhan dropshipper. Omset toko naik drastis tanpa takut selisih stok.',
                name: 'Hendra Wijaya',
                role: 'Pemilik Toko • Garut',
                initial: 'HW'
            },
            {
                quote: 'Tampilannya simpel dan gampang dipahami pemula. Tinggal pilih produk dari katalog, pasang di TikTok Shop, dan pantau resi otomatis.',
                name: 'Dimas Pratama',
                role: 'Mitra Dropshipper • Surabaya',
                initial: 'DP'
            }
        ]
    }"
    x-init="setInterval(() => active = (active + 1) % items.length, 5000)"
    class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-slate-50 via-emerald-50/40 to-teal-50/50 border-l border-slate-100 flex-col justify-center items-center px-12 xl:px-20 py-12 relative overflow-hidden"
>
    {{-- Rating Summary --}}
    <div class="text-center max-w-md">
        <div class="flex items-center justify-center gap-1 mb-3">
            @for ($i = 0; $i < 5; $i++)
                <div class="w-7 h-7 rounded bg-emerald-600 text-white flex items-center justify-center shadow-2xs">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
            @endfor
        </div>

        <div class="text-sm font-semibold text-slate-800">
            Rating 4.9/5 &nbsp;&bull;&nbsp; 1.200+ Mitra Aktif
        </div>
        <p class="mt-2.5 text-sm text-slate-500 leading-relaxed">
            Platform terpercaya yang menghubungkan kasir toko fisik dan jaringan dropshipper secara otomatis.
        </p>
    </div>

    <div class="w-full max-w-md border-t border-slate-200/80 my-8"></div>

    {{-- Testimonial Card --}}
    <div class="w-full max-w-md bg-white rounded-2xl border border-slate-200/80 shadow-xl shadow-slate-200/40 p-7 transition-all">
        <p class="text-sm text-slate-700 leading-relaxed min-h-[72px]" x-text="items[active].quote"></p>

        <div class="mt-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center shrink-0" x-text="items[active].initial"></div>
                <div>
                    <div class="text-sm font-semibold text-slate-900" x-text="items[active].name"></div>
                    <div class="text-xs text-slate-500" x-text="items[active].role"></div>
                </div>
            </div>

            <div class="flex items-center gap-0.5">
                @for ($i = 0; $i < 5; $i++)
                    <div class="w-4 h-4 rounded-xs bg-emerald-600 text-white flex items-center justify-center">
                        <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- Pagination Dots --}}
    <div class="mt-6 flex items-center gap-2">
        <template x-for="(item, idx) in items" :key="idx">
            <button
                type="button"
                @click="active = idx"
                :class="active === idx ? 'w-6 bg-emerald-600' : 'w-2 bg-slate-300 hover:bg-slate-400'"
                class="h-2 rounded-full transition-all duration-300 cursor-pointer"
                :aria-label="'Testimoni ' + (idx + 1)"
            ></button>
        </template>
    </div>
</div>
