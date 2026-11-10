<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaystackService
{
    protected $secretKey;

    protected $baseUrl;

    protected $currency;

    protected $callbackUrl;

    public function __construct()
    {
        $this->secretKey = config('paystack.secret_key');
        $this->baseUrl = rtrim(config('paystack.base_url', 'https://api.paystack.co'), '/');
        $this->currency = config('paystack.currency', 'GHS');
        $this->callbackUrl = config('paystack.callback_url') ?: route('token.callback');
    }

    public function initialize(string $email, string $reference, float $amount, array $metadata = [], ?string $secretKey = null, ?string $callbackUrl = null): array
    {
        $secretKey = $secretKey ?: $this->secretKey;
        $callbackUrl = $callbackUrl ?: $this->callbackUrl;

        if (! $secretKey) {
            return $this->failure('Payment gateway is not configured. Please try again later.');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
                'Content-Type' => 'application/json',
            ])->withOptions(['timeout' => 30, 'verify' => false])->post($this->baseUrl . '/transaction/initialize', [
                'email' => $email,
                'amount' => $this->toMinor($amount),
                'currency' => $this->currency,
                'reference' => $reference,
                'callback_url' => $callbackUrl,
                'metadata' => $metadata,
            ]);

            $body = $response->json();

            if ($response->successful() && ($body['status'] ?? false) === true) {
                return [
                    'ok' => true,
                    'authorization_url' => $body['data']['authorization_url'] ?? null,
                    'access_code' => $body['data']['access_code'] ?? null,
                    'reference' => $reference,
                    'message' => 'Payment initialized.',
                ];
            }

            return $this->failure($body['message'] ?? 'The payment could not be initialized.');
        } catch (\Throwable $e) {
            \Log::error('Paystack initialization error: ' . $e->getMessage());
            return $this->failure('The payment provider could not be reached: ' . $e->getMessage());
        }
    }

    public function verify(string $reference, ?string $secretKey = null): array
    {
        $secretKey = $secretKey ?: $this->secretKey;

        if (! $secretKey) {
            return $this->failure('Payment gateway is not configured.');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
                'Accept' => 'application/json',
            ])->withOptions(['timeout' => 30, 'verify' => false])->get($this->baseUrl . '/transaction/verify/' . rawurlencode($reference));

            $body = $response->json();

            if ($response->successful() && ($body['status'] ?? false) === true) {
                $data = $body['data'] ?? [];

                return [
                    'ok' => true,
                    'status' => $data['status'] ?? null,
                    'transaction_ref' => $data['reference'] ?? null,
                    'paid_at' => $data['paid_at'] ?? null,
                    'amount' => isset($data['amount']) ? ($data['amount'] / 100) : null,
                    'message' => 'Transaction verified.',
                ];
            }

            return $this->failure($body['message'] ?? 'The transaction could not be verified.');
        } catch (\Throwable $e) {
            \Log::error('Paystack verification error: ' . $e->getMessage());
            return $this->failure('The payment provider could not be reached: ' . $e->getMessage());
        }
    }

    protected function toMinor(float $amount): int
    {
        return (int) round($amount * 100);
    }

    protected function failure(string $message): array
    {
        return [
            'ok' => false,
            'message' => $message,
        ];
    }
}