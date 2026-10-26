<?php

namespace App\Http\Controllers;

use App\Enums\RolePengguna;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login view or redirect if already authenticated.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended($this->getDashboardRoute(Auth::user()->role));
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirect authenticated user to appropriate dashboard based on their role.
     */
    public function redirectBasedOnRole(RolePengguna|string $role): RedirectResponse
    {
        return redirect()->to($this->getDashboardRoute($role));
    }

    /**
     * Get dashboard URL route for a given role.
     */
    public function getDashboardRoute(RolePengguna|string $role): string
    {
        $roleValue = $role instanceof RolePengguna ? $role->value : (string) $role;

        return match ($roleValue) {
            'admin' => route('admin.dashboard'),
            'petugas' => route('petugas.dashboard'),
            'teknisi' => route('teknisi.dashboard'),
            default => route('login'),
        };
    }
}
