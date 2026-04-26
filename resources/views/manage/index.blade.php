@extends('layouts.app')

@section('content')
<div style="padding:40px;">

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div style="background:#d4edda; padding:10px; margin-bottom:20px; border-radius:8px;">
            {{ session('success') }}
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

                <input type="date" name="date" id="reschedule_date" required
                    style="margin:10px 0; width:100%; padding:8px;">

                <input type="time" name="time" id="reschedule_time" required
                    style="margin:10px 0; width:100%; padding:8px;">

                <input type="time" name="end_time" id="reschedule_end_time"
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

    document.getElementById('reschedule_date').value = date;
    document.getElementById('reschedule_time').value = time;

    let form = document.getElementById('rescheduleForm');
    let endInput = document.getElementById('reschedule_end_time');

    if (type === 'booking') {
        form.action = '/booking/' + id + '/reschedule';

        document.getElementById('reschedule_date').name = 'event_date';
        document.getElementById('reschedule_time').name = 'event_time';

        // 🔥 tampilkan end_time
        endInput.style.display = 'block';
        endInput.name = 'end_time';
        endInput.value = endTime ?? '';

    } else {
        form.action = '/survey/' + id + '/reschedule';

        document.getElementById('reschedule_date').name = 'proposed_date';
        document.getElementById('reschedule_time').name = 'proposed_time';

        // 🔥 sembunyikan end_time
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