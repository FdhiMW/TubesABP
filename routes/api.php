<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BookingController;

Route::get('/test', function () {
    return response()->json(['message' => 'API OK']);
});

Route::post('/booking', [BookingController::class, 'store']);

Route::get('/payment/{id}', [BookingController::class, 'createPayment']);

Route::post('/midtrans/callback', [BookingController::class, 'callback']);