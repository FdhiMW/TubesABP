<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;

class RegisterController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    /**
     * GET /register
     * Tampilkan form registrasi.
     */
    public function showForm()
    {
        return view('auth.register');
    }

    /**
     * POST /register
     * Proses registrasi, login otomatis, redirect ke dashboard.
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());

        auth()->login($user);

        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang di Pendopo Uti.');
    }
}