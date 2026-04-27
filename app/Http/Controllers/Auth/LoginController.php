<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    /**
     * GET /login
     */
    public function showForm()
    {
        return view('auth.login');
    }

    /**
     * POST /login
     */
    public function login(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Admin → admin dashboard. User biasa → home.
        $redirectRoute = auth()->user()->isAdmin() ? 'admin.dashboard' : 'home';

        return redirect()
            ->intended(route($redirectRoute))
            ->with('success', 'Login berhasil!');
    }
}