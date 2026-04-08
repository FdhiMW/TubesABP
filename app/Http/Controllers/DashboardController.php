<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    /**
     * GET /dashboard
     * Tampilkan halaman dashboard setelah login.
     */
    public function index()
    {
        $user = auth()->user();

        return view('dashboard.index', compact('user'));
    }
}