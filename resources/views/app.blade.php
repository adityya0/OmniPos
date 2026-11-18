<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth bg-[#F4F6F8]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'OmniPOS') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo-omnipos.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-omnipos.png') }}">

        <!-- Google Fonts: Plus Jakarta Sans (600, 700) & Inter (400, 500, 600) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased text-slate-800 bg-[#F4F6F8] min-h-screen">
        @inertia
    </body>
</html>

