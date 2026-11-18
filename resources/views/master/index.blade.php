<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Hub - OmniPOS</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-omnipos.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-omnipos.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <style>body { font-family: 'Figtree', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen selection:bg-green-500 selection:text-white">

    <!-- Top Master Header -->
    <header class="bg-white border-b border-slate-200/90 px-6 py-3.5 sticky top-0 z-40 backdrop-blur-md bg-white/95 flex flex-wrap items-center justify-between gap-4 shadow-xs">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo-omnipos.png') }}" alt="OmniPOS" class="w-8 h-8 object-contain">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-sm font-black text-slate-900 tracking-tight">Master Hub</h1>
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-green-50 text-green-700 border border-green-200 rounded-md">
                        Admin
                    </span>
                </div>
                <p class="text-[11px] text-slate-400">Kelola akun toko, dropshipper, dan kemitraan.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="document.getElementById('modalTambahAkun').classList.remove('hidden')"
                    class="px-4 py-2 bg-green-600 hover:bg-green-500 active:scale-95 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                Buat Akun
            </button>
            <a href="{{ url('/') }}" target="_blank"
               class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-semibold transition shadow-xs">
                Web Utama
            </a>
            <a href="{{ route('master.logout') }}"
               class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition">
                Keluar
            </a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto p-6 space-y-6">

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <span class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider">Tersimpan</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-semibold flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Quick Summary Cards (Light Mode) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200/90 p-4 rounded-2xl shadow-xs">
                <span class="text-[10px] text-slate-400 uppercase tracking-wider font-extrabold">Toko</span>
                <div class="text-2xl font-black text-green-600 mt-1">{{ $suppliers->count() }}</div>
            </div>

            <div class="bg-white border border-slate-200/90 p-4 rounded-2xl shadow-xs">
                <span class="text-[10px] text-slate-400 uppercase tracking-wider font-extrabold">Dropshipper</span>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $dropshippers->count() }}</div>
            </div>

            <div class="bg-white border border-slate-200/90 p-4 rounded-2xl shadow-xs">
                <span class="text-[10px] text-slate-400 uppercase tracking-wider font-extrabold">Total Pengguna</span>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $suppliers->count() + $dropshippers->count() + $otherUsers->count() }}</div>
            </div>

            <div class="bg-white border border-slate-200/90 p-4 rounded-2xl shadow-xs">
                <span class="text-[10px] text-slate-400 uppercase tracking-wider font-extrabold">Kemitraan</span>
                <div class="text-2xl font-black text-amber-600 mt-1">
                    {{ $dropshippers->sum(fn($d) => $d->dropshipSuppliers->count()) }}
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <form method="GET" class="flex gap-2">
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari nama toko, email, atau dropshipper..."
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-xs">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-xs font-bold rounded-xl text-white transition shadow-xs">
                Cari
            </button>
            @if($search)
                <a href="{{ route('master.dashboard') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-xs font-bold rounded-xl text-slate-600 transition flex items-center">
                    Reset
                </a>
            @endif
        </form>

        <!-- SEKSI 1: AKUN DROPSHIPPER & RELASI SUPPLIER -->
        <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-xs">
            <div class="px-6 py-3.5 bg-slate-50/60 border-b border-slate-200/80 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                    <h2 class="text-xs font-black text-slate-900 uppercase tracking-wide">Dropshipper</h2>
                    <span class="text-[10px] font-bold px-2 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-md">
                        Maks. 5 Toko
                    </span>
                </div>
                <span class="text-xs text-slate-400 font-medium">{{ $dropshippers->count() }} Akun</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Nama Dropshipper</th>
                            <th class="px-6 py-3.5">Email</th>
                            <th class="px-6 py-3.5">Supplier Terhubung (Maks. 5)</th>
                            <th class="px-6 py-3.5 text-right">Aksi Super</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dropshippers as $d)
                        @php
                            $connectedCount = $d->dropshipSuppliers->count();
                            $isFull = $connectedCount >= 5;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $d->name }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5 font-mono">ID: #{{ $d->id }} &bull; Bergabung: {{ $d->created_at->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-600 text-xs">{{ $d->email }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $isFull ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-green-100 text-green-800 border border-green-200' }}">
                                        {{ $connectedCount }} / 5 Supplier
                                    </span>
                                    @if(!$isFull)
                                        <button onclick="openConnectModal('{{ $d->id }}', '{{ $d->name }}')" class="text-[11px] text-green-700 hover:text-green-800 font-bold hover:underline cursor-pointer">
                                            Tambah Supplier
                                        </button>
                                    @else
                                        <span class="text-[10px] text-amber-600 font-bold">(Kuota 5 Penuh)</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($d->dropshipSuppliers as $sup)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-[11px] text-slate-700 font-medium">
                                            <span>{{ $sup->store_name }}</span>
                                            <button onclick="unlinkPartner('{{ $d->id }}', '{{ $sup->id }}', '{{ $sup->store_name }}')" title="Lepas Kemitraan" class="text-rose-500 hover:text-rose-700 font-black ml-0.5 cursor-pointer">
                                                &times;
                                            </button>
                                        </span>
                                    @empty
                                        <span class="text-slate-400 italic text-[11px]">Belum terhubung ke supplier mana pun.</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                <!-- 1-Click Login As -->
                                <a href="{{ route('master.loginAs', $d->id) }}"
                                   class="inline-block px-3 py-1.5 bg-green-600 hover:bg-green-500 active:scale-95 text-white font-bold rounded-xl text-xs transition shadow-xs">
                                    Masuk Akun
                                </a>
                                <!-- Reset Password -->
                                <button onclick="promptReset('{{ $d->id }}', '{{ $d->name }}')"
                                        class="px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold rounded-xl text-xs transition shadow-xs cursor-pointer">
                                    Ganti Sandi
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400">Belum ada akun dropshipper terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SEKSI 2: AKUN TOKO & SUPPLIER -->
        <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-xs">
            <div class="px-6 py-3.5 bg-slate-50/60 border-b border-slate-200/80 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                    <h2 class="text-xs font-black text-slate-900 uppercase tracking-wide">Toko &amp; Supplier (POS)</h2>
                </div>
                <span class="text-xs text-slate-400 font-medium">{{ $suppliers->count() }} Toko</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Nama Toko &amp; Pemilik</th>
                            <th class="px-6 py-3.5">Email</th>
                            <th class="px-6 py-3.5">Inventaris &amp; Dropshipper</th>
                            <th class="px-6 py-3.5 text-right">Aksi Super</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($suppliers as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-sm">
                                    {{ $s->store_name }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Pemilik: <strong>{{ $s->owner->name ?? 'Admin' }}</strong> (ID User: #{{ $s->owner_id }})</div>
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-600 text-xs">{{ $s->email ?: ($s->owner->email ?? '-') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 bg-slate-100 rounded-lg text-slate-700 font-bold text-[11px]">
                                        {{ $s->products->count() }} Produk
                                    </span>
                                    <span class="px-2.5 py-1 bg-green-50 border border-green-200 rounded-lg text-green-800 font-bold text-[11px]">
                                        {{ $s->dropshippers->count() }} Mitra
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                @if($s->owner)
                                    <a href="{{ route('master.loginAs', $s->owner->id) }}"
                                       class="inline-block px-3 py-1.5 bg-green-600 hover:bg-green-500 active:scale-95 text-white font-bold rounded-xl text-xs transition shadow-xs">
                                        Masuk Akun
                                    </a>
                                    <button onclick="promptReset('{{ $s->owner->id }}', '{{ $s->owner->name }}')"
                                            class="px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold rounded-xl text-xs transition shadow-xs cursor-pointer">
                                        Ganti Sandi
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400">Belum ada akun supplier terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Hidden Form untuk Reset Password -->
    <form id="formReset" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="new_password" id="inputNewPass">
    </form>

    <!-- Hidden Form untuk Lepas Kemitraan -->
    <form id="formUnlink" action="{{ route('master.unlinkSupplier') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="dropshipper_id" id="unlinkDropshipperId">
        <input type="hidden" name="supplier_id" id="unlinkSupplierId">
    </form>

    <!-- Modal Hubungkan Supplier ke Dropshipper (Light Mode) -->
    <div id="modalConnectSupplier" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 max-w-md w-full rounded-3xl p-6 shadow-2xl">
            <h3 class="text-base font-black text-slate-900 mb-1">Hubungkan Supplier ke Dropshipper</h3>
            <p class="text-xs text-slate-500 mb-4" id="connectModalSubtitle"></p>
            <form action="{{ route('master.linkSupplier') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="dropshipper_id" id="connectDropshipperId">
                <div>
                    <label class="text-xs text-slate-700 block mb-1.5 font-bold">Pilih Toko / Supplier</label>
                    <select name="supplier_id" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-green-500">
                        @foreach($allSuppliersList as $asup)
                            <option value="{{ $asup->id }}">🏪 {{ $asup->store_name }} ({{ $asup->owner->name ?? 'Supplier' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modalConnectSupplier').classList.add('hidden')"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-xs rounded-xl text-slate-600 font-bold cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white font-bold text-xs rounded-xl shadow-sm shadow-green-600/20 cursor-pointer">
                        Hubungkan Mitra
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Buat Akun Baru (Light Mode) -->
    <div id="modalTambahAkun" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 max-w-md w-full rounded-3xl p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-black text-slate-900">Buat Akun Baru (Instan)</h3>
                <button type="button" onclick="document.getElementById('modalTambahAkun').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    ✕
                </button>
            </div>
            <p class="text-xs text-slate-500 mb-4">Buat akun langsung tanpa lewat form register publik.</p>
            <form action="{{ route('master.storeUser') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="text-xs text-slate-700 font-bold block mb-1">Nama Pengguna / Pemilik</label>
                    <input type="text" name="name" required placeholder="Contoh: Budi Santoso"
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="text-xs text-slate-700 font-bold block mb-1">Alamat Email</label>
                    <input type="email" name="email" required placeholder="nama@email.com"
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="text-xs text-slate-700 font-bold block mb-1">Peran Akun (Role)</label>
                    <select name="role" id="selectRole" onchange="toggleStoreNameField(this.value)" required
                            class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="dropshipper">Dropshipper (Mitra Penjual)</option>
                        <option value="admin_supplier">Admin Supplier (Pemilik Toko &amp; Stok)</option>
                    </select>
                </div>
                <div id="fieldStoreName" class="hidden">
                    <label class="text-xs text-slate-700 font-bold block mb-1">Nama Toko Supplier</label>
                    <input type="text" name="store_name" placeholder="Contoh: Toko Berkah Fashion"
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="text-xs text-slate-700 font-bold block mb-1">Password</label>
                    <input type="text" name="password" value="123456" required
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-green-500 font-mono">
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" onclick="document.getElementById('modalTambahAkun').classList.add('hidden')"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-xs rounded-xl text-slate-600 font-bold cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white font-bold text-xs rounded-xl shadow-sm shadow-green-600/20 cursor-pointer">
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function promptReset(userId, userName) {
            const newPass = prompt(`Set password baru untuk akun [${userName}]:`, '123456');
            if (newPass) {
                const form = document.getElementById('formReset');
                form.action = `{{ url($masterUrl) }}/reset-password/${userId}`;
                document.getElementById('inputNewPass').value = newPass;
                form.submit();
            }
        }

        function openConnectModal(dropshipperId, dropshipperName) {
            document.getElementById('connectDropshipperId').value = dropshipperId;
            document.getElementById('connectModalSubtitle').innerText = `Menghubungkan mitra untuk: ${dropshipperName}`;
            document.getElementById('modalConnectSupplier').classList.remove('hidden');
        }

        function unlinkPartner(dropshipperId, supplierId, storeName) {
            if (confirm(`Yakin ingin melepas kemitraan dengan supplier [${storeName}]?`)) {
                document.getElementById('unlinkDropshipperId').value = dropshipperId;
                document.getElementById('unlinkSupplierId').value = supplierId;
                document.getElementById('formUnlink').submit();
            }
        }

        function toggleStoreNameField(role) {
            const field = document.getElementById('fieldStoreName');
            if (role === 'admin_supplier') {
                field.classList.remove('hidden');
            } else {
                field.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
