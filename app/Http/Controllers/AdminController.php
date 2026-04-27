<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Survey;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * GET /admin
     */
    public function dashboard()
    {
        $stats = [
            'total_users'         => User::where('role', 'user')->count(),
            'total_venues'        => Venue::count(),
            'pending_bookings'    => Booking::where('status', 'pending')->count(),
            'awaiting_payment'    => Booking::where('status', 'awaiting_payment')->count(),
            'paid_bookings'       => Booking::where('status', 'paid')->count(),
            'confirmed_bookings'  => Booking::where('status', 'confirmed')->count(),
            'pending_surveys'     => Survey::where('status', 'pending')->count(),
            'confirmed_surveys'   => Survey::where('status', 'confirmed')->count(),
        ];

        $recentPendingBookings = Booking::with(['user', 'venue'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $recentPendingSurveys = Survey::with(['user', 'venue'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentPendingBookings', 'recentPendingSurveys'));
    }

    // ================= BOOKING MANAGEMENT =================

    public function bookings(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = Booking::with(['user', 'venue'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings', 'status'));
    }

    public function showBooking($id)
    {
        $booking = Booking::with(['user', 'venue'])->findOrFail($id);

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Approve booking: pending → awaiting_payment
     * Auto-cancel booking lain di slot waktu yang sama.
     */
    public function approveBooking($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'pending') {
            return back()->withErrors([
                'status' => 'Hanya booking dengan status "pending" yang bisa disetujui.',
            ]);
        }

        // Cek konflik final
        $conflict = Booking::where('venue_id', $booking->venue_id)
            ->where('event_date', $booking->event_date)
            ->where('id', '!=', $booking->id)
            ->whereIn('status', ['awaiting_payment', 'paid', 'confirmed'])
            ->where(function ($q) use ($booking) {
                $q->where('event_time', '<', $booking->end_time)
                    ->where('end_time', '>', $booking->event_time);
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors([
                'conflict' => 'Tidak bisa approve — sudah ada booking lain yang sudah disetujui di slot waktu ini.',
            ]);
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'awaiting_payment']);

            Booking::where('venue_id', $booking->venue_id)
                ->where('event_date', $booking->event_date)
                ->where('id', '!=', $booking->id)
                ->where('status', 'pending')
                ->where(function ($q) use ($booking) {
                    $q->where('event_time', '<', $booking->end_time)
                        ->where('end_time', '>', $booking->event_time);
                })
                ->update([
                    'status'              => 'cancelled',
                    'cancellation_reason' => 'Slot waktu telah diberikan ke booking lain.',
                    'cancelled_at'        => now(),
                ]);
        });

        return redirect()->route('admin.bookings.show', $booking->id)
            ->with('success', 'Booking disetujui. User akan diminta melakukan pembayaran.');
    }

    public function rejectBooking(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $booking = Booking::findOrFail($id);

        if (! in_array($booking->status, ['pending', 'awaiting_payment'])) {
            return back()->withErrors([
                'status' => 'Booking ini tidak bisa ditolak (status: ' . $booking->status . ').',
            ]);
        }

        $booking->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $request->reason,
            'cancelled_at'        => now(),
        ]);

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking ditolak.');
    }

    public function confirmBooking($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'paid') {
            return back()->withErrors([
                'status' => 'Hanya booking yang sudah "paid" yang bisa dikonfirmasi.',
            ]);
        }

        $booking->update(['status' => 'confirmed']);

        return back()->with('success', 'Booking dikonfirmasi.');
    }

    // ================= SURVEY MANAGEMENT =================

    public function surveys(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = Survey::with(['user', 'venue'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $surveys = $query->paginate(15)->withQueryString();

        return view('admin.surveys.index', compact('surveys', 'status'));
    }

    public function showSurvey($id)
    {
        $survey = Survey::with(['user', 'venue'])->findOrFail($id);

        return view('admin.surveys.show', compact('survey'));
    }

    public function approveSurvey(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $survey = Survey::findOrFail($id);

        if ($survey->status !== 'pending') {
            return back()->withErrors([
                'status' => 'Hanya survey dengan status "pending" yang bisa disetujui.',
            ]);
        }

        $survey->update([
            'status'         => 'confirmed',
            'confirmed_date' => $survey->proposed_date,
            'confirmed_time' => $survey->proposed_time,
            'admin_notes'    => $request->admin_notes,
        ]);

        return redirect()->route('admin.surveys.show', $survey->id)
            ->with('success', 'Jadwal survey disetujui.');
    }

    public function rejectSurvey(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        $survey = Survey::findOrFail($id);

        if ($survey->status !== 'pending') {
            return back()->withErrors([
                'status' => 'Survey ini tidak bisa ditolak (status: ' . $survey->status . ').',
            ]);
        }

        $survey->update([
            'status'      => 'cancelled',
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.surveys.index')
            ->with('success', 'Jadwal survey ditolak.');
    }

    public function completeSurvey($id)
    {
        $survey = Survey::findOrFail($id);

        if ($survey->status !== 'confirmed') {
            return back()->withErrors([
                'status' => 'Hanya survey "confirmed" yang bisa di-complete.',
            ]);
        }

        $survey->update(['status' => 'completed']);

        return back()->with('success', 'Survey ditandai selesai.');
    }
}