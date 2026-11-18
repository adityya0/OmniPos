<x-app-layout title="Mitra Dropshipper">
    <div class="py-0" x-data="{ addOpen: false }">
        <!-- Modal Tambah Mitra -->
        <div x-show="addOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div @click.outside="addOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-sm text-slate-900">Tambah Mitra Dropshipper</h3>
                    <button type="button" @click="addOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.dropshippers.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Toko / Mitra Dropship</label>
                        <input type="text" name="name" required placeholder="Contoh: Toko Berkah Toys" class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email Akun</label>
                        <input type="email" name="email" required placeholder="Contoh: berkahtoys@dropshipper.test" class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                        <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Saldo Deposit Awal (Rp)</label>
                        <input type="number" name="wallet_balance" min="0" step="1000" placeholder="0" class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div class="flex justify-end gap-2 pt-3">
                        <button type="button" @click="addOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 cursor-pointer">Simpan Mitra</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="w-full space-y-6">

            @if (session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2 ">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2 ">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Banner Notifikasi Pengajuan Pelepasan Menunggu Persetujuan --}}
            @if ($pendingDisconnectCount > 0)
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs font-semibold flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 ">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
                        <span>
                            Terdapat <strong>{{ $pendingDisconnectCount }} permohonan pelepasan kemitraan</strong> dari dropshipper yang menunggu persetujuan Anda.
                        </span>
                    </div>
                    <span class="text-[11px] font-normal text-amber-700">
                        Kedua belah pihak wajib menyetujui sebelum kemitraan resmi diputus.
                    </span>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h3 class="font-bold text-base text-slate-900">Daftar Mitra Terdaftar</h3>

                    <div class="flex items-center gap-2">
                        <form method="GET" action="{{ route('admin.dropshippers.index') }}" class="flex items-center gap-2">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email..."
                                   class="text-xs rounded-full px-3.5 border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50">
                            <button type="submit" class="px-4 py-2 rounded-full bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs transition cursor-pointer">
                                Cari
                            </button>
                        </form>

                        <button type="button"
                                @click="addOpen = true"
                                title="Tambah Mitra Dropshipper"
                                class="w-9 h-9 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white transition flex items-center justify-center shrink-0 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="sr-only">Tambah Mitra Dropshipper</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3">Mitra Dropshipper</th>
                                <th class="px-5 py-3 text-right">Saldo Deposit</th>
                                <th class="px-5 py-3 text-center">Status Akses</th>
                                <th class="px-5 py-3 text-center">Tgl Bergabung</th>
                                <th class="px-5 py-3 text-center">Aksi Persetujuan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($partners as $partner)
                                @php
                                    $isPendingDisconnect = ($partner->status === 'pending_disconnect');
                                    $el = $partnerEligibility[$partner->id] ?? null;
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition {{ $isPendingDisconnect ? 'bg-amber-50/30' : '' }}">
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-slate-900 text-xs">{{ $partner->dropshipper->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $partner->dropshipper->email }}</div>

                                        @if ($isPendingDisconnect)
                                            <div class="mt-2 p-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-[11px] space-y-0.5">
                                                <div class="font-bold flex items-center gap-1 text-amber-900">
                                                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    <span>Memohon Pelepasan Kemitraan</span>
                                                </div>
                                                <div>Alasan: <em>"{{ $partner->disconnect_reason ?: 'Tidak ada alasan' }}"</em></div>
                                                <div class="text-[10px] text-slate-500">
                                                    Diajukan: {{ $partner->disconnect_requested_at?->translatedFormat('d M Y H:i') ?? '-' }}
                                                    @if ($el && $el['is_orders_cleared'])
                                                        &bull; <span class="text-emerald-700 font-semibold">✓ 0 Pesanan Aktif</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right font-black {{ $partner->wallet_balance < 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                                        Rp {{ number_format($partner->wallet_balance, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if ($isPendingDisconnect)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300 ">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                <span>Menunggu Persetujuan Lepas</span>
                                            </span>
                                        @elseif ($partner->status === 'approved')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span>Approved</span>
                                            </span>
                                        @elseif ($partner->status === 'pending')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                <span>Pending</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span>Rejected</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center text-slate-500">
                                        {{ $partner->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @if ($isPendingDisconnect)
                                                <form method="POST" action="{{ route('admin.dropshippers.approve-disconnect', $partner->id) }}" onsubmit="return confirm('Setujui pemutusan kemitraan dengan {{ $partner->dropshipper->name }}? Hubungan kemitraan resmi diakhiri.');" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs  transition">
                                                        Setujui Lepas
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.dropshippers.reject-disconnect', $partner->id) }}" onsubmit="return confirm('Tolak permohonan pelepasan kemitraan ini?');" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition">
                                                        Tolak
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('admin.dropship-finances.show', $partner->dropshipper_id) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                                    Buku Keuangan
                                                </a>
                                                <span class="text-slate-300">&bull;</span>
                                                <form method="POST" action="{{ route('admin.dropshippers.status', $partner->id) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    @if ($partner->status === 'approved')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 font-semibold" onclick="return confirm('Nonaktifkan akses kemitraan ini?');">
                                                            Suspend
                                                        </button>
                                                    @else
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800 font-semibold">
                                                            Setujui
                                                        </button>
                                                    @endif
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                                        <p class="font-bold text-slate-700 text-xs">Tidak Ada Mitra Dropshipper</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($partners->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $partners->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
