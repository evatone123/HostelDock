<?php

namespace Tests\Feature;

use App\Services\WhatsAppBusinessApiService;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsappBusinessApiTest extends TestCase
{
    protected function configureCloud(array $business = [], array $openwa = [])
    {
        config(array_merge([
            'services.whatsapp_business.enabled' => true,
            'services.whatsapp_business.graph_version' => 'v21.0',
            'services.whatsapp_business.access_token' => 'graph-access-token',
            'services.whatsapp_business.phone_number_id' => '1029384756102938',
            'services.whatsapp_business.app_secret' => 'graph-app-secret',
            'services.whatsapp_business.verify_token' => 'verify-me',
            'services.whatsapp_business.activation_status' => 'pending',
        ], $business));

        config(array_merge([
            'services.whatsapp.enabled' => false,
            'services.whatsapp.base_url' => 'http://openwa.test/api',
            'services.whatsapp.api_key' => 'openwa-key',
            'services.whatsapp.session_name' => 'main',
        ], $openwa));
    }

    protected function graphMessagesUrl(): string
    {
        return 'https://graph.facebook.com/v21.0/1029384756102938/messages';
    }

    protected function openwaUrl(): string
    {
        return 'http://openwa.test/api/sessions/main/messages/send-text';
    }

    public function test_webhook_verification_echoes_challenge_for_valid_token()
    {
        $this->configureCloud();

        $response = $this->get('/api/whatsapp/webhook?hub.mode=subscribe&hub_verify_token=verify-me&hub.challenge=1158201444');

        $response->assertStatus(200);
        $this->assertSame('1158201444', $response->getContent());
    }

    public function test_webhook_verification_rejects_invalid_token()
    {
        $this->configureCloud();

        $this->get('/api/whatsapp/webhook?hub.mode=subscribe&hub_verify_token=wrong&hub.challenge=1158201444')
            ->assertForbidden();
    }

    public function test_webhook_verification_forbidden_when_disabled()
    {
        $this->configureCloud(['services.whatsapp_business.enabled' => false]);

        $this->get('/api/whatsapp/webhook?hub.mode=subscribe&hub_verify_token=verify-me&hub.challenge=1158201444')
            ->assertForbidden();
    }

    public function test_webhook_notify_accepts_valid_signature()
    {
        $this->configureCloud();

        $payload = ['entry' => [['id' => 'waba-id', 'changes' => []]]];
        $body = json_encode($payload);
        $signature = 'sha256=' . hash_hmac('sha256', $body, 'graph-app-secret');

        $response = $this->call('POST', '/api/whatsapp/webhook', [], [], [], [
            'HTTP_X-Hub-Signature-256' => $signature,
        ], $body);

        $response->assertOk();
    }

    public function test_webhook_notify_rejects_bad_signature()
    {
        $this->configureCloud();

        $response = $this->call('POST', '/api/whatsapp/webhook', [], [], [], [
            'HTTP_X-Hub-Signature-256' => 'sha256=invalidsignature',
        ], '{"entry":[]}');

        $response->assertStatus(401);
    }

    public function test_whatsapp_service_uses_business_api_when_active()
    {
        $this->configureCloud(
            ['services.whatsapp_business.activation_status' => 'active'],
            ['services.whatsapp.enabled' => true]
        );

        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.3']]], 200),
            'openwa.test/*' => Http::response(['status' => true], 200),
        ]);

        $sent = app(WhatsAppService::class)->send('0240000000', 'Hello student');

        $this->assertTrue($sent);

        Http::assertSent(function ($request) {
            return $request->url() === $this->graphMessagesUrl();
        });

        Http::assertNotSent(function ($request) {
            return $request->url() === $this->openwaUrl();
        });
    }

    public function test_whatsapp_service_falls_back_to_openwa_while_pending()
    {
        $this->configureCloud(
            ['services.whatsapp_business.activation_status' => 'pending'],
            ['services.whatsapp.enabled' => true]
        );

        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.4']]], 200),
            'openwa.test/*' => Http::response(['status' => true], 200),
        ]);

        $sent = app(WhatsAppService::class)->send('0240000000', 'Hello student');

        $this->assertTrue($sent);

        Http::assertSent(function ($request) {
            return $request->url() === $this->openwaUrl();
        });

        Http::assertNotSent(function ($request) {
            return $request->url() === $this->graphMessagesUrl();
        });
    }

    public function test_business_status_command_exits_successfully()
    {
        $this->configureCloud();

        Http::fake([
            'graph.facebook.com/*' => Http::response([
                'data' => [
                    [
                        'display_phone_number' => '233245550000',
                        'verified_name' => 'HostelDock',
                        'status' => 'ACTIVE',
                        'code_verification_status' => 'NOT_VERIFIED',
                        'quality_rating' => 'GREEN',
                    ],
                ],
            ], 200),
        ]);

        $this->artisan('whatsapp:business:status')->assertExitCode(0);
    }
}