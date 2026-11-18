<x-app-layout title="Laporan Penjualan">
    <span class="sr-only">Laporan Akumulasi Penjualan</span>

    @push('topbar-left')
        <div class="flex items-center gap-2 shrink-0 pl-2 border-l border-slate-200">
            <!-- Kanal Penjualan Selector -->
            <div class="flex items-center gap-0.5 bg-slate-100 p-1 rounded-xl text-xs font-semibold">
                <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['channel' => 'all'])) }}"
                   class="px-2.5 py-1 rounded-lg transition whitespace-nowrap {{ $channel === 'all' ? 'bg-white text-slate-900 font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                    Semua Kanal
                </a>
                <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['channel' => 'pos'])) }}"
                   class="px-2.5 py-1 rounded-lg transition flex items-center gap-1 whitespace-nowrap {{ $channel === 'pos' ? 'bg-white text-emerald-700 font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>POS Kasir</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $channel === 'pos' ? 'bg-emerald-100 text-emerald-800 font-bold' : 'bg-slate-200 text-slate-600' }}">
                        {{ $posMetrics['total_transactions'] }}
                    </span>
                </a>
                <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['channel' => 'dropship'])) }}"
                   class="px-2.5 py-1 rounded-lg transition flex items-center gap-1 whitespace-nowrap {{ $channel === 'dropship' ? 'bg-white text-indigo-700 font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>Dropship</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $channel === 'dropship' ? 'bg-indigo-100 text-indigo-800 font-bold' : 'bg-slate-200 text-slate-600' }}">
                        {{ $dropshipMetrics['total_transactions'] }}
                    </span>
                </a>
            </div>

            <!-- Period Selector -->
            <div class="flex items-center gap-0.5 bg-white p-1 rounded-xl border border-slate-200 text-xs font-semibold">
                <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['period' => 'today'])) }}"
                   class="px-2.5 py-1 rounded-lg transition whitespace-nowrap {{ $period === 'today' ? 'bg-emerald-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                    Hari Ini
                </a>
                <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['period' => '7days'])) }}"
                   class="px-2.5 py-1 rounded-lg transition whitespace-nowrap {{ $period === '7days' ? 'bg-emerald-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                    7 Hari
                </a>
                <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['period' => 'this_month'])) }}"
                   class="px-2.5 py-1 rounded-lg transition whitespace-nowrap {{ $period === 'this_month' ? 'bg-emerald-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                    Bulan Ini
                </a>
                <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['period' => 'all'])) }}"
                   class="px-2.5 py-1 rounded-lg transition whitespace-nowrap {{ $period === 'all' ? 'bg-emerald-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                    Semua
                </a>
            </div>
        </div>
    @endpush

    <div class="py-0" x-data="{ 
        activeTab: '{{ request('tab', 'transactions') }}',
        selectedOrder: null,
        showShiftModal: false,
        printType: 'order',
        selectedOrderIds: [],
        pageOrderIds: {{ json_encode($orders->pluck('id')) }},
        ordersMap: {{ json_encode($orders->getCollection()->keyBy('id')) }},
        transactionSearch: '{{ request('search') }}',
        profitLossSearch: '',
        trendView: 'all',
        ordersList: {{ json_encode($orders->map(fn($o) => ['text' => strtolower($o->invoice_number . ' ' . $o->customer_name . ' ' . ($o->user?->name ?? '') . ' ' . $o->payment_method . ' ' . $o->transaction_type)])) }},
        productBreakdownList: {{ json_encode($productBreakdown->map(fn($p) => ['text' => strtolower($p->product_name . ' ' . $p->product_sku)])) }},
        openOrder(id) {
            this.selectedOrder = this.ordersMap[id] || null;
        },
        setTrendView(mode) {
            this.trendView = mode;
            if (window.salesTrendChartInstance) {
                if (mode === 'all') {
                    window.salesTrendChartInstance.show(0);
                    window.salesTrendChartInstance.show(1);
                } else if (mode === 'sales') {
                    window.salesTrendChartInstance.show(0);
                    window.salesTrendChartInstance.hide(1);
                } else if (mode === 'profit') {
                    window.salesTrendChartInstance.hide(0);
                    window.salesTrendChartInstance.show(1);
                }
            }
        },
        init() {
            this.$watch('activeTab', (val) => {
                if (val === 'profit_loss' && window.productProfitChartInstance) {
                    this.$nextTick(() => window.productProfitChartInstance.resize());
                }
            });
        },
        matchesTransaction(text) {
            if (!this.transactionSearch || !this.transactionSearch.trim()) return true;
            return text.toLowerCase().includes(this.transactionSearch.toLowerCase().trim());
        },
        isTransactionsEmpty() {
            if (!this.transactionSearch || !this.transactionSearch.trim()) return false;
            const q = this.transactionSearch.toLowerCase().trim();
            return !this.ordersList.some(o => o.text.includes(q));
        },
        matchesProfitLoss(text) {
            if (!this.profitLossSearch || !this.profitLossSearch.trim()) return true;
            return text.toLowerCase().includes(this.profitLossSearch.toLowerCase().trim());
        },
        isProfitLossEmpty() {
            if (!this.profitLossSearch || !this.profitLossSearch.trim()) return false;
            const q = this.profitLossSearch.toLowerCase().trim();
            return !this.productBreakdownList.some(p => p.text.includes(q));
        },
        toggleSelectAllOrders(e) {
            if (e.target.checked) {
                this.selectedOrderIds = [...this.pageOrderIds];
            } else {
                this.selectedOrderIds = [];
            }
        },
        isAllOrdersSelected() {
            return this.pageOrderIds.length > 0 && this.selectedOrderIds.length === this.pageOrderIds.length;
        },
        printOrder(order) {
            this.printType = 'order';
            this.selectedOrder = order;
            this.$nextTick(() => {
                window.print();
            });
        },
        printShift() {
            this.printType = 'shift';
            this.$nextTick(() => {
                window.print();
            });
        },
        formatRupiah(val) {
            return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
        },
        formatDateTime(dt) {
            if (!dt) return '-';
            const d = new Date(dt);
            if (isNaN(d.getTime())) return dt;
            return d.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }) + ' ' +
                   d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }
    }">
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

            <!-- Mobile Filter Strip (Only visible on < lg screens since desktop filters are in Topbar) -->
            <div class="lg:hidden px-4 py-3 border-b border-slate-200 bg-white flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-xs font-semibold">
                    <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['channel' => 'all'])) }}"
                       class="px-2.5 py-1 rounded-lg {{ $channel === 'all' ? 'bg-white text-slate-900 font-bold' : 'text-slate-600' }}">Semua</a>
                    <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['channel' => 'pos'])) }}"
                       class="px-2.5 py-1 rounded-lg {{ $channel === 'pos' ? 'bg-white text-emerald-700 font-bold' : 'text-slate-600' }}">POS ({{ $posMetrics['total_transactions'] }})</a>
                    <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['channel' => 'dropship'])) }}"
                       class="px-2.5 py-1 rounded-lg {{ $channel === 'dropship' ? 'bg-white text-indigo-700 font-bold' : 'text-slate-600' }}">Dropship ({{ $dropshipMetrics['total_transactions'] }})</a>
                </div>
                <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 text-xs font-semibold">
                    <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['period' => 'today'])) }}"
                       class="px-2 py-1 rounded-lg {{ $period === 'today' ? 'bg-emerald-600 text-white font-bold' : 'text-slate-600' }}">Hari Ini</a>
                    <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['period' => '7days'])) }}"
                       class="px-2 py-1 rounded-lg {{ $period === '7days' ? 'bg-emerald-600 text-white font-bold' : 'text-slate-600' }}">7 Hari</a>
                    <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['period' => 'this_month'])) }}"
                       class="px-2 py-1 rounded-lg {{ $period === 'this_month' ? 'bg-emerald-600 text-white font-bold' : 'text-slate-600' }}">Bulan Ini</a>
                    <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['period' => 'all'])) }}"
                       class="px-2 py-1 rounded-lg {{ $period === 'all' ? 'bg-emerald-600 text-white font-bold' : 'text-slate-600' }}">Semua</a>
                </div>
            </div>

            <!-- 1. KPI UTAMA (4 Kolom Outline Menyatu) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 border-b border-slate-200 bg-white">
                <!-- Total Omset Penjualan -->
                <div class="bg-white p-5 border-b sm:border-b-0 sm:border-r border-slate-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Omset</span>
                            @if($channel === 'pos')
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">POS Kasir</span>
                            @elseif($channel === 'dropship')
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">Dropship</span>
                            @endif
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mt-2 font-mono">
                            Rp {{ number_format($totalSales, 0, ',', '.') }}
                        </h3>
                    </div>
                    <span class="text-xs text-slate-400 mt-2 block">Dari {{ $totalTransactions }} transaksi lunas</span>
                </div>

                <!-- Total HPP / Modal Barang -->
                <div class="bg-white p-5 border-b sm:border-b-0 lg:border-r border-slate-200 flex flex-col justify-between">
                    <div>
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Modal Barang (HPP)</span>
                        <h3 class="text-2xl font-bold text-slate-800 mt-2 font-mono">
                            Rp {{ number_format($totalCost, 0, ',', '.') }}
                        </h3>
                    </div>
                    <span class="text-xs text-slate-400 mt-2 block">{{ $totalUnitsSold }} unit barang terjual</span>
                </div>

                <!-- Laba Bersih / Net Profit -->
                <div class="bg-white p-5 border-b sm:border-b-0 sm:border-r border-slate-200 flex flex-col justify-between">
                    <div>
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-700">Laba Bersih</span>
                        <h3 class="text-2xl font-bold text-emerald-600 mt-2 font-mono">
                            Rp {{ number_format($netProfit, 0, ',', '.') }}
                        </h3>
                    </div>
                    @php
                        $margin = $totalSales > 0 ? ($netProfit / $totalSales) * 100 : 0;
                    @endphp
                    <span class="text-xs font-semibold text-emerald-600 mt-2 block">
                        Margin Laba: {{ number_format($margin, 1) }}%
                    </span>
                </div>

                <!-- Total Transaksi -->
                <div class="bg-white p-5 flex flex-col justify-between">
                    <div>
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Transaksi</span>
                        <h3 class="text-2xl font-bold text-slate-900 mt-2 font-mono">
                            {{ number_format($totalTransactions) }} <span class="text-xs font-normal text-slate-400">Struk</span>
                        </h3>
                    </div>
                    <span class="text-xs text-slate-400 mt-2 block">
                        @if($channel === 'all')
                            {{ $posMetrics['total_transactions'] }} POS Kasir &bull; {{ $dropshipMetrics['total_transactions'] }} Dropship
                        @else
                            Transaksi {{ $channel === 'pos' ? 'POS Toko' : 'Dropship' }}
                        @endif
                    </span>
                </div>
            </div>

            <!-- 2. RINGKASAN LACI KASIR (Compact 1-Baris Outline, Tanpa Header & Card Berlebihan) -->
            @if($channel !== 'dropship')
                <div class="px-5 py-3 border-b border-slate-200 bg-slate-50/60 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                        <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Rekap Kasir:
                        </span>

                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-500">Tunai (Laci):</span>
                            <span class="font-mono font-bold text-emerald-700">Rp {{ number_format($posMetrics['cash_sales'], 0, ',', '.') }}</span>
                        </div>

                        <span class="text-slate-300 hidden sm:inline">|</span>

                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-500">QRIS:</span>
                            <span class="font-mono font-bold text-slate-800">Rp {{ number_format($posMetrics['qris_sales'], 0, ',', '.') }}</span>
                        </div>

                        <span class="text-slate-300 hidden sm:inline">|</span>

                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-500">Transfer:</span>
                            <span class="font-mono font-bold text-slate-800">Rp {{ number_format($posMetrics['transfer_sales'], 0, ',', '.') }}</span>
                        </div>

                        <span class="text-slate-300 hidden sm:inline">|</span>

                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-500">Diskon:</span>
                            <span class="font-mono font-bold text-amber-700">Rp {{ number_format($posMetrics['total_discounts'], 0, ',', '.') }}</span>
                        </div>

                        <span class="text-slate-300 hidden sm:inline">|</span>

                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-500">Rata-rata (AOV):</span>
                            <span class="font-mono font-bold text-indigo-700">Rp {{ number_format($posMetrics['avg_order_value'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="button"
                            @click="showShiftModal = true"
                            class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center gap-1.5 cursor-pointer shrink-0">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Rekap Kasir</span>
                    </button>
                </div>
            @endif

            <!-- Bulk Delete Action Bar untuk Invoice Transaksi -->
            <div x-show="selectedOrderIds.length > 0"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="p-4 bg-indigo-50 border-b border-indigo-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">
                        <span x-text="selectedOrderIds.length"></span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-800 text-xs"><span x-text="selectedOrderIds.length"></span> invoice transaksi dipilih</span>
                        <p class="text-[11px] text-slate-500">Menghapus invoice akan otomatis mengembalikan kuantitas barang ke stok produk.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button"
                            @click="selectedOrderIds = []"
                            class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition">
                        Batal Pilihan
                    </button>

                    <form method="POST"
                          action="{{ route('admin.reports.orders.bulk-delete') }}"
                          @submit="return confirm('Apakah Anda yakin ingin menghapus ' + selectedOrderIds.length + ' invoice transaksi terpilih? Stok produk akan otomatis dikembalikan ke inventori toko.')">
                        @csrf
                        <template x-for="id in selectedOrderIds" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <button type="submit"
                                class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white text-xs font-bold transition">
                            Hapus <span x-text="selectedOrderIds.length"></span> Invoice Terpilih
                        </button>
                    </form>
                </div>
            </div>

            <!-- Grid 2 Kolom Grafik Sejajar: Tren Penjualan & Laba Produk -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Kolom 1: Tren Penjualan -->
                <div class="bg-white rounded-2xl border border-slate-200/90  p-4 sm:p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between gap-2 mb-3 pb-2.5 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                            <h4 class="font-bold text-sm text-slate-900">Tren Penjualan & Laba</h4>
                        </div>
                        <!-- Toggle Filter -->
                        <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-lg text-xs font-semibold">
                            <button type="button"
                                    @click="setTrendView('all')"
                                    :class="trendView === 'all' ? 'bg-white text-indigo-700  font-bold' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-2.5 py-1 rounded-md transition text-[11px] cursor-pointer">
                                Semua
                            </button>
                            <button type="button"
                                    @click="setTrendView('sales')"
                                    :class="trendView === 'sales' ? 'bg-white text-indigo-700  font-bold' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-2.5 py-1 rounded-md transition text-[11px] cursor-pointer">
                                Omset
                            </button>
                            <button type="button"
                                    @click="setTrendView('profit')"
                                    :class="trendView === 'profit' ? 'bg-white text-emerald-700  font-bold' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-2.5 py-1 rounded-md transition text-[11px] cursor-pointer">
                                Laba
                            </button>
                        </div>
                    </div>

                    @if(count($salesTrend['labels']) > 0)
                        <div class="relative w-full h-[220px]">
                            <canvas id="salesTrendChart"></canvas>
                        </div>
                    @else
                        <div class="py-14 text-center text-slate-400 text-xs font-semibold">
                            Belum ada data penjualan pada periode ini.
                        </div>
                    @endif
                </div>

                <!-- Kolom 2: Laba Rugi per Produk -->
                <div class="bg-white rounded-2xl border border-slate-200/90  p-4 sm:p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between gap-2 mb-3 pb-2.5 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <h4 class="font-bold text-sm text-slate-900">Laba Rugi per Produk</h4>
                        </div>
                        <div class="flex items-center gap-3 text-[11px] text-slate-500 font-semibold">
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Laba</span>
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-indigo-500"></span> Omset</span>
                        </div>
                    </div>

                    @if(count($productChartData['labels']) > 0)
                        <div class="relative w-full h-[220px]">
                            <canvas id="productProfitChart"></canvas>
                        </div>
                    @else
                        <div class="py-14 text-center text-slate-400 text-xs font-semibold">
                            Belum ada produk yang terjual pada periode ini.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tab Switcher Navigation (Tanpa reload halaman) -->
            <div class="bg-white p-1.5 rounded-2xl border border-slate-200  flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="button"
                            @click="activeTab = 'transactions'"
                            :class="activeTab === 'transactions' 
                                ? 'bg-indigo-600 text-white  font-bold' 
                                : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold'"
                            class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span>Riwayat Transaksi Penjualan</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full"
                              :class="activeTab === 'transactions' ? 'bg-indigo-500/60 text-white' : 'bg-slate-200 text-slate-700'">
                            {{ number_format($totalTransactions) }}
                        </span>
                    </button>

                    <button type="button"
                            @click="activeTab = 'profit_loss'"
                            :class="activeTab === 'profit_loss' 
                                ? 'bg-indigo-600 text-white  font-bold' 
                                : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold'"
                            class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Analisis Laba Rugi Produk</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full"
                              :class="activeTab === 'profit_loss' ? 'bg-indigo-500/60 text-white' : 'bg-slate-200 text-slate-700'">
                            {{ count($productBreakdown) }}
                        </span>
                    </button>
                </div>

                <div class="text-xs text-slate-400 hidden sm:block px-3">
                    Klik tab untuk beralih tampilan tabel
                </div>
            </div>

            <!-- TAB 1: Tabel Riwayat Transaksi Penjualan -->
            <div x-show="activeTab === 'transactions'"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="bg-white rounded-2xl border border-slate-200  overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col xl:flex-row xl:items-center justify-between gap-3">
                    <div>
                        <h4 class="font-bold text-sm text-slate-900">Riwayat Transaksi Penjualan</h4>
                        <p class="text-xs text-slate-500">Daftar struk transaksi POS kasir dan bukti pembayaran.</p>
                    </div>

                    <form method="GET" action="{{ route('admin.reports.index') }}" class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                        <input type="hidden" name="tab" value="transactions">
                        @if(request('period') && !$startDate && !$endDate)
                            <input type="hidden" name="period" value="{{ $period }}">
                        @endif

                        <!-- Filter Tanggal Picker -->
                        <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200/90 rounded-xl px-2.5 py-1.5 text-xs shrink-0 ">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-[11px] font-semibold text-slate-400">Tgl:</span>
                            <input type="date"
                                   name="start_date"
                                   value="{{ $startDate }}"
                                   title="Dari Tanggal"
                                   class="border-0 bg-transparent p-0 text-xs text-slate-700 focus:ring-0 font-medium">
                            <span class="text-slate-400 font-bold">&ndash;</span>
                            <input type="date"
                                   name="end_date"
                                   value="{{ $endDate }}"
                                   title="Sampai Tanggal"
                                   class="border-0 bg-transparent p-0 text-xs text-slate-700 focus:ring-0 font-medium">
                        </div>

                        <!-- Pencarian Invoice / Pelanggan (Debounce 300ms) -->
                        <div class="relative">
                            <input type="text"
                                   name="search"
                                   x-model.debounce.300ms="transactionSearch"
                                   @keydown.enter.prevent="$el.closest('form').submit()"
                                   value="{{ request('search') }}"
                                   placeholder="Cari invoice / pelanggan..."
                                   class="text-xs rounded-xl border-slate-200 pl-8 pr-7 py-1.5 focus:border-indigo-500 focus:ring-indigo-500 w-44 sm:w-56 bg-white ">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <button type="button"
                                    x-show="transactionSearch"
                                    @click="transactionSearch = ''; if ('{{ request('search') }}') { $el.closest('form').submit(); }"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Tombol Icon Filter & Cari Sejajar (Ganti teks jadi icon presisi tanpa tumpang tindih) -->
                        <button type="submit"
                                title="Terapkan Filter & Cari"
                                class="w-8 h-8 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white flex items-center justify-center shrink-0 transition ">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>

                        @if(request('search') || $startDate || $endDate)
                            <a href="{{ route('admin.reports.index', ['period' => 'this_month', 'tab' => 'transactions']) }}" 
                               title="Reset Semua Filter"
                               class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-rose-600 flex items-center justify-center shrink-0 transition border border-slate-200"
                               @click="transactionSearch = ''">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-100">
                            <tr>
                                <th class="w-10 px-4 py-3 text-center">
                                    <input type="checkbox"
                                           @change="toggleSelectAllOrders($event)"
                                           :checked="isAllOrdersSelected()"
                                           title="Pilih Semua Invoice di Halaman Ini"
                                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </th>
                                <th class="px-4 py-3">No. Invoice</th>
                                <th class="px-4 py-3">Waktu</th>
                                <th class="px-4 py-3">Kasir / User</th>
                                <th class="px-4 py-3">Pelanggan</th>
                                <th class="px-4 py-3">Metode Bayar</th>
                                <th class="px-4 py-3 text-right">Total Belanja</th>
                                <th class="px-4 py-3 text-right">Laba Bersih</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($orders as $order)
                                <tr x-show="matchesTransaction('{{ addslashes($order->invoice_number . ' ' . $order->customer_name . ' ' . ($order->user?->name ?? '') . ' ' . $order->payment_method) }}')"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    class="hover:bg-slate-50/70 transition" :class="selectedOrderIds.includes({{ $order->id }}) ? 'bg-indigo-50/40' : ''">
                                    <td class="w-10 px-4 py-3 text-center">
                                        <input type="checkbox"
                                               :value="{{ $order->id }}"
                                               x-model.number="selectedOrderIds"
                                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-mono font-bold text-indigo-700">
                                            {{ $order->invoice_number }}
                                        </div>
                                        <div class="mt-0.5">
                                            @if($order->transaction_type === 'pos' || $order->transaction_type === 'pos_offline')
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                                    🏪 POS TOKO
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                                    📦 DROPSHIP
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-500 text-[11px]">
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-slate-800">
                                        {{ $order->user?->name ?? 'Kasir' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $order->customer_name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($order->payment_method === 'cash')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                                💵 TUNAI
                                            </span>
                                        @elseif($order->payment_method === 'qris')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-extrabold bg-violet-50 text-violet-700 border border-violet-200/60">
                                                📱 QRIS
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                                💳 TRANSFER
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-900">
                                        Rp {{ number_format($order->final_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-emerald-700">
                                        +Rp {{ number_format($order->net_profit, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Tombol Rincian -->
                                            <button type="button"
                                                    @click="openOrder({{ $order->id }})"
                                                    title="Lihat Rincian Transaksi"
                                                    class="px-2.5 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition  hover: active:scale-95 cursor-pointer">
                                                Rincian
                                            </button>

                                            <!-- Tombol Cetak Cepat -->
                                            <button type="button"
                                                    @click="printOrder(ordersMap[{{ $order->id }}])"
                                                    title="Cetak Ulang Struk Thermal"
                                                    class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                            </button>

                                            <!-- Tombol Hapus Satuan -->
                                            <form method="POST"
                                                  action="{{ route('admin.reports.orders.destroy', $order) }}"
                                                  onsubmit="return confirm('Hapus invoice {{ $order->invoice_number }}? Stok produk yang terjual akan otomatis dikembalikan ke inventori.');"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        title="Hapus Invoice & Kembalikan Stok"
                                                        class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 hover:text-rose-700 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-12 text-center text-slate-400">
                                        Belum ada riwayat transaksi pada filter ini.
                                    </td>
                                </tr>
                            @endforelse

                            <!-- Tampilan jika pencarian instan tidak menemukan hasil -->
                            <tr x-show="transactionSearch && isTransactionsEmpty()" x-cloak>
                                <td colspan="9" class="px-4 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        <p class="text-xs font-semibold text-slate-600">Tidak ada transaksi yang cocok dengan pencarian "<span x-text="transactionSearch"></span>"</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Tekan ikon cari untuk mencari di seluruh database riwayat transaksi.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if ($orders->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>

            <!-- TAB 2: Breakdown Laba Rugi per Produk -->
            <div x-show="activeTab === 'profit_loss'"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="bg-white rounded-2xl border border-slate-200  overflow-hidden p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div>
                        <h4 class="font-bold text-sm text-slate-900">Analisis Laba Rugi per Produk Terlaris</h4>
                        <p class="text-xs text-slate-500">Menghitung kontribusi omset dan laba bersih dari setiap produk.</p>
                    </div>

                    <!-- Search Bar Laba Rugi Produk dengan Debounce 300ms & Icon Sejajar -->
                    <div class="flex items-center gap-1.5">
                        <div class="relative">
                            <input type="text"
                                   x-model.debounce.300ms="profitLossSearch"
                                   placeholder="Cari produk / SKU..."
                                   class="text-xs rounded-xl border-slate-200 pl-8 pr-7 py-1.5 focus:border-indigo-500 focus:ring-indigo-500 w-48 sm:w-60 bg-white ">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <button type="button"
                                    x-show="profitLossSearch"
                                    @click="profitLossSearch = ''"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Icon Cari Sejajar -->
                        <button type="button"
                                title="Cari Produk"
                                class="w-8 h-8 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white flex items-center justify-center shrink-0 transition ">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-2.5">Produk & SKU</th>
                                <th class="px-4 py-2.5 text-center">Unit Terjual</th>
                                <th class="px-4 py-2.5 text-right">Total Omset</th>
                                <th class="px-4 py-2.5 text-right">Total HPP (Modal)</th>
                                <th class="px-4 py-2.5 text-right">Laba Bersih</th>
                                <th class="px-4 py-2.5 text-center">Margin %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($productBreakdown as $item)
                                @php
                                    $itemMargin = $item->total_sales > 0 ? ($item->total_profit / $item->total_sales) * 100 : 0;
                                @endphp
                                <tr x-show="matchesProfitLoss('{{ addslashes($item->product_name . ' ' . $item->product_sku) }}')"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    class="hover:bg-slate-50/70 transition">
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-900">{{ $item->product_name }}</div>
                                        <span class="font-mono text-[10px] text-slate-400">{{ $item->product_sku }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-800">
                                        {{ $item->total_qty }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-slate-800">
                                        Rp {{ number_format($item->total_sales, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-slate-500">
                                        Rp {{ number_format($item->total_cogs, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-emerald-700">
                                        Rp {{ number_format($item->total_profit, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700">
                                            {{ number_format($itemMargin, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                        Belum ada data penjualan pada periode ini.
                                    </td>
                                </tr>
                            @endforelse

                            <!-- Tampilan jika pencarian laba rugi produk tidak menemukan hasil -->
                            <tr x-show="profitLossSearch && isProfitLossEmpty()" x-cloak>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        <p class="text-xs font-semibold text-slate-600">Tidak ada produk yang cocok dengan pencarian "<span x-text="profitLossSearch"></span>"</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>



        </div>

        <!-- ================= MODAL RINCIAN INVOICE ================= -->
        <template x-teleport="body">
            <div x-show="selectedOrder"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                <div @click.outside="selectedOrder = null"
                     class="bg-white rounded-2xl max-w-md w-full p-6 border border-slate-200-2xl border border-slate-200 text-slate-800">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-mono block">Rincian Transaksi POS</span>
                                <h4 class="font-extrabold text-base text-slate-900" x-text="selectedOrder?.invoice_number"></h4>
                            </div>
                        </div>
                        <button type="button" @click="selectedOrder = null" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center font-bold text-lg transition cursor-pointer">&times;</button>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <div>
                                <span class="text-[10px] text-slate-400 block">Pelanggan</span>
                                <span class="font-bold text-slate-800 text-xs" x-text="selectedOrder?.customer_name || 'Pelanggan Offline'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block">Metode Pembayaran</span>
                                <span class="font-extrabold uppercase text-xs"
                                      :class="selectedOrder?.payment_method === 'cash' ? 'text-emerald-700' : 'text-indigo-700'"
                                      x-text="selectedOrder?.payment_method"></span>
                            </div>
                            <div class="col-span-2 pt-1 border-t border-slate-200 flex justify-between text-[11px] text-slate-500">
                                <span>Status: <strong class="text-emerald-600 uppercase">Lunas</strong></span>
                                <span>Tipe: <strong class="text-slate-700" x-text="selectedOrder?.transaction_type || 'POS Kasir'"></strong></span>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="border border-slate-200 rounded-xl overflow-hidden mt-3">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase border-b border-slate-100">
                                    <tr>
                                        <th class="px-3 py-2">Item</th>
                                        <th class="px-2 py-2 text-center">Qty</th>
                                        <th class="px-3 py-2 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="item in (selectedOrder?.items || [])" :key="item.id">
                                        <tr>
                                            <td class="px-3 py-2">
                                                <div class="font-semibold text-slate-800 truncate" x-text="item.product_name"></div>
                                                <div class="text-[10px] font-mono text-slate-400" x-text="item.product_sku"></div>
                                            </td>
                                            <td class="px-2 py-2 text-center font-bold text-slate-700" x-text="item.quantity"></td>
                                            <td class="px-3 py-2 text-right font-medium text-slate-900">
                                                Rp <span x-text="Number(item.subtotal || 0).toLocaleString('id-ID')"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Total Breakdown -->
                        <div class="pt-2 border-t border-slate-100 space-y-1.5">
                            <div class="flex justify-between text-slate-600">
                                <span>Subtotal Belanja:</span>
                                <span>Rp <span x-text="Number(selectedOrder?.subtotal || 0).toLocaleString('id-ID')"></span></span>
                            </div>
                            <div class="flex justify-between text-slate-600" x-show="Number(selectedOrder?.discount || 0) > 0">
                                <span>Diskon / Potongan:</span>
                                <span class="text-red-500">-Rp <span x-text="Number(selectedOrder?.discount || 0).toLocaleString('id-ID')"></span></span>
                            </div>
                            <div class="flex justify-between text-sm font-black text-slate-900 pt-1.5 border-t border-slate-100">
                                <span>Total Pembayaran:</span>
                                <span class="text-indigo-700">Rp <span x-text="Number(selectedOrder?.final_amount || 0).toLocaleString('id-ID')"></span></span>
                            </div>
                            <template x-if="selectedOrder?.payment_method === 'cash'">
                                <div class="space-y-1 text-[11px] text-slate-500 bg-slate-50 p-2 rounded-lg">
                                    <div class="flex justify-between">
                                        <span>Uang Diterima:</span>
                                        <span>Rp <span x-text="Number(selectedOrder?.cash_amount || 0).toLocaleString('id-ID')"></span></span>
                                    </div>
                                    <div class="flex justify-between font-semibold text-slate-700">
                                        <span>Kembalian:</span>
                                        <span>Rp <span x-text="Number(selectedOrder?.change_amount || 0).toLocaleString('id-ID')"></span></span>
                                    </div>
                                </div>
                            </template>
                            <div class="flex justify-between text-xs font-bold text-emerald-800 bg-emerald-50 px-3 py-2 rounded-xl mt-2 border border-emerald-100">
                                <span>Estimasi Laba Bersih Transaksi:</span>
                                <span>+Rp <span x-text="Number(selectedOrder?.net_profit || 0).toLocaleString('id-ID')"></span></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 space-y-2">
                        <div class="flex items-center gap-2">
                            <!-- Tombol Cetak Ulang Struk Kasir Thermal -->
                            <button type="button"
                                    @click="printOrder(selectedOrder)"
                                    class="flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-xs transition text-center  cursor-pointer">
                                Cetak Ulang Struk
                            </button>

                            <!-- Tombol Hapus Invoice dari Modal -->
                            <form :action="'/admin/reports/orders/' + selectedOrder?.id"
                                  method="POST"
                                  @submit="return confirm('Apakah Anda yakin ingin menghapus invoice ' + selectedOrder?.invoice_number + '? Kuantitas barang yang terjual akan otomatis dikembalikan ke inventori.')"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition text-center border border-rose-200 cursor-pointer">
                                    Hapus Invoice
                                </button>
                            </form>
                        </div>

                        <button type="button"
                                @click="selectedOrder = null"
                                class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
                            Tutup Rincian
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- ================= MODAL REKAPITULASI KAS & CLOSING SHIFT KASIR ================= -->
        <template x-teleport="body">
            <div x-show="showShiftModal"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                <div @click.outside="showShiftModal = false"
                     class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 border border-slate-200-2xl border border-slate-200 text-slate-800 space-y-5 my-auto max-h-[90vh] overflow-y-auto">

                    <!-- Header Modal Shift -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-extrabold text-base text-slate-900">Rekap Kasir &amp; Serah Terima Shift</h3>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                                        CLOSING SHIFT
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Toko: <strong class="text-slate-700">{{ $supplier->store_name }}</strong> &bull; Dicetak: {{ now()->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>

                        <button type="button"
                                @click="showShiftModal = false"
                                class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center font-bold text-lg transition cursor-pointer">
                            &times;
                        </button>
                    </div>

                    <!-- Meta Kasir & Periode -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold">Kasir / User Bertugas:</span>
                            <span class="font-bold text-slate-800 text-sm">{{ Auth::user()->name }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold">Periode Rekap:</span>
                            <span class="font-bold text-indigo-700 uppercase">{{ $period === 'today' ? 'Hari Ini' : ($period === '7days' ? '7 Hari Terakhir' : ($period === 'this_month' ? 'Bulan Ini' : 'Semua Data')) }}</span>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <span class="text-[10px] text-slate-400 block font-semibold">Total Transaksi POS:</span>
                            <span class="font-extrabold text-emerald-700 text-sm">{{ $posMetrics['total_transactions'] }} Struk</span>
                        </div>
                    </div>

                    <!-- Breakdown Kas Fisik & Non-Tunai -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-400">Rincian Saldo Masuk Kasir:</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <!-- Fisik Tunai -->
                            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200">
                                <span class="text-[10px] font-bold text-emerald-800 uppercase block">💵 Tunai di Laci (Cash Drawer)</span>
                                <div class="text-lg font-black text-emerald-700 mt-1 font-mono">
                                    Rp {{ number_format($posMetrics['cash_sales'], 0, ',', '.') }}
                                </div>
                                <span class="text-[10px] text-emerald-600 block mt-0.5">Wajib sama dengan uang fisik</span>
                            </div>

                            <!-- QRIS -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] font-bold text-slate-500 uppercase block">📱 QRIS Digital</span>
                                <div class="text-lg font-black text-slate-900 mt-1 font-mono">
                                    Rp {{ number_format($posMetrics['qris_sales'], 0, ',', '.') }}
                                </div>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Masuk saldo merchant</span>
                            </div>

                            <!-- Transfer Bank -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] font-bold text-slate-500 uppercase block">💳 Transfer Rekening</span>
                                <div class="text-lg font-black text-slate-900 mt-1 font-mono">
                                    Rp {{ number_format($posMetrics['transfer_sales'], 0, ',', '.') }}
                                </div>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Masuk mutasi bank</span>
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Keuangan POS -->
                    <div class="p-4 rounded-2xl bg-slate-900 text-white space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-300">Total Omset Penjualan POS:</span>
                            <span class="font-bold font-mono">Rp {{ number_format($posMetrics['total_sales'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-300">Total Potongan Diskon Kasir:</span>
                            <span class="font-bold text-amber-400 font-mono">-Rp {{ number_format($posMetrics['total_discounts'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-300">Rata-rata Nilai Belanja (AOV):</span>
                            <span class="font-bold font-mono">Rp {{ number_format($posMetrics['avg_order_value'], 0, ',', '.') }} / transaksi</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-slate-800 text-sm font-black">
                            <span class="text-emerald-400">Estimasi Laba Bersih Toko (POS):</span>
                            <span class="text-emerald-400 font-mono">+Rp {{ number_format($posMetrics['net_profit'], 0, ',', '.') }} ({{ $posMetrics['margin_pct'] }}%)</span>
                        </div>
                    </div>

                    <!-- Top 5 Produk Kasir Terlaris -->
                    @if(count($topPosProducts) > 0)
                        <div class="space-y-2">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-400">Top 5 Produk Kasir Terlaris (Periode Ini):</h4>
                            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase border-b border-slate-100">
                                        <tr>
                                            <th class="px-3 py-2 w-8 text-center">#</th>
                                            <th class="px-3 py-2">Nama Produk</th>
                                            <th class="px-3 py-2 text-center">Qty</th>
                                            <th class="px-3 py-2 text-right">Total Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($topPosProducts as $i => $tp)
                                            <tr>
                                                <td class="px-3 py-2 text-center font-bold text-slate-400">{{ $i + 1 }}</td>
                                                <td class="px-3 py-2 font-medium text-slate-800">{{ $tp->product_name }}</td>
                                                <td class="px-3 py-2 text-center font-bold text-emerald-700">{{ $tp->total_qty }} pcs</td>
                                                <td class="px-3 py-2 text-right font-mono font-bold text-slate-900">
                                                    Rp {{ number_format($tp->total_subtotal, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Area Tanda Tangan Serah Terima -->
                    <div class="pt-2 border-t border-slate-100 grid grid-cols-2 gap-4 text-center text-xs">
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-[10px] text-slate-400 uppercase font-bold block mb-12">Diserahkan Oleh (Kasir):</span>
                            <span class="font-bold text-slate-800 block underline">{{ Auth::user()->name }}</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-[10px] text-slate-400 uppercase font-bold block mb-12">Diterima Oleh (Owner / SPV):</span>
                            <span class="font-bold text-slate-800 block">( ............................................ )</span>
                        </div>
                    </div>

                    <!-- Tombol Aksi Modal Shift -->
                    <div class="flex items-center gap-2 pt-2">
                        <button type="button"
                                @click="printShift()"
                                class="flex-1 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white font-extrabold text-xs transition flex items-center justify-center gap-2   cursor-pointer">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Cetak Slip Rekap Kasir (Thermal 58/80mm)</span>
                        </button>
                        <button type="button"
                                @click="showShiftModal = false"
                                class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                            Tutup
                        </button>
                    </div>

                </div>
            </div>
        </template>

        @php
            $iconSymbols = [
                'shopping-bag' => '🛍️',
                'store' => '🏪',
                'receipt' => '🧾',
                'star' => '⭐',
                'tag' => '🏷️',
                'heart' => '❤️',
                'gift' => '🎁',
            ];
            $receiptSymbol = $iconSymbols[$supplier->receipt_icon ?? 'shopping-bag'] ?? '🛍️';
            $paperWidthClass = ($supplier->receipt_paper_size === '80mm') ? 'max-w-[80mm]' : 'max-w-[58mm]';
        @endphp

        <!-- Print Area Khusus Cetak Ulang Struk Kasir Thermal (58mm / 80mm) -->
        <div id="receipt-print-area"
             :class="printType === 'order' ? 'print-active' : ''"
             class="bg-white border border-dashed border-slate-300 rounded-xl p-4 text-xs font-mono space-y-2 mb-4 mx-auto text-slate-900 hidden {{ $paperWidthClass }}">
            <!-- Logo / Icon Header -->
            @if ($supplier->receipt_show_logo)
                <div class="text-center pb-1">
                    @if ($supplier->receipt_logo)
                        <div class="inline-block w-12 h-12 rounded-full overflow-hidden border border-black p-0.5 mx-auto mb-1">
                            <img src="{{ asset('storage/' . $supplier->receipt_logo) }}" alt="Logo" class="w-full h-full object-cover rounded-full filter grayscale contrast-200">
                        </div>
                    @else
                        <div class="inline-block p-1.5 rounded-full border border-black mb-1">
                            <span class="text-lg">{{ $receiptSymbol }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Header Identitas Toko -->
            <div class="text-center pb-2 border-b border-dashed border-slate-300 space-y-0.5">
                @if ($supplier->receipt_show_header)
                    <div class="font-bold text-sm uppercase">{{ $supplier->receipt_header_title ?: $supplier->store_name }}</div>
                @endif
                @if ($supplier->receipt_show_address && $supplier->warehouse_address)
                    <div class="text-[10px] text-slate-600 leading-tight">{{ $supplier->warehouse_address }}</div>
                @endif
                @if ($supplier->receipt_show_phone && $supplier->contact_phone)
                    <div class="text-[10px] text-slate-600">Telp: {{ $supplier->contact_phone }}</div>
                @endif
                @if ($supplier->receipt_show_email && $supplier->email)
                    <div class="text-[10px] text-slate-600">Email: {{ $supplier->email }}</div>
                @endif
            </div>

            <!-- Meta Transaksi (Invoice, Tanggal, Kasir, Pelanggan) -->
            <div class="space-y-1 text-[11px] text-slate-600">
                @if ($supplier->receipt_show_invoice)
                    <div class="flex justify-between">
                        <span>Invoice:</span>
                        <span class="font-bold text-slate-900" x-text="selectedOrder?.invoice_number"></span>
                    </div>
                @endif
                @if ($supplier->receipt_show_date)
                    <div class="flex justify-between">
                        <span>Waktu:</span>
                        <span x-text="formatDateTime(selectedOrder?.created_at)"></span>
                    </div>
                @endif
                @if ($supplier->receipt_show_cashier)
                    <div class="flex justify-between">
                        <span>Kasir:</span>
                        <span x-text="selectedOrder?.user?.name || 'Kasir'"></span>
                    </div>
                @endif
                @if ($supplier->receipt_show_customer)
                    <div class="flex justify-between">
                        <span>Pelanggan:</span>
                        <span x-text="selectedOrder?.customer_name || 'Pelanggan Offline'"></span>
                    </div>
                @endif
            </div>

            <!-- Items List -->
            <div class="border-t border-dashed border-slate-300 pt-2 space-y-1 text-[11px]">
                <template x-for="item in (selectedOrder?.items || [])" :key="item.id">
                    <div class="flex justify-between">
                        <span class="truncate pr-2" x-text="item.quantity + 'x ' + item.product_name"></span>
                        <span class="shrink-0 font-medium" x-text="formatRupiah(item.subtotal)"></span>
                    </div>
                </template>
            </div>

            <!-- Subtotal, Diskon & Total Tagihan -->
            <div class="border-t border-dashed border-slate-300 pt-2 space-y-1 text-[11px]">
                @if ($supplier->receipt_show_subtotal)
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span x-text="formatRupiah(selectedOrder?.subtotal)"></span>
                    </div>
                @endif
                @if ($supplier->receipt_show_discount)
                    <div class="flex justify-between" x-show="Number(selectedOrder?.discount || 0) > 0">
                        <span>Diskon:</span>
                        <span x-text="'-' + formatRupiah(selectedOrder?.discount)"></span>
                    </div>
                @endif
                <div class="flex justify-between font-extrabold text-slate-900 pt-1 border-t border-slate-200 text-xs">
                    <span>TOTAL:</span>
                    <span x-text="formatRupiah(selectedOrder?.final_amount)"></span>
                </div>
                @if ($supplier->receipt_show_payment_method)
                    <div class="flex justify-between">
                        <span>Bayar (<span x-text="(selectedOrder?.payment_method || 'CASH').toUpperCase()"></span>):</span>
                        <span x-text="formatRupiah(selectedOrder?.cash_amount)"></span>
                    </div>
                @endif
                @if ($supplier->receipt_show_change)
                    <div class="flex justify-between" x-show="Number(selectedOrder?.change_amount || 0) > 0">
                        <span>Kembali:</span>
                        <span x-text="formatRupiah(selectedOrder?.change_amount)"></span>
                    </div>
                @endif
            </div>

            <!-- Footer & Pesan Ucapan -->
            @if ($supplier->receipt_show_footer && $supplier->receipt_footer_notes)
                <div class="text-center pt-2 text-[10px] text-slate-600 border-t border-dashed border-slate-300 whitespace-pre-line leading-relaxed font-semibold">
                    {{ $supplier->receipt_footer_notes }}
                </div>
            @endif
        </div>

        <!-- ================= PRINT AREA REKAP KASIR / CLOSING SHIFT (THERMAL 58mm / 80mm) ================= -->
        <div id="shift-print-area"
             :class="printType === 'shift' ? 'print-active' : ''"
             class="bg-white border border-dashed border-slate-300 rounded-xl p-4 text-xs font-mono space-y-2 mb-4 mx-auto text-slate-900 hidden {{ $paperWidthClass }}">
            <!-- Header Toko -->
            <div class="text-center pb-2 border-b border-dashed border-slate-300 space-y-0.5">
                <div class="font-bold text-sm uppercase">{{ $supplier->store_name }}</div>
                @if ($supplier->warehouse_address)
                    <div class="text-[10px] text-slate-600 leading-tight">{{ $supplier->warehouse_address }}</div>
                @endif
                @if ($supplier->contact_phone)
                    <div class="text-[10px] text-slate-600">Telp: {{ $supplier->contact_phone }}</div>
                @endif
                <div class="font-extrabold text-xs uppercase pt-1 text-slate-900">*** REKAP CLOSING SHIFT KASIR ***</div>
            </div>

            <!-- Meta Shift -->
            <div class="space-y-1 text-[11px] text-slate-700 pb-2 border-b border-dashed border-slate-300">
                <div class="flex justify-between">
                    <span>Kasir Bertugas:</span>
                    <span class="font-bold">{{ Auth::user()->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Waktu Cetak:</span>
                    <span>{{ now()->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Periode Laporan:</span>
                    <span class="font-semibold uppercase">{{ $period === 'today' ? 'Hari Ini' : ($period === '7days' ? '7 Hari' : ($period === 'this_month' ? 'Bulan Ini' : 'Semua')) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Total Struk POS:</span>
                    <span class="font-bold">{{ $posMetrics['total_transactions'] }} Struk</span>
                </div>
            </div>

            <!-- Saldo Kas Laci & Digital -->
            <div class="space-y-1.5 text-[11px] pb-2 border-b border-dashed border-slate-300">
                <div class="flex justify-between font-bold text-slate-900 text-xs">
                    <span>KAS TUNAI (LACI):</span>
                    <span>Rp {{ number_format($posMetrics['cash_sales'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-700">
                    <span>Non-Tunai QRIS:</span>
                    <span>Rp {{ number_format($posMetrics['qris_sales'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-700">
                    <span>Non-Tunai Transfer:</span>
                    <span>Rp {{ number_format($posMetrics['transfer_sales'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-700">
                    <span>Total Diskon Kasir:</span>
                    <span>-Rp {{ number_format($posMetrics['total_discounts'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-extrabold text-slate-900 pt-1 border-t border-slate-300 text-xs">
                    <span>TOTAL OMSET POS:</span>
                    <span>Rp {{ number_format($posMetrics['total_sales'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-700">
                    <span>Rata-rata (AOV):</span>
                    <span>Rp {{ number_format($posMetrics['avg_order_value'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-slate-900">
                    <span>Estimasi Laba Bersih:</span>
                    <span>+Rp {{ number_format($posMetrics['net_profit'], 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Top Produk Kasir -->
            @if(count($topPosProducts) > 0)
                <div class="pb-2 border-b border-dashed border-slate-300 text-[10px] space-y-1">
                    <div class="font-bold uppercase text-slate-800">Top 5 Produk Kasir Terlaris:</div>
                    @foreach($topPosProducts as $idx => $tp)
                        <div class="flex justify-between">
                            <span class="truncate pr-1">{{ $idx + 1 }}. {{ $tp->product_name }}</span>
                            <span class="font-medium shrink-0">{{ $tp->total_qty }}x</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Tanda Tangan Serah Terima Kasir -->
            <div class="pt-3 pb-1 text-[10px] space-y-8">
                <div class="flex justify-between text-center">
                    <div class="w-1/2">
                        <span>Diserahkan Oleh:</span>
                        <div class="h-10"></div>
                        <span class="font-bold uppercase">({{ Auth::user()->name }})</span>
                    </div>
                    <div class="w-1/2">
                        <span>Diterima Oleh:</span>
                        <div class="h-10"></div>
                        <span class="font-bold uppercase">( .................... )</span>
                    </div>
                </div>
                <div class="text-center text-[9px] text-slate-500">
                    *** OmniPOS Toko Fisik &bull; Shift Closing Record ***
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
            .print-active, .print-active * {
                visibility: visible !important;
            }
            .print-active {
                display: block !important;
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
            }
            @page {
                margin: 0;
                size: {{ $supplier->receipt_paper_size === '80mm' ? '80mm auto' : '58mm auto' }};
            }
        }
    </style>

    @push('scripts')
    <script src="{{ asset('js/chart.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js library is not available');
                return;
            }

            const formatRupiah = (val) => {
                return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
            };

            // 1. Inisialisasi Grafik Tren Penjualan & Laba (Line / Area Chart)
            const trendCanvas = document.getElementById('salesTrendChart');
            if (trendCanvas) {
                const trendData = @json($salesTrend);
                const ctx = trendCanvas.getContext('2d');

                const gradientSales = ctx.createLinearGradient(0, 0, 0, 260);
                gradientSales.addColorStop(0, 'rgba(99, 102, 241, 0.25)');
                gradientSales.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

                const gradientProfit = ctx.createLinearGradient(0, 0, 0, 260);
                gradientProfit.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
                gradientProfit.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

                window.salesTrendChartInstance = new Chart(trendCanvas, {
                    type: 'line',
                    data: {
                        labels: trendData.labels || [],
                        datasets: [
                            {
                                label: 'Omset Penjualan',
                                data: trendData.sales || [],
                                borderColor: '#6366f1',
                                backgroundColor: gradientSales,
                                borderWidth: 2.5,
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: '#6366f1',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 7,
                            },
                            {
                                label: 'Laba Bersih',
                                data: trendData.profit || [],
                                borderColor: '#10b981',
                                backgroundColor: gradientProfit,
                                borderWidth: 2.5,
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 7,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 12 },
                                padding: 10,
                                cornerRadius: 10,
                                callbacks: {
                                    label: function (context) {
                                        let label = context.dataset.label || '';
                                        if (label) label += ': ';
                                        label += formatRupiah(context.parsed.y);
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                },
                                ticks: {
                                    font: { size: 11 },
                                    color: '#64748b'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9',
                                },
                                ticks: {
                                    font: { size: 10 },
                                    color: '#64748b',
                                    callback: function (val) {
                                        if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'jt';
                                        if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'rb';
                                        return 'Rp ' + val;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 2. Inisialisasi Grafik Analisis Tiap Produk di Laba Rugi (Bar Chart)
            const prodCanvas = document.getElementById('productProfitChart');
            if (prodCanvas) {
                const prodData = @json($productChartData);

                window.productProfitChartInstance = new Chart(prodCanvas, {
                    type: 'bar',
                    data: {
                        labels: prodData.labels || [],
                        datasets: [
                            {
                                label: 'Laba Bersih',
                                data: prodData.profits || [],
                                backgroundColor: '#10b981',
                                hoverBackgroundColor: '#059669',
                                borderRadius: 6,
                                maxBarThickness: 26,
                            },
                            {
                                label: 'Total Omset',
                                data: prodData.sales || [],
                                backgroundColor: '#6366f1',
                                hoverBackgroundColor: '#4f46e5',
                                borderRadius: 6,
                                maxBarThickness: 26,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { size: 11, weight: 'bold' },
                                bodyFont: { size: 11 },
                                padding: 10,
                                cornerRadius: 10,
                                callbacks: {
                                    label: function (context) {
                                        let label = context.dataset.label || '';
                                        if (label) label += ': ';
                                        label += formatRupiah(context.parsed.y);
                                        return label;
                                    },
                                    afterBody: function (items) {
                                        if (items.length > 0) {
                                            const idx = items[0].dataIndex;
                                            const qty = prodData.qtys[idx] || 0;
                                            const margin = prodData.margins[idx] || 0;
                                            return [
                                                'Terjual: ' + qty + ' pcs',
                                                'Margin Laba: ' + margin + '%'
                                            ];
                                        }
                                        return [];
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: { size: 10 },
                                    color: '#64748b',
                                    maxRotation: 35,
                                    minRotation: 0
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9'
                                },
                                ticks: {
                                    font: { size: 10 },
                                    color: '#64748b',
                                    callback: function (val) {
                                        if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'jt';
                                        if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'rb';
                                        return 'Rp ' + val;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
