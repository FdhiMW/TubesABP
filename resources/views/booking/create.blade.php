@extends('layouts.app')

@section('title', 'Booking - Pendopo UTI')

@section('content')
<!-- Navigation Bar -->
<nav style="background:#f5f1ed; padding:18px 0; border-bottom:1px solid #e8e0d8; font-family:Georgia, serif; position:sticky; top:0; z-index:100;">
    <div style="max-width:1200px; margin:0 auto; display:flex; justify-content:space-between; align-items:center; padding:0 30px;">
        <a href="{{ url('/') }}" style="text-decoration:none;">
            <span style="color:#0b3120; font-size:22px; font-weight:bold; letter-spacing:1px;">PENDOPO UTI</span>
        </a>
        <div style="display:flex; gap:35px; align-items:center;">
            <a href="{{ url('/') }}" style="color:#8a8a8a; text-decoration:none; font-size:15px;">Home</a>
            <a href="{{ url('/#facilities') }}" style="color:#8a8a8a; text-decoration:none; font-size:15px;">Facilities</a>
            <a href="{{ route('booking.create') }}" style="color:#0b3120; text-decoration:none; font-size:15px; font-weight:600;">Booking</a>
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

<script>
function openCalendar() {
    document.getElementById('calendarModal').style.display = 'block';
    loadCalendar();
}

function closeCalendar() {
    document.getElementById('calendarModal').style.display = 'none';
}

function loadCalendar() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: '/availability-data'
    });

    calendar.render();
}

function closePopup() {
    document.getElementById('successPopup').style.display = 'none';
}
</script>
@if(session('success'))
<div id="successPopup" style="
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.5);
    display:flex;
    align-items:center;
    justify-content:center;
    z-index:9999;
">
    <div style="
        background:#fff;
        padding:30px;
        border-radius:16px;
        text-align:center;
        width:300px;
        box-shadow:0 10px 30px rgba(0,0,0,0.2);
    ">
        <!-- Icon Checklist -->
        <div style="
            width:60px;
            height:60px;
            margin:0 auto 15px;
            background:#4CAF50;
            color:white;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:30px;
        ">
            ✔
        </div>

        <!-- Text -->
        <h3 style="margin-bottom:10px; color:#333;">Berhasil!</h3>
        <p style="color:#666;">{{ session('success') }}</p>

        <!-- Button -->
        <button onclick="closePopup()" style="
            margin-top:20px;
            padding:10px 20px;
            border:none;
            background:#4CAF50;
            color:white;
            border-radius:8px;
            cursor:pointer;
            font-weight:bold;
        ">
            OK
        </button>
    </div>
</div>
@endif
<div style="min-height:100vh; background:linear-gradient(90deg,#fbf6ef 0%, #ffffff 60%); font-family: Georgia, serif;">
    <div style="display:flex;">

        <!-- Left column (text + floral decoration) -->
        <div style="width:50%; position:relative; padding-left:36px; padding-top:60px; padding-bottom:60px; display:flex; align-items:center;">
            <!-- decorative left floral placed behind content -->
            <div style="position:absolute; left:-80px; top:40px; width:260px; height:520px; background-image:url('{{ asset('asset/images/flowerbg1.png') }}'); background-size:cover; background-position:center left; transform:translateX(0); z-index:0; pointer-events:none; filter:drop-shadow(0 10px 20px rgba(0,0,0,0.12));"></div>

            <div style="max-width:500px; position:relative; z-index:1;">
                <p style="color:#6a6a6a; margin:8px 0 18px 0; font-weight:600;">— Hello and Welcome,</p>

                <h1 style="font-size:68px; line-height:0.95; margin:0 0 18px 0; color:#0b3120;">
                    <span style="display:block; font-weight:700;">Book Your</span>
                    <span style="display:block; color:#c96f40; font-weight:700; background:linear-gradient(transparent 60%, rgba(201,111,64,0.18) 60%);">Dream Wedding</span>
                    <span style="display:block; font-weight:700;">With Us</span>
                </h1>

                <p style="color:#6a6a6a; max-width:420px; font-size:16px; margin-bottom:26px;">Mulai perjalanan menuju momen istimewa Anda dengan reservasi sekarang, untuk pengalaman yang hangat dan tak terlupakan.</p>

                <div style="display:flex; flex-direction:column; gap:16px;">
                    <!-- Button with green fill and black drop shadow to the bottom-right -->
                    <a href="#" onclick="openCalendar()" style="display:inline-block; text-decoration:none; width:260px; background:#cfeeb2; color:#0c3b2a; padding:14px 18px; border-radius:10px; box-shadow:10px 10px 0 rgba(0,0,0,0.85); font-weight:800;">LIHAT KETERSEDIAAN TANGGAL <span style="float:right; background:#0c3b2a; color:#fff; border-radius:999px; padding:8px 10px; margin-left:8px;">→</span></a>

                    <a href="{{ route('booking.form') }}" style="display:flex; align-items:center; justify-content:space-between; text-decoration:none; width:260px; background:#cfeeb2; color:#0c3b2a; padding:14px 18px; border-radius:10px; border:none; box-shadow:10px 10px 0 rgba(0,0,0,0.85); font-weight:800;">BOOK NOW <span style="background:#0c3b2a; color:#fff; border-radius:999px; padding:8px 10px;">→</span></a>

                    <a href="{{ route('survey.form') }}" style="display:flex; align-items:center; justify-content:space-between; text-decoration:none; width:260px; background:#cfeeb2; color:#0c3b2a; padding:14px 18px; border-radius:10px; border:none; box-shadow:10px 10px 0 rgba(0,0,0,0.85); font-weight:800;">BOOK A SURVEY <span style="background:#0c3b2a; color:#fff; border-radius:999px; padding:8px 10px;">→</span></a>
                </div>
            </div>
        </div>

        <!-- Right column (large image) -->
        <div style="width:50%; height:100vh; overflow:hidden; position:relative;">
            <img src="{{ asset('asset/images/wedding3.png') }}" alt="wedding" style="width:100%; height:100%; object-fit:cover; display:block; filter:contrast(1.02) saturate(1.05);" />
            <!-- white gradient fade on left to mimic design -->
            <div style="position:absolute; left:0; top:0; bottom:0; width:40%; background:linear-gradient(90deg, rgba(255,255,255,1) 0%, rgba(255,255,255,0.2) 100%);"></div>
        </div>

        <div id="calendarModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
            <div style="background:white; width:1000px; margin:50px auto; padding:20px; border-radius:10px;">
                <div id="calendar"></div>
                <div style="text-align:center; margin-top:15px;">
                    <button onclick="closeCalendar()" style="
                        padding:8px 16px;
                        background:#0c3b2a;
                        color:white;
                        border:none;
                        border-radius:6px;
                        cursor:pointer;
                    ">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection