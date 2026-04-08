<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    public function create()
    {
        $user = auth()->user();

        return view('booking.form', compact('user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_date' => 'required|date',
            'event_time' => 'required',
            'guest_count' => 'required|integer',
            'venue_id' => 'required',
        ]);

        Booking::create([
            'booking_code' => 'BOOK-' . time(),
            'user_id' => auth()->id(),
            'venue_id' => $request->venue_id,
            'event_date' => $request->event_date,
            'end_date' => $request->event_date,
            'event_time' => $request->event_time,
            'guest_count' => $request->guest_count,
            'total_price' => 25000000,
            'status' => 'pending',
        ]);

        return redirect()->route('home')
            ->with('success', 'Booking berhasil!');
    }
}