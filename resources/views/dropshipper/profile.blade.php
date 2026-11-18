<x-app-layout title="Profil & Ketentuan Dropshipper">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-lg text-slate-900 leading-tight flex items-center gap-2">
                    <span>Profil & Ketentuan Kemitraan</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                        Mitra Resmi
                    </span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Kelola identitas toko reseller, pantau performa kemitraan, dan pedoman operasional dropship.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('dropshipper.suppliers.index') }}" class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50  transition flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Pilih Penyuplai</span>
                </a>
                <a href="{{ route('dropshipper.catalog.index') }}" class="px-3.5 py-1.5 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-semibold  transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Katalog</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-0" x-data="{ activeTab: 'summary' }">
        <div class="w-full space-y-6">

            {{-- Flash Notification --}}
            @if (session('status') === 'profile-updated')
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2.5 ">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Profil dropshipper Anda berhasil diperbarui!</span>
                </div>
            @endif

            @if (session('status') === 'password-updated')
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2.5 ">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Kata sandi berhasil diperbarui!</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2.5 ">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Terdapat kesalahan pada isian form. Silakan periksa kembali.</span>
                </div>
            @endif

            {{-- Profile Hero Banner Card --}}
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white border border-slate-200-xl border border-slate-800 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-80 h-80 bg-green-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4 sm:gap-6 min-w-0">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-green-600 text-white font-bold text-xl sm:text-2xl flex items-center justify-center shrink-0   border-2 border-white/10">
                            {{ mb_substr($user->name, 0, 2) }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h1 class="text-lg sm:text-xl font-bold text-white truncate" title="{{ $user->name }}">
                                    {{ $user->name }}
                                </h1>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-500/20 text-green-300 border border-green-500/30">
                                    DROPSHIPPER TERVERIFIKASI
                                </span>
                            </div>

                            <p class="text-xs sm:text-sm text-slate-300 mt-1 flex items-center gap-2 flex-wrap">
                                <span>Toko: <strong class="text-white">{{ $user->dropship_store_name ?: $user->name }}</strong></span>
                                <span class="text-slate-500">•</span>
                                <span>ID: <code class="font-mono text-green-400">#DS-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</code></span>
                                <span class="text-slate-500">•</span>
                                <span>Bergabung: {{ $user->created_at?->translatedFormat('d M Y') ?? '-' }}</span>
                            </p>

                            <div class="mt-3 flex items-center gap-3 text-xs text-slate-300 flex-wrap">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    {{ $user->email }}
                                </span>
                                @if ($user->phone)
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        {{ $user->phone }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Fast Stat Pills --}}
                    <div class="flex items-center gap-2 self-start md:self-center shrink-0">
                        <button type="button" @click="activeTab = 'settings'" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold border border-white/15 transition flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Edit Profil</span>
                        </button>
                        <button type="button" @click="activeTab = 'rules'" class="px-4 py-2 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-semibold  transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Ketentuan & SOP</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 4 Performance KPI Stat Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-200 ">
                    <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Mitra Penyuplai</div>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="text-xl sm:text-2xl font-bold text-green-600">{{ $connectedCount }}</span>
                        <span class="text-xs font-medium text-slate-400">Toko Aktif</span>
                    </div>
                    <div class="mt-2 text-xs text-slate-500">Toko penyuplai terhubung</div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 ">
                    <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Pesanan</div>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="text-xl sm:text-2xl font-bold text-slate-900">{{ $totalOrders }}</span>
                        <span class="text-xs font-medium text-slate-400">Order</span>
                    </div>
                    <div class="mt-2 text-xs text-slate-500">Seluruh pesanan dropship Anda</div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 ">
                    <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Pesanan Sukses</div>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="text-xl sm:text-2xl font-bold text-emerald-600">{{ $completedOrders }}</span>
                        <span class="text-xs font-medium text-slate-400">Selesai</span>
                    </div>
                    <div class="mt-2 text-xs text-slate-500">Berhasil dikirim ke pembeli</div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 ">
                    <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Akumulasi Belanja</div>
                    <div class="mt-1 text-lg sm:text-xl font-bold text-slate-900">
                        Rp {{ number_format($totalSpend, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-xs text-slate-500">Total belanja produk terselesaikan</div>
                </div>
            </div>

            {{-- Navigation Tabs --}}
            <div class="border-b border-slate-200 bg-white rounded-2xl px-3 py-2  flex items-center gap-2 overflow-x-auto no-scrollbar">
                <button type="button"
                        @click="activeTab = 'summary'"
                        :class="activeTab === 'summary' ? 'bg-green-600 text-white  font-bold' : 'text-slate-600 hover:bg-slate-100 font-medium'"
                        class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Identitas & Ringkasan</span>
                </button>

                <button type="button"
                        @click="activeTab = 'suppliers'"
                        :class="activeTab === 'suppliers' ? 'bg-green-600 text-white  font-bold' : 'text-slate-600 hover:bg-slate-100 font-medium'"
                        class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Toko Penyuplai Aktif ({{ $connectedCount }})</span>
                </button>

                <button type="button"
                        @click="activeTab = 'rules'"
                        :class="activeTab === 'rules' ? 'bg-green-600 text-white  font-bold' : 'text-slate-600 hover:bg-slate-100 font-medium'"
                        class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Ketentuan & SOP Dropshipper</span>
                </button>

                <button type="button"
                        @click="activeTab = 'settings'"
                        :class="activeTab === 'settings' ? 'bg-green-600 text-white  font-bold' : 'text-slate-600 hover:bg-slate-100 font-medium'"
                        class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Edit Profil & Keamanan</span>
                </button>
            </div>

            {{-- TAB 1: IDENTITAS & RINGKASAN --}}
            <div x-show="activeTab === 'summary'" x-cloak class="space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 ">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">Data Identitas Bisnis Dropship</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Informasi profil yang digunakan untuk koordinasi pesanan dengan toko penyuplai.</p>
                        </div>
                        <button type="button" @click="activeTab = 'settings'" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                            Ubah Data
                        </button>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                        <div class="space-y-1.5 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-500 font-semibold uppercase tracking-wider text-xs">Nama Pemilik Akun</span>
                            <div class="font-semibold text-sm text-slate-900">{{ $user->name }}</div>
                            <div class="text-xs text-slate-500">Nama terdaftar pada akun login OmniPOS</div>
                        </div>

                        <div class="space-y-1.5 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-500 font-semibold uppercase tracking-wider text-xs">Nama Toko Online / Reseller</span>
                            <div class="font-semibold text-sm text-slate-900">{{ $user->dropship_store_name ?: ($user->name . ' Store') }}</div>
                            <div class="text-xs text-slate-500">Nama toko Anda di Shopee / Tokopedia / TikTok Shop</div>
                        </div>

                        <div class="space-y-1.5 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-500 font-semibold uppercase tracking-wider text-xs">Nomor WhatsApp / HP</span>
                            <div class="font-semibold text-sm text-emerald-700">{{ $user->phone ?: 'Belum diisi (Klik Ubah Data)' }}</div>
                            <div class="text-xs text-slate-500">Digunakan pihak supplier untuk konfirmasi ketersediaan barang atau resi</div>
                        </div>

                        <div class="space-y-1.5 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-500 font-semibold uppercase tracking-wider text-xs">Alamat Email</span>
                            <div class="font-mono font-semibold text-sm text-slate-800">{{ $user->email }}</div>
                            <div class="text-xs text-slate-500">Email untuk notifikasi akun dan transaksi</div>
                        </div>

                        <div class="space-y-1.5 p-4 rounded-2xl bg-slate-50 border border-slate-100 md:col-span-2">
                            <span class="text-slate-500 font-semibold uppercase tracking-wider text-xs">Alamat Operasional Reseller / Domisili</span>
                            <div class="font-medium text-sm text-slate-800 leading-relaxed">
                                {{ $user->address ?: 'Belum mengatur alamat domisili operasional reseller.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 2: TOKO PENYUPLAI AKTIF --}}
            <div x-show="activeTab === 'suppliers'" x-cloak class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-slate-900">Toko Penyuplai Terhubung ({{ $connectedCount }} / {{ $maxAllowed }})</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar toko penyuplai resmi tempat Anda mengambil produk untuk dijual kembali.</p>
                    </div>
                    <a href="{{ route('dropshipper.suppliers.index') }}" class="px-3.5 py-2 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-semibold  transition">
                        Kelola Kemitraan
                    </a>
                </div>

                @if ($connectedSuppliers->isEmpty())
                    <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center">
                        <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h4 class="font-semibold text-slate-800 text-sm">Belum Ada Toko Penyuplai Terhubung</h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                            Anda dapat memilih toko penyuplai untuk mulai memasarkan produk tanpa modal stok fisik.
                        </p>
                        <a href="{{ route('dropshipper.suppliers.index') }}" class="inline-block mt-4 px-4 py-2 rounded-xl bg-green-600 text-white text-xs font-semibold hover:bg-green-500 transition">
                            Pilih Penyuplai Sekarang
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($connectedSuppliers as $cs)
                            <div class="bg-white rounded-2xl border border-green-200 p-5  hover:border-slate-300 transition flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-700 border border-green-200 font-bold text-base flex items-center justify-center shrink-0">
                                                {{ mb_substr($cs->store_name, 0, 2) }}
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-semibold text-sm text-slate-900 truncate" title="{{ $cs->store_name }}">{{ $cs->store_name }}</h4>
                                                <div class="text-xs text-green-700 font-medium flex items-center gap-1 mt-0.5">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    <span class="truncate">{{ $cs->shipping_origin_city ?: ($cs->warehouse_address ?: 'Gudang Pusat') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-300 shrink-0">
                                            TERHUBUNG
                                        </span>
                                    </div>

                                    <div class="mt-4 pt-3 border-t border-slate-100 space-y-2 text-xs">
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-500 text-xs">Jam Kerja:</span>
                                            <span class="font-medium text-slate-800 text-xs">{{ $cs->operating_hours ?: '08:00 - 17:00 WIB' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-500 text-xs">Batas Kirim (Cut-off):</span>
                                            <span class="font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded text-xs">{{ $cs->cut_off_time ?: '15:00 WIB' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-500 text-xs">SLA Packing:</span>
                                            <span class="font-medium text-emerald-700 text-xs">{{ $cs->packing_sla ?: '< 24 Jam Kerja' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-500 text-xs">Stok Tersedia:</span>
                                            <span class="font-semibold text-slate-900 bg-slate-100 px-2 py-0.5 rounded text-xs">{{ $cs->products_count }} Produk</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5 pt-3 border-t border-slate-100 flex items-center gap-2">
                                    <a href="{{ route('dropshipper.catalog.index', ['supplier_id' => $cs->id]) }}"
                                       class="flex-1 py-2 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-semibold text-center transition">
                                        Buka Katalog
                                    </a>
                                    @if ($cs->whatsapp_url)
                                        <a href="{{ $cs->whatsapp_url }}" target="_blank"
                                           class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 transition" title="Chat CS WhatsApp">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- TAB 3: KETENTUAN & SOP DROPSHIPPER --}}
            <div x-show="activeTab === 'rules'" x-cloak class="space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 ">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-slate-900">Ketentuan & Kebijakan Operasional Dropshipper</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Panduan resmi, hak, kewajiban, dan SOP fulfillment platform OmniPOS.</p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        {{-- Aturan 1 --}}
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 hover:border-green-300 transition space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-green-600 text-white font-semibold text-xs flex items-center justify-center shrink-0">1</span>
                                <h4 class="font-semibold text-sm text-slate-900">Batas Kemitraan Toko Penyuplai</h4>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed pl-8">
                                Setiap akun dropshipper dapat terhubung dengan hingga 5 toko penyuplai aktif. Aturan ini bertujuan menjaga fokus penjualan reseller dan mencegah kesalahan titik asal pengiriman (origin warehouse) di toko marketplace Anda (Shopee, Tokopedia, TikTok Shop) yang dapat menimbulkan selisih ongkir. Apabila sudah mencapai batas 5 toko, Anda dapat melepas salah satu kemitraan terlebih dahulu sebelum menghubungkan toko baru.
                            </p>
                        </div>

                        {{-- Aturan 2 --}}
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 hover:border-green-300 transition space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-green-600 text-white font-semibold text-xs flex items-center justify-center shrink-0">2</span>
                                <h4 class="font-semibold text-sm text-slate-900">Kewajiban Input Nomor Resi Pengiriman Asli Marketplace</h4>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed pl-8">
                                Pada saat membuat pesanan dropship di menu Katalog, dropshipper <strong>wajib menginput nomor resi resmi</strong> dan memilih ekspedisi kurir yang sesuai dari pesanan yang masuk di toko marketplace Anda. Sangat disarankan untuk mengunggah file label resi barcode (format PDF thermal / JPG) agar tim gudang toko supplier dapat langsung mencetak dan menempelkannya ke paket pengiriman tanpa risiko salah ketik.
                            </p>
                        </div>

                        {{-- Aturan 3 --}}
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 hover:border-green-300 transition space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-green-600 text-white font-semibold text-xs flex items-center justify-center shrink-0">3</span>
                                <h4 class="font-semibold text-sm text-slate-900">Siklus Alur Pesanan & Batas Waktu Pengemasan (SLA)</h4>
                            </div>
                            <div class="text-xs text-slate-600 leading-relaxed pl-8 space-y-2">
                                <p>
                                    Tahapan alur proses pemenuhan pesanan dropship:
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 pt-1">
                                    <div class="p-2.5 rounded-xl bg-white border border-slate-200 text-center">
                                        <div class="font-semibold text-amber-700 text-xs">1. Verifikasi</div>
                                        <div class="text-xs text-slate-500 mt-0.5">Toko approve pesanan & cek resi</div>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-white border border-slate-200 text-center">
                                        <div class="font-semibold text-sky-700 text-xs">2. Antrean Gudang</div>
                                        <div class="text-xs text-slate-500 mt-0.5">Ambil stok & cetak label resi</div>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-white border border-slate-200 text-center">
                                        <div class="font-semibold text-indigo-700 text-xs">3. Packing Standar</div>
                                        <div class="text-xs text-slate-500 mt-0.5">Bungkus bubble wrap & kardus</div>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-white border border-slate-200 text-center">
                                        <div class="font-semibold text-emerald-700 text-xs">4. Pickup Kurir</div>
                                        <div class="text-xs text-slate-500 mt-0.5">Diserahkan ke kurir ekspedisi</div>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 pt-1">
                                    <strong>SLA Pengemasan:</strong> Pesanan dengan resi yang masuk sebelum batas waktu cut-off (pukul 15:00 WIB) diproses dan diserahkan ke kurir pada hari kerja yang sama.
                                </p>
                            </div>
                        </div>

                        {{-- Aturan 4 --}}
                        <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200/80 hover:border-amber-300 transition space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-amber-600 text-white font-semibold text-xs flex items-center justify-center shrink-0">4</span>
                                <h4 class="font-semibold text-sm text-amber-950">Prosedur 3 Syarat Wajib Pelepasan Kemitraan (Disconnect Policy)</h4>
                            </div>
                            <div class="text-xs text-amber-900 leading-relaxed pl-8 space-y-1.5">
                                <p>
                                    Untuk melindungi kelangsungan pesanan pembeli dan mencegah pemutusan sepihak yang merugikan, pelepasan kemitraan dengan toko penyuplai wajib memenuhi 3 syarat berurutan:
                                </p>
                                <ul class="list-disc list-inside space-y-1 pl-1 text-xs">
                                    <li><strong>1. Bebas Transaksi Aktif:</strong> Seluruh pesanan ke toko terkait telah berstatus Selesai atau Dibatalkan (tidak boleh ada pesanan yang sedang diproses atau dipacking).</li>
                                    <li><strong>2. Jeda Pemesanan Min. 5 Hari Kerja:</strong> Minimal 5 hari kerja tidak pernah membuat pesanan baru sejak pesanan terakhir ke toko terkait.</li>
                                    <li><strong>3. Persetujuan Kedua Belah Pihak:</strong> Pengajuan pelepasan harus disetujui bersama oleh pihak toko supplier dan dropshipper.</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Aturan 5 --}}
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 hover:border-green-300 transition space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-green-600 text-white font-semibold text-xs flex items-center justify-center shrink-0">5</span>
                                <h4 class="font-semibold text-sm text-slate-900">Kebijakan Garansi, Cacat Barang & Bukti Unboxing</h4>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed pl-8">
                                Semua toko supplier terikat standar kemasan aman. Apabila pembeli menerima paket dengan kondisi barang rusak fisik atau kuantitas kurang akibat kelalaian internal, komplain retur wajib menyertakan <strong>video unboxing utuh tanpa jeda/cut</strong> maksimal 2x24 jam sejak resi dinyatakan terkirim oleh kurir.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 4: EDIT PROFIL & KEAMANAN --}}
            <div x-show="activeTab === 'settings'" x-cloak class="space-y-6">
                {{-- Form Informasi Profil --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 ">
                    <div class="pb-4 border-b border-slate-100">
                        <h3 class="font-bold text-base text-slate-900">Perbarui Informasi Profil Dropshipper</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Ubah nama akun, identitas toko online, dan nomor WhatsApp untuk koordinasi pesanan.</p>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-4">
                        @csrf
                        @method('patch')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Reseller *</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500 ">
                                <x-input-error class="mt-1" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <label for="dropship_store_name" class="block text-xs font-bold text-slate-700 mb-1">Nama Toko Online (Marketplace)</label>
                                <input type="text" id="dropship_store_name" name="dropship_store_name" value="{{ old('dropship_store_name', $user->dropship_store_name) }}" placeholder="Contoh: Dunia Mainan Official Store"
                                       class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500 ">
                                <x-input-error class="mt-1" :messages="$errors->get('dropship_store_name')" />
                            </div>

                            <div>
                                <label for="phone" class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp / HP Aktif</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890"
                                       class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500 ">
                                <x-input-error class="mt-1" :messages="$errors->get('phone')" />
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Alamat Email *</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500 ">
                                <x-input-error class="mt-1" :messages="$errors->get('email')" />
                            </div>

                            <div class="sm:col-span-2">
                                <label for="address" class="block text-xs font-bold text-slate-700 mb-1">Alamat Operasional Reseller / Domisili</label>
                                <textarea id="address" name="address" rows="2" placeholder="Alamat domisili atau kantor toko online Anda..."
                                          class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500 ">{{ old('address', $user->address) }}</textarea>
                                <x-input-error class="mt-1" :messages="$errors->get('address')" />
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-bold  transition">
                                Simpan Perubahan Profil
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Form Ganti Kata Sandi --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 ">
                    <div class="pb-4 border-b border-slate-100">
                        <h3 class="font-bold text-base text-slate-900">Perbarui Kata Sandi (Password)</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Pastikan akun Anda menggunakan kata sandi yang kuat dan aman.</p>
                    </div>

                    <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
                        @csrf
                        @method('put')

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="update_password_current_password" class="block text-xs font-bold text-slate-700 mb-1">Sandi Saat Ini *</label>
                                <input type="password" id="update_password_current_password" name="current_password" autocomplete="current-password"
                                       class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500 ">
                                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
                            </div>

                            <div>
                                <label for="update_password_password" class="block text-xs font-bold text-slate-700 mb-1">Sandi Baru *</label>
                                <input type="password" id="update_password_password" name="password" autocomplete="new-password"
                                       class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500 ">
                                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
                            </div>

                            <div>
                                <label for="update_password_password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Sandi Baru *</label>
                                <input type="password" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password"
                                       class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500 ">
                                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold  transition">
                                Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

