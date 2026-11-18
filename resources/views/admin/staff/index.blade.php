<x-app-layout title="Kelola Staf Toko">
    <div class="py-0"
         x-data="{ addStaffOpen: {{ $errors->any() ? 'true' : 'false' }} }"
         @open-add-staff.window="addStaffOpen = true">
        <div class="w-full space-y-6">

            {{-- Flash Messages --}}
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

            {{-- Daftar Staf Toko Table Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
                    <h3 class="font-bold text-sm text-slate-900">Daftar Akun Staf Toko</h3>
                    <button type="button"
                            @click="addStaffOpen = true"
                            title="Tambah Staf Toko"
                            class="w-9 h-9 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white transition flex items-center justify-center shrink-0 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                        <span class="sr-only">Tambah Staf</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3">Nama Staf</th>
                                <th class="px-5 py-3">Email Login</th>
                                <th class="px-5 py-3 text-center">Divisi / Peran</th>
                                <th class="px-5 py-3 text-center">Tgl Dibuat</th>
                                <th class="px-5 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($staffList as $staff)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-5 py-4 font-bold text-slate-900">
                                        {{ $staff->name }}
                                        @if ($staff->id === Auth::id())
                                            <span class="ml-1 text-[10px] font-normal text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">(Anda)</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-slate-500 font-mono">
                                        {{ $staff->email }}
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if ($staff->role === 'tim_packing')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                📦 Tim Packing
                                            </span>
                                        @elseif ($staff->role === 'tim_media')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                                📸 Tim Media
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                Owner Toko
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center text-slate-400">
                                        {{ $staff->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if ($staff->id !== Auth::id())
                                            <form method="POST" action="{{ route('admin.staff.destroy', $staff) }}" onsubmit="return confirm('Hapus akun staf {{ $staff->name }}?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold text-rose-600 hover:text-rose-800 hover:bg-rose-50 transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-300 text-[11px]">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                                        <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mb-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                        </div>
                                        <p class="font-bold text-slate-700 text-xs">Belum Ada Staf Tambahan</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Tambahkan akun staf khusus Tim Media atau Tim Packing.</p>
                                        <button type="button" @click="addStaffOpen = true" class="mt-3 px-3.5 py-1.5 rounded-xl bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700 transition">
                                            + Tambah Staf Baru
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($staffList->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $staffList->links() }}
                    </div>
                @endif
            </div>

        </div>

        {{-- MODAL TAMBAH STAF TOKO (Teleported to body for guaranteed top layer) --}}
        <template x-teleport="body">
            <div x-show="addStaffOpen"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                
                <div @click.outside="addStaffOpen = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                     class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 border border-slate-200-2xl border border-slate-200 space-y-4 max-h-[90vh] overflow-y-auto">
                    
                    {{-- Modal Header --}}
                    <div class="flex items-start justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-700 font-bold flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-black text-base text-slate-900">Tambah Akun Staf Baru</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Buat akses operasional toko untuk tim Anda.</p>
                            </div>
                        </div>
                        <button type="button" @click="addStaffOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="staff_name" class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Staf *</label>
                            <input type="text" id="staff_name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Rian - Tim Packing"
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 ">
                            @error('name')
                                <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="staff_email" class="block text-xs font-bold text-slate-700 mb-1">Email Login *</label>
                            <input type="email" id="staff_email" name="email" value="{{ old('email') }}" required placeholder="Contoh: rian.packing@toko.test"
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 ">
                            @error('email')
                                <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="staff_password" class="block text-xs font-bold text-slate-700 mb-1">Password Baru *</label>
                            <input type="password" id="staff_password" name="password" required placeholder="Minimal 6 karakter"
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 ">
                            @error('password')
                                <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="staff_role" class="block text-xs font-bold text-slate-700 mb-1">Divisi / Peran Staf *</label>
                            <select id="staff_role" name="role" required
                                    class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 ">
                                <option value="tim_packing" {{ old('role') === 'tim_packing' ? 'selected' : '' }}>
                                    📦 Tim Packing (Fokus Antrean Packing & Update Resi)
                                </option>
                                <option value="tim_media" {{ old('role') === 'tim_media' ? 'selected' : '' }}>
                                    📸 Tim Media (Fokus Input Foto & Manajemen Produk)
                                </option>
                            </select>
                            @error('role')
                                <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                            <button type="button" @click="addStaffOpen = false"
                                    class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700  transition">
                                Simpan Akun Staf
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
