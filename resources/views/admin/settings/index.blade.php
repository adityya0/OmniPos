<x-app-layout title="Pengaturan & Profil">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-black text-xl text-slate-900 leading-tight flex items-center gap-2">
                    <span>Pengaturan & Profil Toko</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                        {{ $supplier->store_name ?? 'OmniPOS' }}
                    </span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Konfigurasi preferensi kasir POS, profil akun, identitas toko, dan perangkat printer.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.pos.index') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white text-xs font-bold  transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Buka Kasir POS</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-0" x-data="{ currentTab: '{{ session('tab') ?? $activeTab ?? 'pos' }}' }">
        <div class="w-full space-y-6">

            {{-- Flash Notification --}}
            @if (session('message') || session('status'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between ">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span>{{ session('message') ?? (session('status') === 'profile-updated' ? 'Profil akun berhasil diperbarui.' : 'Pengaturan berhasil disimpan.') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2.5 ">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Terdapat kesalahan pada input form. Mohon periksa kembali field yang berwarna merah.</span>
                </div>
            @endif

            {{-- Tab Buttons --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-1.5  flex items-center gap-1.5 overflow-x-auto no-scrollbar">
                <button type="button"
                        @click="currentTab = 'pos'"
                        :class="currentTab === 'pos' ? 'bg-green-600 text-white  font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Preferensi Kasir POS</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-extrabold" x-show="currentTab === 'pos'">Aktif</span>
                </button>

                <button type="button"
                        @click="currentTab = 'profile'"
                        :class="currentTab === 'profile' ? 'bg-green-600 text-white  font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Profil Pengguna & Keamanan</span>
                </button>

                <button type="button"
                        @click="currentTab = 'store'"
                        :class="currentTab === 'store' ? 'bg-green-600 text-white  font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Profil Toko & Gudang</span>
                </button>

                <button type="button"
                        @click="currentTab = 'printer'"
                        :class="currentTab === 'printer' ? 'bg-green-600 text-white  font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs whitespace-nowrap transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Printer Kasir</span>
                </button>
            </div>

            {{-- ================= TAB 1: PREFERENSI KASIR POS ================= --}}
            <div x-show="currentTab === 'pos'" x-cloak class="space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200/90  overflow-hidden">
                    <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                    <span>Mode Tampilan Tingkatan Harga Kasir POS</span>
                                </h3>
                                <p class="text-xs text-slate-500 mt-1">
                                    Pilih format tampilan harga multi-level (Eceran, Grosir Lusin, Grosir Besar) pada kartu produk katalog kasir.
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-black bg-green-100 text-green-800">
                                Mode Saat Ini: {{ ($supplier->pos_pricing_mode ?? 'global_filter') === 'global_filter' ? 'Filter Global' : 'Tombol di Card' }}
                            </span>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.pos-pricing-mode') }}" method="POST" class="p-6 space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Option 1: Global Filter (Default) --}}
                            <label class="relative flex flex-col p-5 rounded-2xl border-2 cursor-pointer transition select-none group"
                                   :class="'{{ $supplier->pos_pricing_mode ?? 'global_filter' }}' === 'global_filter' ? 'border-green-600 bg-green-50/40 ring-2 ring-green-600/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="flex items-center gap-3">
                                        <input type="radio"
                                               name="pos_pricing_mode"
                                               value="global_filter"
                                               {{ ($supplier->pos_pricing_mode ?? 'global_filter') === 'global_filter' ? 'checked' : '' }}
                                               class="w-4 h-4 text-green-600 border-slate-300 focus:ring-green-500">
                                        <div>
                                            <span class="font-black text-sm text-slate-900 block">Mode 1: Filter Tingkatan Harga Global</span>
                                            <span class="text-[11px] font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded-md inline-block mt-0.5">
                                                ★ Default / Direkomendasikan
                                            </span>
                                        </div>
                                    </div>
                                    <div class="w-8 h-8 rounded-xl bg-green-100 text-green-700 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                        </svg>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                    Menempatkan <strong>Filter Tingkatan Harga</strong> di baris filter sebelah Kategori. Ketika kasir menekan level tertentu (misal: <em>Level 2 Grosir</em>), <strong>seluruh kartu produk di katalog langsung berganti ke harga level tersebut</strong>.
                                </p>

                                {{-- Ilustrasi Mockup --}}
                                <div class="mt-auto p-3.5 rounded-xl bg-white border border-slate-200  space-y-2">
                                    <div class="flex items-center gap-1.5 text-[10px]">
                                        <span class="text-slate-400 font-bold">Filter Level:</span>
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-bold">L1 Eceran</span>
                                        <span class="px-2 py-0.5 rounded-md bg-green-600 text-white font-extrabold ">L2 Grosir ✓</span>
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-bold">L3 Dus</span>
                                    </div>
                                    <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between text-[11px]">
                                        <span class="font-bold text-slate-800">Semua Produk Katalog:</span>
                                        <span class="font-black text-green-700">Rp 540.000 <span class="text-[9px] text-slate-500 font-normal">(Lusin)</span></span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 italic">
                                        ✓ Tampilan kartu tetap bersih, lega, foto tampak penuh & klik langsung masuk keranjang.
                                    </div>
                                </div>
                            </label>

                            {{-- Option 2: Card Buttons --}}
                            <label class="relative flex flex-col p-5 rounded-2xl border-2 cursor-pointer transition select-none group"
                                   :class="'{{ $supplier->pos_pricing_mode ?? 'global_filter' }}' === 'card_buttons' ? 'border-green-600 bg-green-50/40 ring-2 ring-green-600/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="flex items-center gap-3">
                                        <input type="radio"
                                               name="pos_pricing_mode"
                                               value="card_buttons"
                                               {{ ($supplier->pos_pricing_mode ?? 'global_filter') === 'card_buttons' ? 'checked' : '' }}
                                               class="w-4 h-4 text-green-600 border-slate-300 focus:ring-green-500">
                                        <div>
                                            <span class="font-black text-sm text-slate-900 block">Mode 2: Tombol Multi-Level di Setiap Kartu</span>
                                            <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md inline-block mt-0.5">
                                                Pilihan Tombol di Setiap Card
                                            </span>
                                        </div>
                                    </div>
                                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                        </svg>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                    Menampilkan <strong>3 tombol tingkatan harga langsung di bagian bawah setiap kartu produk</strong>. Kasir dapat memilih tombol level yang berbeda-beda untuk tiap produk secara langsung di grid kartu.
                                </p>

                                {{-- Ilustrasi Mockup --}}
                                <div class="mt-auto p-3.5 rounded-xl bg-white border border-slate-200  space-y-1.5">
                                    <div class="text-[10px] text-slate-500 font-bold">Tombol di Dalam Tiap Kartu:</div>
                                    <div class="px-2 py-1 rounded-md bg-green-50 border border-green-200 flex items-center justify-between text-[10px]">
                                        <span class="font-bold text-slate-800">Eceran (1 pcs)</span>
                                        <span class="font-black text-green-700">Rp 50.000</span>
                                    </div>
                                    <div class="px-2 py-1 rounded-md bg-green-50 border border-green-200 flex items-center justify-between text-[10px]">
                                        <span class="font-bold text-slate-800">Grosir Lusin (12 pcs)</span>
                                        <span class="font-black text-green-700">Rp 540.000</span>
                                    </div>
                                    <div class="px-2 py-1 rounded-md bg-green-50 border border-green-200 flex items-center justify-between text-[10px]">
                                        <span class="font-bold text-slate-800">Grosir Besar (72 pcs)</span>
                                        <span class="font-black text-green-700">Rp 2.880.000</span>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                            <div class="text-xs text-slate-500">
                                Pengaturan ini akan langsung diterapkan pada tampilan katalog Kasir POS.
                            </div>
                            <button type="submit"
                                    class="px-5 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold text-xs  transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Simpan Preferensi Kasir</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ================= TAB 2: PROFIL PENGGUNA & KEAMANAN ================= --}}
            <div x-show="currentTab === 'profile'" x-cloak class="space-y-6">
                {{-- Info Akun yang Sedang Login & Tombol Keluar --}}
                <div class="bg-white rounded-3xl border border-slate-200/90 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-700 font-black text-sm flex items-center justify-center shrink-0 border border-emerald-200/60">
                            {{ strtoupper(mb_substr(trim($user->name ?: 'U'), 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-black text-sm text-slate-900 truncate">{{ $user->name }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 truncate mt-0.5">{{ $user->email }} &bull; Sesi Aktif</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Keluar / Log Out</span>
                        </button>
                    </form>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Update Profile Info Form --}}
                <div class="bg-white rounded-3xl border border-slate-200/90  p-6">
                    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-green-600 text-white font-black text-sm flex items-center justify-center ">
                            {{ mb_substr($user->name, 0, 2) }}
                        </div>
                        <div>
                            <h3 class="font-black text-base text-slate-900">Informasi Akun Pengguna</h3>
                            <p class="text-xs text-slate-400">Perbarui identitas login dan kontak email Anda.</p>
                        </div>
                    </div>

                    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf
                        @method('patch')

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name', $user->name) }}"
                                   required
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500">
                            @error('name')
                                <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Email Login</label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email', $user->email) }}"
                                   required
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500">
                            @error('email')
                                <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Hak Akses / Peran</label>
                            <div class="px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700 flex items-center justify-between">
                                <span>{{ strtoupper(str_replace('_', ' ', $user->role)) }}</span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] bg-green-100 text-green-800">Aktif</span>
                            </div>
                        </div>

                        <div class="pt-3 flex justify-end">
                            <button type="submit"
                                    class="px-5 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold text-xs  transition">
                                Simpan Perubahan Profil
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Update Password Form --}}
                <div class="bg-white rounded-3xl border border-slate-200/90  p-6">
                    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center ">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-slate-900">Perbarui Kata Sandi</h3>
                            <p class="text-xs text-slate-400">Pastikan akun menggunakan kata sandi yang kuat.</p>
                        </div>
                    </div>

                    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                        @csrf
                        @method('put')

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi Saat Ini</label>
                            <input type="password"
                                   name="current_password"
                                   autocomplete="current-password"
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500">
                            @error('current_password', 'updatePassword')
                                <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi Baru</label>
                            <input type="password"
                                   name="password"
                                   autocomplete="new-password"
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500">
                            @error('password', 'updatePassword')
                                <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Ulangi Kata Sandi Baru</label>
                            <input type="password"
                                   name="password_confirmation"
                                   autocomplete="new-password"
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500">
                            @error('password_confirmation', 'updatePassword')
                                <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-3 flex justify-end">
                            <button type="submit"
                                    class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs  transition">
                                Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>
                </div>
            </div>

            {{-- ================= TAB 3: PROFIL IDENTITAS TOKO & GUDANG ================= --}}
            <div x-show="currentTab === 'store'" x-cloak class="bg-white rounded-3xl border border-slate-200/90  overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                    <h3 class="font-black text-base text-slate-900">Profil Identitas Toko & Gudang</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Data ini digunakan pada pencetakan struk kasir, informasi asal pengiriman, dan transparansi ke mitra dropshipper.
                    </p>
                </div>

                <form action="{{ route('admin.settings.store-profile') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Toko *</label>
                            <input type="text"
                                   name="store_name"
                                   value="{{ old('store_name', $supplier->store_name) }}"
                                   required
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kota Asal Pengiriman Toko</label>
                            <input type="text"
                                   name="shipping_origin_city"
                                   value="{{ old('shipping_origin_city', $supplier->shipping_origin_city) }}"
                                   placeholder="Contoh: Jakarta Barat, DKI Jakarta"
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Kontak WhatsApp / Telp</label>
                            <input type="text"
                                   name="contact_phone"
                                   value="{{ old('contact_phone', $supplier->contact_phone) }}"
                                   placeholder="Contoh: 081234567890"
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Email Resmi Toko</label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email', $supplier->email) }}"
                                   placeholder="toko@domain.com"
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jam Operasional Toko</label>
                            <input type="text"
                                   name="operating_hours"
                                   value="{{ old('operating_hours', $supplier->operating_hours) }}"
                                   placeholder="Contoh: Senin - Sabtu: 08.00 - 17.00 WIB"
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Cut-Off Time Order Harian</label>
                            <input type="text"
                                   name="cut_off_time"
                                   value="{{ old('cut_off_time', $supplier->cut_off_time) }}"
                                   placeholder="Contoh: 15.00 WIB"
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Fisik Gudang / Toko</label>
                            <textarea name="warehouse_address"
                                      rows="3"
                                      placeholder="Alamat lengkap lokasi fisik toko atau gudang pengiriman..."
                                      class="w-full text-xs rounded-xl border-slate-200 focus:border-green-500 focus:ring-green-500">{{ old('warehouse_address', $supplier->warehouse_address) }}</textarea>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold text-xs  transition">
                            Simpan Profil Toko
                        </button>
                    </div>
                </form>
            </div>

            {{-- ================= TAB 4: PRINTER KASIR ================= --}}
            <div x-show="currentTab === 'printer'" x-cloak class="bg-white rounded-3xl border border-slate-200/90  p-6 space-y-6">
                <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="font-black text-base text-slate-900">Perangkat & Struk Printer Thermal</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Konfigurasi ukuran kertas struk kasir, logo cetak, footer teks, dan printer Bluetooth POS.
                        </p>
                    </div>

                    <a href="{{ route('admin.printer.index') }}"
                       class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs  transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        </svg>
                        <span>Buka Konfigurasi Lengkap Printer</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Ukuran Kertas Kasir</span>
                        <span class="font-extrabold text-base text-slate-900 mt-1 block">
                            {{ $supplier->receipt_paper_size === '80mm' ? '80mm (Lebar / Standar Kasir)' : '58mm (Kompak / Mobile Mini)' }}
                        </span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Judul Header Struk</span>
                        <span class="font-extrabold text-base text-slate-900 mt-1 block truncate">
                            {{ $supplier->receipt_header_title ?: $supplier->store_name }}
                        </span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Status Bluetooth</span>
                        <span class="font-extrabold text-base text-emerald-700 mt-1 block">
                            Siap Terhubung di Kasir POS
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

