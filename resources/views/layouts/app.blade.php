<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Pendopo UTI - Wedding Venue')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Georgia&display=swap" rel="stylesheet">

    {{-- App CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />

    {{-- Calender --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    @stack('styles')
</head>
<body>
    <div id="app">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>