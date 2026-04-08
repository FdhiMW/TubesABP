<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| Halaman Utama → redirect ke login
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
| Protected Routes (harus sudah login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    // Nanti tambah route lain di sini:
    // Route::resource('venues', VenueController::class);
    // Route::resource('bookings', BookingController::class);
    // dll.
});

Route::get('/venue', [VenueController::class, 'show']);

Route::get('/booking-step1', [BookingController::class, 'step1']);
Route::post('/booking-step1', [BookingController::class, 'step1Store']);

Route::get('/booking-step2', [BookingController::class, 'step2']);
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

