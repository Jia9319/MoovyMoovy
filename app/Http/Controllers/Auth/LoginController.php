<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    private function sanitizeRedirect(?string $redirect): ?string
    {
        $value = trim((string) $redirect);

        if ($value === '') {
            return null;
        }

        // Only allow in-app absolute paths to avoid open redirect issues.
        if (str_starts_with($value, '/') && !str_starts_with($value, '//')) {
            return $value;
        }

        return null;
    }

    /**
     * show login page
     */
    public function showLoginForm(Request $request)
    {
        $redirect = $this->sanitizeRedirect($request->query('redirect'));

        if ($redirect !== null) {
            $request->session()->put('url.intended', $redirect);
        }

        return view('auth.login', [
            'redirect' => $redirect,
        ]);
    }

    /**
     * manage login request
     */
    public function login(Request $request)
    {
        // 1. Verify input
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'redirect' => ['nullable', 'string'],
        ]);

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        $redirect = $this->sanitizeRedirect($validated['redirect'] ?? null);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if ($redirect !== null) {
                $request->session()->put('url.intended', $redirect);
            }

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->intended('/admin/dashboard'); 
            }

            return redirect()->intended('/profile');
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Logout page
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}