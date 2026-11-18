<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Terbatas | OmniPOS</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-omnipos.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-lg w-full bg-slate-800/90 backdrop-blur-md border border-slate-700/80 rounded-3xl p-6 sm:p-8 shadow-2xl text-center relative overflow-hidden">
        <!-- Ambient Glow -->
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-48 h-48 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-48 h-48 bg-green-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 space-y-5">
            <!-- Logo & Error Badge -->
            <div class="flex items-center justify-center gap-2 mb-2">
                <img src="{{ asset('images/logo-omnipos.png') }}" alt="OmniPOS" class="w-8 h-8 object-contain">
                <span class="font-extrabold text-base tracking-tight text-white">Omni<span class="text-green-400">POS</span></span>
            </div>

            <div class="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center mx-auto shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m11-3.5a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <div>
                <span class="text-[11px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    403 &bull; Akses Dibatasi
                </span>
                <h1 class="text-xl sm:text-2xl font-black text-white mt-3">
                    Hak Akses Akun Tidak Sesuai
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed">
                    {{ $exception->getMessage() ?: 'Halaman ini memiliki pembatasan hak akses dan tidak dapat dibuka dengan akun yang sedang aktif.' }}
                </p>
            </div>

            <!-- Active User Session Info Box -->
            @auth
                <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-700/80 text-left space-y-1.5 text-xs">
                    <div class="text-[10px] uppercase font-bold text-slate-400">Sesi Login Aktif di Browser Ini:</div>
                    <div class="flex items-center justify-between gap-2">
                        <div class="font-bold text-white truncate">{{ Auth::user()->name }}</div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-green-500/20 text-green-300 border border-green-500/30">
                            {{ str_replace('_', ' ', Auth::user()->role) }}
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email }}</div>

                    <div class="pt-2 mt-2 border-t border-slate-800 text-[11px] text-amber-200/90 leading-tight">
                        @if (Auth::user()->isDropshipper())
                            💡 Akun ini bertindak sebagai <strong>Dropshipper</strong>. Halaman kasir dan kelola produk khusus untuk akun Pemilik Toko.
                        @else
                            💡 Akun ini bertindak sebagai <strong>Pemilik Toko / Staf Toko</strong>. Halaman seperti "Pilih Penyuplai" hanya untuk mitra Dropshipper.
                        @endif
                    </div>
                </div>
            @endauth

            <!-- Action Buttons -->
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-2.5 text-xs">
                <a href="{{ route('dashboard') }}"
                   class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-green-600 hover:bg-green-500 text-white font-bold transition shadow-lg shadow-green-600/20 flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Ke Dashboard Saya</span>
                </a>

                @if (session()->has('master_logged_in') || request()->cookie('master_session'))
                    <a href="{{ url('/' . \App\Http\Controllers\MasterAdminController::MASTER_URL . '/dashboard') }}"
                       class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold transition shadow-xs flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        <span>Ganti Akun (Master Hub)</span>
                    </a>
                @endif

                <button type="button"
                        onclick="window.history.back()"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 font-bold transition shadow-xs">
                    &larr; Kembali
                </button>
            </div>
        </div>
    </div>
</body>
</html>

