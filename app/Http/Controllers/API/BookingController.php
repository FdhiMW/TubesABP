<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
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
        $validated = $request->validate([
            'venue_id'    => 'required|exists:venues,id',
            'event_date'  => 'required|date|after_or_equal:today',
            'end_date'    => 'nullable|date|after_or_equal:event_date',
            'event_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:event_time',
            'guest_count' => 'required|integer|min:1',
        ]);

        $venue = Venue::findOrFail($validated['venue_id']);

        $eventDate = Carbon::parse($validated['event_date']);
        $endDate   = Carbon::parse($validated['end_date'] ?? $validated['event_date']);
        $days      = max(1, $eventDate->diffInDays($endDate) + 1);

        $booking = Booking::create([
            'booking_code' => 'BOOK-' . strtoupper(uniqid()),
            'user_id'      => $request->user()->id,
            'venue_id'     => $venue->id,
            'event_date'   => $validated['event_date'],
            'end_date'     => $validated['end_date'] ?? $validated['event_date'],
            'event_time'   => $validated['event_time'],
            'end_time'     => $validated['end_time'],
            'guest_count'  => $validated['guest_count'],
            'total_price'  => $venue->price_per_day * $days,
            'status'       => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibuat.',
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

        $booking->update(['payment_reference' => $snapToken]);

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