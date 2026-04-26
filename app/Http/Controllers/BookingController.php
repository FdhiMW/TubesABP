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
            'event_time' => 'required|date_format:H:i|after_or_equal:07:00|before_or_equal:22:00',
            'end_time' => 'required',
            'guest_count' => 'required|integer',
            'venue_id' => 'required',
        ]);

        $totalBooking = Booking::where('venue_id', $request->venue_id)
            ->where('event_date', $request->event_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $totalSurvey = Survey::where('venue_id', $request->venue_id)
            ->where('proposed_date', $request->event_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if (($totalBooking + $totalSurvey) >= 2) {
            return back()->withErrors([
                'event_date' => 'Tanggal sudah penuh (maksimal 2 booking)'
            ])->withInput();
        }

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

        return redirect()->route('booking.create')
            ->with('success', 'Booking berhasil!');
    }

    public function availability()
    {
        $dates = [];

        // booking
        $bookings = Booking::selectRaw('event_date as date, COUNT(*) as total')
            ->whereIn('status', ['pending', 'confirmed'])
            ->groupBy('event_date')
            ->get();

        // survey
        $surveys = Survey::selectRaw('proposed_date as date, COUNT(*) as total')
            ->whereIn('status', ['pending', 'confirmed'])
            ->groupBy('proposed_date')
            ->get();

        // gabung data
        foreach ($bookings as $b) {
            $dates[$b->date] = ($dates[$b->date] ?? 0) + $b->total;
        }

        foreach ($surveys as $s) {
            $dates[$s->date] = ($dates[$s->date] ?? 0) + $s->total;
        }

        $events = [];

        $start = Carbon::now()->startOfYear();
        $end = Carbon::now()->endOfYear();

        for ($date = $start->copy(); $date <= $end; $date->addDay()) {

            $d = $date->format('Y-m-d');
            $total = $dates[$d] ?? 0;

            // 🔥 cek full day
            $isFullDay = Booking::whereDate('event_date', $d)
                ->where('event_time', '<=', '07:00:00')
                ->where('end_time', '>=', '22:00:00')
                ->exists();

            if ($isFullDay) {
                $color = '#ff6b6b'; // merah
            } elseif ($total == 0) {
                $color = '#b7e4c7'; // hijau
            } elseif ($total == 1) {
                $color = '#ffe066'; // kuning
            } else {
                $color = '#ff6b6b'; // merah
            }

            $events[] = [
                'title' => $total . ' booking',
                'start' => $d,
                'display' => 'background', // 🔥 penting
                'color' => $color
            ];
        }

        return response()->json($events);
    }
}