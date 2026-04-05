<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\Booking;

class VenueController extends Controller
{
    public function show()
    {
        $venue = Venue::first(); // karena cuma 1 venue

        $bookings = Booking::pluck('start_date')->toArray();

        return view('venue.show', compact('venue', 'bookings'));
    }
}
