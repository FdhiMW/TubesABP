<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SurveyController;

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
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
Route::get('/booking/form', [BookingController::class, 'form'])->name('booking.form');
Route::get('/survey/form', [SurveyController::class, 'form'])->name('survey.form');
Route::get('/survey/create', [SurveyController::class, 'create'])->name('survey.create');
Route::post('/survey/store', [SurveyController::class, 'store'])->name('survey.store');
