@extends('layouts.auth')

@section('title', 'Masuk')

@section('visual-description')
    Selamat datang kembali. Masuk ke akun Anda untuk
    mengelola reservasi, melihat status booking,
    dan berkomunikasi dengan tim kami.
@endsection

@section('content')
    <!-- Brand -->
    <div class="brand">
        <div class="brand-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
            </svg>
        </div>
        <h2>Selamat Datang</h2>
        <p>Masuk ke akun Pendopo Uti Anda</p>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form --}}
    <form class="auth-form" method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Alamat Email</label>
            <div class="input-wrapper">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 4l-10 8L2 4"/></svg>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="contoh@email.com" class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required autofocus>
            </div>
            @error('email') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="password">Kata Sandi</label>
            <div class="input-wrapper">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" required>
                <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
        </div>

        <div class="form-options">
            <label class="remember-me">
                <input type="checkbox" name="remember">
                <span>Ingat saya</span>
            </label>
            <a href="#" class="forgot-link">Lupa sandi?</a>
        </div>

        <button type="submit" class="btn-primary"><span>Masuk</span></button>

        <div class="divider"><span>atau</span></div>

        <div class="form-footer">
            <p>Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
        </div>
    </form>
@endsection