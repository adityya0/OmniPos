<x-app-layout title="Pengaturan Printer">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Pengaturan Printer & Struk</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola sambungan printer kasir dan tampilan struk toko.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.pos.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                    Kembali ke POS
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-0" x-data="printerSettingsApp()">
        <div class="w-full space-y-6">

            <!-- Success Alert -->
            @if (session('status'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 ">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- Left Column: Settings & Connection (7 cols) -->
                <div class="lg:col-span-7 space-y-5">

                    <!-- Card 1: Koneksi Printer -->
                    <div class="bg-white rounded-2xl p-5  border border-slate-200">
                        <div class="mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center text-white shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v18l6-6-6-6 6-6-6-6M9 9l6 3-6 3" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">Sambungan Printer</h3>
                                <p class="text-[11px] text-slate-400">Pilih metode cetak yang ingin digunakan di kasir.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Bluetooth Thermal Box -->
                            <div class="p-3.5 rounded-xl border transition flex flex-col justify-between gap-3"
                                 :class="bluetoothConnected ? 'border-emerald-400 bg-emerald-50/50' : 'border-slate-200 bg-slate-50/40'">
                                <div>
                                    <div class="flex items-center gap-2.5 mb-2">
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white shrink-0"
                                             :class="bluetoothConnected ? 'bg-emerald-600' : 'bg-indigo-600'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v18l6-6-6-6 6-6-6-6M9 9l6 3-6 3" />
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-bold text-xs text-slate-900">Bluetooth Thermal</div>
                                            <div class="text-[10px] font-medium truncate"
                                                 :class="bluetoothConnected ? 'text-emerald-700' : 'text-slate-400'"
                                                 x-text="bluetoothConnected ? ('✓ ' + bluetoothDeviceName) : 'Belum terhubung'"></div>
                                        </div>
                                        <span class="w-2 h-2 rounded-full shrink-0"
                                              :class="bluetoothConnected ? 'bg-emerald-500' : 'bg-slate-300'"></span>
                                    </div>
                                    <p class="text-[11px] text-slate-500">Untuk printer thermal portabel kasir via Bluetooth di HP atau laptop.</p>
                                </div>
                                <div class="space-y-1.5">
                                    <button type="button"
                                            @click="connectBluetooth()"
                                            :disabled="isConnectingBluetooth"
                                            class="w-full py-2 px-3 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5"
                                            :class="bluetoothConnected ? 'bg-slate-800 hover:bg-slate-900 text-white' : 'bg-indigo-600 hover:bg-indigo-700 text-white'">
                                        <span x-text="bluetoothConnected ? 'Ganti Printer' : (isConnectingBluetooth ? 'Mencari...' : 'Sambungkan Bluetooth')"></span>
                                    </button>
                                    <button type="button"
                                            x-show="bluetoothConnected"
                                            @click="testPrintBluetooth()"
                                            class="w-full py-1.5 px-3 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition flex items-center justify-center gap-1.5">
                                        <span>Tes Cetak</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Universal Browser / USB Box -->
                            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/40 flex flex-col justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2.5 mb-2">
                                        <div class="w-8 h-8 rounded-xl bg-slate-700 flex items-center justify-center text-white shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-xs text-slate-900">Cetak Sistem / USB</div>
                                            <div class="text-[10px] text-emerald-600 font-bold">● Siap Pakai</div>
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-slate-500">Bekerja di semua perangkat (USB, WiFi, kabel) via dialog printer sistem bawaan.</p>
                                </div>
                                <button type="button"
                                        @click="testPrintBrowser()"
                                        class="w-full py-2 px-3 rounded-lg text-xs font-bold bg-white hover:bg-slate-100 text-slate-800 border border-slate-300 transition flex items-center justify-center gap-1.5 ">
                                    <span>Tes Cetak Sistem</span>
                                </button>
                            </div>
                        </div>

                        <!-- Status Message -->
                        <div x-show="bluetoothStatusMessage"
                             x-cloak
                             class="mt-3 p-2.5 rounded-xl bg-slate-100 border border-slate-200 text-xs text-slate-700 font-medium text-center flex items-center justify-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-ping"></span>
                            <span x-text="bluetoothStatusMessage"></span>
                        </div>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('admin.printer.update') }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <!-- Card: Ukuran Kertas -->
                        <div class="bg-white rounded-2xl p-5  border border-slate-200">
                            <div class="mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-slate-700 flex items-center justify-center text-white shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-sm text-slate-900">Ukuran Kertas</h3>
                                    <p class="text-[11px] text-slate-400">Pilih lebar kertas printer thermal Anda.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer border-2 rounded-xl p-3.5 flex items-center gap-3 transition"
                                       :class="form.receipt_paper_size === '58mm' ? 'border-indigo-600 bg-indigo-50/40 text-indigo-900' : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                                    <input type="radio" name="receipt_paper_size" value="58mm" x-model="form.receipt_paper_size" class="text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <div class="font-bold text-sm">58 mm</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">Standar thermal mini kasir</div>
                                    </div>
                                </label>
                                <label class="cursor-pointer border-2 rounded-xl p-3.5 flex items-center gap-3 transition"
                                       :class="form.receipt_paper_size === '80mm' ? 'border-indigo-600 bg-indigo-50/40 text-indigo-900' : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                                    <input type="radio" name="receipt_paper_size" value="80mm" x-model="form.receipt_paper_size" class="text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <div class="font-bold text-sm">80 mm</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">Lebar kasir desktop</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Card: Identitas Toko -->
                        <div class="bg-white rounded-2xl p-5  border border-slate-200 space-y-5">
                            <div class="pb-3 border-b border-slate-100 flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-indigo-500 flex items-center justify-center text-white shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-sm text-slate-900">Identitas Toko</h3>
                                    <p class="text-[11px] text-slate-400">Atur ikon, nama, dan kontak yang tampil di struk.</p>
                                </div>
                            </div>

                            <!-- Ikon / Logo -->
                            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 space-y-3.5">
                                <!-- Toggle header -->
                                <div class="flex items-center gap-2.5">
                                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                        <input type="checkbox" name="receipt_show_logo" value="1" x-model="form.receipt_show_logo" class="sr-only peer">
                                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                    <span class="text-xs font-bold text-slate-800">Ikon / Logo Toko</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-bold"
                                          :class="form.receipt_show_logo ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-200 text-slate-400'"
                                          x-text="form.receipt_show_logo ? 'Aktif' : 'Nonaktif'"></span>
                                </div>

                                <div x-show="form.receipt_show_logo" class="space-y-3">
                                    <!-- Pilihan Icon Cepat -->
                                    <div class="flex flex-wrap gap-1.5">
                                        <template x-for="iconOption in iconOptions" :key="iconOption.id">
                                            <button type="button"
                                                    @click="form.receipt_icon = iconOption.id"
                                                    class="px-2.5 py-1.5 rounded-lg border text-xs font-medium flex items-center gap-1.5 transition"
                                                    :class="form.receipt_icon === iconOption.id ? 'bg-indigo-600 text-white border-indigo-600 ' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                                                <span x-text="iconOption.symbol"></span>
                                                <span x-text="iconOption.label"></span>
                                            </button>
                                        </template>
                                    </div>
                                    <input type="hidden" name="receipt_icon" :value="form.receipt_icon">

                                    <!-- Upload Logo -->
                                    <div class="pt-3 border-t border-slate-200 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[11px] font-semibold text-slate-700">Logo Gambar Toko:</span>
                                            <template x-if="logoPreviewUrl && !form.remove_logo">
                                                <button type="button"
                                                        @click="form.remove_logo = true; logoPreviewUrl = null"
                                                        class="text-[10px] text-rose-500 hover:text-rose-700 font-semibold flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Hapus Logo
                                                </button>
                                            </template>
                                        </div>
                                        <input type="hidden" name="remove_logo" :value="form.remove_logo ? '1' : '0'">

                                        <div x-show="logoPreviewUrl && !form.remove_logo" class="flex items-center gap-3 p-2.5 bg-white rounded-xl border border-slate-200">
                                            <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 border-2 border-indigo-200 shrink-0 flex items-center justify-center ">
                                                <img :src="logoPreviewUrl" alt="Logo Toko" class="w-full h-full object-cover rounded-full filter grayscale contrast-125">
                                            </div>
                                            <div>
                                                <div class="text-[11px] font-bold text-slate-800">Logo Tersimpan (Bulat)</div>
                                                <div class="text-[10px] text-emerald-600 font-medium">Tampil di semua struk kasir</div>
                                            </div>
                                        </div>

                                        <input type="file" name="receipt_logo" accept="image/*" @change="previewImage($event)"
                                               class="block w-full text-xs text-slate-500 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                        <p class="text-[10px] text-slate-400">Dipotong otomatis menjadi lingkaran pada struk kasir.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Nama Toko -->
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                        <input type="checkbox" name="receipt_show_header" value="1" x-model="form.receipt_show_header" class="sr-only peer">
                                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                    <span class="text-xs font-bold text-slate-700">Nama Toko</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-bold"
                                          :class="form.receipt_show_header ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-200 text-slate-400'"
                                          x-text="form.receipt_show_header ? 'Tampil' : 'Sembunyi'"></span>
                                </div>
                                <input type="text" name="store_name" x-model="form.store_name" required
                                       class="w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Nama Toko">
                            </div>

                            <!-- Alamat -->
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                        <input type="checkbox" name="receipt_show_address" value="1" x-model="form.receipt_show_address" class="sr-only peer">
                                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                    <span class="text-xs font-bold text-slate-700">Alamat</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-bold"
                                          :class="form.receipt_show_address ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-200 text-slate-400'"
                                          x-text="form.receipt_show_address ? 'Tampil' : 'Sembunyi'"></span>
                                </div>
                                <textarea name="warehouse_address" x-model="form.warehouse_address" rows="2"
                                          class="w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                                          placeholder="Alamat Toko"></textarea>
                            </div>

                            <!-- Telepon & Email (2 col) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                            <input type="checkbox" name="receipt_show_phone" value="1" x-model="form.receipt_show_phone" class="sr-only peer">
                                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                        <span class="text-xs font-bold text-slate-700">No. Telepon / WA</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded font-bold"
                                              :class="form.receipt_show_phone ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-200 text-slate-400'"
                                              x-text="form.receipt_show_phone ? 'Tampil' : 'Sembunyi'"></span>
                                    </div>
                                    <input type="text" name="contact_phone" x-model="form.contact_phone"
                                           class="w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="08123456789">
                                </div>

                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                            <input type="checkbox" name="receipt_show_email" value="1" x-model="form.receipt_show_email" class="sr-only peer">
                                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                        <span class="text-xs font-bold text-slate-700">Email</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded font-bold"
                                              :class="form.receipt_show_email ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-200 text-slate-400'"
                                              x-text="form.receipt_show_email ? 'Tampil' : 'Sembunyi'"></span>
                                    </div>
                                    <input type="email" name="email" x-model="form.email"
                                           class="w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="toko@email.com">
                                </div>
                            </div>
                        </div>

                        <!-- Card: Elemen Transaksi -->
                        <div class="bg-white rounded-2xl p-5  border border-slate-200">
                            <div class="mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-violet-600 flex items-center justify-center text-white shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-sm text-slate-900">Elemen Transaksi</h3>
                                    <p class="text-[11px] text-slate-400">Pilih baris informasi yang tampil di struk.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <label class="p-3 rounded-xl border border-slate-200 flex items-center gap-2.5 hover:bg-slate-50 cursor-pointer transition group">
                                    <input type="checkbox" name="receipt_show_invoice" value="1" x-model="form.receipt_show_invoice" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                    <span class="text-xs font-semibold text-slate-700 group-hover:text-slate-900">Nomor Invoice</span>
                                </label>

                                <label class="p-3 rounded-xl border border-slate-200 flex items-center gap-2.5 hover:bg-slate-50 cursor-pointer transition group">
                                    <input type="checkbox" name="receipt_show_date" value="1" x-model="form.receipt_show_date" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                    <span class="text-xs font-semibold text-slate-700 group-hover:text-slate-900">Tanggal & Waktu</span>
                                </label>

                                <label class="p-3 rounded-xl border border-slate-200 flex items-center gap-2.5 hover:bg-slate-50 cursor-pointer transition group">
                                    <input type="checkbox" name="receipt_show_cashier" value="1" x-model="form.receipt_show_cashier" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                    <span class="text-xs font-semibold text-slate-700 group-hover:text-slate-900">Nama Kasir</span>
                                </label>

                                <label class="p-3 rounded-xl border border-slate-200 flex items-center gap-2.5 hover:bg-slate-50 cursor-pointer transition group">
                                    <input type="checkbox" name="receipt_show_customer" value="1" x-model="form.receipt_show_customer" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                    <span class="text-xs font-semibold text-slate-700 group-hover:text-slate-900">Nama Pelanggan</span>
                                </label>

                                <label class="p-3 rounded-xl border border-slate-200 flex items-center gap-2.5 hover:bg-slate-50 cursor-pointer transition group">
                                    <input type="checkbox" name="receipt_show_subtotal" value="1" x-model="form.receipt_show_subtotal" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                    <span class="text-xs font-semibold text-slate-700 group-hover:text-slate-900">Subtotal & Diskon</span>
                                </label>

                                <label class="p-3 rounded-xl border border-slate-200 flex items-center gap-2.5 hover:bg-slate-50 cursor-pointer transition group">
                                    <input type="checkbox" name="receipt_show_payment_method" value="1" x-model="form.receipt_show_payment_method" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                    <span class="text-xs font-semibold text-slate-700 group-hover:text-slate-900">Metode Bayar & Kembali</span>
                                </label>
                            </div>
                        </div>

                        <!-- Card: Pesan Bawah (Footer) -->
                        <div class="bg-white rounded-2xl p-5  border border-slate-200 space-y-4">
                            <div class="pb-3 border-b border-slate-100 flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-amber-500 flex items-center justify-center text-white shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-sm text-slate-900">Pesan Bawah (Footer)</h3>
                                    <p class="text-[11px] text-slate-400">Ucapan terima kasih atau catatan di bawah struk.</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="receipt_show_footer" value="1" x-model="form.receipt_show_footer" class="sr-only peer">
                                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-bold"
                                          :class="form.receipt_show_footer ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-200 text-slate-400'"
                                          x-text="form.receipt_show_footer ? 'Aktif' : 'Nonaktif'"></span>
                                </div>
                            </div>

                            <div x-show="form.receipt_show_footer" class="space-y-2">
                                <textarea name="receipt_footer_notes" x-model="form.receipt_footer_notes" rows="2"
                                          class="w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                                          placeholder="Tulis pesan..."></textarea>
                                <div class="flex flex-wrap gap-2 text-[11px]">
                                    <button type="button" @click="form.receipt_footer_notes = 'Terima Kasih Atas Kunjungan Anda!'" class="text-indigo-600 underline hover:text-indigo-800 font-medium">Ucapan Terima Kasih</button>
                                    <span class="text-slate-300">•</span>
                                    <button type="button" @click="form.receipt_footer_notes = 'Terima Kasih!\nBarang yang dibeli tidak dapat ditukar.'" class="text-indigo-600 underline hover:text-indigo-800 font-medium">+ Catatan Retur</button>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit"
                                class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm  transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Pengaturan
                        </button>
                    </form>
                </div>

                <!-- Right Column: Live Preview (5 cols) -->
                <div class="lg:col-span-5 sticky top-6">
                    <div class="bg-slate-900 rounded-3xl p-5 border border-slate-200-xl text-white">
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-800">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-indigo-400">Live Preview</span>
                                <h4 class="font-bold text-sm text-white">Pratinjau Struk</h4>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-indigo-900/60 text-indigo-300 border border-indigo-700"
                                  x-text="form.receipt_paper_size"></span>
                        </div>

                        <!-- Paper Receipt Mockup -->
                        <div class="flex justify-center p-2 bg-slate-950/60 rounded-2xl overflow-hidden">
                            <div id="receipt-preview-paper"
                                 class="bg-[#fffef7] text-black font-mono border border-slate-200-2xl p-4 sm:p-5 transition-all duration-300 relative select-none"
                                 :class="form.receipt_paper_size === '80mm' ? 'w-full max-w-[340px] text-xs' : 'w-full max-w-[275px] text-[11px]'"
                                 style="filter: drop-border border-slate-200(0 10px 15px rgba(0,0,0,0.3)); border-top: 3px dashed #d1d5db; border-bottom: 3px dashed #d1d5db;">

                                <!-- Logo / Icon -->
                                <div x-show="form.receipt_show_logo" class="text-center pb-2">
                                    <template x-if="logoPreviewUrl && !form.remove_logo">
                                        <div class="inline-block w-12 h-12 rounded-full overflow-hidden border border-black p-0.5 mx-auto mb-1">
                                            <img :src="logoPreviewUrl" alt="Logo" class="w-full h-full object-cover rounded-full filter grayscale contrast-200">
                                        </div>
                                    </template>
                                    <template x-if="!logoPreviewUrl || form.remove_logo">
                                        <div class="inline-block p-2 rounded-full border-2 border-black mb-1">
                                            <span class="text-xl" x-text="getIconSymbol(form.receipt_icon)"></span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Store Header -->
                                <div class="text-center pb-2 border-b border-dashed border-gray-400 space-y-0.5">
                                    <div x-show="form.receipt_show_header" class="font-bold text-sm tracking-wider uppercase" x-text="form.store_name || 'NAMA TOKO'"></div>
                                    <div x-show="form.receipt_show_address && form.warehouse_address" class="text-[10px] leading-tight text-gray-700" x-text="form.warehouse_address"></div>
                                    <div x-show="form.receipt_show_phone && form.contact_phone" class="text-[10px] text-gray-700" x-text="'Telp: ' + form.contact_phone"></div>
                                    <div x-show="form.receipt_show_email && form.email" class="text-[10px] text-gray-700" x-text="'Email: ' + form.email"></div>
                                </div>

                                <!-- Meta Info -->
                                <div class="py-2 border-b border-dashed border-gray-400 space-y-1 text-[10px]">
                                    <div x-show="form.receipt_show_invoice" class="flex justify-between">
                                        <span>Invoice:</span>
                                        <span class="font-bold">INV-{{ date('Ymd') }}-0042</span>
                                    </div>
                                    <div x-show="form.receipt_show_date" class="flex justify-between">
                                        <span>Waktu:</span>
                                        <span>{{ date('d/m/Y H:i') }}</span>
                                    </div>
                                    <div x-show="form.receipt_show_cashier" class="flex justify-between">
                                        <span>Kasir:</span>
                                        <span>{{ Auth::user()->name }}</span>
                                    </div>
                                    <div x-show="form.receipt_show_customer" class="flex justify-between">
                                        <span>Pelanggan:</span>
                                        <span>Pelanggan Offline</span>
                                    </div>
                                </div>

                                <!-- Items -->
                                <div class="py-2 border-b border-dashed border-gray-400 space-y-1.5">
                                    <div>
                                        <div class="truncate font-semibold">Squishy Glitter Mainan Anak</div>
                                        <div class="flex justify-between text-[10px]">
                                            <span>2 x Rp 32.000</span>
                                            <span>Rp 64.000</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="truncate font-semibold">Squishy Halloween Series</div>
                                        <div class="flex justify-between text-[10px]">
                                            <span>1 x Rp 32.000</span>
                                            <span>Rp 32.000</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Totals -->
                                <div class="py-2 space-y-1 text-[11px]">
                                    <div x-show="form.receipt_show_subtotal" class="flex justify-between">
                                        <span>Subtotal:</span>
                                        <span>Rp 96.000</span>
                                    </div>
                                    <div x-show="form.receipt_show_subtotal" class="flex justify-between text-gray-700">
                                        <span>Diskon:</span>
                                        <span>-Rp 6.000</span>
                                    </div>
                                    <div class="flex justify-between font-extrabold text-xs pt-1 border-t border-dashed border-gray-400">
                                        <span>TOTAL:</span>
                                        <span>Rp 90.000</span>
                                    </div>
                                    <div x-show="form.receipt_show_payment_method" class="flex justify-between text-[10px]">
                                        <span>Bayar (TUNAI):</span>
                                        <span>Rp 100.000</span>
                                    </div>
                                    <div x-show="form.receipt_show_payment_method" class="flex justify-between text-[10px] font-bold">
                                        <span>Kembali:</span>
                                        <span>Rp 10.000</span>
                                    </div>
                                </div>

                                <!-- Footer Notes -->
                                <div x-show="form.receipt_show_footer && form.receipt_footer_notes"
                                     class="pt-3 border-t border-dashed border-gray-400 text-center text-[10px] text-gray-700 whitespace-pre-line leading-relaxed font-semibold">
                                    <p x-text="form.receipt_footer_notes"></p>
                                </div>

                                <!-- Cut Line -->
                                <div class="mt-4 pt-2 border-t border-dotted border-gray-300 text-center text-[8px] text-gray-400 tracking-widest uppercase">
                                    -- Potong Kertas Disini --
                                </div>
                            </div>
                        </div>

                        <!-- Quick Test -->
                        <div class="mt-4 pt-4 border-t border-slate-800 flex gap-2">
                            <button type="button"
                                    @click="testPrintBrowser()"
                                    class="flex-1 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition text-center ">
                                Cetak Sampel Struk
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Hidden Print Area -->
    <div id="print-test-container" class="hidden">
        <div id="test-receipt-print-area">
            <!-- Will be dynamically populated by testPrintBrowser -->
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden !important;
            }
            #receipt-preview-paper, #receipt-preview-paper * {
                visibility: visible !important;
            }
            #receipt-preview-paper {
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                max-width: 58mm !important;
                margin: 0 !important;
                padding: 4px !important;
                background: #ffffff !important;
                color: #000000 !important;
                border: none !important;
                font-size: 11px !important;
                box-border border-slate-200: none !important;
            }
            @page {
                size: 58mm auto;
                margin: 0;
            }
        }
    </style>

    @push('scripts')
    <script>
        function printerSettingsApp() {
            return {
                form: {
                    store_name: @json($supplier->store_name ?? ''),
                    receipt_header_title: @json($supplier->receipt_header_title ?? ''),
                    warehouse_address: @json($supplier->warehouse_address ?? ''),
                    contact_phone: @json($supplier->contact_phone ?? ''),
                    email: @json($supplier->email ?? ''),
                    receipt_icon: @json($supplier->receipt_icon ?? 'shopping-bag'),
                    receipt_paper_size: @json($supplier->receipt_paper_size ?? '58mm'),
                    receipt_footer_notes: @json($supplier->receipt_footer_notes ?? "Terima Kasih Atas Kunjungan Anda!\nBarang yang sudah dibeli tidak dapat ditukar."),
                    receipt_show_logo: {{ $supplier->receipt_show_logo ? 'true' : 'false' }},
                    receipt_show_header: {{ $supplier->receipt_show_header ? 'true' : 'false' }},
                    receipt_show_address: {{ $supplier->receipt_show_address ? 'true' : 'false' }},
                    receipt_show_phone: {{ $supplier->receipt_show_phone ? 'true' : 'false' }},
                    receipt_show_email: {{ $supplier->receipt_show_email ? 'true' : 'false' }},
                    receipt_show_invoice: {{ $supplier->receipt_show_invoice ? 'true' : 'false' }},
                    receipt_show_date: {{ $supplier->receipt_show_date ? 'true' : 'false' }},
                    receipt_show_cashier: {{ $supplier->receipt_show_cashier ? 'true' : 'false' }},
                    receipt_show_customer: {{ $supplier->receipt_show_customer ? 'true' : 'false' }},
                    receipt_show_subtotal: {{ $supplier->receipt_show_subtotal ? 'true' : 'false' }},
                    receipt_show_discount: {{ $supplier->receipt_show_discount ? 'true' : 'false' }},
                    receipt_show_payment_method: {{ $supplier->receipt_show_payment_method ? 'true' : 'false' }},
                    receipt_show_change: {{ $supplier->receipt_show_change ? 'true' : 'false' }},
                    receipt_show_footer: {{ $supplier->receipt_show_footer ? 'true' : 'false' }},
                    remove_logo: false,
                },

                logoPreviewUrl: @json($supplier->receipt_logo ? asset('storage/' . $supplier->receipt_logo) : null),
                iconOptions: [
                    { id: 'shopping-bag', symbol: '🛍️', label: 'Belanja' },
                    { id: 'store', symbol: '🏪', label: 'Toko' },
                    { id: 'cart', symbol: '🛒', label: 'Troli' },
                    { id: 'star', symbol: '⭐', label: 'Bintang' },
                    { id: 'smile', symbol: '😊', label: 'Senyum' },
                    { id: 'heart', symbol: '❤️', label: 'Hati' },
                    { id: 'gift', symbol: '🎁', label: 'Hadiah' },
                ],

                // Bluetooth State
                bluetoothDevice: null,
                bluetoothCharacteristic: null,
                bluetoothConnected: false,
                bluetoothDeviceName: '',
                isConnectingBluetooth: false,
                bluetoothStatusMessage: '',

                getIconSymbol(iconId) {
                    const found = this.iconOptions.find(o => o.id === iconId);
                    return found ? found.symbol : '🛍️';
                },

                previewImage(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.form.remove_logo = false;
                        this.logoPreviewUrl = URL.createObjectURL(file);
                    }
                },

                async connectBluetooth() {
                    if (!navigator.bluetooth) {
                        alert('Browser pada perangkat ini belum mendukung Web Bluetooth API. Gunakan browser Google Chrome di Android/PC, atau gunakan opsi "Universal System Print" untuk mencetak ke semua printer.');
                        return;
                    }

                    this.isConnectingBluetooth = true;
                    this.bluetoothStatusMessage = 'Sedang mencari printer Bluetooth di sekitar Anda...';

                    try {
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

                        this.bluetoothStatusMessage = 'Menyambungkan ke ' + this.bluetoothDevice.name + '...';
                        const server = await this.bluetoothDevice.gatt.connect();
                        const services = await server.getPrimaryServices();

                        if (services.length === 0) {
                            throw new Error('Service printer tidak ditemukan.');
                        }

                        const characteristics = await services[0].getCharacteristics();
                        const writeChar = characteristics.find(c => c.properties.write || c.properties.writeWithoutResponse);

                        if (!writeChar) {
                            throw new Error('Karakteristik komunikasi printer tidak tersedia.');
                        }

                        this.bluetoothCharacteristic = writeChar;
                        this.bluetoothConnected = true;
                        this.bluetoothDeviceName = this.bluetoothDevice.name || 'Printer Bluetooth';
                        this.bluetoothStatusMessage = 'Berhasil terhubung ke ' + this.bluetoothDeviceName + '!';

                        this.bluetoothDevice.addEventListener('gattserverdisconnected', () => {
                            this.bluetoothConnected = false;
                            this.bluetoothCharacteristic = null;
                            this.bluetoothStatusMessage = 'Printer Bluetooth terputus.';
                        });

                    } catch (error) {
                        console.error('Bluetooth Connection Error:', error);
                        this.bluetoothStatusMessage = 'Koneksi batal: ' + error.message;
                    } finally {
                        this.isConnectingBluetooth = false;
                    }
                },

                async testPrintBluetooth() {
                    if (!this.bluetoothCharacteristic) {
                        alert('Silakan hubungkan printer Bluetooth terlebih dahulu.');
                        return;
                    }

                    try {
                        this.bluetoothStatusMessage = 'Mengirim data uji cetak ke ' + this.bluetoothDeviceName + '...';
                        const ESC = 0x1B;
                        const GS = 0x1D;
                        let commands = [];
                        const encoder = new TextEncoder();

                        const addText = (text) => {
                            const bytes = encoder.encode(text);
                            for (let i = 0; i < bytes.length; i++) commands.push(bytes[i]);
                        };

                        commands.push(ESC, 0x40);
                        commands.push(ESC, 0x61, 1);
                        commands.push(ESC, 0x45, 1);
                        addText((this.form.store_name || 'TOKO SAYA') + "\n");
                        commands.push(ESC, 0x45, 0);

                        if (this.form.warehouse_address) addText(this.form.warehouse_address + "\n");
                        if (this.form.contact_phone) addText("Telp: " + this.form.contact_phone + "\n");
                        if (this.form.email) addText("Email: " + this.form.email + "\n");
                        addText("--------------------------------\n");

                        commands.push(ESC, 0x61, 0);
                        addText("TES CETAK PRINTER BERHASIL!\n");
                        addText("Waktu: " + new Date().toLocaleString('id-ID') + "\n");
                        addText("Ukuran: " + this.form.receipt_paper_size + "\n");
                        addText("--------------------------------\n");

                        commands.push(ESC, 0x61, 1);
                        if (this.form.receipt_footer_notes) {
                            addText(this.form.receipt_footer_notes + "\n");
                        }
                        addText("\n\n\n");

                        commands.push(GS, 0x56, 66, 0);

                        const bytes = new Uint8Array(commands);
                        const chunkSize = 100;
                        for (let i = 0; i < bytes.length; i += chunkSize) {
                            const chunk = bytes.slice(i, i + chunkSize);
                            if (this.bluetoothCharacteristic.writeValueWithoutResponse) {
                                await this.bluetoothCharacteristic.writeValueWithoutResponse(chunk);
                            } else {
                                await this.bluetoothCharacteristic.writeValue(chunk);
                            }
                            await new Promise(r => setTimeout(r, 20));
                        }

                        this.bluetoothStatusMessage = 'Uji cetak via Bluetooth selesai!';
                    } catch (err) {
                        alert('Gagal mencetak: ' + err.message);
                        this.bluetoothStatusMessage = 'Gagal mencetak: ' + err.message;
                    }
                },

                testPrintBrowser() {
                    window.print();
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
