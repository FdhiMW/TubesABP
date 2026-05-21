<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Survey;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function form()
    {
        $user     = auth()->user();
        $packages = Package::active()->get();   // hanya paket aktif
        $venue    = Venue::first();

        return view('booking.form', compact('user', 'packages', 'venue'));
    }

    public function create()
    {
        return view('booking.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_date'  => 'required|date|after_or_equal:today',
            'event_time'  => 'required|date_format:H:i|after_or_equal:07:00|before_or_equal:22:00',
            'end_time'    => 'required|date_format:H:i|before_or_equal:22:00',
            'guest_count' => 'required|integer|min:1',
            'venue_id'    => 'required|exists:venues,id',
            'package_id'  => 'required|exists:packages,id',
        ], [
            'package_id.required' => 'Mohon pilih paket terlebih dahulu.',
            'package_id.exists'   => 'Paket yang dipilih tidak valid.',
        ]);

        // Pastikan paket masih aktif
        $package = Package::active()->find($request->package_id);
        if (! $package) {
            return back()->withErrors([
                'package_id' => 'Paket yang dipilih sudah tidak tersedia.',
            ])->withInput();
        }

        // Validasi manual: end_time > event_time
        if ($request->end_time <= $request->event_time) {
            return back()->withErrors([
                'end_time' => 'Waktu selesai harus setelah waktu mulai.',
            ])->withInput();
        }

        $venue = Venue::findOrFail($request->venue_id);
        $activeBookingStatus = ['pending', 'awaiting_payment', 'paid', 'confirmed'];
        $activeSurveyStatus  = ['pending', 'confirmed'];

        // Cek 1: Full-day taken?
        $isFullDayTaken = Booking::where('venue_id', $request->venue_id)
            ->where('event_date', $request->event_date)
            ->whereIn('status', $activeBookingStatus)
            ->where('event_time', '<=', '07:00:00')
            ->where('end_time', '>=', '22:00:00')
            ->exists();

        if ($isFullDayTaken) {
            return back()->withErrors([
                'event_date' => 'Tanggal ini sudah dipesan untuk seharian penuh (07:00 - 22:00).',
            ])->withInput();
        }

        // Cek 2: Slot penuh (max 2)?
        $totalBooking = Booking::where('venue_id', $request->venue_id)
            ->where('event_date', $request->event_date)
            ->whereIn('status', $activeBookingStatus)
            ->count();

        $totalSurvey = Survey::where('venue_id', $request->venue_id)
            ->where('proposed_date', $request->event_date)
            ->whereIn('status', $activeSurveyStatus)
            ->count();

        if (($totalBooking + $totalSurvey) >= 2) {
            return back()->withErrors([
                'event_date' => 'Tanggal ini sudah penuh (maksimal 2 booking per hari).',
            ])->withInput();
        }

        // Cek 3: Minta full-day padahal sudah terisi?
        $isFullDayRequest = $request->event_time === '07:00' && $request->end_time === '22:00';

        if ($isFullDayRequest && ($totalBooking + $totalSurvey) >= 1) {
            return back()->withErrors([
                'event_date' => 'Tidak bisa booking seharian — sudah ada booking/survey lain di tanggal ini.',
            ])->withInput();
        }

        // Cek 4: Bentrok jam
        $start = Carbon::parse($request->event_time);
        $end   = Carbon::parse($request->end_time);

        $bookingConflict = Booking::where('venue_id', $request->venue_id)
            ->where('event_date', $request->event_date)
            ->whereIn('status', $activeBookingStatus)
            ->where(function ($q) use ($start, $end) {
                $q->where('event_time', '<', $end->format('H:i:s'))
                  ->where('end_time',   '>', $start->format('H:i:s'));
            })
            ->exists();

        $surveyConflict = Survey::where('venue_id', $request->venue_id)
            ->whereDate('proposed_date', $request->event_date)
            ->whereIn('status', $activeSurveyStatus)
            ->where(function ($q) use ($start, $end) {
                $q->where('proposed_time', '<', $end->format('H:i:s'))
                  ->whereRaw('ADDTIME(proposed_time, "01:00:00") > ?', [$start->format('H:i:s')]);
            })
            ->exists();

        if ($bookingConflict || $surveyConflict) {
            return back()->withErrors([
                'event_time' => 'Jam yang Anda pilih bentrok dengan booking/survey lain.',
            ])->withInput();
        }

        // Simpan booking
        Booking::create([
            'booking_code' => 'BOOK-' . strtoupper(uniqid()),
            'user_id'      => auth()->id(),
            'venue_id'     => $venue->id,
            'event_date'   => $request->event_date,
            'end_date'     => $request->event_date,
            'event_time'   => $request->event_time,
            'end_time'     => $request->end_time,
            'guest_count'  => $request->guest_count,
            'package_id'   => $package->id,
            'total_price'  => $package->price,
            'status'       => 'pending',
        ]);

        return redirect()->route('manage.index')
            ->with('success', 'Booking berhasil dibuat! Menunggu persetujuan admin.');
    }

    public function availability()
    {
        $activeBookingStatus = ['pending', 'awaiting_payment', 'paid', 'confirmed'];
        $activeSurveyStatus  = ['pending', 'confirmed'];

        $bookings = Booking::selectRaw('event_date as date, COUNT(*) as total')
            ->whereIn('status', $activeBookingStatus)
            ->groupBy('event_date')
            ->get();

        $surveys = Survey::selectRaw('proposed_date as date, COUNT(*) as total')
            ->whereIn('status', $activeSurveyStatus)
            ->groupBy('proposed_date')
            ->get();

        $dates = [];
        foreach ($bookings as $b) $dates[$b->date] = ($dates[$b->date] ?? 0) + $b->total;
        foreach ($surveys as $s)  $dates[$s->date] = ($dates[$s->date] ?? 0) + $s->total;

        $fullDayDates = Booking::whereIn('status', $activeBookingStatus)
            ->where('event_time', '<=', '07:00:00')
            ->where('end_time', '>=', '22:00:00')
            ->pluck('event_date')
            ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        $events = [];
        $start = Carbon::now()->startOfYear();
        $end   = Carbon::now()->endOfYear();

        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $d     = $date->format('Y-m-d');
            $total = $dates[$d] ?? 0;

            if (in_array($d, $fullDayDates) || $total >= 2) {
                $color = '#ff6b6b'; $title = 'Penuh';
            } elseif ($total == 1) {
                $color = '#ffe066'; $title = '1 booking';
            } else {
                $color = '#b7e4c7'; $title = 'Tersedia';
            }

            $events[] = [
                'title'   => $title,
                'start'   => $d,
                'display' => 'background',
                'color'   => $color,
            ];
        }

        return response()->json($events);
    }
}