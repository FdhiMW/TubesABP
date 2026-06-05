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
    @php($navHero = trim($__env->yieldContent('nav-mode')) === 'hero')

    {{-- ===================== NAVBAR GLOBAL =====================
         Satu navbar untuk semua halaman (didefinisikan di layout).
         - Halaman beranda: mode "hero" (transparan di atas, solid saat scroll).
         - Halaman lain: solid (bg-forest-deep) sejak awal.
    ========================================================== --}}
    <nav id="navbar"
         data-hero="{{ $navHero ? 'true' : 'false' }}"
         data-at-top="true"
         data-home="{{ route('home') }}"
         class="fixed inset-x-0 top-0 z-50 transition-all duration-300 {{ $navHero ? '' : 'bg-forest-deep/95 shadow-lg backdrop-blur' }}">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between site-px">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('asset/images/pendopoutinobg.png') }}"
                     alt="Pendopo UTI"
                     class="h-11 w-auto object-contain sm:h-12">
            </a>

            {{-- Menu desktop --}}
            <ul class="hidden items-center gap-7 text-sm font-medium tracking-wide text-white lg:flex xl:gap-9">
                <li><a href="{{ route('home') }}" class="transition-colors hover:text-gold-soft {{ request()->routeIs('home') ? 'text-gold-soft' : '' }}">Home</a></li>
                <li><a href="{{ route('home') }}#facilities" class="transition-colors hover:text-gold-soft">Facilities</a></li>
                <li><a href="{{ route('booking.create') }}" class="transition-colors hover:text-gold-soft {{ request()->routeIs('booking.*') ? 'text-gold-soft' : '' }}">Booking</a></li>
                <li><a href="{{ route('manage.index') }}" class="transition-colors hover:text-gold-soft {{ request()->routeIs('manage.*') ? 'text-gold-soft' : '' }}">Manage</a></li>
                <li><a href="#" id="openChatbot" class="transition-colors hover:text-gold-soft">AI Chatbot</a></li>

                <x-admin-link variant="hero" />

                @auth
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex h-10 items-center rounded-full border border-white/40 px-6 text-white transition-colors hover:border-gold-soft hover:text-gold-soft">
                                Logout
                            </button>
                        </form>
                    </li>
                @else
                    <li>
                        <a href="{{ route('login') }}"
                           class="inline-flex h-10 items-center rounded-full bg-gold px-6 text-white transition-colors hover:bg-gold-soft">
                            Login
                        </a>
                    </li>
                @endauth
            </ul>

            {{-- Hamburger (mobile) --}}
            <button id="navToggle" type="button"
                    class="-mr-2 flex h-10 w-10 items-center justify-center rounded-md text-white lg:hidden"
                    aria-label="Buka menu" aria-expanded="false">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                </svg>
            </button>
        </div>

        {{-- Menu mobile (overlay) --}}
        <div id="navMenu" class="hidden border-t border-white/10 bg-forest-deep/95 backdrop-blur lg:hidden">
            <ul class="flex flex-col gap-1 px-5 py-4 text-white">
                <li><a href="{{ route('home') }}" class="block rounded-md px-3 py-3 transition-colors hover:bg-white/10">Home</a></li>
                <li><a href="{{ route('home') }}#facilities" class="block rounded-md px-3 py-3 transition-colors hover:bg-white/10">Facilities</a></li>
                <li><a href="{{ route('booking.create') }}" class="block rounded-md px-3 py-3 transition-colors hover:bg-white/10">Booking</a></li>
                <li><a href="{{ route('manage.index') }}" class="block rounded-md px-3 py-3 transition-colors hover:bg-white/10">Manage</a></li>
                <li><a href="#" id="openChatbotMobile" class="block rounded-md px-3 py-3 transition-colors hover:bg-white/10">AI Chatbot</a></li>
                @auth
                    @if(auth()->user()->isAdmin())
                        <li><a href="{{ route('admin.dashboard') }}" class="block rounded-md px-3 py-3 transition-colors hover:bg-white/10">🛡️ Admin Panel</a></li>
                    @endif
                    <li class="px-3 pt-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-full border border-white/40 px-5 py-2.5 transition-colors hover:border-gold-soft hover:text-gold-soft">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="px-3 pt-2">
                        <a href="{{ route('login') }}" class="block rounded-full bg-gold px-5 py-2.5 text-center transition-colors hover:bg-gold-soft">Login</a>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>

    <main class="@yield('main-class', 'pt-20')">
        @yield('content')
    </main>

    {{-- Perilaku navbar global (scroll-to-solid untuk hero, hamburger, fallback chatbot) --}}
    <script>
    (function () {
        const navbar = document.getElementById('navbar');
        if (!navbar) return;

        if (navbar.dataset.hero === 'true') {
            const setNav = () => {
                const atTop = window.scrollY < 40;
                navbar.dataset.atTop = atTop;
                navbar.classList.toggle('bg-forest-deep/95', !atTop);
                navbar.classList.toggle('shadow-lg', !atTop);
                navbar.classList.toggle('backdrop-blur', !atTop);
            };
            setNav();
            window.addEventListener('scroll', setNav, { passive: true });
        }

        const navToggle = document.getElementById('navToggle');
        const navMenu = document.getElementById('navMenu');
        if (navToggle && navMenu) {
            navToggle.addEventListener('click', () => {
                const open = navMenu.classList.toggle('hidden') === false;
                navToggle.setAttribute('aria-expanded', String(open));
            });
            navMenu.querySelectorAll('a').forEach(a =>
                a.addEventListener('click', () => navMenu.classList.add('hidden'))
            );
        }

        // Jika halaman ini tidak punya modal chatbot, "AI Chatbot" mengarah ke beranda.
        document.querySelectorAll('#openChatbot, #openChatbotMobile').forEach(el => {
            el.addEventListener('click', (e) => {
                if (!document.getElementById('chatbotModal')) {
                    e.preventDefault();
                    window.location.href = navbar.dataset.home || '/';
                }
            });
        });
    })();
    </script>

    @stack('scripts')
</body>
</html>