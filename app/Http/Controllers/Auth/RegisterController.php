<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    private function sanitizeRedirect(?string $redirect): ?string
    {
        $value = trim((string) $redirect);

        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, '/') && !str_starts_with($value, '//')) {
            return $value;
        }

        return null;
    }

    /**
     * Show register page
     */
    public function showRegistrationForm(Request $request)
    {
        $redirect = $this->sanitizeRedirect($request->query('redirect'));

        if ($redirect !== null) {
            $request->session()->put('url.intended', $redirect);
        }

        return view('auth.register', [
            'redirect' => $redirect,
        ]);
    }

    /**
     * Manage register reqeust
     */
    public function register(Request $request)
    {
        // Verify the data
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'redirect' => ['nullable', 'string'],
        ]);

        // Create User
        $user = $this->create($validated);

        Auth::login($user);

        $redirect = $this->sanitizeRedirect($validated['redirect'] ?? null);

        if ($redirect !== null) {
            $request->session()->put('url.intended', $redirect);
        }

        return redirect()->intended(route('profile.show'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}