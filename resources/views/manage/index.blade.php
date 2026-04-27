@extends('layouts.app')

@section('content')
<!-- Navigation Bar -->
<nav style="background:#f5f1ed; padding:20px 0; border-bottom:1px solid #e8e0d8; font-family: Georgia, serif;">
    <div style="max-width:1100px; margin:0 auto; display:flex; justify-content:flex-end; align-items:center; gap:40px; padding:0 20px;">
        <a href="{{ url('/') }}" style="color:#8a8a8a; text-decoration:none; font-size:16px; font-weight:500;">Home</a>
        <a href="#" style="color:#8a8a8a; text-decoration:none; font-size:16px; font-weight:500;">Facilities</a>
        <a href="{{ url('/booking#') }}" style="color:#8a8a8a; text-decoration:none; font-size:16px; font-weight:500;">Booking</a>
        <a href="{{ route('manage.index') }}" style="color:#0b3120; text-decoration:none; font-size:16px; font-weight:500;">Manage</a>

        {{-- Link Admin Panel — hanya muncul kalau role admin --}}
        <x-admin-link />
    </div>
</nav>

<div style="padding:40px;">

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div style="background:#d4edda; padding:10px; margin-bottom:20px; border-radius:8px;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
    <div style="background:#f8d7da; padding:10px; margin-bottom:20px; border-radius:8px; color:red;">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    {{-- ================= BOOKING ================= --}}
    <h2>Riwayat Booking Venue</h2>

    @foreach($bookings as $b)
        <div style="border:1px solid #ddd; padding:15px; margin-bottom:10px; border-radius:8px;">
            <p><b>Tanggal:</b> {{ $b->event_date }}</p>
            <p><b>Jam:</b> {{ $b->event_time }} - {{ $b->end_time }}</p>
            <p><b>Status:</b> {{ $b->status }}</p>

            @if($b->status == 'pending')
                <div style="display:flex; gap:10px; margin-top:10px;">
                    
                    {{-- CANCEL --}}
                    <form action="{{ route('booking.cancel', $b->id) }}" method="POST">
                        @csrf
                        <button style="background:red; color:white; border:none; padding:6px 10px; border-radius:6px;">
                            Cancel
                        </button>
                    </form>

                    {{-- RESCHEDULE --}}
                    <button onclick="openReschedule('booking', {{ $b->id }}, '{{ $b->event_date }}', '{{ $b->event_time }}', '{{ $b->end_time }}')"
                        style="background:#c9a861; color:white; border:none; padding:6px 10px; border-radius:6px;">
                        Reschedule
                    </button>
                </div>
            @endif
        </div>
    @endforeach


    {{-- ================= SURVEY ================= --}}
    <h2 style="margin-top:40px;">Riwayat Survey</h2>

    @foreach($surveys as $s)
        <div style="border:1px solid #ddd; padding:15px; margin-bottom:10px; border-radius:8px;">
            <p><b>Tanggal:</b> {{ $s->proposed_date }}</p>
            <p><b>Jam:</b> {{ $s->proposed_time }} - {{ $s->end_time }}</p>
            <p><b>Status:</b> {{ $s->status }}</p>

            @if($s->status == 'pending')
                <div style="display:flex; gap:10px; margin-top:10px;">

                    {{-- CANCEL --}}
                    <form action="{{ route('survey.cancel', $s->id) }}" method="POST">
                        @csrf
                        <button style="background:red; color:white; border:none; padding:6px 10px; border-radius:6px;">
                            Cancel
                        </button>
                    </form>

                    {{-- RESCHEDULE --}}
                    <button onclick="openReschedule('survey', {{ $s->id }}, '{{ $s->proposed_date }}', '{{ $s->proposed_time }}')"
                        style="background:#c9a861; color:white; border:none; padding:6px 10px; border-radius:6px;">
                        Reschedule
                    </button>
                </div>
            @endif
        </div>
    @endforeach


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
    ">
        <div style="
            background:#fff;
            padding:30px;
            border-radius:12px;
            width:350px;
            text-align:center;
            position:relative;
        ">

            <h3>Reschedule</h3>

            <form id="rescheduleForm" method="POST">
                @csrf

                <input type="date" id="reschedule_date" required
                    style="margin:10px 0; width:100%; padding:8px;">

                <input type="time" id="reschedule_time" required
                    style="margin:10px 0; width:100%; padding:8px;">

                <input type="time" id="reschedule_end_time"
                    style="margin:10px 0; width:100%; padding:8px; display:none;">

                <div style="display:flex; gap:10px; margin-top:15px;">
                    <button type="button" onclick="closeModal()" style="flex:1;">Batal</button>
                    <button type="submit" style="flex:1; background:#c9a861; color:#fff;">Simpan</button>
                </div>
            </form>

        </div>
    </div>

</div>

{{-- ================= SCRIPT ================= --}}
<script>
function openReschedule(type, id, date, time, endTime = null) {
    document.getElementById('rescheduleModal').style.display = 'flex';

    let dateInput = document.getElementById('reschedule_date');
    let timeInput = document.getElementById('reschedule_time');
    let endInput  = document.getElementById('reschedule_end_time');

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

        endInput.style.display = 'block';
        endInput.name = 'end_time';
        endInput.value = endTime ? endTime.substring(0,5) : '';

    } else {
        form.action = '/survey/' + id + '/reschedule';

        dateInput.name = 'proposed_date';
        timeInput.name = 'proposed_time';

        endInput.style.display = 'none';
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