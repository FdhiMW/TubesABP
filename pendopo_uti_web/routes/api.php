<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\BookingController as WebBookingController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;
use App\Models\Package;
use App\Http\Controllers\AiController;
use App\Http\Controllers\Api\NotificationController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Ketersediaan kalender (sama dengan web `/availability-data`, untuk Flutter + CORS api/*)
Route::get('/availability-data', [WebBookingController::class, 'availability']);

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

    // Survey gedung — SurveyController (sama web survey.store)
    Route::post('/surveys', [SurveyController::class, 'store']);

    // Payment Midtrans dari Flutter
    Route::post('/bookings/{id}/payment', [BookingController::class, 'createPayment']);

    // Manage — controller yang sama dengan web (ManageController)
    Route::get('/manage', [ManageController::class, 'index']);
    Route::post('/booking/{id}/cancel', [ManageController::class, 'cancelBooking']);
    Route::post('/survey/{id}/cancel', [ManageController::class, 'cancelSurvey']);
    Route::post('/booking/{id}/reschedule', [ManageController::class, 'rescheduleBooking']);
    Route::post('/survey/{id}/reschedule', [ManageController::class, 'rescheduleSurvey']);
    Route::get('/payment/{id}', [PaymentController::class, 'pay']);
    Route::post('/bookings/{id}/confirm-payment', [PaymentController::class, 'confirmFromClient']);

    // AI Chatbot — AiController (sama web POST /ai/chat)
    Route::post('/ai/chat', [AiController::class, 'chat']);

    // Notifikasi
    Route::post('/save-fcm-token', [AuthController::class, 'saveFcmToken']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
});

/*
|--------------------------------------------------------------------------
| Midtrans Callback (public, karena dipanggil Midtrans)
|--------------------------------------------------------------------------
*/
Route::post('/bookings/callback', [BookingController::class, 'callback']);