<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Survey;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function form()
    {
        $user = auth()->user();

        return view('booking.form', compact('user'));
    }

    public function create()
    {
        return view('booking.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_date' => 'required|date',
            'event_time' => 'required',
            'end_time' => 'required',
            'guest_count' => 'required|integer',
            'venue_id' => 'required',
        ]);

        $start = Carbon::parse($request->event_time);
        $end = Carbon::parse($request->end_time);

        // 🔥 CEK BENTROK BOOKING
        $bookingExists = Booking::where('venue_id', $request->venue_id)
            ->where('event_date', $request->event_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($start, $end) {
                $query->where('event_time', '<', $end)
                    ->where('end_time', '>', $start);
            })
            ->exists();

        // 🔥 CEK BENTROK SURVEY (anggap survey = 1 jam)
        $surveyExists = Survey::where('venue_id', $request->venue_id)
            ->whereDate('proposed_date', $request->event_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($start, $end) {
                $query->where('proposed_time', '<', $end)
                    ->whereRaw('ADDTIME(proposed_time, "01:00:00") > ?', [$start]);
            })
            ->exists();

        // 🔥 VALIDASI BENTROK
        if ($bookingExists || $surveyExists) {
            return back()->withErrors([
                'event_date' => 'Waktu tidak tersedia (bentrok dengan booking atau survey).'
            ])->withInput();
        }

        // 🔥 SIMPAN DATA
        Booking::create([
            'booking_code' => 'BOOK-' . time(),
            'user_id' => auth()->id(),
            'venue_id' => $request->venue_id,
            'event_date' => $request->event_date,
            'end_date' => $request->event_date,
            'event_time' => $request->event_time,
            'end_time' => $request->end_time, // 🔥 tambahan
            'guest_count' => $request->guest_count,
            'total_price' => 25000000,
            'status' => 'pending',
        ]);

        return redirect()->route('home')
            ->with('success', 'Booking berhasil!');
    }
}