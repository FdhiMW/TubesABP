@extends('layouts.app')

@section('title', 'Facilities - Pendopo UTI')

@section('content')
    <div id="top" class="font-sans text-ink antialiased">

        {{-- ===================== NAVBAR ===================== --}}
        <nav id="navbar"
             class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
             data-at-top="true">
            <div class="mx-auto flex h-20 max-w-7xl items-center justify-between site-px">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('asset/images/pendopoutinobg.png') }}"
                         alt="Pendopo UTI"
                         class="h-11 w-auto object-contain sm:h-12">
                </a>

                {{-- Menu desktop --}}
                <ul class="hidden items-center gap-7 text-sm font-medium tracking-wide text-white lg:flex xl:gap-9">
                    <li><a href="{{ route('home') }}" class="transition-colors hover:text-gold-soft">Home</a></li>
                    <li><a href="{{ route('facilities') }}" class="transition-colors hover:text-gold-soft">Facilities</a></li>
                    <li><a href="{{ route('booking.create') }}" class="transition-colors hover:text-gold-soft">Booking</a></li>
                    <li><a href="{{ route('manage.index') }}" class="transition-colors hover:text-gold-soft">Manage</a></li>
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
            <div id="navMenu"
                 class="hidden border-t border-white/10 bg-forest-deep/95 backdrop-blur lg:hidden">
                <ul class="flex flex-col gap-1 px-5 py-4 text-white">
                    <li><a href="{{ route('home') }}" class="block rounded-md px-3 py-3 transition-colors hover:bg-white/10">Home</a></li>
                    <li><a href="{{ route('facilities') }}" class="block rounded-md px-3 py-3 transition-colors hover:bg-white/10">Facilities</a></li>
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

        {{-- HERO SECTION --}}
        <header class="relative flex min-h-dvh flex-col justify-center overflow-hidden pt-20 bg-forest-deep">
            <img src="{{ asset('asset/images/120539-kofr3vthh0j1 1.png') }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="eager">
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/30 to-black/60"></div>
            <div class="relative z-10 mx-auto w-full max-w-7xl site-px text-center">
                <div class="flex flex-col items-center gap-2">
                    <p class="text-sm uppercase tracking-widest text-gold-soft">Welcome To</p>
                    <h1 class="text-5xl md:text-6xl font-display font-semibold text-cream drop-shadow-xl">FACILITIES</h1>
                </div>
            </div>
        </header>

        {{-- CONTENT SECTION --}}
        <main class="bg-cream py-32">
            <div class="mx-auto max-w-7xl site-px">
                {{-- Heading section --}}
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-4xl font-display font-semibold text-ink text-balance">FACILITIES</h2>
                    <div class="mx-auto mt-8 h-1 w-16 bg-gold/60"></div>
                    <p class="mt-10 text-pretty text-ink/70">We want your stay at our venue to be truly unforgettable. That is why we give special attention to all of your needs so that we can ensure an experience quite unique. Luxury hotels offers the perfect setting with stunning views for leisure and our modern luxury resort facilities will help you enjoy the best of all.</p>
                </div>

                {{-- Image blocks --}}
                <div class="mt-40 space-y-32">
                    {{-- Pendopo image block --}}
                    <div class="mx-auto w-full max-w-4xl pt-8 pb-20">
                        <div class="relative overflow-visible">
                            <img src="{{ asset('asset/images/pendopo.png') }}" alt="Pendopo" class="w-full h-72 object-cover shadow-xl">
                            <div class="absolute left-1/2 -translate-x-1/2 bottom-0 translate-y-1/2">
                                <div class="bg-white px-10 py-3 font-semibold shadow-lg text-ink text-sm tracking-wide">PENDOPO</div>
                            </div>
                        </div>
                    </div>

                    {{-- Poolside image block --}}
                    <div class="mx-auto w-full max-w-4xl pt-8 pb-20">
                        <div class="relative overflow-visible">
                            <img src="{{ asset('asset/images/poolside.png') }}" alt="Poolside bar" class="w-full h-72 object-cover shadow-xl">
                            <div class="absolute left-1/2 -translate-x-1/2 bottom-0 translate-y-1/2">
                                <div class="bg-white px-10 py-3 font-semibold shadow-lg text-ink text-sm tracking-wide">POOLSIDE BAR</div>
                            </div>
                        </div>
                    </div>

                    {{-- Spa image block --}}
                    <div class="mx-auto w-full max-w-4xl pt-8 pb-20">
                        <div class="relative overflow-visible">
                            <img src="{{ asset('asset/images/spa.png') }}" alt="The spa" class="w-full h-72 object-cover shadow-xl">
                            <div class="absolute left-1/2 -translate-x-1/2 bottom-0 translate-y-1/2">
                                <div class="bg-white px-10 py-3 font-semibold shadow-lg text-ink text-sm tracking-wide">THE SPA</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        {{-- ===================== FOOTER ===================== --}}
        <footer id="contact" class="bg-ink text-cream/80">
            <div class="mx-auto grid max-w-7xl gap-10 px-5 py-14 sm:grid-cols-2 sm:px-8 sm:py-16 lg:grid-cols-3 lg:gap-12 lg:px-10">
                {{-- Brand --}}
                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="font-display text-2xl font-semibold leading-tight text-cream">
                        PENDOPO<br>UTI
                    </div>
                    <p class="mt-4 text-sm leading-relaxed text-cream/60">
                        Jl. Contoh Raya No. 123,<br>Bandung, Jawa Barat 40123
                    </p>
                    <p class="mt-3 text-sm text-cream/60">halo@pendopouti.example</p>
                </div>

                {{-- Links --}}
                <div>
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-cream">Tautan</h4>
                    <ul class="mt-4 space-y-3 text-sm">
                        <li><a href="{{ route('facilities') }}" class="transition-colors hover:text-gold-soft">Facilities</a></li>
                        <li><a href="{{ route('booking.create') }}" class="transition-colors hover:text-gold-soft">Booking</a></li>
                        <li><a href="{{ route('manage.index') }}" class="transition-colors hover:text-gold-soft">Manage</a></li>
                        <li><a href="#contact" class="transition-colors hover:text-gold-soft">Kontak</a></li>
                    </ul>
                </div>

                {{-- Social --}}
                <div>
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-cream">Ikuti Kami</h4>
                    <div class="mt-4 flex gap-3">
                        {{-- Facebook --}}
                        <a href="#" aria-label="Facebook"
                           class="flex h-10 w-10 items-center justify-center rounded-full border border-gold/50 text-gold transition-colors hover:bg-gold hover:text-ink">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.99 3.66 9.13 8.44 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.78-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99C18.34 21.13 22 16.99 22 12z"/></svg>
                        </a>
                        {{-- Instagram --}}
                        <a href="#" aria-label="Instagram"
                           class="flex h-10 w-10 items-center justify-center rounded-full border border-gold/50 text-gold transition-colors hover:bg-gold hover:text-ink">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 0 1-1.38-.9 3.7 3.7 0 0 1-.9-1.38c-.16-.42-.36-1.06-.41-2.23C2.17 15.58 2.16 15.2 2.16 12s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16zm0 1.62c-3.15 0-3.52.01-4.76.07-.99.05-1.53.21-1.88.35-.47.18-.81.4-1.16.75-.35.35-.57.69-.75 1.16-.14.35-.3.89-.35 1.88-.06 1.24-.07 1.61-.07 4.76s.01 3.52.07 4.76c.05.99.21 1.53.35 1.88.18.47.4.81.75 1.16.35.35.69.57 1.16.75.35.14.89.3 1.88.35 1.24.06 1.61.07 4.76.07s3.52-.01 4.76-.07c.99-.05 1.53-.21 1.88-.35.47-.18.81-.4 1.16-.75.35-.35.57-.69.75-1.16.14-.35.3-.89.35-1.88.06-1.24.07-1.61.07-4.76s-.01-3.52-.07-4.76c-.05-.99-.21-1.53-.35-1.88a3.1 3.1 0 0 0-.75-1.16 3.1 3.1 0 0 0-1.16-.75c-.35-.14-.89-.3-1.88-.35-1.24-.06-1.61-.07-4.76-.07zm0 2.76a5.46 5.46 0 1 1 0 10.92 5.46 5.46 0 0 1 0-10.92zm0 9a3.54 3.54 0 1 0 0-7.08 3.54 3.54 0 0 0 0 7.08zm5.68-9.16a1.28 1.28 0 1 1-2.56 0 1.28 1.28 0 0 1 2.56 0z"/></svg>
                        </a>
                        {{-- WhatsApp --}}
                        <a href="#" aria-label="WhatsApp"
                           class="flex h-10 w-10 items-center justify-center rounded-full border border-gold/50 text-gold transition-colors hover:bg-gold hover:text-ink">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.06 24l1.68-6.13a11.86 11.86 0 0 1-1.6-5.94C.15 5.34 5.5 0 12.06 0a11.8 11.8 0 0 1 8.4 3.49 11.8 11.8 0 0 1 3.48 8.41c0 6.56-5.34 11.9-11.9 11.9a11.9 11.9 0 0 1-5.69-1.45L.06 24zM6.6 20.13c1.68 1 3.28 1.59 5.4 1.59 5.45 0 9.9-4.43 9.9-9.88a9.82 9.82 0 0 0-2.9-7 9.82 9.82 0 0 0-7-2.9c-5.46 0-9.9 4.44-9.9 9.9 0 2.23.65 3.9 1.74 5.65l-.99 3.62 3.75-.98zm11.39-5.5c-.07-.12-.27-.2-.57-.35-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.61-.92-2.21-.24-.58-.49-.5-.67-.51l-.57-.01c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.06 2.87 1.21 3.07.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.69.62.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2-1.41.25-.69.25-1.28.18-1.41z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-white/10">
                <div class="mx-auto max-w-7xl site-px py-6 text-center text-xs text-cream/40">
                    © {{ date('Y') }} Pendopo UTI. All rights reserved.
                </div>
            </div>
        </footer>

        {{-- ===================== CHATBOT (FAB + modal) ===================== --}}
        {{-- Tombol mengambang --}}
        <button id="chatbotFab" type="button" aria-label="Buka AI Chatbot"
                class="fixed bottom-6 right-6 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-gold text-white shadow-lg shadow-black/25 transition-all hover:-translate-y-1 hover:bg-gold-soft">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
            </svg>
        </button>

        {{-- Modal --}}
        <div id="chatbotModal" class="fixed bottom-6 right-6 z-50 hidden">
            <div class="flex h-[min(70vh,500px)] w-[min(90vw,360px)] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5">
                {{-- Header --}}
                <div class="flex items-center justify-between bg-forest px-5 py-4 text-white">
                    <div class="flex items-center gap-2">
                        <span class="flex h-2.5 w-2.5 rounded-full bg-green-400"></span>
                        <h3 class="text-base font-semibold">Wedding AI Assistant</h3>
                    </div>
                    <button id="closeChatbot" aria-label="Tutup" class="text-white/80 transition-colors hover:text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Messages --}}
                <div id="chatMessages" class="flex-1 space-y-3 overflow-y-auto bg-cream/60 p-4">
                    <div class="max-w-[80%] rounded-2xl rounded-tl-sm bg-white px-4 py-2.5 text-sm leading-relaxed text-ink shadow-sm">
                        Halo 👋 Ada yang bisa saya bantu tentang venue?
                    </div>
                </div>

                {{-- Input --}}
                <form id="chatForm" class="flex items-center gap-2 border-t border-black/5 bg-white p-3">
                    <input type="text" id="chatInput" autocomplete="off"
                           placeholder="Tanyakan sesuatu..."
                           class="flex-1 rounded-full bg-cream/70 px-4 py-2.5 text-sm text-ink outline-none ring-1 ring-transparent transition focus:ring-gold/50">
                    <button type="submit" aria-label="Kirim"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gold text-white transition-colors hover:bg-gold-soft">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.27 3.13a.6.6 0 0 1 .82-.73l16.5 8.25a.6.6 0 0 1 0 1.07L4.09 19.97a.6.6 0 0 1-.82-.73L6 12Zm0 0h6" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        /* ---------- Navbar: solid saat di-scroll ---------- */
        const navbar = document.getElementById('navbar');
        const setNav = () => {
            const atTop = window.scrollY < 40;
            navbar.dataset.atTop = atTop;
            navbar.classList.toggle('bg-forest-deep/95', !atTop);
            navbar.classList.toggle('shadow-lg', !atTop);
            navbar.classList.toggle('backdrop-blur', !atTop);
        };
        setNav();
        window.addEventListener('scroll', setNav, { passive: true });

        /* ---------- Menu mobile ---------- */
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

        /* ---------- Chatbot ---------- */
        const modal = document.getElementById('chatbotModal');
        const fab = document.getElementById('chatbotFab');
        const openLinks = ['openChatbot', 'openChatbotMobile'].map(id => document.getElementById(id)).filter(Boolean);
        const closeBtn = document.getElementById('closeChatbot');
        const chatForm = document.getElementById('chatForm');
        const chatInput = document.getElementById('chatInput');
        const chatMessages = document.getElementById('chatMessages');

        const openChat = (e) => { if (e) e.preventDefault(); modal.classList.remove('hidden'); if (fab) fab.classList.add('hidden'); chatInput?.focus(); };
        const closeChat = () => { modal.classList.add('hidden'); if (fab) fab.classList.remove('hidden'); };

        if (fab) fab.addEventListener('click', openChat);
        openLinks.forEach(l => l.addEventListener('click', openChat));
        if (closeBtn) closeBtn.addEventListener('click', closeChat);

        const bubble = (text, who) => {
            const div = document.createElement('div');
            const base = 'max-w-[80%] rounded-2xl px-4 py-2.5 text-sm leading-relaxed shadow-sm';
            div.className = who === 'user'
                ? base + ' ml-auto rounded-tr-sm bg-gold text-white'
                : base + ' rounded-tl-sm bg-white text-ink';
            div.textContent = text;
            chatMessages.appendChild(div);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            return div;
        };

        if (chatForm) {
            chatForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const question = chatInput.value.trim();
                if (!question) return;

                bubble(question, 'user');
                chatInput.value = '';

                const loading = bubble('Mengetik…', 'bot');
                loading.classList.add('animate-pulse', 'text-ink/50');

                try {
                    const response = await fetch('/ai/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({ message: question }),
                    });
                    const data = await response.json();
                    loading.remove();
                    bubble(data.reply || 'Maaf, terjadi kesalahan.', 'bot');
                } catch (error) {
                    loading.remove();
                    bubble('Maaf, terjadi kesalahan. Coba lagi nanti.', 'bot');
                    console.error(error);
                }
            });
        }
    });
</script>
@endpush
