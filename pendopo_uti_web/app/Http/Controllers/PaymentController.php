<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Midtrans\Snap;
use Midtrans\Config;

class PaymentController extends Controller
{
    public function pay($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->payment_status == 'paid') {
            return response()->json(['message' => 'Sudah dibayar'], 400);
        }

        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = config('midtrans.isProduction');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = 'BOOK-' . $booking->id . '-' . time();

        // ✅ SIMPAN
        $booking->midtrans_order_id = $orderId;
        $booking->payment_status = 'pending';
        $booking->save();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $booking->total_price,
            ],
            'customer_details' => [
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'token' => $snapToken
        ]);
    }

    public function callback(Request $request)
    {
        \Log::info('MIDTRANS CALLBACK:', $request->all());

        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;
        $fraudStatus = $request->fraud_status ?? null;

        // ✅ FIX: cari berdasarkan order_id
        $booking = Booking::where('midtrans_order_id', $orderId)->first();

        if (!$booking) {
            \Log::error('Booking tidak ditemukan', ['order_id' => $orderId]);
            return response()->json(['message' => 'Not found'], 404);
        }

        // DEBUG tambahan
        \Log::info('UPDATE BOOKING:', [
            'booking_id' => $booking->id,
            'status' => $transactionStatus,
        ]);

        // ✅ HANDLE STATUS
        if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
            $booking->update(['payment_status' => 'paid']);
        } elseif ($transactionStatus === 'settlement') {
            $booking->update(['payment_status' => 'paid']);
        } elseif ($transactionStatus === 'pending') {
            $booking->update(['payment_status' => 'pending']);
        } elseif (in_array($transactionStatus, ['cancel', 'expire', 'deny'])) {
            $booking->update(['payment_status' => 'failed']);
        }

        return response()->json(['message' => 'OK']);
    }
}