<?php

namespace Tests\Unit;

use App\Services\MTNMoMoService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MTNMoMoServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'mtnmomo.sandbox_base_url' => 'https://sandbox.mtn.test',
            'mtnmomo.live_base_url' => 'https://live.mtn.test',
            'mtnmomo.currency' => 'GHS',
        ]);
    }

    protected function credentials(): array
    {
        return [
            'api_user' => 'api-user-01',
            'api_key' => 'api-key-01',
            'subscription_key' => 'sub-key-01',
            'target' => MTNMoMoService::TARGET_SANDBOX,
        ];
    }

    public function test_normalize_msisdn_handles_ghanaian_formats()
    {
        $service = new MTNMoMoService();

        $this->assertSame('233240001234', $service->normalizeMsisdn('0240001234'));
        $this->assertSame('233240001234', $service->normalizeMsisdn('233240001234'));
        $this->assertSame('233240001234', $service->normalizeMsisdn('+233240001234'));
        $this->assertSame('233240001234', $service->normalizeMsisdn('0240 001 234'));
        $this->assertSame('233240001234', $service->normalizeMsisdn('240001234'));
        $this->assertSame('441234567890', $service->normalizeMsisdn('+44 1234 567890'));
    }

    public function test_base_url_switches_between_sandbox_and_live()
    {
        $service = new MTNMoMoService();

        $this->assertSame('https://sandbox.mtn.test', $service->baseUrl());
        $this->assertSame(
            'https://live.mtn.test',
            $service->baseUrl(MTNMoMoService::TARGET_LIVE)
        );
    }

    public function test_token_returns_access_token_on_success()
    {
        Http::fake([
            'sandbox.mtn.test/*' => Http::response(['access_token' => 'token-abc', 'expires_in' => 3600], 200),
        ]);

        $service = new MTNMoMoService();
        $result = $service->token($this->credentials());

        $this->assertTrue($result['ok']);
        $this->assertSame('token-abc', $result['access_token']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://sandbox.mtn.test/collection/token/'
                && $request->hasHeader('Ocp-Apim-Subscription-Key', 'sub-key-01')
                && $request['grant_type'] === 'client_credentials';
        });
    }

    public function test_token_returns_error_message_on_auth_failure()
    {
        Http::fake([
            'sandbox.mtn.test/*' => Http::response(['message' => 'Unauthorized'], 401),
        ]);

        $service = new MTNMoMoService();
        $result = $service->token($this->credentials());

        $this->assertFalse($result['ok']);
        $this->assertSame('Unauthorized', $result['message']);
    }

    public function test_request_to_pay_accepts_202_and_builds_payload()
    {
        Http::fake([
            'sandbox.mtn.test/collection/token/*' => Http::response(['access_token' => 'token-abc'], 200),
            'sandbox.mtn.test/collection/v1_0/requesttopay' => Http::response([], 202),
        ]);

        $service = new MTNMoMoService();
        $result = $service->requestToPay($this->credentials(), 1500.0, '0240001234', 'HSD-XYZ');

        $this->assertTrue($result['ok']);
        $this->assertNotEmpty($result['reference']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://sandbox.mtn.test/collection/v1_0/requesttopay'
                && (float) $request['amount'] === 1500.0
                && $request['currency'] === 'GHS'
                && $request['externalId'] === 'HSD-XYZ'
                && $request['payer']['partyId'] === '233240001234'
                && $request->hasHeader('X-Reference-Id')
                && $request->hasHeader('X-Target-Environment', MTNMoMoService::TARGET_SANDBOX);
        });
    }

    public function test_request_to_pay_returns_failure_when_not_accepted()
    {
        Http::fake([
            'sandbox.mtn.test/collection/token/*' => Http::response(['access_token' => 'token-abc'], 200),
            'sandbox.mtn.test/collection/v1_0/requesttopay' => Http::response(['message' => 'Invalid payer'], 400),
        ]);

        $service = new MTNMoMoService();
        $result = $service->requestToPay($this->credentials(), 1500.0, '0240001234', 'HSD-XYZ');

        $this->assertFalse($result['ok']);
        $this->assertSame('Invalid payer', $result['message']);
    }

    public function test_get_request_to_pay_status_reports_successful()
    {
        Http::fake([
            'sandbox.mtn.test/collection/token/*' => Http::response(['access_token' => 'token-abc'], 200),
            'sandbox.mtn.test/*' => Http::response([
                'status' => 'SUCCESSFUL',
                'financialTransactionId' => 'TXN-8827',
                'amount' => '1500.0',
                'currency' => 'GHS',
                'payer' => ['partyId' => '233240001234'],
            ], 200),
        ]);

        $service = new MTNMoMoService();
        $result = $service->getRequestToPayStatus($this->credentials(), 'uuid-1234');

        $this->assertTrue($result['ok']);
        $this->assertSame('SUCCESSFUL', $result['status']);
        $this->assertSame('TXN-8827', $result['financial_transaction_id']);
        $this->assertSame(1500.0, $result['amount']);
    }
}