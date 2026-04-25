<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
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
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
