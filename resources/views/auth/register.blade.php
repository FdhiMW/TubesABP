@extends('layouts.auth')

@section('title', 'Daftar Akun')

@section('visual-description')
    Wujudkan pernikahan impian Anda di Pendopo Uti.
    Reservasi venue dengan mudah, atur jadwal survei lokasi,
    dan koordinasi langsung dengan tim wedding organizer kami.
@endsection

@section('content')
    <!-- Brand -->
    <div class="brand">
        <div class="brand-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
            </svg>
        </div>
        <h2>Buat Akun Baru</h2>
        <p>Daftar untuk mulai reservasi venue</p>
    </div>

    {{-- Alert Success --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form --}}
    <form class="auth-form" method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <div class="input-wrapper">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" class="{{ $errors->has('name') ? 'is-invalid' : '' }}" required>
            </div>
            @error('name') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="email">Alamat Email</label>
            <div class="input-wrapper">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 4l-10 8L2 4"/></svg>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="contoh@email.com" class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required>
            </div>
            @error('email') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="phone">Nomor Telepon</label>
            <div class="input-wrapper">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="081234567890" class="{{ $errors->has('phone') ? 'is-invalid' : '' }}" required>
            </div>
            <p class="form-hint">Format: 08xxxxxxxxxx</p>
            @error('phone') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <div class="input-wrapper">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    <input type="password" id="password" name="password" placeholder="Min. 8 karakter" class="{{ $errors->has('password') ? 'is-invalid' : '' }}" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Konfirmasi</label>
                <div class="input-wrapper">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi sandi" required>
                </div>
            </div>
        </div>
        <p class="form-hint" style="margin-top:-0.5rem;">Minimal 8 karakter, huruf besar, huruf kecil, dan angka.</p>
        @error('password') <p class="form-error">{{ $message }}</p> @enderror

        <button type="submit" class="btn-primary"><span>Daftar Sekarang</span></button>

        <div class="divider"><span>atau</span></div>

        <div class="form-footer">
            <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
        </div>
    </form>
@endsection