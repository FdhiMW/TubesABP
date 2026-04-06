
<div class="container">

    <!-- HERO -->
    <div class="card mb-4">
        <img src="{{ asset('images/venue.jpg') }}" class="card-img-top">

        <div class="card-body">
            <h2>{{ $venue->name }}</h2>

            <p>{{ $venue->description }}</p>

            <h4>Harga: Rp {{ number_format($venue->price_per_day) }}</h4>

            <p>Kapasitas: {{ $venue->capacity }} orang</p>

            <p>Lokasi: {{ $venue->location }}</p>
        </div>
    </div>

    <!-- KALENDER -->
    <div class="card p-3">
        <h4>Cek Ketersediaan Tanggal</h4>

        <input type="date" id="datePicker" class="form-control mb-3">

        <button id="bookingBtn" class="btn btn-success" disabled>
            Booking Sekarang
        </button>
    </div>

</div>

<script>
    const bookedDates = @json($bookings);

    document.getElementById('datePicker').addEventListener('change', function() {
        let selected = this.value;

        if (bookedDates.includes(selected)) {
            alert("Tanggal sudah dibooking!");
            document.getElementById('bookingBtn').disabled = true;
        } else {
            document.getElementById('bookingBtn').disabled = false;
        }

        document.getElementById('bookingBtn').onclick = function() {
            window.location.href = "/booking?date=" + selected;
        }
    });
</script>
