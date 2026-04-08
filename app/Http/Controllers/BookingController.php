<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\Booking;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $date = $request->date;

        return view('booking.create', compact('date'));
    }

    public function showForm(Request $request)
    {
        return view('booking.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_date' => 'required|date',
        ]);

        $exists = Booking::where('start_date', $request->event_date)->exists();

        if ($exists) {
            return back()->withErrors('Tanggal sudah dibooking');
        }

        Booking::create([
            'booking_code' => 'BOOK-' . time(),
            'user_id' => 1,
            'venue_id' => 1,
            'start_date' => $request->event_date,
            'end_date' => $request->event_date,
            'total_price' => 25000000,
            'status' => 'pending'
        ]);

        return redirect('/venue')->with('success', 'Booking berhasil!');
    }
}

