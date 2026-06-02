<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class ManageController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with('venue')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $surveys = Survey::with('venue')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'bookings' => $bookings,
                'surveys'  => $surveys,
            ]);
        }

        return view('manage.index', compact('bookings', 'surveys'));
    }

    // ================= RESCHEDULE FORMS =================
    public function rescheduleBookingForm($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id != auth()->id()) {
            abort(403);
        }

        return view('manage.reschedule_booking', compact('booking'));
    }

    public function rescheduleSurveyForm($id)
    {
        $survey = Survey::findOrFail($id);

        if ($survey->user_id != auth()->id()) {
            abort(403);
        }

        return view('manage.reschedule_survey', compact('survey'));
    }

    // ================= CANCEL BOOKING =================
    public function cancelBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id != auth()->id()) {
            return $this->deny($request, 'Forbidden', Response::HTTP_FORBIDDEN);
        }

        if ($booking->status != 'pending') {
            return $this->fail($request, 'Tidak bisa cancel, sudah disetujui');
        }

        $booking->status = 'cancelled';
        $booking->save();

        return $this->ok($request, 'Booking berhasil dibatalkan');
    }

    // ================= CANCEL SURVEY =================
    public function cancelSurvey(Request $request, $id)
    {
        $survey = Survey::findOrFail($id);

        if ($survey->user_id != auth()->id()) {
            return $this->deny($request, 'Forbidden', Response::HTTP_FORBIDDEN);
        }

        if ($survey->status != 'pending') {
            return $this->fail($request, 'Tidak bisa cancel, sudah disetujui');
        }

        $survey->status = 'cancelled';
        $survey->save();

        return $this->ok($request, 'Survey berhasil dibatalkan');
    }

    // ================= RESCHEDULE BOOKING =================
    public function rescheduleBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id != auth()->id()) {
            return $this->deny($request, 'Forbidden', Response::HTTP_FORBIDDEN);
        }

        if ($booking->status != 'pending') {
            return $this->fail($request, 'Tidak bisa reschedule');
        }

        $request->validate([
            'event_date' => 'required|date',
            'event_time' => 'required|date_format:H:i|after_or_equal:07:00|before_or_equal:22:00',
            'end_time'   => 'required|date_format:H:i|after:event_time|before_or_equal:22:00',
        ]);

        $totalBooking = Booking::where('venue_id', $booking->venue_id)
            ->where('event_date', $request->event_date)
            ->where('id', '!=', $booking->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $totalSurvey = Survey::where('venue_id', $booking->venue_id)
            ->where('proposed_date', $request->event_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if (($totalBooking + $totalSurvey) >= 2) {
            return $this->fail($request, 'Tanggal sudah penuh');
        }

        $bookingConflict = Booking::where('venue_id', $booking->venue_id)
            ->where('event_date', $request->event_date)
            ->where('id', '!=', $booking->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('event_time', [$request->event_time, $request->end_time])
                ->orWhereBetween('end_time', [$request->event_time, $request->end_time])
                ->orWhere(function ($q2) use ($request) {
                    $q2->where('event_time', '<=', $request->event_time)
                        ->where('end_time', '>=', $request->end_time);
                });
            })
            ->exists();

        if ($bookingConflict) {
            return $this->fail($request, 'Waktu bentrok dengan booking lain');
        }

        $surveyConflict = Survey::where('venue_id', $booking->venue_id)
            ->where('proposed_date', $request->event_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('proposed_time', [$request->event_time, $request->end_time])
                ->orWhereBetween('end_time', [$request->event_time, $request->end_time])
                ->orWhere(function ($q2) use ($request) {
                    $q2->where('proposed_time', '<=', $request->event_time)
                        ->where('end_time', '>=', $request->end_time);
                });
            })
            ->exists();

        if ($surveyConflict) {
            return $this->fail($request, 'Waktu bentrok dengan jadwal survey');
        }

        $booking->event_date = $request->event_date;
        $booking->event_time = $request->event_time;
        $booking->end_time   = $request->end_time;

        $booking->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reschedule booking berhasil',
                'data'    => $booking->load('venue'),
            ]);
        }

        return redirect()->route('manage.index')->with('success', 'Reschedule booking berhasil');
    }

    // ================= RESCHEDULE SURVEY =================
    public function rescheduleSurvey(Request $request, $id)
    {
        $survey = Survey::findOrFail($id);

        if ($survey->user_id != auth()->id()) {
            return $this->deny($request, 'Forbidden', Response::HTTP_FORBIDDEN);
        }

        if ($survey->status != 'pending') {
            return $this->fail($request, 'Tidak bisa reschedule');
        }

        $request->validate([
            'proposed_date' => 'required|date',
            'proposed_time' => 'required|date_format:H:i|after_or_equal:07:00|before_or_equal:22:00',
        ]);

        $totalBooking = Booking::where('venue_id', $survey->venue_id)
            ->where('event_date', $request->proposed_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $totalSurvey = Survey::where('venue_id', $survey->venue_id)
            ->where('proposed_date', $request->proposed_date)
            ->where('id', '!=', $survey->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if (($totalBooking + $totalSurvey) >= 2) {
            return $this->fail($request, 'Tanggal sudah penuh');
        }

        $survey->proposed_date = $request->proposed_date;
        $survey->proposed_time = $request->proposed_time;
        $survey->end_time      = Carbon::parse($request->proposed_time)->addHour();

        $start = $request->proposed_time;
        $end = Carbon::parse($start)->addHour()->format('H:i');

        $conflict = Survey::where('venue_id', $survey->venue_id)
            ->where('proposed_date', $request->proposed_date)
            ->where('id', '!=', $survey->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('proposed_time', [$start, $end])
                ->orWhereBetween('end_time', [$start, $end])
                ->orWhere(function ($q2) use ($start, $end) {
                    $q2->where('proposed_time', '<=', $start)
                        ->where('end_time', '>=', $end);
                });
            })
            ->exists();

        if ($conflict) {
            return $this->fail($request, 'Waktu survey bentrok');
        }

        $bookingConflict = Booking::where('venue_id', $survey->venue_id)
            ->where('event_date', $request->proposed_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('event_time', [$start, $end])
                ->orWhereBetween('end_time', [$start, $end])
                ->orWhere(function ($q2) use ($start, $end) {
                    $q2->where('event_time', '<=', $start)
                        ->where('end_time', '>=', $end);
                });
            })
            ->exists();

        if ($bookingConflict) {
            return $this->fail($request, 'Waktu bentrok dengan booking venue');
        }

        $survey->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reschedule survey berhasil',
                'data'    => $survey->load('venue'),
            ]);
        }

        return redirect()->route('manage.index')->with('success', 'Reschedule survey berhasil');
    }

    private function ok(Request $request, string $message): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    private function fail(Request $request, string $message): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return back()->withErrors($message);
    }

    private function deny(Request $request, string $message, int $status): JsonResponse|\Illuminate\Http\Response
    {
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }

        abort($status, $message);
    }
}
