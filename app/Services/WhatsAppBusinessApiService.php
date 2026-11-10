<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppBusinessApiService
{
    protected $enabled;

    protected $graphVersion;

    protected $accessToken;

    protected $phoneNumberId;

    protected $wabaId;

    protected $appId;

    protected $appSecret;

    protected $verifyToken;

    protected $activationStatus;

    public function __construct()
    {
        $this->enabled = (bool) config('services.whatsapp_business.enabled', false);
        $this->graphVersion = (string) config('services.whatsapp_business.graph_version', 'v21.0');
        $this->accessToken = (string) config('services.whatsapp_business.access_token');
        $this->phoneNumberId = (string) config('services.whatsapp_business.phone_number_id');
        $this->wabaId = (string) config('services.whatsapp_business.waba_id');
        $this->appId = (string) config('services.whatsapp_business.app_id');
        $this->appSecret = (string) config('services.whatsapp_business.app_secret');
        $this->verifyToken = (string) config('services.whatsapp_business.verify_token');
        $this->activationStatus = (string) config('services.whatsapp_business.activation_status', 'pending');
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isConfigured(): bool
    {
        return $this->enabled
            && $this->accessToken !== ''
            && $this->phoneNumberId !== '';
    }

    public function isActive(): bool
    {
        return $this->isConfigured()
            && $this->activationStatus === 'active';
    }

    public function isPending(): bool
    {
        return $this->isConfigured()
            && $this->activationStatus !== 'active';
    }

    public function adminAlertNumber(): string
    {
        return (string) config('services.whatsapp_business.admin_alert_number', '');
    }

    /**
     * Send a WhatsApp text message via the Meta Cloud API. Best-effort:
     * never throws, never blocks the request when the gateway is unavailable.
     */
    public function send(string $to, string $message): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        try {
            $response = Http::withToken($this->accessToken)
                ->withOptions(['timeout' => 8, 'verify' => false])
                ->post($this->graphUrl($this->phoneNumberId . '/messages'), [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $to,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $message,
                    ],
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::warning('WhatsApp Business API send failed', [
                'to' => $to,
                'status' => $response->status(),
                'body' => substr((string) $response->body(), 0, 500),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::warning('WhatsApp Business API send error: ' . $e->getMessage(), ['to' => $to]);
            return false;
        }
    }

    public function sendTestMessage(string $to): bool
    {
        return $this->send($to, "Hello from HostelDock! Your WhatsApp Business API channel is active and ready to deliver booking updates.");
    }

    /**
     * Query the WhatsApp Business Account's registered phone numbers.
     * Returns local config plus each number's activation/verification state.
     */
    public function status(): array
    {
        $local = [
            'enabled' => $this->enabled,
            'configured' => $this->isConfigured(),
            'activation_status' => $this->activationStatus,
            'channel_status' => $this->isActive() ? 'active' : 'pending',
            'phone_number_id' => $this->phoneNumberId,
            'waba_id' => $this->wabaId,
            'app_id' => $this->appId,
            'graph_version' => $this->graphVersion,
            'numbers' => [],
            'error' => null,
        ];

        if (! $this->enabled || $this->wabaId === '' || $this->accessToken === '') {
            return $local;
        }

        try {
            $response = Http::withToken($this->accessToken)
                ->withOptions(['timeout' => 8, 'verify' => false])
                ->get($this->graphUrl($this->wabaId . '/phone_numbers'));

            if (! $response->successful()) {
                $local['error'] = $response->status() . ' ' . substr((string) $response->body(), 0, 300);
                return $local;
            }

            foreach (($response->json('data') ?: []) as $number) {
                $local['numbers'][] = [
                    'display_phone_number' => $number['display_phone_number'] ?? 'N/A',
                    'verified_name' => $number['verified_name'] ?? 'N/A',
                    'status' => $number['status'] ?? 'UNKNOWN',
                    'code_verification_status' => $number['code_verification_status'] ?? 'NOT_VERIFIED',
                    'quality_rating' => $number['quality_rating'] ?? 'N/A',
                ];
            }
        } catch (\Throwable $e) {
            $local['error'] = $e->getMessage();
        }

        return $local;
    }

    /**
     * Subscribe the app to the whatsapp_business_account webhook object.
     */
    public function subscribeApp(): bool
    {
        if (! $this->enabled || $this->appId === '') {
            return false;
        }

        try {
            $response = Http::withToken($this->accessToken)
                ->withOptions(['timeout' => 8, 'verify' => false])
                ->post($this->graphUrl($this->appId . '/subscriptions'), [
                    'object' => 'whatsapp_business_account',
                    'callback_url' => route('api.whatsapp.webhook'),
                    'verify_token' => $this->verifyToken,
                    'fields' => 'messages,message_template_status_update,phone_number_quality_update',
                ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('WhatsApp Business API subscribe app error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Subscribe the WhatsApp Business Account to the app (required for
     * webhook events to flow once the number is activated).
     */
    public function subscribeWaba(): bool
    {
        if (! $this->enabled || $this->wabaId === '') {
            return false;
        }

        try {
            $response = Http::withToken($this->accessToken)
                ->withOptions(['timeout' => 8, 'verify' => false])
                ->post($this->graphUrl($this->wabaId . '/subscribed_apps'));

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('WhatsApp Business API subscribe waba error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate the Meta webhook verification handshake (hub.challenge).
     */
    public function verifyWebhookToken(string $mode, string $token): bool
    {
        return $mode === 'subscribe'
            && $this->verifyToken !== ''
            && hash_equals($this->verifyToken, $token);
    }

    /**
     * Validate the X-Hub-Signature-256 header against the app secret.
     */
    public function verifySignature(string $signature, string $rawBody): bool
    {
        if ($this->appSecret === '' || $signature === '') {
            return false;
        }

        $expected = 'sha256=' . hash_hmac('sha256', $rawBody, $this->appSecret);

        return hash_equals($expected, $signature);
    }

    protected function graphUrl(string $path): string
    {
        return 'https://graph.facebook.com/' . $this->graphVersion . '/' . $path;
    }
}