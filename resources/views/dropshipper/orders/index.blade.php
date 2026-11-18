<x-app-layout title="Pesanan Saya">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-lg sm:text-xl text-slate-900 tracking-tight leading-snug">
                    {{ __('Pesanan Dropship Saya') }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Pantau status approval admin toko dan proses kemas barang oleh tim packing.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dropshipper.catalog.index') }}" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs  transition">
                    Buat Pesanan Baru
                </a>
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

            <!-- Tabel Pesanan Dropshipper -->
            <div class="bg-white rounded-2xl border border-slate-200  overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-sm sm:text-base text-slate-900">Riwayat Pesanan Dropship</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Setiap pesanan wajib dilengkapi resi pengiriman sebelum diproses tim packing.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3.5 whitespace-nowrap">Kode &amp; Tanggal</th>
                                <th class="px-5 py-3.5 whitespace-nowrap">Toko Supplier</th>
                                <th class="px-5 py-3.5">Barang &amp; Qty</th>
                                <th class="px-5 py-3.5 whitespace-nowrap">Ekspedisi &amp; Resi</th>
                                <th class="px-5 py-3.5 text-center whitespace-nowrap">Status Alur</th>
                                <th class="px-5 py-3.5 text-center whitespace-nowrap">Aksi / Kontrol</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-mono font-semibold text-indigo-700 text-xs">{{ $order->order_code }}</div>
                                        <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $order->created_at->format('d M Y, H:i') }} WIB</div>
                                        <div class="text-xs text-slate-600 font-medium mt-1">Penerima: {{ $order->customer_name }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap font-semibold text-slate-900">
                                        🏪 {{ $order->supplier?->store_name }}
                                    </td>
                                    <td class="px-5 py-4 min-w-[200px]">
                                        <div class="space-y-1">
                                            @foreach ($order->items as $item)
                                                <div class="text-xs text-slate-800 flex items-center justify-between">
                                                    <span class="font-normal truncate max-w-[180px]">{{ $item->product_name }}</span>
                                                    <span class="font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 text-xs">
                                                        {{ $item->quantity }}x
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="space-y-1">
                                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $order->courier_name ?: 'Kurir Marketplace' }}
                                            </span>
                                            <div class="font-mono text-xs font-semibold text-slate-900">
                                                {{ $order->shipping_receipt_number }}
                                            </div>
                                            @if($order->shipping_label_file)
                                                <a href="{{ asset('storage/' . $order->shipping_label_file) }}" target="_blank" class="inline-block text-xs text-indigo-600 hover:underline font-medium">
                                                    Lihat Label
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        @if ($order->status === 'menunggu_approval')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                                                Menunggu Approval
                                            </span>
                                        @elseif ($order->status === 'menunggu_packing')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                Antrean Packing
                                            </span>
                                        @elseif ($order->status === 'dipacking')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                Sedang Dikemas
                                            </span>
                                        @elseif (in_array($order->status, ['dikirim', 'selesai']))
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Sudah Dikirim
                                            </span>
                                        @elseif ($order->status === 'dibatalkan')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                                Dibatalkan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        @if (in_array($order->status, ['menunggu_approval', 'menunggu_packing']))
                                            <form method="POST" action="{{ route('dropshipper.orders.cancel', $order) }}"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan {{ $order->order_code }}? Stok barang akan otomatis dikembalikan ke inventori toko supplier.');"
                                                  class="inline">
                                                @csrf
                                                <button type="submit"
                                                        title="Batalkan pesanan sebelum dipacking"
                                                        class="px-3 py-1.5 rounded-xl border border-rose-200 bg-rose-50/70 hover:bg-rose-100 text-rose-700 font-semibold text-xs transition  hover: active:scale-95 cursor-pointer">
                                                    Batalkan
                                                </button>
                                            </form>
                                        @elseif ($order->status === 'dibatalkan')
                                            <span class="text-xs text-slate-400 font-normal">Stok Dikembalikan</span>
                                        @else
                                            <span class="text-xs text-slate-400 font-normal">Paket Berjalan</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                        <p class="font-semibold text-slate-700 text-sm">Belum Ada Pesanan Dropship</p>
                                        <p class="text-xs text-slate-400 mt-1">Buka katalog produk untuk memilih barang dan meneruskan order pembeli dengan resi.</p>
                                        <a href="{{ route('dropshipper.catalog.index') }}" class="mt-3 inline-block px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold">
                                            Buka Katalog Produk
                                        </a>
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

