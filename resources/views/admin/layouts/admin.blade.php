@extends('layouts.app')

@section('title', 'Admin - Pendopo UTI')

@section('content')
<div style="display:flex; min-height:100vh; font-family:Georgia, serif;">

    {{-- ===== SIDEBAR ===== --}}
    <aside style="width:240px; background:#0b3120; color:#f5f1ed; padding:30px 0; flex-shrink:0; position:relative;">
        <div style="padding:0 25px 25px; border-bottom:1px solid #1a4a32;">
            <h2 style="margin:0; font-size:20px; letter-spacing:1px;">PENDOPO UTI</h2>
            <p style="margin:5px 0 0; font-size:12px; color:#b7a98c; text-transform:uppercase; letter-spacing:1.5px;">Admin Panel</p>
        </div>

        <nav style="padding:20px 0;">
            @php
                $current = request()->route()->getName() ?? '';
                $linkStyle = 'display:block; padding:12px 25px; color:#f5f1ed; text-decoration:none; font-size:15px; border-left:3px solid transparent; transition:all 0.2s;';
                $activeStyle = 'background:#1a4a32; border-left-color:#d4af37;';
            @endphp

            <a href="{{ route('admin.dashboard') }}"
               style="{{ $linkStyle }} {{ $current === 'admin.dashboard' ? $activeStyle : '' }}">
                📊 Dashboard
            </a>
            <a href="{{ route('admin.bookings.index') }}"
               style="{{ $linkStyle }} {{ str_starts_with($current, 'admin.bookings') ? $activeStyle : '' }}">
                🏛️ Booking
            </a>
            <a href="{{ route('admin.surveys.index') }}"
               style="{{ $linkStyle }} {{ str_starts_with($current, 'admin.surveys') ? $activeStyle : '' }}">
                📅 Survey
            </a>
        </nav>

        {{-- ===== TOMBOL KEMBALI KE BERANDA (di sidebar bawah) ===== --}}
        <div style="padding:20px 25px; margin-top:20px; border-top:1px solid #1a4a32;">
            <a href="{{ route('home') }}"
               style="display:block; padding:12px; background:#d4af37; color:#0b3120; text-decoration:none; font-size:14px; font-weight:bold; text-align:center; border-radius:6px; transition:all 0.2s;"
               onmouseover="this.style.background='#e5c14e';"
               onmouseout="this.style.background='#d4af37';">
                🏠 Kembali ke Beranda
            </a>
        </div>

        {{-- ===== INFO USER & LOGOUT ===== --}}
        <div style="padding:20px 25px; border-top:1px solid #1a4a32; margin-top:20px;">
            <p style="margin:0 0 8px; font-size:13px; color:#b7a98c;">Login sebagai:</p>
            <p style="margin:0 0 12px; font-size:14px; font-weight:bold;">{{ auth()->user()->name }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        style="width:100%; padding:8px; background:transparent; border:1px solid #b7a98c; color:#f5f1ed; cursor:pointer; font-family:Georgia, serif; font-size:13px;">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <main style="flex:1; background:#faf8f5; padding:40px;">

        {{-- ===== TOP BAR — TOMBOL KEMBALI KE BERANDA (paling atas, paling kelihatan) ===== --}}
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; padding-bottom:15px; border-bottom:1px solid #e8e0d8;">
            <div style="font-size:13px; color:#8a8a8a;">
                📍 Anda berada di <strong style="color:#0b3120;">Admin Panel</strong>
            </div>
            <a href="{{ route('home') }}"
               style="background:#0b3120; color:#f5f1ed; text-decoration:none; padding:10px 20px; border-radius:6px; font-size:14px; font-weight:500; transition:all 0.2s;"
               onmouseover="this.style.background='#1a4a32';"
               onmouseout="this.style.background='#0b3120';">
                ← Kembali ke Dashboard Biasa
            </a>
        </div>

        @if(session('success'))
            <div style="background:#d4edda; color:#155724; padding:15px 20px; margin-bottom:20px; border-radius:6px; border-left:4px solid #28a745;">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background:#f8d7da; color:#721c24; padding:15px 20px; margin-bottom:20px; border-radius:6px; border-left:4px solid #dc3545;">
                @foreach($errors->all() as $error)
                    <p style="margin:0;">✗ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        @yield('admin_content')
    </main>
</div>
@endsection