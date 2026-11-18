<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Master - OmniPOS</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-omnipos.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-omnipos.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <style>body { font-family: 'Figtree', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 flex items-center justify-center min-h-screen p-4 selection:bg-green-500 selection:text-white">
    
    <!-- Background Glow -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-gradient-to-tr from-green-200/40 via-emerald-100/30 to-transparent blur-3xl rounded-full"></div>
    </div>

    <div class="max-w-md w-full bg-white border border-slate-200/90 rounded-3xl p-8 sm:p-9 shadow-xl relative overflow-hidden">

        <div class="text-center mb-7">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/logo-omnipos.png') }}" alt="OmniPOS" class="w-14 h-14 object-contain drop-shadow-xs">
            </div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-[11px] font-black uppercase tracking-wider mb-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                Master Control Hub
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Login Super Admin</h1>
            <p class="text-xs text-slate-500 mt-1">Akses khusus pemilik sistem &mdash; kelola seluruh akun tanpa batas</p>
        </div>

        @if(session('error'))
            <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-2xl text-center font-bold">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-2xl text-center font-bold">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('master.auth') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">PIN / Kunci Rahasia Master</label>
                <input type="password" name="secret_key" required autofocus placeholder="Masukkan PIN Rahasia..."
                    class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-mono font-bold">
                <p class="text-[11px] text-slate-400 mt-1.5">
                    Default PIN: <code class="text-green-700 font-mono font-bold bg-green-50 px-1.5 py-0.5 rounded border border-green-200">adminpos12345</code>
                </p>
            </div>

            <button type="submit"
                    class="w-full py-3.5 bg-green-600 hover:bg-green-500 active:scale-95 text-white font-black rounded-2xl text-sm transition-all shadow-lg shadow-green-600/25 hover:shadow-green-600/40 cursor-pointer">
                Buka Master Dashboard
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <a href="{{ url('/') }}" class="text-xs text-slate-400 hover:text-green-600 transition font-medium">
                Kembali ke Halaman Utama
            </a>
        </div>
    </div>
</body>
</html>
