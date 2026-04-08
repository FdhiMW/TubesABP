<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    /**
     * GET /login
     * Tampilkan form login.
     */
    public function showForm()
    {
        return view('auth.login');
    }

    /**
     * POST /login
     * Proses login dengan rate limiting.
     */
    public function login(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()
            ->intended(route('home'))
            ->with('success', 'Login berhasil!');
    }
}