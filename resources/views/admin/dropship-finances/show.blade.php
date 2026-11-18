<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dropship-finances.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 transition ">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-black text-xl text-slate-800 leading-tight">
                        Rincian Keuangan: {{ $dropshipper->name }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $dropshipper->email }} &bull; Mitra {{ ucfirst($relation->status) }}
                    </p>
                </div>
            </div>

            <!-- Tombol Aksi Admin -->
            <div class="flex items-center gap-2" x-data="{ topUpOpen: false, markAllOpen: false }">
                <!-- Tombol Top Up / Tambah Saldo Manual -->
                <button type="button"
                        @click="topUpOpen = true"
                        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs  transition">
                    Top Up Saldo
                </button>

                <!-- Tombol Tandai Lunas Seluruh Tagihan -->
                <form method="POST" action="{{ route('admin.dropship-finances.mark-paid', $dropshipper->id) }}" onsubmit="return confirm('Tandai seluruh pesanan yang belum lunas sebagai Lunas?');">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-bold text-xs transition">
                        Tandai Semua Lunas
                    </button>
                </form>

                <!-- Modal Top Up Saldo Manual -->
                <div x-show="topUpOpen"
                     x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                    <div @click.outside="topUpOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 border border-slate-200-2xl border border-slate-200 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 class="font-black text-sm text-slate-900">Tambah Saldo Manual Dropshipper</h3>
                            <button @click="topUpOpen = false" class="text-slate-400 hover:text-slate-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form method="POST" action="{{ route('admin.dropship-finances.top-up', $dropshipper->id) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Tambah Saldo (Rp)</label>
                                <input type="number" name="amount" min="1000" step="1000" required placeholder="Contoh: 500000"
                                       class="w-full text-sm rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                                <span class="text-[10px] text-slate-400 mt-1 block">Saldo deposit akan bertambah secara realtime dan dicatat pada akun dropshipper.</span>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Bukti Transfer (Opsional)</label>
                                <input type="text" name="notes" placeholder="Contoh: Transfer BCA via WhatsApp"
                                       class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                            </div>

                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" @click="topUpOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                                    Batal
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 ">
                                    Simpan & Tambah Saldo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-0">
        <div class="w-full space-y-6">

            @if (session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2 ">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- 3 Kartu Ringkasan Akun Dropshipper -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 ">
                    <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <span>Total Resi Terkirim</span>
                        <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-slate-900 mt-2">{{ number_format($totalResiTerkirim) }} <span class="text-xs font-normal text-slate-400">Paket</span></div>
                    <p class="text-[11px] text-slate-400 mt-1">Order marketplace yang sudah ber-resi</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 ">
                    <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <span>Total Biaya Modal Barang</span>
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-black text-xs border border-indigo-200 ">
                            Rp
                        </div>
                    </div>
                    <div class="text-2xl font-black text-indigo-700 mt-2">Rp {{ number_format($totalBiayaModal, 0, ',', '.') }}</div>
                    <p class="text-[11px] text-slate-400 mt-1">Akumulasi modal belanja dropship</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 ">
                    <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <span>Status Saldo / Deposit</span>
                        <div class="w-8 h-8 rounded-lg {{ $walletBalance < 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black {{ $walletBalance < 0 ? 'text-rose-600' : 'text-emerald-700' }} mt-2">
                        Rp {{ number_format($walletBalance, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] {{ $walletBalance < 0 ? 'text-rose-500 font-bold' : 'text-slate-400' }} mt-1">
                        {{ $walletBalance < 0 ? '⚠️ Saldo minus / Piutang tertunda' : 'Saldo deposit aktif untuk tempel resi' }}
                    </p>
                </div>
            </div>

            <!-- Tabel Rincian per Transaksi Pesanan -->
            <div class="bg-white rounded-2xl border border-slate-200  overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900">Rincian Transaksi Pesanan Dropship</h3>
                        <p class="text-xs text-slate-500">Histori detail kode order, resi marketplace, daftar SKU produk, dan status bayar.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3 whitespace-nowrap">Tanggal</th>
                                <th class="px-5 py-3 whitespace-nowrap">Order Code & Pelanggan</th>
                                <th class="px-5 py-3 whitespace-nowrap">No. Resi</th>
                                <th class="px-5 py-3">List SKU Barang & Qty</th>
                                <th class="px-5 py-3 text-right whitespace-nowrap">Total Modal Toko</th>
                                <th class="px-5 py-3 text-center whitespace-nowrap">Status Bayar</th>
                                <th class="px-5 py-3 text-center whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-5 py-3.5 whitespace-nowrap text-slate-500 font-medium">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <div class="font-mono font-bold text-indigo-700">{{ $order->order_code ?: $order->invoice_number }}</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $order->customer_name }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        @if ($order->shipping_receipt_number)
                                            <span class="inline-block bg-slate-100 px-2 py-0.5 rounded font-mono text-[11px] font-semibold text-slate-800 border border-slate-200">
                                                {{ $order->shipping_receipt_number }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">Belum Ada Resi</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="space-y-1">
                                            @foreach ($order->items as $item)
                                                <div class="flex items-center gap-1.5 text-[11px]">
                                                    <span class="font-bold text-slate-800">{{ $item->quantity }}x</span>
                                                    <span class="text-slate-700 truncate max-w-[180px]">{{ $item->product_name }}</span>
                                                    <span class="font-mono text-[10px] text-slate-400 bg-slate-50 px-1 rounded border border-slate-200">
                                                        #{{ $item->product_sku }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap font-bold text-slate-900">
                                        Rp {{ number_format($order->final_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                        @if ($order->payment_status === 'paid')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span>Lunas</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                <span>Pending</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                        @if ($order->payment_status !== 'paid')
                                            <form method="POST" action="{{ route('admin.dropship-finances.mark-paid', $dropshipper->id) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-[11px] border border-emerald-200 transition">
                                                    Tandai Lunas
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-300 text-[11px]">&check; Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                                        <p class="font-bold text-slate-700 text-xs">Belum Ada Transaksi Dropship</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Mitra ini belum pernah membuat pesanan dropship.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($orders->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
