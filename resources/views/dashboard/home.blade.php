@extends('layouts.app')

@section('title', 'Pendopo UTI - Wedding Venue')

@section('content')

    {{-- ========================
         HERO SECTION (Home.vue)
    ======================== --}}
    <header class="hero">
        <img class="hero-bg" src="{{ asset('asset/images/hero.png') }}" alt="Hero background" />

        <nav class="top-nav">
            <img class="logo" src="{{ asset('asset/images/pendopoutinobg.png') }}" alt="Pendopo UTI" />
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#facilities">Facilities</a></li>
                <li><a href="{{ route('booking.create') }}">Booking</a></li>
                <li><a href="{{ route('manage.index') }}">Manage</a></li>

                <x-admin-link variant="hero" />

                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                @else
                    <li><a href="{{ route('login') }}">Login</a></li>
                @endauth
            </ul>
        </nav>

        <div class="hero-inner">
            <div class="hero-text-content">
                <p class="eyebrow">WELCOME TO</p>
                <h1 class="title">PENDOPO UTI</h1>
                <p class="subtitle">Wedding Venue</p>
                <p class="description">Booking sekarang dan dapatkan pengalaman pernikahan yang sempurna!</p>
            </div>

            <a href="#book" class="btn-primary">BOOK NOW</a>

            <div class="scroll-section">
                <div class="scroll-hint">Scroll</div>
                <a href="#extended" class="scroll-btn" id="scrollBtn">&#9660;</a>
            </div>
        </div>
    </header>

    {{-- ========================
         EXTENDED SECTION (HomeExt.vue)
    ======================== --}}
    <section id="extended" class="home-ext">
        <main>
            <section class="intro">
                <h2>All our room types are including complementary breakfast</h2>

                <div class="feature-list">

                    {{-- Feature 1 --}}
                    <article class="feature">
                        <div class="feature-text">
                            <h3>Elegan &amp; Berkelas</h3>
                            <p>Menghadirkan suasana elegan dengan desain arsitektur klasik yang dipadukan dengan sentuhan modern. Lorong luas dengan pilar-pilar megah serta pencahayaan alami menjadikannya tempat yang ideal untuk berbagai acara.</p>
                        </div>
                        <div class="feature-image">
                            <img src="{{ asset('asset/images/colonnade.png') }}" alt="Elegan & Berkelas" />
                        </div>
                    </article>

                    {{-- Feature 2 --}}
                    <article class="feature">
                        <div class="feature-text">
                            <h3>Momen Bahagia Tak Terlupakan</h3>
                            <p>Tempat ini menghadirkan pengalaman sederhana namun berkesan dimana setiap langkah terasa ringan dan penuh ketenangan.</p>
                        </div>
                        <div class="feature-image">
                            <img src="{{ asset('asset/images/wedding1.png') }}" alt="Momen Bahagia Tak Terlupakan" />
                        </div>
                    </article>

                </div>
            </section>
        </main>

        {{-- Footer --}}
        <footer class="site-footer" id="contact">
            <div class="footer-container">
                <div class="footer-section footer-brand">
                    <div class="brand-name">LUXURY<br/>HOTELS</div>
                    <p class="footer-address">457 Evergreen Rd, Roseville,<br/>CA 95672</p>
                    <p class="footer-contact">luxury.hotels@example.com</p>
                </div>

                <div class="footer-section footer-links">
                    <div class="link-group">
                        <a href="#">About Us</a>
                        <a href="#">Contact</a>
                        <a href="#">Terms &amp; Conditions</a>
                        <div class="social-icons" style="margin-top: 20px;">
                            <a href="#" class="social-icon">f</a>
                            <a href="#" class="social-icon">𝕏</a>
                            <a href="#" class="social-icon">📷</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </section>

@endsection

@push('scripts')
<script>
    // Smooth scroll ke section extended saat klik scroll button
    document.getElementById('scrollBtn').addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('extended').scrollIntoView({ behavior: 'smooth' });
    });
</script>
@endpush