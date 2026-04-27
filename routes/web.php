<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Utama
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('dashboard.home');
})->name('home');

/*
|--------------------------------------------------------------------------
| Auth Routes (hanya untuk tamu / belum login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/register',  [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login',  [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/venue', [VenueController::class, 'show']);
Route::get('/availability-data', [BookingController::class, 'availability']);

/*
|--------------------------------------------------------------------------
| Protected Routes — User
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    // Booking
    Route::get('/booking',        [BookingController::class, 'create'])->name('booking.create');
    Route::get('/booking/form',   [BookingController::class, 'form'])->name('booking.form');
    Route::post('/booking',       [BookingController::class, 'store'])->name('booking.store');

    // Survey
    Route::get('/survey/form',    [SurveyController::class, 'form'])->name('survey.form');
    Route::get('/survey/create',  [SurveyController::class, 'create'])->name('survey.create');
    Route::post('/survey/store',  [SurveyController::class, 'store'])->name('survey.store');

    // Manage
    Route::get('/manage', [ManageController::class, 'index'])->name('manage.index');

    Route::post('/booking/{id}/cancel', [ManageController::class, 'cancelBooking'])->name('booking.cancel');
    Route::post('/survey/{id}/cancel',  [ManageController::class, 'cancelSurvey'])->name('survey.cancel');

    Route::get('/booking/{id}/reschedule',  [ManageController::class, 'rescheduleBookingForm'])->name('booking.reschedule.form');
    Route::post('/booking/{id}/reschedule', [ManageController::class, 'rescheduleBooking'])->name('booking.reschedule');

    Route::get('/survey/{id}/reschedule',  [ManageController::class, 'rescheduleSurveyForm'])->name('survey.reschedule.form');
    Route::post('/survey/{id}/reschedule', [ManageController::class, 'rescheduleSurvey'])->name('survey.reschedule');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (login + role admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Booking Management
    Route::get('/bookings',                [AdminController::class, 'bookings'])->name('bookings.index');
    Route::get('/bookings/{id}',           [AdminController::class, 'showBooking'])->name('bookings.show');
    Route::post('/bookings/{id}/approve',  [AdminController::class, 'approveBooking'])->name('bookings.approve');
    Route::post('/bookings/{id}/reject',   [AdminController::class, 'rejectBooking'])->name('bookings.reject');
    Route::post('/bookings/{id}/confirm',  [AdminController::class, 'confirmBooking'])->name('bookings.confirm');

    // Survey Management
    Route::get('/surveys',                 [AdminController::class, 'surveys'])->name('surveys.index');
    Route::get('/surveys/{id}',            [AdminController::class, 'showSurvey'])->name('surveys.show');
    Route::post('/surveys/{id}/approve',   [AdminController::class, 'approveSurvey'])->name('surveys.approve');
    Route::post('/surveys/{id}/reject',    [AdminController::class, 'rejectSurvey'])->name('surveys.reject');
    Route::post('/surveys/{id}/complete',  [AdminController::class, 'completeSurvey'])->name('surveys.complete');
});