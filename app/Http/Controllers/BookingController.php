<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function step1()
    {
        $user = Auth::user();

        return view('booking.step1', compact('user'));
    }

    public function step1Store(Request $request)
    {
        session([
            'booking.name' => $request->name,
            'booking.email' => $request->email,
            'booking.phone' => $request->phone,
        ]);

        return redirect('/booking-step2');
    }

    public function step2()
    {
        return view('booking.step2');
    }

    public function store(Request $request)
    {
        Booking::create([
            'booking_code' => 'BOOK-' . time(),
            'user_id' => 1, // sementara
            'venue_id' => 1,
            'start_date' => $request->event_date,
            'end_date' => $request->event_date,
            'total_price' => 25000000,
            'status' => 'pending'
        ]);

        session()->forget('booking');

        return redirect('/venue')->with('success', 'Booking berhasil!');
    }
}