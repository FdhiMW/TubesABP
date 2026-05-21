<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\API\BookingController;
use Illuminate\Support\Facades\Route;
use App\Models\Package;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AiController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (butuh token Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Packages untuk Flutter booking page
    Route::get('/packages', function () {
        return response()->json([
            'success' => true,
            'data'    => Package::active()->get(),
        ]);
    });

    // Booking dari Flutter
    Route::post('/bookings', [BookingController::class, 'store']);

    // Payment Midtrans dari Flutter
    Route::post('/bookings/{id}/payment', [BookingController::class, 'createPayment']);
});

/*
|--------------------------------------------------------------------------
| Midtrans Callback (public, karena dipanggil Midtrans)
|--------------------------------------------------------------------------
*/
Route::post('/bookings/callback', [BookingController::class, 'callback']);