<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\BookingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/venue', [VenueController::class, 'show'])->name('venue.show');

Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');

Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');