<x-app-layout title="Analisis Stok">
    <x-slot name="topbar">
        <div class="flex items-center justify-between gap-4 w-full pr-2">
            <h2 class="font-bold text-base sm:text-lg text-slate-900 truncate">
                Analisis Stok
            </h2>

            <div class="flex items-center gap-2 shrink-0">
                <!-- Rentang Data -->
                <div class="flex items-center gap-0.5 bg-slate-100 p-1 rounded-xl text-xs font-semibold">
                    @foreach ([7 => '7 Hari', 14 => '14 Hari', 30 => '30 Hari'] as $dVal => $dLabel)
                        <a href="{{ route('admin.stock-analytics.index', ['days' => $dVal]) }}"
                           class="px-2.5 py-1 rounded-lg transition whitespace-nowrap {{ $days === $dVal ? 'bg-emerald-600 text-white font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                            {{ $dLabel }}
                        </a>
                    @endforeach
                </div>

                <!-- Atur Parameter Lead Time & Target Hari -->
                <button type="button"
                        onclick="window.dispatchEvent(new CustomEvent('open-param-modal'))"
                        class="px-3.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 hover:text-emerald-700 border border-slate-200 hover:border-emerald-300 font-semibold text-xs rounded-full transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    <span>Parameter ({{ $leadTimeDays }}h / {{ $targetDays }}h)</span>
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Mobile Topbar Fallback -->
    <div class="lg:hidden px-4 py-3 border-b border-slate-200 bg-white flex flex-wrap items-center justify-between gap-2">
        <h2 class="font-bold text-sm text-slate-900">Analisis Stok</h2>
        <div class="flex items-center gap-1.5">
            @foreach ([7 => '7H', 14 => '14H', 30 => '30H'] as $dVal => $dLabel)
                <a href="{{ route('admin.stock-analytics.index', ['days' => $dVal]) }}"
                   class="px-2 py-1 rounded-lg text-xs font-semibold {{ $days === $dVal ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                    {{ $dLabel }}
                </a>
            @endforeach
            <button type="button"
                    onclick="window.dispatchEvent(new CustomEvent('open-param-modal'))"
                    class="px-2.5 py-1 bg-white border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg">
                Parameter
            </button>
        </div>
    </div>

    <div class="py-0"
         x-data="stockAnalyticsApp()"
         @open-param-modal.window="paramModalOpen = true">

        @if (session('success'))
            <div class="px-5 py-3 bg-emerald-50 border-b border-emerald-200 text-emerald-800 text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <!-- 1. KPI 4 KOLOM FLUSH OUTLINE (Tanpa Ikon, Tanpa Sub-teks) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 border-b border-slate-200 bg-white divide-x divide-y lg:divide-y-0 divide-slate-200">
            <div class="p-5 flex flex-col justify-between">
                <span class="text-sm font-semibold text-rose-700">Perlu Restock</span>
                <h3 class="text-2xl sm:text-3xl font-bold text-rose-600 mt-2 font-mono">{{ number_format($stats['needs_restock']) }}</h3>
            </div>
            <div class="p-5 flex flex-col justify-between">
                <span class="text-sm font-semibold text-emerald-700">Fast Moving</span>
                <h3 class="text-2xl sm:text-3xl font-bold text-emerald-700 mt-2 font-mono">{{ number_format($stats['fast_moving']) }}</h3>
            </div>
            <div class="p-5 flex flex-col justify-between">
                <span class="text-sm font-semibold text-slate-600">Slow Moving</span>
                <h3 class="text-2xl sm:text-3xl font-bold text-slate-800 mt-2 font-mono">{{ number_format($stats['slow_moving']) }}</h3>
            </div>
            <div class="p-5 flex flex-col justify-between">
                <span class="text-sm font-semibold text-slate-600">Estimasi Biaya Restock</span>
                <h3 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-2 font-mono">Rp {{ number_format($stats['estimated_cost'], 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- 2. GRAFIK 2 KOLOM FLUSH OUTLINE -->
        <div class="grid grid-cols-1 lg:grid-cols-2 border-b border-slate-200 bg-white divide-y lg:divide-y-0 lg:divide-x divide-slate-200">
            <!-- Grafik Kiri: Tren Barang Keluar -->
            <div class="p-5">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-bold text-sm text-slate-900">Tren Barang Keluar</h4>
                    <div class="flex items-center gap-3 text-[11px] font-semibold text-slate-500">
                        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> POS</span>
                        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-slate-700"></span> Dropship</span>
                    </div>
                </div>
                <div class="relative w-full h-[210px]">
                    <canvas id="stockTrendChart"></canvas>
                </div>
            </div>

            <!-- Grafik Kanan: Perbandingan Terjual vs Sisa Stok -->
            <div class="p-5">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-bold text-sm text-slate-900">Produk Tercepat vs Sisa Stok</h4>
                    <div class="flex items-center gap-3 text-[11px] font-semibold text-slate-500">
                        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Terjual</span>
                        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-slate-300"></span> Sisa Stok</span>
                    </div>
                </div>
                <div class="relative w-full h-[210px]">
                    <canvas id="stockVelocityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 3. TAB FILTER & SEARCH BAR -->
        <div class="px-5 py-3 border-b border-slate-200 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar text-xs font-semibold">
                <button type="button"
                        @click="activeTab = 'all'"
                        :class="activeTab === 'all' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                        class="px-3.5 py-1.5 rounded-xl transition cursor-pointer whitespace-nowrap">
                    Semua ({{ $products->count() }})
                </button>
                <button type="button"
                        @click="activeTab = 'restock'"
                        :class="activeTab === 'restock' ? 'bg-rose-600 text-white' : 'bg-white text-rose-700 border border-rose-200 hover:bg-rose-50'"
                        class="px-3.5 py-1.5 rounded-xl transition cursor-pointer whitespace-nowrap">
                    Perlu Restock ({{ $stats['needs_restock'] }})
                </button>
                <button type="button"
                        @click="activeTab = 'fast'"
                        :class="activeTab === 'fast' ? 'bg-emerald-600 text-white' : 'bg-white text-emerald-700 border border-emerald-200 hover:bg-emerald-50'"
                        class="px-3.5 py-1.5 rounded-xl transition cursor-pointer whitespace-nowrap">
                    Fast Moving ({{ $stats['fast_moving'] }})
                </button>
                <button type="button"
                        @click="activeTab = 'slow'"
                        :class="activeTab === 'slow' ? 'bg-slate-800 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                        class="px-3.5 py-1.5 rounded-xl transition cursor-pointer whitespace-nowrap">
                    Slow Moving ({{ $stats['slow_moving'] }})
                </button>
            </div>

            <div class="relative w-full sm:w-64">
                <input type="text"
                       x-model="search"
                       placeholder="Cari produk atau SKU..."
                       class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50/70">
            </div>
        </div>

        <!-- 4. TABEL KALKULASI & AKSI RESTOCK -->
        <div class="overflow-x-auto bg-white">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3 whitespace-nowrap">Produk</th>
                        <th class="px-4 py-3 whitespace-nowrap">Kategori Gerak</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Keluar / Hari</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Sisa Gudang</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Stok Online</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Estimasi Habis</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Saran Restock</th>
                        <th class="px-5 py-3 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="item in filteredProducts" :key="item.id">
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img :src="item.image_url" :alt="item.name" class="w-10 h-10 rounded-lg object-cover bg-slate-100 border border-slate-200 shrink-0">
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 truncate max-w-[240px]" x-text="item.name"></div>
                                        <span class="inline-block mt-0.5 font-mono text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded" x-text="item.sku"></span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span x-show="item.movement === 'fast'" class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Fast Moving</span>
                                <span x-show="item.movement === 'normal'" class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">Normal</span>
                                <span x-show="item.movement === 'slow'" class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Slow Moving</span>
                            </td>

                            <td class="px-4 py-3.5 text-right font-mono whitespace-nowrap">
                                <span class="font-bold text-slate-900" x-text="item.daily_velocity"></span>
                                <span class="text-slate-400">/ hari</span>
                            </td>

                            <td class="px-4 py-3.5 text-center font-mono font-bold whitespace-nowrap"
                                :class="item.restock_status === 'critical' ? 'text-rose-600' : (item.restock_status === 'warning' ? 'text-amber-600' : 'text-slate-900')"
                                x-text="item.stock">
                            </td>

                            <td class="px-4 py-3.5 text-center font-mono whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                      :class="item.available_online === 0 ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-slate-50 text-slate-700 border border-slate-200'"
                                      x-text="item.available_online"></span>
                            </td>

                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <span x-show="item.days_remaining !== null"
                                      class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold font-mono"
                                      :class="item.restock_status === 'critical'
                                          ? 'bg-rose-50 text-rose-700 border border-rose-200'
                                          : (item.restock_status === 'warning'
                                              ? 'bg-amber-50 text-amber-700 border border-amber-200'
                                              : 'bg-emerald-50 text-emerald-700 border border-emerald-200')"
                                      x-text="item.days_remaining + ' hari'"></span>
                                <span x-show="item.days_remaining === null" class="text-slate-400">-</span>
                            </td>

                            <td class="px-4 py-3.5 text-center font-mono whitespace-nowrap">
                                <span x-show="item.suggested_restock > 0"
                                      class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200"
                                      x-text="'+' + item.suggested_restock + ' unit'"></span>
                                <span x-show="item.suggested_restock === 0" class="text-emerald-600 font-semibold">Aman</span>
                            </td>

                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <button type="button"
                                        @click="openRestockModal(item)"
                                        class="px-3.5 py-1.5 rounded-full border border-slate-200 bg-white hover:bg-emerald-50 hover:border-emerald-300 text-slate-700 hover:text-emerald-700 font-semibold text-xs transition cursor-pointer">
                                    Restock
                                </button>
                            </td>
                        </tr>
                    </template>

                    <tr x-show="filteredProducts.length === 0">
                        <td colspan="8" class="px-5 py-12 text-center text-slate-400 text-xs">
                            Tidak ada produk pada filter ini.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- MODAL 1: ATUR PARAMETER PREDIKSI (Lead Time & Target Stok Aman) -->
        <div x-show="paramModalOpen"
             x-cloak
             class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="paramModalOpen = false"
                 class="bg-white rounded-2xl max-w-sm w-full border border-slate-200 overflow-hidden text-slate-800">
                <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="font-bold text-sm text-slate-900">Parameter Prediksi Stok</h3>
                    <button type="button" @click="paramModalOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.stock-analytics.parameters') }}" class="p-5 space-y-4 text-xs">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="days" value="{{ $days }}">

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Lama Tunggu Restock (Hari)</label>
                        <input type="number" name="stock_lead_time_days" min="1" max="60" value="{{ $leadTimeDays }}" required
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Target Ketahanan Stok (Hari)</label>
                        <input type="number" name="stock_target_days" min="2" max="90" value="{{ $targetDays }}" required
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="paramModalOpen = false" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 2: RESTOCK CEPAT (Otomatis Terisi Angka Saran) -->
        <div x-show="restockModalOpen"
             x-cloak
             class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="restockModalOpen = false"
                 class="bg-white rounded-2xl max-w-sm w-full border border-slate-200 overflow-hidden text-slate-800">
                <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="font-bold text-sm text-slate-900 truncate pr-2" x-text="'Restock: ' + (restockItem?.name || '')"></h3>
                    <button type="button" @click="restockModalOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" :action="`/admin/products/${restockItem?.id}/restock`" class="p-5 space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jumlah Tambah Stok (Unit)</label>
                        <input type="number" name="additional_stock" min="1" x-model.number="restockQty" required
                               class="w-full text-sm font-bold rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Cadangan Display Offline (Safety Stock)</label>
                        <input type="number" name="safety_stock" min="0" x-model.number="restockSafety"
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="restockModalOpen = false" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold cursor-pointer">Simpan Restock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        function stockAnalyticsApp() {
            return {
                products: @json($products),
                activeTab: 'all',
                search: '',
                paramModalOpen: false,
                restockModalOpen: false,
                restockItem: null,
                restockQty: 1,
                restockSafety: 0,

                get filteredProducts() {
                    return this.products.filter(p => {
                        if (this.activeTab === 'restock' && !p.needs_restock) return false;
                        if (this.activeTab === 'fast' && p.movement !== 'fast') return false;
                        if (this.activeTab === 'slow' && p.movement !== 'slow') return false;

                        if (this.search.trim()) {
                            const q = this.search.toLowerCase().trim();
                            return p.name.toLowerCase().includes(q) || (p.sku && p.sku.toLowerCase().includes(q));
                        }
                        return true;
                    });
                },

                openRestockModal(item) {
                    this.restockItem = item;
                    this.restockQty = item.suggested_restock > 0 ? item.suggested_restock : 10;
                    this.restockSafety = item.safety_stock ?? 0;
                    this.restockModalOpen = true;
                }
            };
        }

        document.addEventListener('DOMContentLoaded', () => {
            const trendCtx = document.getElementById('stockTrendChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: @json($trendChart['labels']),
                        datasets: [
                            {
                                label: 'POS',
                                data: @json($trendChart['pos']),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.12)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: 2
                            },
                            {
                                label: 'Dropship',
                                data: @json($trendChart['dropship']),
                                borderColor: '#334155',
                                backgroundColor: 'rgba(51, 65, 85, 0.06)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#94a3b8' } },
                            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, color: '#94a3b8', precision: 0 } }
                        }
                    }
                });
            }

            const velCtx = document.getElementById('stockVelocityChart');
            if (velCtx) {
                new Chart(velCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($velocityChart['labels']),
                        datasets: [
                            {
                                label: 'Terjual',
                                data: @json($velocityChart['sold']),
                                backgroundColor: '#10b981',
                                borderRadius: 6
                            },
                            {
                                label: 'Sisa Stok',
                                data: @json($velocityChart['stock']),
                                backgroundColor: '#cbd5e1',
                                borderRadius: 6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#64748b' } },
                            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, color: '#94a3b8', precision: 0 } }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
