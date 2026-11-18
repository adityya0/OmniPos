<x-app-layout title="Pilih Penyuplai">
    <x-slot name="topbar">
        <div class="flex items-center justify-between gap-4 w-full pr-2">
            <div class="flex items-center gap-2.5 min-w-0">
                <h2 class="font-bold text-base sm:text-lg text-slate-900 truncate">
                    Pilih Penyuplai
                </h2>
                @if ($connectedCount > 0)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                        {{ $connectedCount }} Terhubung
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('dropshipper.profile') }}" class="px-3.5 py-1.5 rounded-full bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-700 hover:border-emerald-300 transition flex items-center gap-1.5">
                    <span>Profil & Ketentuan</span>
                </a>
                <a href="{{ route('dropshipper.catalog.index') }}" class="px-3.5 py-1.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition flex items-center gap-1.5">
                    <span>Katalog</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-0">
        <div class="w-full p-5 sm:p-6 space-y-6 bg-[#F8FAFC] min-h-full">

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


            {{-- Toko yang Sedang Terhubung --}}
            @if ($connectedCount > 0)
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-sm text-slate-800 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            <span>Toko Terhubung ({{ $connectedCount }})</span>
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 keep-card-gap">
                        @foreach ($connectedSuppliers as $cs)
                            @php
                                $el = $eligibilityMap[$cs->id] ?? null;
                                $isPending = $el && $el['is_pending_approval'];
                            @endphp
                            <div x-data="{ openDisconnectModal: false, openDetailModal: false }" class="bg-white rounded-2xl border-2 {{ $isPending ? 'border-amber-300 bg-amber-50/15' : 'border-green-200' }} p-5  flex flex-col justify-between hover:border-slate-300 transition">
                                <div>
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-12 h-12 rounded-2xl {{ $isPending ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-green-50 text-green-700 border-green-200' }} font-black text-base flex items-center justify-center shrink-0 border">
                                                {{ mb_substr($cs->store_name, 0, 2) }}
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-black text-sm text-slate-900 truncate" title="{{ $cs->store_name }}">{{ $cs->store_name }}</h4>
                                                <div class="text-[11px] text-green-700 font-semibold flex items-center gap-1 mt-0.5">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    <span class="truncate">{{ $cs->shipping_origin_city ?: ($cs->warehouse_address ?: 'Gudang Pusat') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($isPending)
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300 shrink-0">
                                                MENUNGGU LEPAS
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-green-100 text-green-800 border border-green-300 shrink-0">
                                                TERHUBUNG
                                            </span>
                                        @endif
                                    </div>

                                    @if ($isPending)
                                        <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
                                            <div class="font-bold flex items-center gap-1.5 text-amber-800">
                                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>Pengajuan Lepas Terkirim</span>
                                            </div>
                                            <p class="text-[11px] text-amber-700 leading-relaxed">
                                                Diajukan pada {{ $el['relation']?->disconnect_requested_at?->translatedFormat('d M Y H:i') ?? '-' }}. Menunggu persetujuan dari pihak toko.
                                            </p>
                                        </div>
                                    @endif

                                    {{-- Transparansi Info Grid --}}
                                    <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                                        <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                                            <div class="text-[10px] text-slate-400 font-bold uppercase">Jam Kerja & Cut-Off</div>
                                            <div class="font-bold text-slate-800 text-[11px] mt-0.5 truncate">{{ $cs->cut_off_time ?: '15:00 WIB' }}</div>
                                            <div class="text-[10px] text-slate-500 truncate">{{ $cs->operating_hours ?: '08:00 - 17:00' }}</div>
                                        </div>

                                        <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                                            <div class="text-[10px] text-slate-400 font-bold uppercase">SLA & Stok</div>
                                            <div class="font-bold text-emerald-700 text-[11px] mt-0.5">{{ $cs->packing_sla ?: '< 24 Jam' }}</div>
                                            <div class="text-[10px] text-slate-500">{{ $cs->products_count }} Produk Siap Jual</div>
                                        </div>
                                    </div>

                                    {{-- Kurir Badge Singkat --}}
                                    <div class="mt-2.5 flex items-center gap-1.5 flex-wrap">
                                        <span class="text-[10px] text-slate-400 font-bold">Kurir:</span>
                                        @php
                                            $couriersList = explode(',', $cs->supported_couriers ?: 'J&T, JNE, SiCepat, SPX, GoSend');
                                        @endphp
                                        @foreach (array_slice($couriersList, 0, 4) as $cour)
                                            <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-semibold">
                                                {{ trim($cour) }}
                                            </span>
                                        @endforeach
                                        @if (count($couriersList) > 4)
                                            <span class="text-[10px] text-slate-400 font-bold">+{{ count($couriersList) - 4 }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-5 pt-3 border-t border-slate-100 flex flex-col gap-2">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('dropshipper.catalog.index', ['supplier_id' => $cs->id]) }}"
                                           class="flex-1 py-2 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-bold text-center transition">
                                            Lihat Produk
                                        </a>

                                        <button type="button" @click="openDetailModal = true"
                                                class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold border border-slate-200 transition" title="Lihat Detail Transparansi Toko">
                                            Detail Toko
                                        </button>

                                        @if ($isPending)
                                            <form method="POST" action="{{ route('dropshipper.suppliers.cancel-disconnect', $cs) }}" onsubmit="return confirm('Batalkan pengajuan pelepasan kemitraan dengan {{ $cs->store_name }}?');">
                                                @csrf
                                                <button type="submit" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold border border-slate-200 transition" title="Batalkan Pengajuan">
                                                    Batal Lepas
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" @click="openDisconnectModal = true" class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition" title="Lepas Kemitraan">
                                                Lepas
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- MODAL DETAIL & TRANSPARANSI TOKO --}}
                                <div x-show="openDetailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                                    <div @click.outside="openDetailModal = false" class="bg-white rounded-3xl max-w-xl w-full p-6 border border-slate-200-2xl border border-slate-200 space-y-4 max-h-[90vh] overflow-y-auto">
                                        
                                        <div class="flex items-start justify-between border-b border-slate-100 pb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-700 font-black text-lg flex items-center justify-center border border-green-200 shrink-0">
                                                    {{ mb_substr($cs->store_name, 0, 2) }}
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <h3 class="font-black text-base text-slate-900">{{ $cs->store_name }}</h3>
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-green-100 text-green-800 border border-green-200">
                                                            ✓ Verified Supplier
                                                        </span>
                                                    </div>
                                                    <p class="text-xs text-slate-400 mt-0.5">PIC Toko: {{ $cs->pic_name ?: ($cs->owner?->name ?? 'Admin Fulfillment') }}</p>
                                                </div>
                                            </div>
                                            <button type="button" @click="openDetailModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>

                                        {{-- Informasi Alamat Asal & Petunjuk Ongkir --}}
                                        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 space-y-2">
                                            <div class="flex items-center gap-2 text-emerald-900 font-extrabold text-xs">
                                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span>Titik Asal Pengiriman Gudang (Penting untuk Marketplace)</span>
                                            </div>
                                            <div class="font-black text-sm text-slate-900">
                                                {{ $cs->shipping_origin_city ?: ($cs->warehouse_address ?: 'Kab. Garut, Jawa Barat') }}
                                            </div>
                                            <div class="text-[11px] text-slate-600">
                                                Alamat Gudang: {{ $cs->warehouse_address ?: 'Gudang Pusat Suplai' }}
                                            </div>
                                            <p class="text-[11px] text-emerald-800 leading-relaxed pt-1 border-t border-emerald-200/60">
                                                💡 <strong>Panduan Reseller:</strong> Atur kota/kabupaten asal di atas sebagai <em>Alamat Pengembalian / Titik Penjemputan (Pickup Address)</em> toko Shopee/Tokopedia/TikTok Shop Anda agar tarif ongkir pembeli dihitung akurat dan bebas selisih ongkir.
                                            </p>
                                        </div>

                                        {{-- Detail Operasional & Ekspedisi --}}
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                                                <span class="text-slate-400 font-bold uppercase text-[10px]">Jam Operasional Gudang</span>
                                                <div class="font-bold text-slate-800">{{ $cs->operating_hours ?: 'Senin - Sabtu: 08:00 - 17:00 WIB' }}</div>
                                                <div class="text-[10px] text-slate-500">Minggu / Libur Nasional: Off Pengiriman</div>
                                            </div>

                                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                                                <span class="text-slate-400 font-bold uppercase text-[10px]">Batas Waktu Pengiriman (Cut-Off)</span>
                                                <div class="font-black text-indigo-700">{{ $cs->cut_off_time ?: '15:00 WIB' }}</div>
                                                <div class="text-[10px] text-slate-500">Order resi sebelum jam ini dikemas hari ini</div>
                                            </div>

                                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                                                <span class="text-slate-400 font-bold uppercase text-[10px]">Kecepatan Kemas (SLA)</span>
                                                <div class="font-bold text-emerald-700">{{ $cs->packing_sla ?: '< 24 Jam Kerja' }}</div>
                                                <div class="text-[10px] text-slate-500">Rata-rata waktu proses fulfillment pesanan</div>
                                            </div>

                                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                                                <span class="text-slate-400 font-bold uppercase text-[10px]">Standar Pengemasan</span>
                                                <div class="font-semibold text-slate-800">{{ $cs->packing_standard ?: 'Bubble Wrap Tebal + Dus Pelindung + Fragile' }}</div>
                                            </div>
                                        </div>

                                        {{-- Ekspedisi Kurir Didukung --}}
                                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs space-y-1.5">
                                            <span class="text-slate-400 font-bold uppercase text-[10px]">Ekspedisi Kurir yang Didukung Gudang</span>
                                            <div class="font-semibold text-slate-800 leading-relaxed">
                                                {{ $cs->supported_couriers ?: 'J&T Express, JNE (Reg/YES), SiCepat, Shopee Xpress (SPX), Anteraja, GoSend' }}
                                            </div>
                                            <div class="text-[10px] text-slate-500">Kurir di atas menyediakan layanan pick-up rutin atau drop-off langsung dari gudang.</div>
                                        </div>

                                        {{-- Kebijakan Komplain Toko --}}
                                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs space-y-1">
                                            <span class="text-slate-400 font-bold uppercase text-[10px]">Kebijakan Garansi & Komplain</span>
                                            <p class="text-[11px] text-slate-600 leading-relaxed">
                                                {{ $cs->return_policy ?: 'Komplain barang wajib menyertakan video unboxing utuh tanpa jeda maksimal 2x24 jam sejak resi terkirim.' }}
                                            </p>
                                        </div>

                                        {{-- Kontak Langsung CS Toko --}}
                                        <div class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <div class="text-xs">
                                                <span class="text-slate-400 text-[10px] font-bold uppercase">Kontak CS Toko:</span>
                                                <div class="font-bold text-slate-800">{{ $cs->contact_phone ?: '-' }} • {{ $cs->email ?: 'Email belum diatur' }}</div>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                @if ($cs->whatsapp_url)
                                                    <a href="{{ $cs->whatsapp_url }}" target="_blank"
                                                       class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold  transition flex items-center gap-1.5">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                        <span>Chat CS WhatsApp</span>
                                                    </a>
                                                @endif
                                                <button type="button" @click="openDetailModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                                                    Tutup
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- Modal Verifikasi 3 Syarat Pelepasan Kemitraan --}}
                                <div x-show="openDisconnectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                                    <div @click.outside="openDisconnectModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 border border-slate-200-2xl border border-slate-200 space-y-4 max-h-[90vh] overflow-y-auto">
                                        
                                        <div class="flex items-start justify-between border-b border-slate-100 pb-3">
                                            <div>
                                                <h3 class="font-black text-base text-slate-900">Pelepasan Kemitraan Toko</h3>
                                                <p class="text-xs text-slate-400 mt-0.5">{{ $cs->store_name }}</p>
                                            </div>
                                            <button type="button" @click="openDisconnectModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>

                                        <p class="text-xs text-slate-600 leading-relaxed">
                                            Sesuai ketentuan platform, dropshipper <strong>tidak dapat langsung memutus kemitraan sepihak</strong>. Proses pelepasan wajib mematuhi 3 syarat berikut:
                                        </p>

                                        {{-- 3 Syarat Kepatuhan --}}
                                        <div class="space-y-3">
                                            <div class="p-3.5 rounded-2xl border {{ $el['is_orders_cleared'] ? 'bg-emerald-50/60 border-emerald-200' : 'bg-rose-50/60 border-rose-200' }}">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="font-extrabold text-xs {{ $el['is_orders_cleared'] ? 'text-emerald-900' : 'text-rose-900' }}">
                                                        1. Penyelesaian Transaksi
                                                    </span>
                                                    @if ($el['is_orders_cleared'])
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                            ✓ Terpenuhi
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-300">
                                                            ✕ Belum Terpenuhi
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-[11px] mt-1 {{ $el['is_orders_cleared'] ? 'text-emerald-700' : 'text-rose-700' }} leading-relaxed">
                                                    @if ($el['is_orders_cleared'])
                                                        Bebas transaksi aktif. Seluruh pesanan telah berstatus Selesai atau Dibatalkan.
                                                    @else
                                                        Masih terdapat <strong>{{ $el['active_orders_count'] }} pesanan aktif</strong> yang sedang diproses. Anda wajib menyelesaikan transaksi sebelum mengajukan pelepasan.
                                                    @endif
                                                </p>
                                            </div>

                                            <div class="p-3.5 rounded-2xl border {{ $el['is_days_threshold_met'] ? 'bg-emerald-50/60 border-emerald-200' : 'bg-rose-50/60 border-rose-200' }}">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="font-extrabold text-xs {{ $el['is_days_threshold_met'] ? 'text-emerald-900' : 'text-rose-900' }}">
                                                        2. Jeda Pemesanan (Min. 5 Hari Kerja)
                                                    </span>
                                                    @if ($el['is_days_threshold_met'])
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                            ✓ Terpenuhi
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-300">
                                                            ✕ Belum Terpenuhi
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-[11px] mt-1 {{ $el['is_days_threshold_met'] ? 'text-emerald-700' : 'text-rose-700' }} leading-relaxed">
                                                    @if ($el['is_days_threshold_met'])
                                                        @if ($el['last_order'])
                                                            Pesanan terakhir tercatat tanggal {{ $el['last_order']->created_at->translatedFormat('d M Y') }} (sudah {{ $el['business_days_passed'] }} hari kerja yang lalu).
                                                        @else
                                                            Belum pernah melakukan pesanan dengan toko ini (0 pesanan).
                                                        @endif
                                                    @else
                                                        Pesanan terakhir dibuat pada <strong>{{ $el['last_order']->created_at->translatedFormat('d M Y H:i') }}</strong> (Baru {{ $el['business_days_passed'] }} dari 5 hari kerja yang diwajibkan). Anda baru dapat mengajukan pelepasan pada <strong>{{ $el['target_eligible_date']?->translatedFormat('d M Y') }}</strong>.
                                                    @endif
                                                </p>
                                            </div>

                                            <div class="p-3.5 rounded-2xl border bg-indigo-50/60 border-indigo-200">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="font-extrabold text-xs text-indigo-900">
                                                        3. Persetujuan Kedua Belah Pihak
                                                    </span>
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-indigo-100 text-indigo-800 border border-indigo-300">
                                                        ℹ️ Wajib Disetujui Bersama
                                                    </span>
                                                </div>
                                                <p class="text-[11px] mt-1 text-indigo-700 leading-relaxed">
                                                    Setelah permohonan dikirim, pihak toko supplier akan memeriksa dan menyetujui pelepasan kemitraan ini.
                                                </p>
                                            </div>
                                        </div>

                                        @if ($el['can_request'])
                                            <form method="POST" action="{{ route('dropshipper.suppliers.request-disconnect', $cs) }}" class="space-y-3 pt-2">
                                                @csrf
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Pemutusan Kemitraan (Opsional):</label>
                                                    <textarea name="reason" rows="2" placeholder="Contoh: Fokus ke kategori produk lain / ganti supplier utama..."
                                                              class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500"></textarea>
                                                </div>

                                                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                                    <button type="button" @click="openDisconnectModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                                                        Batal
                                                    </button>
                                                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700  transition">
                                                        Ajukan Permohonan Lepas
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                                <span class="text-[11px] font-semibold text-rose-600">
                                                    Lengkapi semua syarat di atas sebelum mengajukan pelepasan.
                                                </span>
                                                <button type="button" @click="openDisconnectModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                                                    Tutup
                                                </button>
                                            </div>
                                        @endif

                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Section Eksplorasi Semua Toko Supplier --}}
            <div class="space-y-4 pt-2">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-sm text-slate-800">Daftar Toko Penyuplai</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Pilih toko untuk menambahkan produk ke katalog Anda dengan informasi yang transparan.</p>
                    </div>

                    {{-- Form Pencarian Toko --}}
                    <form method="GET" action="{{ route('dropshipper.suppliers.index') }}" class="flex items-center gap-2">
                        <div class="relative min-w-[240px]">
                            <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama toko / kota / produk..."
                                   class="w-full pl-9 pr-4 py-2 bg-white rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 ">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                            Cari
                        </button>
                        @if ($search)
                            <a href="{{ route('dropshipper.suppliers.index') }}" class="p-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                @if ($allSuppliers->isEmpty())
                    <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center">
                        <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm">Tidak Ada Toko Ditemukan</h4>
                        <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian yang berbeda.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 keep-card-gap">
                        @foreach ($allSuppliers as $supplier)
                            @php
                                $isConnected = in_array($supplier->id, $connectedIds);
                                $isFull = $connectedCount >= $maxAllowed;
                                $el = $isConnected ? ($eligibilityMap[$supplier->id] ?? null) : null;
                                $isPending = $el && $el['is_pending_approval'];
                            @endphp
                            <div x-data="{ openDetailModal: false }" class="bg-white rounded-2xl border {{ $isConnected ? ($isPending ? 'border-amber-300 bg-amber-50/15' : 'border-green-300 bg-green-50/20') : 'border-slate-200/90' }} p-5  hover:border-slate-300 transition flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-11 h-11 rounded-2xl {{ $isConnected ? ($isPending ? 'bg-amber-600 text-white' : 'bg-green-600 text-white') : 'bg-slate-100 text-slate-700' }} font-black text-sm flex items-center justify-center shrink-0">
                                                {{ mb_substr($supplier->store_name, 0, 2) }}
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-black text-sm text-slate-900 truncate" title="{{ $supplier->store_name }}">{{ $supplier->store_name }}</h4>
                                                <div class="text-[11px] text-green-700 font-semibold flex items-center gap-1 mt-0.5">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    <span class="truncate">{{ $supplier->shipping_origin_city ?: ($supplier->warehouse_address ?: 'Gudang Pusat') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($isConnected)
                                            @if ($isPending)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300 shrink-0">
                                                    ⏳ Menunggu Lepas
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-green-100 text-green-800 border border-green-200 shrink-0">
                                                    ✓ Terhubung
                                                </span>
                                            @endif
                                        @endif
                                    </div>

                                    <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                                        <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                                            <span class="text-[10px] text-slate-400 font-bold uppercase">Cut-Off Kirim</span>
                                            <div class="font-bold text-slate-900 text-[11px] mt-0.5">{{ $supplier->cut_off_time ?: '15:00 WIB' }}</div>
                                            <div class="text-[10px] text-slate-500">{{ $supplier->operating_hours ?: '08:00 - 17:00' }}</div>
                                        </div>

                                        <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                                            <span class="text-[10px] text-slate-400 font-bold uppercase">Katalog Stok</span>
                                            <div class="font-black text-slate-900 text-[11px] mt-0.5">{{ $supplier->products_count }} item</div>
                                            <div class="text-[10px] text-emerald-700 font-semibold">{{ $supplier->packing_sla ?: '< 24 Jam' }}</div>
                                        </div>
                                    </div>

                                    {{-- Micro Couriers Badge --}}
                                    <div class="mt-2.5 flex items-center gap-1.5 flex-wrap">
                                        <span class="text-[10px] text-slate-400 font-bold">Kurir:</span>
                                        @php
                                            $cList = explode(',', $supplier->supported_couriers ?: 'J&T, JNE, SiCepat, SPX, GoSend');
                                        @endphp
                                        @foreach (array_slice($cList, 0, 4) as $c)
                                            <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-semibold">
                                                {{ trim($c) }}
                                            </span>
                                        @endforeach
                                        @if (count($cList) > 4)
                                            <span class="text-[10px] text-slate-400 font-bold">+{{ count($cList) - 4 }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-5 pt-3 border-t border-slate-100 flex flex-col gap-2">
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="openDetailModal = true"
                                                class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold border border-slate-200 transition">
                                            Detail Toko
                                        </button>

                                        @if ($isConnected)
                                            <a href="{{ route('dropshipper.catalog.index', ['supplier_id' => $supplier->id]) }}"
                                               class="flex-1 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold text-center transition">
                                                Buka Katalog
                                            </a>
                                        @else
                                            @if ($isFull)
                                                <button type="button" disabled
                                                        class="flex-1 py-2.5 rounded-xl bg-slate-100 text-slate-400 text-xs font-bold cursor-not-allowed text-center border border-slate-200"
                                                        title="Batas maksimal kemitraan tercapai (5 toko). Lepas salah satu toko terlebih dahulu jika ingin menambah toko baru.">
                                                    Batas Mitra Tercapai
                                                </button>
                                            @else
                                                <form method="POST" action="{{ route('dropshipper.suppliers.connect', $supplier) }}" class="flex-1">
                                                    @csrf
                                                    <button type="submit"
                                                            class="w-full py-2.5 rounded-xl bg-green-600 hover:bg-green-500 active:scale-95 text-white text-xs font-bold transition text-center ">
                                                        Hubungkan
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                {{-- MODAL DETAIL & TRANSPARANSI TOKO --}}
                                <div x-show="openDetailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                                    <div @click.outside="openDetailModal = false" class="bg-white rounded-3xl max-w-xl w-full p-6 border border-slate-200-2xl border border-slate-200 space-y-4 max-h-[90vh] overflow-y-auto">
                                        
                                        <div class="flex items-start justify-between border-b border-slate-100 pb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-700 font-black text-lg flex items-center justify-center border border-green-200 shrink-0">
                                                    {{ mb_substr($supplier->store_name, 0, 2) }}
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <h3 class="font-black text-base text-slate-900">{{ $supplier->store_name }}</h3>
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-green-100 text-green-800 border border-green-200">
                                                            ✓ Verified Supplier
                                                        </span>
                                                    </div>
                                                    <p class="text-xs text-slate-400 mt-0.5">PIC Toko: {{ $supplier->pic_name ?: ($supplier->owner?->name ?? 'Admin Fulfillment') }}</p>
                                                </div>
                                            </div>
                                            <button type="button" @click="openDetailModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>

                                        {{-- Informasi Alamat Asal & Petunjuk Ongkir --}}
                                        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 space-y-2">
                                            <div class="flex items-center gap-2 text-emerald-900 font-extrabold text-xs">
                                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span>Titik Asal Pengiriman Gudang (Penting untuk Marketplace)</span>
                                            </div>
                                            <div class="font-black text-sm text-slate-900">
                                                {{ $supplier->shipping_origin_city ?: ($supplier->warehouse_address ?: 'Kab. Garut, Jawa Barat') }}
                                            </div>
                                            <div class="text-[11px] text-slate-600">
                                                Alamat Gudang: {{ $supplier->warehouse_address ?: 'Gudang Pusat Suplai' }}
                                            </div>
                                            <p class="text-[11px] text-emerald-800 leading-relaxed pt-1 border-t border-emerald-200/60">
                                                💡 <strong>Panduan Reseller:</strong> Atur kota/kabupaten asal di atas sebagai <em>Alamat Pengembalian / Titik Penjemputan (Pickup Address)</em> toko Shopee/Tokopedia/TikTok Shop Anda agar tarif ongkir pembeli dihitung akurat dan bebas selisih ongkir.
                                            </p>
                                        </div>

                                        {{-- Detail Operasional & Ekspedisi --}}
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                                                <span class="text-slate-400 font-bold uppercase text-[10px]">Jam Operasional Gudang</span>
                                                <div class="font-bold text-slate-800">{{ $supplier->operating_hours ?: 'Senin - Sabtu: 08:00 - 17:00 WIB' }}</div>
                                                <div class="text-[10px] text-slate-500">Minggu / Libur Nasional: Off Pengiriman</div>
                                            </div>

                                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                                                <span class="text-slate-400 font-bold uppercase text-[10px]">Batas Waktu Pengiriman (Cut-Off)</span>
                                                <div class="font-black text-indigo-700">{{ $supplier->cut_off_time ?: '15:00 WIB' }}</div>
                                                <div class="text-[10px] text-slate-500">Order resi sebelum jam ini dikemas hari ini</div>
                                            </div>

                                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                                                <span class="text-slate-400 font-bold uppercase text-[10px]">Kecepatan Kemas (SLA)</span>
                                                <div class="font-bold text-emerald-700">{{ $supplier->packing_sla ?: '< 24 Jam Kerja' }}</div>
                                                <div class="text-[10px] text-slate-500">Rata-rata waktu proses fulfillment pesanan</div>
                                            </div>

                                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                                                <span class="text-slate-400 font-bold uppercase text-[10px]">Standar Pengemasan</span>
                                                <div class="font-semibold text-slate-800">{{ $supplier->packing_standard ?: 'Bubble Wrap Tebal + Dus Pelindung + Fragile' }}</div>
                                            </div>
                                        </div>

                                        {{-- Ekspedisi Kurir Didukung --}}
                                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs space-y-1.5">
                                            <span class="text-slate-400 font-bold uppercase text-[10px]">Ekspedisi Kurir yang Didukung Gudang</span>
                                            <div class="font-semibold text-slate-800 leading-relaxed">
                                                {{ $supplier->supported_couriers ?: 'J&T Express, JNE (Reg/YES), SiCepat, Shopee Xpress (SPX), Anteraja, GoSend' }}
                                            </div>
                                            <div class="text-[10px] text-slate-500">Kurir di atas menyediakan layanan pick-up rutin atau drop-off langsung dari gudang.</div>
                                        </div>

                                        {{-- Kebijakan Komplain Toko --}}
                                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs space-y-1">
                                            <span class="text-slate-400 font-bold uppercase text-[10px]">Kebijakan Garansi & Komplain</span>
                                            <p class="text-[11px] text-slate-600 leading-relaxed">
                                                {{ $supplier->return_policy ?: 'Komplain barang wajib menyertakan video unboxing utuh tanpa jeda maksimal 2x24 jam sejak resi terkirim.' }}
                                            </p>
                                        </div>

                                        {{-- Kontak Langsung CS Toko --}}
                                        <div class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <div class="text-xs">
                                                <span class="text-slate-400 text-[10px] font-bold uppercase">Kontak CS Toko:</span>
                                                <div class="font-bold text-slate-800">{{ $supplier->contact_phone ?: '-' }} • {{ $supplier->email ?: 'Email belum diatur' }}</div>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                @if ($supplier->whatsapp_url)
                                                    <a href="{{ $supplier->whatsapp_url }}" target="_blank"
                                                       class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold  transition flex items-center gap-1.5">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                        <span>Chat CS WhatsApp</span>
                                                    </a>
                                                @endif
                                                <button type="button" @click="openDetailModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                                                    Tutup
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
