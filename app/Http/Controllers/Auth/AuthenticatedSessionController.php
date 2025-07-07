<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // ==========================================================
        // AWAL DARI PERUBAHAN LOGIKA PENGALIHAN
        // ==========================================================

        $user = Auth::user(); // Ambil data user yang baru saja login

        if ($user->hasRole('fakultas')) {
            return redirect()->intended(route('fakultas.dashboard'));
        }

        if ($user->hasRole('prodi')) {
            return redirect()->intended(route('prodi.dashboard'));
        }

        if ($user->hasRole('mahasiswa')) {
            return redirect()->intended(route('mahasiswa.dashboard'));
        }

        // Pengalihan default jika user tidak punya peran spesifik
        // atau untuk kasus lain. Arahkan ke halaman yang umum.
        return redirect()->intended(route('hasil.fet'));
        // ==========================================================
        // AKHIR DARI PERUBAHAN LOGIKA PENGALIHAN
        // ==========================================================
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
