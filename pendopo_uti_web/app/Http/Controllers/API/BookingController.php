<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Survey;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    /**
     * POST /api/bookings
     */
    public function store(Request $request): JsonResponse
    {
        $venue    = Venue::find($request->venue_id);
        $maxGuest = $venue?->capacity ?? 1;

        $request->validate([
            'venue_id'    => 'required|exists:venues,id',
            'package_id'  => 'required|exists:packages,id',
            'event_date'  => 'required|date|after_or_equal:today',
            'event_time'  => 'required|date_format:H:i|after_or_equal:07:00|before_or_equal:22:00',
            'end_time'    => 'required|date_format:H:i|before_or_equal:22:00',
            'guest_count' => "required|integer|min:1|max:{$maxGuest}",
        ], [
            'package_id.required'  => 'Mohon pilih paket terlebih dahulu.',
            'package_id.exists'    => 'Paket yang dipilih tidak valid.',
            'event_time.after_or_equal'  => 'Jam mulai minimal 07:00.',
            'event_time.before_or_equal' => 'Jam mulai maksimal 22:00.',
            'end_time.before_or_equal'   => 'Jam selesai maksimal 22:00.',
            'guest_count.max'      => "Jumlah tamu melebihi kapasitas venue (maksimal {$maxGuest} orang).",
        ]);

        // Paket aktif
        $package = Package::active()->find($request->package_id);
        if (! $package) {
            return response()->json([
                'success' => false,
                'message' => 'Paket yang dipilih sudah tidak tersedia.',
                'errors'  => ['package_id' => ['Paket yang dipilih sudah tidak tersedia.']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // end_time harus setelah event_time
        if ($request->end_time <= $request->event_time) {
            return response()->json([
                'success' => false,
                'message' => 'Waktu selesai harus setelah waktu mulai.',
                'errors'  => ['end_time' => ['Waktu selesai harus setelah waktu mulai.']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $activeBookingStatus = ['pending', 'awaiting_payment', 'paid', 'confirmed'];
        $activeSurveyStatus  = ['pending', 'confirmed'];

        // Cek full-day sudah terisi
        $isFullDayTaken = Booking::where('venue_id', $request->venue_id)
            ->where('event_date', $request->event_date)
            ->whereIn('status', $activeBookingStatus)
            ->where('event_time', '<=', '07:00:00')
            ->where('end_time', '>=', '22:00:00')
            ->exists();

        if ($isFullDayTaken) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal ini sudah dipesan untuk seharian penuh (07:00 - 22:00).',
                'errors'  => ['event_date' => ['Tanggal ini sudah dipesan untuk seharian penuh.']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Cek slot penuh (max 2)
        $totalBooking = Booking::where('venue_id', $request->venue_id)
            ->where('event_date', $request->event_date)
            ->whereIn('status', $activeBookingStatus)
            ->count();

        $totalSurvey = Survey::where('venue_id', $request->venue_id)
            ->where('proposed_date', $request->event_date)
            ->whereIn('status', $activeSurveyStatus)
            ->count();

        if (($totalBooking + $totalSurvey) >= 2) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal ini sudah penuh (maksimal 2 booking per hari).',
                'errors'  => ['event_date' => ['Tanggal ini sudah penuh.']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Cek request full-day tapi slot sudah ada isi
        $isFullDayRequest = $request->event_time === '07:00' && $request->end_time === '22:00';
        if ($isFullDayRequest && ($totalBooking + $totalSurvey) >= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa booking seharian — sudah ada booking/survey lain di tanggal ini.',
                'errors'  => ['event_date' => ['Tidak bisa booking seharian.']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Cek bentrok jam
        $start = Carbon::parse($request->event_time);
        $end   = Carbon::parse($request->end_time);

        $bookingConflict = Booking::where('venue_id', $request->venue_id)
            ->where('event_date', $request->event_date)
            ->whereIn('status', $activeBookingStatus)
            ->where(function ($q) use ($start, $end) {
                $q->where('event_time', '<', $end->format('H:i:s'))
                  ->where('end_time', '>', $start->format('H:i:s'));
            })->exists();

        $surveyConflict = Survey::where('venue_id', $request->venue_id)
            ->whereDate('proposed_date', $request->event_date)
            ->whereIn('status', $activeSurveyStatus)
            ->where(function ($q) use ($start, $end) {
                $q->where('proposed_time', '<', $end->format('H:i:s'))
                  ->whereRaw('ADDTIME(proposed_time, "01:00:00") > ?', [$start->format('H:i:s')]);
            })->exists();

        if ($bookingConflict || $surveyConflict) {
            return response()->json([
                'success' => false,
                'message' => 'Jam yang dipilih bentrok dengan booking/survey lain.',
                'errors'  => ['event_time' => ['Jam yang dipilih bentrok dengan booking/survey lain.']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $booking = Booking::create([
            'booking_code' => 'BOOK-' . strtoupper(uniqid()),
            'user_id'      => $request->user()->id,
            'venue_id'     => $venue->id,
            'package_id'   => $package->id,
            'event_date'   => $request->event_date,
            'end_date'     => $request->event_date,
            'event_time'   => $request->event_time,
            'end_time'     => $request->end_time,
            'guest_count'  => $request->guest_count,
            'total_price'  => $package->price,
            'status'       => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibuat! Menunggu persetujuan admin.',
            'data'    => $booking,
        ], Response::HTTP_CREATED);
    }

    /**
     * POST /api/bookings/{id}/payment
     */
    public function createPayment(Request $request, int $id): JsonResponse
    {
        $booking = Booking::with('user')->findOrFail($id);

        if ($booking->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak mengakses booking ini.',
            ], Response::HTTP_FORBIDDEN);
        }

        if ($booking->status !== 'awaiting_payment') {
            return response()->json([
                'success' => false,
                'message' => 'Booking ini belum siap untuk dibayar.',
            ], Response::HTTP_BAD_REQUEST);
        }

        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        $params = [
            'transaction_details' => [
                'order_id'     => $booking->booking_code,
                'gross_amount' => (int) $booking->total_price,
            ],
            'customer_details' => [
                'first_name' => $booking->user->name,
                'email'      => $booking->user->email,
                'phone'      => $booking->user->phone,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'success' => true,
            'data'    => ['token' => $snapToken],
        ]);
    }

    /**
     * POST /api/bookings/callback
     */
    public function callback(Request $request): JsonResponse
    {
        $booking = Booking::where('booking_code', $request->order_id)->first();

        if (! $booking) {
            return response()->json(['message' => 'Booking tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }

        $status = match ($request->transaction_status) {
            'settlement', 'capture' => 'paid',
            'pending'               => 'awaiting_payment',
            'cancel', 'expire'      => 'cancelled',
            'deny', 'failure'       => 'cancelled',
            default                 => $booking->status,
        };

        $booking->update(['status' => $status]);

        return response()->json(['message' => 'OK']);
    }
}