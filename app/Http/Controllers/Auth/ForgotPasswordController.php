<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request, RecaptchaService $recaptcha)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $this->requireRecaptcha($request, $recaptcha);

        Password::sendResetLink($validated);

        return back()->with('status', __('passwords.sent'));
    }

    protected function requireRecaptcha(Request $request, RecaptchaService $recaptcha): void
    {
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