<?php

<<<<<<< Updated upstream
=======
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingController;
>>>>>>> Stashed changes
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
<<<<<<< Updated upstream
=======

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

// Public booking page (dummy data)
Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
Route::get('/booking/form', [BookingController::class, 'showForm'])->name('booking.form');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
>>>>>>> Stashed changes
