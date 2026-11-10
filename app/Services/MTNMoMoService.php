<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MTNMoMoService
{
    public const TARGET_SANDBOX = 'sandbox';
    public const TARGET_LIVE = 'mtnmomo';

    public function baseUrl(string $targetEnvironment = self::TARGET_SANDBOX): string
    {
        return $targetEnvironment === self::TARGET_LIVE
            ? config('mtnmomo.live_base_url')
            : config('mtnmomo.sandbox_base_url');
    }

    public function token(array $creds): array
    {
        $target = $creds['target'] ?? self::TARGET_SANDBOX;

        try {
            $response = Http::withOptions(['timeout' => 20, 'verify' => false])
                ->withBasicAuth($creds['api_user'] ?? '', $creds['api_key'] ?? '')
                ->withHeaders(['Ocp-Apim-Subscription-Key' => $creds['subscription_key'] ?? ''])
                ->asForm()
                ->post($this->baseUrl($target) . '/collection/token/', [
                    'grant_type' => 'client_credentials',
                ]);
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Could not reach MTN MoMo. Please try again later.'];
        }

        if (! $response->successful() || ! $response->json('access_token')) {
            return ['ok' => false, 'message' => $this->messageFromResponse($response)];
        }

        return [
            'ok' => true,
            'access_token' => $response->json('access_token'),
            'expires_in' => $response->json('expires_in', 3600),
        ];
    }

    public function requestToPay(array $creds, float $amount, string $payerMsisdn, string $externalId): array
    {
        $token = $this->token($creds);

        if (! $token['ok']) {
            return $token;
        }

        $referenceId = (string) Str::uuid();
        $target = $creds['target'] ?? self::TARGET_SANDBOX;

        try {
            $response = Http::withOptions(['timeout' => 30, 'verify' => false])
                ->withToken($token['access_token'])
                ->withHeaders([
                    'X-Reference-Id' => $referenceId,
                    'X-Target-Environment' => $target,
                    'Ocp-Apim-Subscription-Key' => $creds['subscription_key'] ?? '',
                ])
                ->post($this->baseUrl($target) . '/collection/v1_0/requesttopay', [
                    'amount' => $amount,
                    'currency' => config('mtnmomo.currency', 'GHS'),
                    'externalId' => $externalId,
                    'payer' => [
                        'partyIdType' => 'MSISDN',
                        'partyId' => $this->normalizeMsisdn($payerMsisdn),
                    ],
                    'payerMessage' => 'Payment for your hostel booking.',
                    'payeeNote' => 'Hostel booking payment ' . $externalId,
                ]);
        } catch (\Throwable $e) {
            return ['ok' => false, 'reference' => $referenceId, 'message' => 'Could not reach MTN MoMo. Please try again later.'];
        }

        if ($response->status() === 202) {
            return ['ok' => true, 'reference' => $referenceId, 'message' => 'Payment request sent to your phone.'];
        }

        return ['ok' => false, 'reference' => $referenceId, 'message' => $this->messageFromResponse($response)];
    }

    public function getRequestToPayStatus(array $creds, string $referenceId): array
    {
        $token = $this->token($creds);

        if (! $token['ok']) {
            return $token;
        }

        $target = $creds['target'] ?? self::TARGET_SANDBOX;

        try {
            $response = Http::withOptions(['timeout' => 20, 'verify' => false])
                ->withToken($token['access_token'])
                ->withHeaders([
                    'X-Target-Environment' => $target,
                    'Ocp-Apim-Subscription-Key' => $creds['subscription_key'] ?? '',
                ])
                ->get($this->baseUrl($target) . '/collection/v1_0/requesttopay/' . rawurlencode($referenceId));
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Could not reach MTN MoMo. Please try again later.'];
        }

        if (! $response->successful()) {
            return ['ok' => false, 'message' => $this->messageFromResponse($response)];
        }

        return [
            'ok' => true,
            'status' => strtoupper((string) $response->json('status')),
            'financial_transaction_id' => $response->json('financialTransactionId'),
            'amount' => (float) $response->json('amount'),
            'currency' => $response->json('currency'),
            'payer' => $response->json('payer'),
        ];
    }

    public function normalizeMsisdn(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone) ?? '';

        if (strlen($phone) === 9) {
            $phone = '233' . $phone;
        } elseif (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            $phone = '233' . substr($phone, 1);
        }

        return $phone;
    }

    protected function messageFromResponse($response): string
    {
        $json = $response->json();

        if (is_array($json) && isset($json['message'])) {
            return (string) $json['message'];
        }

        if (is_array($json) && isset($json['error']) && is_array($json['error']) && isset($json['error']['message'])) {
            return (string) $json['error']['message'];
        }

        if ($response->status() === 401 || $response->status() === 403) {
            return 'MTN MoMo credentials are invalid. Check the API user, API key and subscription key.';
        }

        return 'MTN MoMo request failed (HTTP ' . $response->status() . ').';
    }
}