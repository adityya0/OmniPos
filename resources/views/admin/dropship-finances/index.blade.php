<x-app-layout title="Keuangan Dropship">
    <x-slot name="topbar">
        <div class="flex items-center justify-between gap-4 w-full pr-2">
            <h2 class="font-bold text-base sm:text-lg text-slate-900 truncate">
                Keuangan & Saldo Dropshipper
            </h2>
            <a href="{{ route('admin.dropshippers.index') }}" class="px-3.5 py-1.5 rounded-full bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-700 hover:border-emerald-300 transition shrink-0">
                Daftar Mitra Dropshipper
            </a>
        </div>
    </x-slot>

    <div class="py-0">
        <div class="w-full space-y-6">

            @if (session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- 4 Metrik Ringkasan Keuangan Dropship (Simple Flush Outline) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <span class="text-sm font-semibold text-slate-600">Total Mitra Aktif</span>
                    <div class="text-2xl sm:text-3xl font-bold text-slate-900 mt-2">{{ $totalMitra }} <span class="text-xs font-normal text-slate-400">Mitra</span></div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <span class="text-sm font-semibold text-slate-600">Akumulasi Modal Barang</span>
                    <div class="text-2xl sm:text-3xl font-bold text-slate-900 mt-2 font-mono">Rp {{ number_format($totalAkumulasiModal, 0, ',', '.') }}</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <span class="text-sm font-semibold text-emerald-700">Total Deposit Tersedia</span>
                    <div class="text-2xl sm:text-3xl font-bold text-emerald-700 mt-2 font-mono">Rp {{ number_format($totalSaldoDeposit, 0, ',', '.') }}</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200">
                    <span class="text-sm font-semibold text-rose-700">Piutang Belum Tertagih</span>
                    <div class="text-2xl sm:text-3xl font-bold text-rose-600 mt-2 font-mono">Rp {{ number_format($totalPiutangTertunda, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Tabel Seluruh Dropshipper & Keuangan -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-sm text-slate-900">Buku Besar Rekening Mitra</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3">Nama Mitra Dropshipper</th>
                                <th class="px-5 py-3 text-center">Total Pesanan</th>
                                <th class="px-5 py-3 text-right">Akumulasi Modal Barang</th>
                                <th class="px-5 py-3 text-right">Saldo Deposit / Tagihan</th>
                                <th class="px-5 py-3 text-center">Status Hutang</th>
                                <th class="px-5 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($dropshipperStats as $stat)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-slate-900 text-xs">{{ $stat->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $stat->email }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-center font-bold text-slate-800">
                                        {{ number_format($stat->total_orders) }} <span class="text-[10px] font-normal text-slate-400">Order</span>
                                    </td>
                                    <td class="px-5 py-4 text-right font-bold text-slate-900">
                                        Rp {{ number_format($stat->total_modal_barang, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-4 text-right font-black {{ $stat->wallet_balance < 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                                        Rp {{ number_format($stat->wallet_balance, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if ($stat->financial_status === 'Ada Tagihan')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                <span>Ada Tagihan</span>
                                            </span>
                                        @elseif ($stat->financial_status === 'Surplus Saldo')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span>Saldo Aktif</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                                <span>Lancar</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <a href="{{ route('admin.dropship-finances.show', $stat->dropshipper_id) }}"
                                           class="inline-block px-3.5 py-1.5 rounded-full bg-white hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 font-semibold text-xs transition border border-slate-200 hover:border-emerald-300">
                                            Rincian Buku
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                        <p class="font-bold text-slate-700 text-xs">Belum Ada Data Mitra Dropshipper</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Daftarkan mitra dropshipper Anda untuk mulai mencatat keuangan dan pesanan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
