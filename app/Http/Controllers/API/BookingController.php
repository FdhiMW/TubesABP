<?php

namespace App\Http\Controllers\API;

use App\Models\Booking;
use Illuminate\Http\Request;
use Midtrans\Snap;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $booking = Booking::create([
            'user_id' => 1,
            'venue_id' => $request->venue_id,
            'booking_date' => $request->booking_date,
            'total_price' => 5000000,
            'status' => 'pending'
        ]);

        return response()->json($booking);
    }

    public function createPayment($id)
    {
        $booking = Booking::findOrFail($id);

        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => $booking->id,
                'gross_amount' => $booking->total_price,
            ],
            'customer_details' => [
                'first_name' => 'User',
                'email' => 'user@mail.com',
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'token' => $snapToken
        ]);
    }

    public function callback(Request $request)
    {
        $booking = Booking::find($request->order_id);

        if ($request->transaction_status == 'settlement') {
            $booking->status = 'paid';
        } else {
            $booking->status = 'failed';
        }

        $booking->save();

        return response()->json(['message' => 'OK']);
    }
}
