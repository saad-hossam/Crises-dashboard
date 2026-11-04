<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = 'login.' . Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => [__('auth.throttle', ['seconds' => $seconds])],
            ]);
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        // Block inactive accounts AFTER credential validation
        if (! Auth::user()->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => [__('auth.inactive')],
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        // Record last login timestamp
        Auth::user()->update(['last_login_at' => now()]);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}