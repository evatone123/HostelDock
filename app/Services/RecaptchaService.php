<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    public const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    public function isEnabled(): bool
    {
        return (bool) config('services.recaptcha.enabled')
            && (bool) config('services.recaptcha.site_key')
            && (bool) config('services.recaptcha.secret_key');
    }

    public function verify(string $responseToken): bool
    {
        if (! $this->isEnabled()) {
            return true;
        }

        if ($responseToken === '') {
            return false;
        }

        try {
            $response = Http::withOptions(['timeout' => 10, 'verify' => false])
                ->asForm()
                ->post(self::VERIFY_URL, [
                    'secret' => config('services.recaptcha.secret_key'),
                    'response' => $responseToken,
                ]);
        } catch (\Throwable $e) {
            return false;
        }

        return (bool) $response->json('success');
    }
}