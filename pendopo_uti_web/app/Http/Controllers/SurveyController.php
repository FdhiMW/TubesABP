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
        $validated = $request->validate([
            'venue_id'      => 'nullable|exists:venues,id',
            'proposed_date' => 'required|date',
            'proposed_time' => 'required|date_format:H:i|after_or_equal:07:00|before_or_equal:22:00',
            'notes'         => 'nullable|string',
        ]);

        $venueId = $validated['venue_id'] ?? $request->input('venue_id', 1);

        $totalBooking = Booking::where('venue_id', $venueId)
            ->where('event_date', $validated['proposed_date'])
            ->count();

        $totalSurvey = Survey::where('venue_id', $venueId)
            ->where('proposed_date', $validated['proposed_date'])
            ->count();

        if (($totalBooking + $totalSurvey) >= 2) {
            return $this->surveyFail(
                $request,
                'Tanggal sudah penuh (maksimal 2 booking)',
                'proposed_date'
            );
        }

        $start = Carbon::parse($validated['proposed_time']);
        $end = $start->copy()->addHour();

        $bookingConflict = Booking::where('venue_id', $venueId)
            ->where('event_date', $validated['proposed_date'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($start, $end) {
                $query->where('event_time', '<', $end)
                    ->where('end_time', '>', $start);
            })
            ->exists();

        $surveyConflict = Survey::where('venue_id', $venueId)
            ->where('proposed_date', $validated['proposed_date'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($start, $end) {
                $query->where('proposed_time', '<', $end)
                    ->whereRaw('ADDTIME(proposed_time, "01:00:00") > ?', [$start]);
            })
            ->exists();

        if ($bookingConflict || $surveyConflict) {
            return $this->surveyFail(
                $request,
                'Waktu tidak tersedia (bentrok dengan booking atau survey).',
                'proposed_date'
            );
        }

        $survey = Survey::create([
            'user_id'       => auth()->id(),
            'venue_id'      => $venueId,
            'proposed_date' => $validated['proposed_date'],
            'proposed_time' => $validated['proposed_time'],
            'end_time'      => $end->format('H:i:s'),
            'notes'         => $validated['notes'] ?? null,
            'status'        => 'pending',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Survey berhasil dibooking!',
                'data'    => $survey,
            ], 201);
        }

        return redirect()->route('booking.create')->with('success', 'Survey berhasil dibooking!');
    }

    private function surveyFail(Request $request, string $message, string $field)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors'  => [$field => [$message]],
            ], 422);
        }

        return back()->withErrors([$field => $message])->withInput();
    }
}
