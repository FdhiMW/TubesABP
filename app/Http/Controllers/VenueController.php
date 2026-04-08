<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\Booking;

class VenueController extends Controller
{
    public function show()
    {
        $venue = Venue::first();
        $bookings = Booking::pluck('event_date')->toArray();

        return view('venue.show', compact('venue', 'bookings'));
    }
}