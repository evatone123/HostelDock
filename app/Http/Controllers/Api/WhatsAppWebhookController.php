<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppBusinessApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request, WhatsAppBusinessApiService $business)
    {
        $mode = (string) $request->query('hub_mode', '');
        $token = (string) $request->query('hub_verify_token', '');
        $challenge = (string) $request->query('hub_challenge', '');

        if ($business->isEnabled() && $business->verifyWebhookToken($mode, $token)) {
            Cache::put('whatsapp_cloud_webhook_verified', true, 3600);

            return response($challenge, 200);
        }

        return response()->json(['error' => 'verification failed'], 403);
    }

    public function notify(Request $request, WhatsAppBusinessApiService $business)
    {
        $rawBody = $request->getContent();
        $signature = (string) $request->header('X-Hub-Signature-256', '');

        if ($business->isEnabled() && ! $business->verifySignature($signature, $rawBody)) {
            Log::channel('daily')->warning('WhatsApp webhook signature mismatch');

            return response()->json(['error' => 'invalid signature'], 401);
        }

        Log::channel('daily')->info('WhatsApp Business API webhook', [
            'payload' => $request->json('entry', []),
        ]);

        return response()->json(['status' => 'ok'], 200);
    }
}