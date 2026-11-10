<?php

namespace Tests\Unit;

use App\Services\WhatsAppBusinessApiService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppBusinessApiServiceTest extends TestCase
{
    protected function configureCloud(array $overrides = [])
    {
        config(array_merge([
            'services.whatsapp_business.enabled' => true,
            'services.whatsapp_business.graph_version' => 'v21.0',
            'services.whatsapp_business.access_token' => 'graph-access-token',
            'services.whatsapp_business.phone_number_id' => '1029384756102938',
            'services.whatsapp_business.waba_id' => '112233445566778899',
            'services.whatsapp_business.app_id' => '123456789',
            'services.whatsapp_business.app_secret' => 'graph-app-secret',
            'services.whatsapp_business.verify_token' => 'verify-me',
            'services.whatsapp_business.activation_status' => 'pending',
            'services.whatsapp_business.admin_alert_number' => '0240000001',
        ], $overrides));
    }

    protected function messagesUrl(): string
    {
        return 'https://graph.facebook.com/v21.0/1029384756102938/messages';
    }

    public function test_send_returns_false_while_pending_activation()
    {
        $this->configureCloud();

        Http::fake();

        $sent = app(WhatsAppBusinessApiService::class)->send('233240000000', 'Hello');

        $this->assertFalse($sent);
        Http::assertNothingSent();
    }

    public function test_send_returns_false_when_disabled()
    {
        $this->configureCloud(['services.whatsapp_business.enabled' => false]);

        Http::fake();

        $sent = app(WhatsAppBusinessApiService::class)->send('233240000000', 'Hello');

        $this->assertFalse($sent);
        Http::assertNothingSent();
    }

    public function test_send_posts_correct_payload_when_active()
    {
        $this->configureCloud(['services.whatsapp_business.activation_status' => 'active']);

        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.1']]], 200),
        ]);

        $sent = app(WhatsAppBusinessApiService::class)->send('233240000000', 'Hello from HostelDock');

        $this->assertTrue($sent);

        Http::assertSent(function ($request) {
            return $request->url() === $this->messagesUrl()
                && $request->hasHeader('Authorization', 'Bearer graph-access-token')
                && $request['messaging_product'] === 'whatsapp'
                && $request['to'] === '233240000000'
                && $request['type'] === 'text'
                && $request['text']['body'] === 'Hello from HostelDock'
                && $request['text']['preview_url'] === false;
        });
    }

    public function test_send_returns_false_on_api_error()
    {
        $this->configureCloud(['services.whatsapp_business.activation_status' => 'active']);

        Http::fake([
            'graph.facebook.com/*' => Http::response(['error' => ['message' => 'bad']], 400),
        ]);

        $this->assertFalse(app(WhatsAppBusinessApiService::class)->send('233240000000', 'Hello'));
    }

    public function test_status_parses_registered_phone_numbers()
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

        $status = app(WhatsAppBusinessApiService::class)->status();

        $this->assertTrue($status['enabled']);
        $this->assertTrue($status['configured']);
        $this->assertSame('pending', $status['activation_status']);
        $this->assertSame('pending', $status['channel_status']);
        $this->assertSame('233245550000', $status['numbers'][0]['display_phone_number']);
        $this->assertSame('NOT_VERIFIED', $status['numbers'][0]['code_verification_status']);
        $this->assertNull($status['error']);
    }

    public function test_status_reports_network_errors()
    {
        $this->configureCloud();

        Http::fake([
            'graph.facebook.com/*' => Http::response(['error' => ['message' => 'boom']], 500),
        ]);

        $status = app(WhatsAppBusinessApiService::class)->status();

        $this->assertNotEmpty($status['error']);
        $this->assertEmpty($status['numbers']);
    }

    public function test_subscribe_waba_and_subscribe_app()
    {
        $this->configureCloud();

        Http::fake([
            'graph.facebook.com/*' => Http::sequence()
                ->push(['success' => true], 200)
                ->push(['error' => ['message' => 'denied']], 400)
                ->push(['success' => true], 200)
                ->push(['error' => ['message' => 'denied']], 400),
        ]);

        $service = app(WhatsAppBusinessApiService::class);

        $this->assertTrue($service->subscribeWaba());
        $this->assertFalse($service->subscribeWaba());
        $this->assertTrue($service->subscribeApp());
        $this->assertFalse($service->subscribeApp());

        Http::assertSent(function ($request) {
            return strpos($request->url(), '/112233445566778899/subscribed_apps') !== false;
        });
        Http::assertSent(function ($request) {
            return strpos($request->url(), '/123456789/subscriptions') !== false
                && $request['object'] === 'whatsapp_business_account'
                && $request['verify_token'] === 'verify-me';
        });
    }

    public function test_verify_webhook_token()
    {
        $this->configureCloud();

        $service = app(WhatsAppBusinessApiService::class);

        $this->assertTrue($service->verifyWebhookToken('subscribe', 'verify-me'));
        $this->assertFalse($service->verifyWebhookToken('subscribe', 'wrong'));
        $this->assertFalse($service->verifyWebhookToken('unsubscribe', 'verify-me'));
    }

    public function test_verify_signature()
    {
        $this->configureCloud();

        $service = app(WhatsAppBusinessApiService::class);

        $body = '{"entry":[]}';
        $expected = 'sha256=' . hash_hmac('sha256', $body, 'graph-app-secret');

        $this->assertTrue($service->verifySignature($expected, $body));
        $this->assertFalse($service->verifySignature('sha256=deadbeef', $body));
        $this->assertFalse($service->verifySignature('', $body));
    }

    public function test_send_test_message_uses_same_channel()
    {
        $this->configureCloud(['services.whatsapp_business.activation_status' => 'active']);

        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.2']]], 200),
        ]);

        $this->assertTrue(app(WhatsAppBusinessApiService::class)->sendTestMessage('233240000000'));

        Http::assertSent(function ($request) {
            return $request->url() === $this->messagesUrl()
                && strpos($request['text']['body'], 'HostelDock') !== false;
        });
    }
}