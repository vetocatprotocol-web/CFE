<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $throttleKey = 'login.'.$request->input('email');

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => "Too many failed attempts. Please try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        $credentials = $request->validated();

        if (! Auth::attempt($credentials, $request->boolean('remember_me'))) {
            RateLimiter::hit($throttleKey, 900);

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);

        $user = Auth::user();

        $user->update(['last_login_at' => now()]);

        return $this->redirectBasedOnRole($user);
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    protected function redirectBasedOnRole($user): RedirectResponse
    {
        if ($user->isAdmin() || $user->isOwner()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->isDokter()) {
            return redirect()->intended(route('dokter.dashboard'));
        }

        if ($user->isKasir()) {
            return redirect()->intended(route('kasir.dashboard'));
        }

        if ($user->isCustomer()) {
            return redirect()->intended(route('portal.dashboard'));
        }

        return redirect()->intended(route('login'));
    }
}
