@props(['title' => '', 'maxWidth' => 'sm:max-w-md', 'split' => false])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ !empty($title) ? $title . ' - OmniPOS' : 'OmniPOS' }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo-omnipos.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-omnipos.png') }}">

        <!-- Google Fonts: Plus Jakarta Sans (600, 700) & Inter (400, 500, 600) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 antialiased bg-white">
        @if ($split)
            <div class="min-h-screen w-full">
                {{ $slot }}
            </div>
        @else
            <div class="min-h-screen flex flex-col sm:justify-center items-center py-8 px-4 sm:px-6 bg-slate-50">
                <div class="text-center mb-2">
                    <a href="/" class="inline-flex items-center gap-2.5 group">
                        <img src="{{ asset('images/logo-omnipos.png') }}" alt="OmniPOS" class="w-10 h-10 object-contain group-hover:scale-105 transition-transform duration-200">
                        <span class="font-bold text-xl tracking-tight text-slate-800">Omni<span class="text-emerald-600">POS</span></span>
                    </a>
                </div>

                <div class="w-full {{ $maxWidth }} mt-4 px-6 py-6 bg-white shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden sm:rounded-2xl">
                    {{ $slot }}
                </div>
            </div>
        @endif
    </body>
</html>
