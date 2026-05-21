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
                <li><a href="#" id="openChatbot">AI Chatbot</a></li>

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

        <!-- CHATBOT POPUP -->
        <div id="chatbotModal" class="chatbot-modal">

            <div class="chatbot-box">

                <!-- HEADER -->
                <div class="chatbot-header">
                    <h3>Wedding AI Assistant</h3>

                    <button id="closeChatbot">
                        ✕
                    </button>
                </div>

                <!-- BODY -->
                <div id="chatMessages" class="chatbot-messages">

                    <div class="bot-message">
                        Halo 👋
                        <br>
                        Ada yang bisa saya bantu tentang venue?
                    </div>

                </div>

                <!-- INPUT -->
                <form id="chatForm" class="chatbot-input-area">

                    <input
                        type="text"
                        id="chatInput"
                        placeholder="Tanyakan sesuatu..."
                        autocomplete="off"
                    >

                    <button type="submit">
                        Kirim
                    </button>

                </form>

            </div>

        </div>

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

<style>
    .chatbot-modal {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;

        display: none;
    }

    .chatbot-box {
        width: 350px;
        height: 500px;

        background: white;

        border-radius: 20px;

        overflow: hidden;

        box-shadow: 0 10px 30px rgba(0,0,0,0.2);

        display: flex;
        flex-direction: column;
    }

    .chatbot-header {
        background: #8b5e3c;
        color: white;

        padding: 15px;

        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chatbot-header h3 {
        margin: 0;
        font-size: 18px;
    }

    .chatbot-header button {
        background: transparent;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
    }

    .chatbot-messages {
        flex: 1;

        padding: 15px;

        overflow-y: auto;

        background: #f9f9f9;
    }

    .bot-message,
    .user-message {
        padding: 12px;
        border-radius: 12px;
        margin-bottom: 10px;
        max-width: 80%;
        line-height: 1.5;
    }

    .bot-message {
        background: #ececec;
    }

    .user-message {
        background: #8b5e3c;
        color: white;
        margin-left: auto;
    }

    .chatbot-input-area {
        display: flex;
        border-top: 1px solid #ddd;
    }

    .chatbot-input-area input {
        flex: 1;
        border: none;
        padding: 15px;
        outline: none;
    }

    .chatbot-input-area button {
        background: #8b5e3c;
        color: white;
        border: none;
        padding: 0 20px;
        cursor: pointer;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chatbotModal = document.getElementById('chatbotModal');
        const openChatbot = document.getElementById('openChatbot');
        const closeChatbot = document.getElementById('closeChatbot');
        const chatForm = document.getElementById('chatForm');
        const chatInput = document.getElementById('chatInput');
        const chatMessages = document.getElementById('chatMessages');
        const scrollBtn = document.getElementById('scrollBtn');

        if (scrollBtn) {
            scrollBtn.addEventListener('click', function (e) {
                e.preventDefault();
                document.getElementById('extended').scrollIntoView({ behavior: 'smooth' });
            });
        }

        if (openChatbot) {
            openChatbot.addEventListener('click', function (e) {
                e.preventDefault();
                chatbotModal.style.display = 'block';
            });
        }

        if (closeChatbot) {
            closeChatbot.addEventListener('click', function () {
                chatbotModal.style.display = 'none';
            });
        }

        if (chatForm) {
            chatForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const question = chatInput.value.trim();
                if (!question) return;

                const userDiv = document.createElement('div');
                userDiv.classList.add('user-message');
                userDiv.innerText = question;
                chatMessages.appendChild(userDiv);

                chatInput.value = '';
                chatMessages.scrollTop = chatMessages.scrollHeight;

                const loadingDiv = document.createElement('div');
                loadingDiv.classList.add('bot-message');
                loadingDiv.innerText = 'Mengetik...';
                chatMessages.appendChild(loadingDiv);

                try {
                    const response = await fetch('/ai/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            question: question
                        })
                    });

                    const raw = await response.text();
                    console.log('RAW RESPONSE:', raw);

                    const data = JSON.parse(raw);

                    loadingDiv.remove();

                    const botDiv = document.createElement('div');
                    botDiv.classList.add('bot-message');
                    botDiv.innerText = data.answer || 'Maaf terjadi error';
                    chatMessages.appendChild(botDiv);

                    chatMessages.scrollTop = chatMessages.scrollHeight;
                } catch (error) {
                    console.error(error);
                    loadingDiv.remove();

                    const errorDiv = document.createElement('div');
                    errorDiv.classList.add('bot-message');
                    errorDiv.innerText = 'Terjadi kesalahan server';
                    chatMessages.appendChild(errorDiv);
                }
            });
        }
    });
</script>
@endpush