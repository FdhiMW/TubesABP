<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pendopo UTI - Wedding Venue')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />

    {{-- Google Fonts: Cormorant Garamond (display) + Instrument Sans (body) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Tailwind v4 (lewat Vite). Jalankan `npm run build` / `npm run dev`. --}}
    @vite(['resources/css/app.css'])

    {{-- Aset spesifik halaman (CSS lama, FullCalendar, dll) dimuat lewat @push('styles') --}}
    @stack('styles')
</head>
<body>
    <div id="app">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>