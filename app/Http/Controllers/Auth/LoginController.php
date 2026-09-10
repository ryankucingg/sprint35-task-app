<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (!Auth::user()->isAdmin()) {
                Log::warning('Login denied: user has no admin access', [
                    'username' => $credentials['username'],
                    'ip' => $request->ip(),
                ]);
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Akun Anda tidak memiliki akses administrator.',
                ])->onlyInput('username');
            }

            Log::info('Login successful', [
                'user_id' => Auth::id(),
                'username' => Auth::user()->username,
                'ip' => $request->ip(),
            ]);

            return redirect()->intended(route('admin.dashboard'));
        }

        Log::warning('Failed login attempt', [
            'username' => $credentials['username'],
            'ip' => $request->ip(),
        ]);

        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Log::info('User logged out', [
            'user_id' => Auth::id(),
            'username' => Auth::user()?->username,
            'ip' => $request->ip(),
        ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
