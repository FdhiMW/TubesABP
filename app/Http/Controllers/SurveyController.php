<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking; 
use App\Models\Survey;
use Carbon\Carbon;

class SurveyController extends Controller
{
    public function form()
    {
        $user = auth()->user();

        return view('survey.survey', compact('user'));
    }

    public function create()
    {
        return view('survey.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'proposed_date' => 'required|date',
            'proposed_time' => 'required|date_format:H:i|after_or_equal:07:00|before_or_equal:22:00',
            'notes' => 'nullable|string'
        ]);

        $totalBooking = Booking::where('venue_id', $request->venue_id)
            ->where('event_date', $request->proposed_date)
            ->count();

        $totalSurvey = Survey::where('venue_id', $request->venue_id)
            ->where('proposed_date', $request->proposed_date)
            ->count();

        if (($totalBooking + $totalSurvey) >= 2) {
            return back()->withErrors([
                'proposed_date' => 'Tanggal sudah penuh (maksimal 2 booking)'
            ]);
        }

        $start = Carbon::parse($request->proposed_time);
        $end = $start->copy()->addHour(); // 🔥 survey = 1 jam

        // 🔥 CEK BENTROK BOOKING
        $bookingConflict = Booking::where('venue_id', $request->venue_id)
            ->where('event_date', $request->proposed_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($start, $end) {
                $query->where('event_time', '<', $end)
                    ->where('end_time', '>', $start);
            })
            ->exists();

        // 🔥 CEK BENTROK SURVEY
        $surveyConflict = Survey::where('venue_id', $request->venue_id)
            ->where('proposed_date', $request->proposed_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($start, $end) {
                $query->where('proposed_time', '<', $end)
                    ->whereRaw('ADDTIME(proposed_time, "01:00:00") > ?', [$start]);
            })
            ->exists();

        if ($bookingConflict || $surveyConflict) {
            return back()->withErrors([
                'proposed_date' => 'Waktu tidak tersedia (bentrok dengan booking atau survey).'
            ])->withInput();
        }

        // 🔥 SIMPAN
        Survey::create([
            'user_id' => auth()->id(),
            'venue_id' => $request->venue_id,
            'proposed_date' => $request->proposed_date,
            'proposed_time' => $request->proposed_time,
            'end_time' => $end->format('H:i:s'),
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        return redirect()->route('booking.create')->with('success', 'Survey berhasil dibooking!');
    }
}
