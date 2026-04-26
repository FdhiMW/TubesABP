<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Survey;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ManageController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('user_id', auth()->id())->latest()->get();
        $surveys = Survey::where('user_id', auth()->id())->latest()->get();

        return view('manage.index', compact('bookings', 'surveys'));
    }

    // ================= CANCEL BOOKING =================
    public function cancelBooking($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id != auth()->id()) {
            abort(403);
        }

        if ($booking->status != 'pending') {
            return back()->withErrors('Tidak bisa cancel, sudah disetujui');
        }

        $booking->status = 'cancelled';
        $booking->save();

        return back()->with('success', 'Booking berhasil dibatalkan');
    }

    // ================= CANCEL SURVEY =================
    public function cancelSurvey($id)
    {
        $survey = Survey::findOrFail($id);

        if ($survey->user_id != auth()->id()) {
            abort(403);
        }

        if ($survey->status != 'pending') {
            return back()->withErrors('Tidak bisa cancel, sudah disetujui');
        }

        $survey->status = 'cancelled';
        $survey->save();

        return back()->with('success', 'Survey berhasil dibatalkan');
    }

    // ================= RESCHEDULE BOOKING =================
    public function rescheduleBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id != auth()->id()) {
            abort(403);
        }

        if ($booking->status != 'pending') {
            return back()->withErrors('Tidak bisa reschedule');
        }

        $request->validate([
            'event_date' => 'required|date',
            'event_time' => 'required|date_format:H:i|after_or_equal:07:00|before_or_equal:22:00',
            'end_time'   => 'required|date_format:H:i|after:event_time|before_or_equal:22:00',
        ]);

        // 🔥 cek max 2 booking/hari
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
            return back()->withErrors('Tanggal sudah penuh');
        }

        // 🔥 cek bentrok BOOKING
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
            return back()->withErrors('Waktu bentrok dengan booking lain');
        }

        // 🔥 cek bentrok dengan SURVEY
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
            return back()->withErrors('Waktu bentrok dengan jadwal survey');
        }

        // ✅ baru update
        $booking->event_date = $request->event_date;
        $booking->event_time = $request->event_time;
        $booking->end_time   = $request->end_time;

        $booking->save();

        return redirect()->route('manage.index')->with('success', 'Reschedule booking berhasil');
    }

    // ================= RESCHEDULE SURVEY =================
    public function rescheduleSurvey(Request $request, $id)
    {
        $survey = Survey::findOrFail($id);

        if ($survey->user_id != auth()->id()) {
            abort(403);
        }

        if ($survey->status != 'pending') {
            return back()->withErrors('Tidak bisa reschedule');
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
            return back()->withErrors('Tanggal sudah penuh');
        }

        // ✅ tetap auto +1 jam
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
            return back()->withErrors('Waktu survey bentrok');
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
            return back()->withErrors('Waktu bentrok dengan booking venue');
        }

        $survey->save();

        return redirect()->route('manage.index')->with('success', 'Reschedule survey berhasil');
    }
}