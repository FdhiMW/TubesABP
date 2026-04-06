
<div class="container">

    <div class="card p-4">
        <h3>Booking Pendopo Uti</h3>

        <form action="{{ route('booking.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Tanggal Acara</label>
                <input type="date" name="event_date" 
                       value="{{ $date }}" 
                       class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label>Catatan Tambahan</label>
                <textarea name="note" class="form-control"></textarea>
            </div>

            <button class="btn btn-primary">Submit Booking</button>
        </form>
    </div>

</div>
