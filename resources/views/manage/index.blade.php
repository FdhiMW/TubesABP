@extends('layouts.app')

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
            <a href="{{ route('booking.create') }}" style="color:#8a8a8a; text-decoration:none; font-size:15px;">Booking</a>
            <a href="{{ route('manage.index') }}" style="color:#0b3120; text-decoration:none; font-size:15px; font-weight:600;">Manage</a>

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

<div style="min-height:100vh; background:linear-gradient(90deg,#fbf6ef 0%, #ffffff 60%); font-family:Georgia, serif; padding:40px 60px;">

    {{-- HEADER --}}
    <div style="display:flex; align-items:center; gap:15px; margin-bottom:30px;">
        <a href="{{ url('/') }}" style="color:#666; font-size:20px; text-decoration:none; cursor:pointer;">←</a>
        <h1 style="margin:0; font-size:24px; font-weight:500; color:#333;">Riwayat</h1>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div style="background:#c8e6c9; padding:15px; margin-bottom:20px; border-radius:8px; color:#2e7d32; border-left:4px solid #4caf50; display:flex; align-items:center; gap:10px;">
            <span style="font-size:18px;">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div style="background:#ffcdd2; padding:15px; margin-bottom:20px; border-radius:8px; color:#c62828;">
            @foreach ($errors->all() as $error)
                <p style="margin:5px 0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- TABS --}}
    <div style="display:flex; gap:30px; border-bottom:1px solid #e0e0e0; margin-bottom:30px;">
        <button class="tab-btn" onclick="switchTab('booking')" data-tab="booking" 
            style="background:none; border:none; padding:12px 0; font-size:14px; color:#666; cursor:pointer; border-bottom:3px solid transparent; transition:all 0.3s; font-weight:500;">
            Booking Venue
        </button>
        <button class="tab-btn" onclick="switchTab('survey')" data-tab="survey"
            style="background:none; border:none; padding:12px 0; font-size:14px; color:#999; cursor:pointer; border-bottom:3px solid transparent; transition:all 0.3s; font-weight:500;">
            Survei Gedung
        </button>
    </div>

    {{-- ================= BOOKING TAB ================= --}}
    <div id="booking-tab" class="tab-content" style="display:block;">
        @forelse($bookings as $b)
            <div style="
                background: {{ ($b->status == 'confirmed' && $b->payment_status == 'paid') ? '#e8f5e9' : 'white' }};
                border: 1px solid {{ ($b->status == 'confirmed' && $b->payment_status == 'paid') ? '#a5d6a7' : '#e0e0e0' }};
                border-radius:8px;
                padding:20px;
                margin-bottom:15px;
                display:flex;
                gap:20px;
                align-items:flex-start;
            ">
                
                {{-- VENUE ICON --}}
                <div style="flex-shrink:0; width:60px; height:60px; background:#f5f5f5; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:28px;">
                    📍
                </div>

                {{-- CONTENT --}}
                <div style="flex:1;">
                    <h3 style="margin:0 0 5px 0; font-size:16px; color:#333; font-weight:500;">{{ $b->venue->name ?? 'Venue' }}</h3>
                    <p style="margin:0 0 10px 0; font-size:13px; color:#999;">{{ $b->venue->location ?? 'Location' }}</p>
                    <p style="margin:0; font-size:13px; color:#666; display:flex; align-items:center; gap:8px;">
                        📅 {{ \Carbon\Carbon::parse($b->event_date)->format('l, j M Y') }} • 
                        🕐 {{ \Carbon\Carbon::parse($b->event_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($b->end_time)->format('H:i') }}
                    </p>
                </div>

                {{-- STATUS & ACTIONS --}}
                <div style="flex-shrink:0; display:flex; flex-direction:column; align-items:flex-end; gap:10px;">

                    {{-- 🔹 BARIS ATAS: STATUS --}}
                    <div style="display:flex; align-items:center; gap:8px;">

                        {{-- STATUS BOOKING --}}
                        @if($b->status == 'pending')
                            <span style="background:#fff3cd; color:#856404; padding:6px 12px; border-radius:20px; font-size:12px;">
                                Menunggu
                            </span>
                        @elseif($b->status == 'confirmed')
                            <span style="background:#d4edda; color:#155724; padding:6px 12px; border-radius:20px; font-size:12px;">
                                Terkonfirmasi
                            </span>
                        @elseif($b->status == 'cancelled')
                            <span style="background:#f8d7da; color:#721c24; padding:6px 12px; border-radius:20px; font-size:12px;">
                                Dibatalkan
                            </span>
                        @endif

                        {{-- STATUS PAYMENT --}}
                        @if($b->payment_status == 'paid')
                            <span style="background:#d4edda; color:#155724; padding:6px 12px; border-radius:20px; font-size:12px;">
                                Sudah Bayar
                            </span>
                        @else
                            <span style="background:#f8d7da; color:#721c24; padding:6px 12px; border-radius:20px; font-size:12px;">
                                Belum Bayar
                            </span>
                        @endif

                    </div>

                    {{-- 🔹 BARIS BAWAH: ACTION BUTTON --}}
                    @if($b->status == 'pending')
                        <div style="display:flex; gap:8px;">
                            <form action="{{ route('booking.cancel', $b->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    style="background:#ffebee; color:#d32f2f; border:1px solid #ffcdd2; padding:6px 12px; border-radius:6px;">
                                    ✕ Batalkan
                                </button>
                            </form>

                            <button onclick="openReschedule('booking', {{ $b->id }}, '{{ $b->event_date }}', '{{ $b->event_time }}', '{{ $b->end_time }}')"
                                style="background:#e8f5e9; color:#1976d2; border:1px solid #c8e6c9; padding:6px 12px; border-radius:6px;">
                                ⟲ Reschedule
                            </button>
                        </div>
                    @endif

                    {{-- TOMBOL BAYAR --}}
                    @if($b->status == 'confirmed' && $b->payment_status == 'unpaid')
                        <button onclick="payNow({{ $b->id }})"
                            style="background:#1976d2; color:white; border:none; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:12px;">
                            💳 Bayar
                        </button>
                    @endif

                </div>
            </div>
        @empty
            <div style="text-align:center; padding:40px; color:#999;">
                <p>Belum ada riwayat booking</p>
            </div>
        @endforelse
    </div>

    {{-- ================= SURVEY TAB ================= --}}
    <div id="survey-tab" class="tab-content" style="display:none;">
        @forelse($surveys as $s)
            <div style="background:white; border:1px solid #e0e0e0; border-radius:8px; padding:20px; margin-bottom:15px; display:flex; gap:20px; align-items:flex-start;">
                
                {{-- VENUE ICON --}}
                <div style="flex-shrink:0; width:60px; height:60px; background:#f5f5f5; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:28px;">
                    📋
                </div>

                {{-- CONTENT --}}
                <div style="flex:1;">
                    <h3 style="margin:0 0 5px 0; font-size:16px; color:#333; font-weight:500;">{{ $s->venue->name ?? 'Venue' }}</h3>
                    <p style="margin:0 0 10px 0; font-size:13px; color:#999;">{{ $s->venue->location ?? 'Location' }}</p>
                    <p style="margin:0; font-size:13px; color:#666; display:flex; align-items:center; gap:8px;">
                        📅 {{ \Carbon\Carbon::parse($s->proposed_date)->format('l, j M Y') }} • 
                        🕐 {{ \Carbon\Carbon::parse($s->proposed_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->end_time)->format('H:i') }}
                    </p>
                </div>

                {{-- STATUS & ACTIONS --}}
                <div style="flex-shrink:0; display:flex; flex-direction:column; align-items:flex-end; gap:10px;">
                    @if($s->status == 'pending')
                        <span style="background:#fff3cd; color:#856404; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:500;">
                            Menunggu
                        </span>
                    @elseif($s->status == 'approved')
                        <span style="background:#d4edda; color:#155724; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:500;">
                            Disetujui
                        </span>
                    @elseif($s->status == 'rejected')
                        <span style="background:#f8d7da; color:#721c24; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:500;">
                            Ditolak
                        </span>
                    @else
                        <span style="background:#e9ecef; color:#495057; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:500;">
                            {{ ucfirst($s->status) }}
                        </span>
                    @endif

                    @if($s->status == 'pending')
                        <div style="display:flex; gap:8px;">
                            {{-- CANCEL --}}
                            <form action="{{ route('survey.cancel', $s->id) }}" method="POST" style="margin:0;">
                                @csrf
                                <button type="submit" style="background:#ffebee; color:#d32f2f; border:1px solid #ffcdd2; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:12px; font-weight:500;">
                                    ✕ Batalkan
                                </button>
                            </form>

                            {{-- RESCHEDULE --}}
                            <button onclick="openReschedule('survey', {{ $s->id }}, '{{ $s->proposed_date }}', '{{ $s->proposed_time }}')"
                                style="background:#e8f5e9; color:#1976d2; border:1px solid #c8e6c9; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:12px; font-weight:500;">
                                ⟲ Reschedule
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div style="text-align:center; padding:40px; color:#999;">
                <p>Belum ada riwayat survey</p>
            </div>
        @endforelse
    </div>


    {{-- ================= MODAL ================= --}}
    <div id="rescheduleModal" style="
        display:none;
        position:fixed;
        top:0; left:0;
        width:100%; height:100%;
        background:rgba(0,0,0,0.5);
        z-index:9999;
        align-items:center;
        justify-content:center;
        padding:20px;
    ">
        <div style="
            background:#fff;
            padding:40px;
            border-radius:16px;
            width:100%;
            max-width:420px;
            text-align:center;
            position:relative;
            box-shadow:0 8px 30px rgba(0,0,0,0.2);
        ">

            {{-- DECORATIVE TOP --}}
            <div style="font-size:24px; margin-bottom:20px; letter-spacing:4px;">✧ ✧ ✧</div>

            {{-- TITLE & SUBTITLE --}}
            <h3 style="margin:0 0 5px 0; font-size:24px; color:#333; font-weight:600;">Reschedule</h3>
            <p style="margin:0 0 20px 0; font-size:11px; color:#999; letter-spacing:1px; font-weight:600;">PILIH TANGGAL & WAKTU BARU</p>

            {{-- DIVIDER LINE --}}
            <div style="display:flex; align-items:center; margin-bottom:30px;">
                <div style="flex:1; height:1px; background:#e0e0e0;"></div>
                <div style="width:8px; height:8px; background:#c9a861; border-radius:50%; margin:0 10px;"></div>
                <div style="flex:1; height:1px; background:#e0e0e0;"></div>
            </div>

            <form id="rescheduleForm" method="POST">
                @csrf

                {{-- TANGGAL --}}
                <div style="margin-bottom:25px; text-align:left;">
                    <label style="display:block; font-size:11px; color:#999; margin-bottom:10px; font-weight:700; letter-spacing:0.5px;">TANGGAL</label>
                    <div style="position:relative;">
                        <input type="date" id="reschedule_date" required
                            style="width:100%; padding:12px 14px 12px 14px; border:1px solid #e0e0e0; border-radius:8px; font-size:14px; box-sizing:border-box; background:#f9f9f9; color:#333; transition:border 0.3s;">
                        <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:18px; pointer-events:none;">📅</span>
                    </div>
                </div>

                {{-- WAKTU --}}
                <div style="margin-bottom:25px; text-align:left;">
                    <label style="display:block; font-size:11px; color:#999; margin-bottom:10px; font-weight:700; letter-spacing:0.5px;">WAKTU</label>
                    <div style="position:relative;">
                        <input type="time" id="reschedule_time" required
                            style="width:100%; padding:12px 14px 12px 14px; border:1px solid #e0e0e0; border-radius:8px; font-size:14px; box-sizing:border-box; background:#f9f9f9; color:#333; transition:border 0.3s;">
                        <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:18px; pointer-events:none;">🕐</span>
                    </div>
                </div>

                {{-- END TIME (hidden for survey) --}}
                <div id="endTimeContainer" style="margin-bottom:25px; display:none; text-align:left;">
                    <label style="display:block; font-size:11px; color:#999; margin-bottom:10px; font-weight:700; letter-spacing:0.5px;">WAKTU SELESAI</label>
                    <div style="position:relative;">
                        <input type="time" id="reschedule_end_time"
                            style="width:100%; padding:12px 14px 12px 14px; border:1px solid #e0e0e0; border-radius:8px; font-size:14px; box-sizing:border-box; background:#f9f9f9; color:#333; transition:border 0.3s;">
                        <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:18px; pointer-events:none;">🕐</span>
                    </div>
                </div>

                {{-- BUTTONS --}}
                <div style="display:flex; gap:12px; margin-top:30px;">
                    <button type="button" onclick="closeModal()" 
                        style="flex:1; padding:12px; border:1px solid #ddd; background:#fff; color:#666; border-radius:8px; cursor:pointer; font-weight:600; font-size:14px; transition:all 0.3s;">
                        Batal
                    </button>
                    <button type="submit" 
                        style="flex:1; padding:12px; border:none; background:#c9a861; color:#fff; border-radius:8px; cursor:pointer; font-weight:600; font-size:14px; transition:all 0.3s; box-shadow:0 2px 8px rgba(201, 168, 97, 0.3);">
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

<style>
.tab-btn.active {
    color: #333 !important;
    border-bottom-color: #333 !important;
}

.tab-btn:hover {
    color: #555 !important;
}
</style>

{{-- ================= SCRIPT ================= --}}
<script>
function switchTab(tabName) {
    // Hide all tabs
    document.getElementById('booking-tab').style.display = 'none';
    document.getElementById('survey-tab').style.display = 'none';

    // Show selected tab
    document.getElementById(tabName + '-tab').style.display = 'block';

    // Update button styles
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-tab') === tabName) {
            btn.classList.add('active');
            btn.style.color = '#333';
            btn.style.borderBottomColor = '#333';
        } else {
            btn.style.color = '#999';
            btn.style.borderBottomColor = 'transparent';
        }
    });
}

function payNow(id) {
    fetch('/payment/' + id)
        .then(response => response.json())
        .then(data => {
            window.snap.pay(data.token, {
                onSuccess: function(result) {
                    alert("Pembayaran berhasil!");
                    location.reload();
                },
                onPending: function(result) {
                    alert("Menunggu pembayaran");
                },
                onError: function(result) {
                    alert("Pembayaran gagal");
                },
                onClose: function() {
                    alert("Kamu menutup popup tanpa bayar");
                }
            });
        });
}

// Set active tab on load
document.addEventListener('DOMContentLoaded', function() {
    switchTab('booking');
});

function openReschedule(type, id, date, time, endTime = null) {
    document.getElementById('rescheduleModal').style.display = 'flex';

    let dateInput = document.getElementById('reschedule_date');
    let timeInput = document.getElementById('reschedule_time');
    let endInput  = document.getElementById('reschedule_end_time');
    let endContainer = document.getElementById('endTimeContainer');

    dateInput.value = date;
    timeInput.value = time ? time.substring(0,5) : '';

    let form = document.getElementById('rescheduleForm');

    // RESET dulu
    dateInput.removeAttribute('name');
    timeInput.removeAttribute('name');
    endInput.removeAttribute('name');

    if (type === 'booking') {
        form.action = '/booking/' + id + '/reschedule';

        dateInput.name = 'event_date';
        timeInput.name = 'event_time';

        endContainer.style.display = 'block';
        endInput.name = 'end_time';
        endInput.value = endTime ? endTime.substring(0,5) : '';

    } else {
        form.action = '/survey/' + id + '/reschedule';

        dateInput.name = 'proposed_date';
        timeInput.name = 'proposed_time';

        endContainer.style.display = 'none';
    }
}

function closeModal() {
    document.getElementById('rescheduleModal').style.display = 'none';
}

// klik luar modal = close
window.onclick = function(e) {
    let modal = document.getElementById('rescheduleModal');
    if (e.target === modal) {
        modal.style.display = 'none';
    }
}
</script>

@endsection