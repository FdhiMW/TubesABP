@extends('layouts.app')

@section('title', 'Pendopo UTI - Wedding Venue')

@section('content')

    {{-- ========================
         HERO SECTION (Home.vue)
    ======================== --}}
    <header class="hero">
        <img class="hero-bg" src="{{ asset('asset/images/hero.png') }}" alt="Hero background" />

        {{-- ========== TOP NAVBAR ========== --}}
        <nav style="background:#f5f1ed; padding:18px 0; border-bottom:1px solid #e8e0d8; font-family:Georgia, serif; position:sticky; top:0; z-index:100;">
            <div style="max-width:1200px; margin:0 auto; display:flex; justify-content:space-between; align-items:center; padding:0 30px;">
                <a href="{{ url('/') }}" style="text-decoration:none;">
                    <span style="color:#0b3120; font-size:22px; font-weight:bold; letter-spacing:1px;">PENDOPO UTI</span>
                </a>
                <div style="display:flex; gap:35px; align-items:center;">
                    <a href="{{ url('/') }}" style="color:#0b3120; text-decoration:none; font-size:15px; font-weight:600;">Home</a>
                    <a href="{{ url('/#facilities') }}" style="color:#8a8a8a; text-decoration:none; font-size:15px;">Facilities</a>
                    <a href="{{ route('booking.create') }}" style="color:#8a8a8a; text-decoration:none; font-size:15px;">Booking</a>
                    <a href="{{ route('manage.index') }}" style="color:#8a8a8a; text-decoration:none; font-size:15px;">Manage</a>

                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}"
                               style="background:#d4af37; color:#0b3120; text-decoration:none; padding:8px 16px; border-radius:4px; font-size:14px; font-weight:bold;">
                                🛡️ Admin Panel
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" style="background:transparent; border:1px solid #8a8a8a; color:#8a8a8a; padding:8px 16px; border-radius:4px; cursor:pointer; font-family:Georgia, serif; font-size:14px;">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" style="color:#8a8a8a; text-decoration:none; font-size:15px;">Login</a>
                    @endauth
                </div>
            </div>
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
        <footer style="background:#5a5238; padding:60px 40px; position:relative; margin-top:80px;">
            <!-- Decorative triangle shape at top -->
            <div style="position:absolute; top:-40px; left:50%; transform:translateX(-50%); width:0; height:0; border-left:40px solid transparent; border-right:40px solid transparent; border-bottom:40px solid #5a5238;"></div>

            <div style="max-width:1200px; margin:0 auto; display:flex; justify-content:space-between; align-items:flex-start; gap:60px; font-family:Georgia, serif;">
                
                <!-- Left: Brand & Contact Info -->
                <div style="flex:1; text-align:left;">
                    <div style="margin-bottom:20px;">
                        <div style="font-size:32px; font-weight:bold; color:#d4af37; letter-spacing:2px; line-height:1.2; margin-bottom:20px;">PENDOPO<br/>UTI</div>
                    </div>
                    <p style="color:#e8e0d8; font-size:14px; margin:10px 0; line-height:1.6;">
                        Jl. Tim. Ash, RT.007/RW.008, Jatiasih Bekasi<br/>
                        +0815 920 398<br/>
                        pendopoutwedding@gmail.com
                    </p>
                </div>

                <!-- Middle: Links -->
                <div style="flex:1;">
                    <div style="display:flex; flex-direction:column; gap:20px;">
                        <a href="#" style="color:#e8e0d8; text-decoration:none; font-size:15px; transition:color 0.3s;">About Us</a>
                        <a href="#" style="color:#e8e0d8; text-decoration:none; font-size:15px; transition:color 0.3s;">Contact</a>
                        <a href="#" style="color:#e8e0d8; text-decoration:none; font-size:15px; transition:color 0.3s;">Terms &amp; Conditions</a>
                    </div>
                </div>

                <!-- Right: Social Media -->
                <div style="flex:1;">
                    <div style="display:flex; flex-direction:column; gap:20px;">
                        <div style="display:flex; align-items:center; gap:15px;">
                            <span style="color:#d4af37; font-size:14px; font-weight:bold;">f</span>
                            <a href="#" style="color:#e8e0d8; text-decoration:none; font-size:15px;">Facebook</a>
                        </div>
                        <div style="display:flex; align-items:center; gap:15px;">
                            <span style="color:#d4af37; font-size:14px; font-weight:bold;">𝕏</span>
                            <a href="#" style="color:#e8e0d8; text-decoration:none; font-size:15px;">Twitter</a>
                        </div>
                        <div style="display:flex; align-items:center; gap:15px;">
                            <span style="color:#d4af37; font-size:14px; font-weight:bold;">📷</span>
                            <a href="#" style="color:#e8e0d8; text-decoration:none; font-size:15px;">@pendopoutiofficial</a>
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