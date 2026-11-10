<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Services\RecaptchaService;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $this->requireRecaptcha($request);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $home = Auth::user()->isAdmin()
                ? route('admin.dashboard')
                : (Auth::user()->isManager() ? route('manager.dashboard') : route('profile'));

            return redirect()->intended($home)->with('success', 'Welcome back!');
        }

        return back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'phone' => 'nullable|string|max:191',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $this->requireRecaptcha($request);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('profile')->with('success', 'Account created successfully. Welcome to Student Room Book!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    protected function requireRecaptcha(Request $request): void
    {
        $recaptcha = app(RecaptchaService::class);

        if (! $recaptcha->isEnabled()) {
            return;
        }

        $token = (string) $request->input('g-recaptcha-response', '');

        if (! $recaptcha->verify($token)) {
            throw ValidationException::withMessages([
                'recaptcha' => 'Please complete the reCAPTCHA verification to continue.',
            ]);
        }
    }
}