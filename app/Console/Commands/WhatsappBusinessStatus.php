<?php

namespace App\Console\Commands;

use App\Services\WhatsAppBusinessApiService;
use Illuminate\Console\Command;

class WhatsappBusinessStatus extends Command
{
    protected $signature = 'whatsapp:business:status {--test= : Phone number (233...) to send a test message through the Cloud API}';

    protected $description = 'Check the WhatsApp Business Cloud API activation status.';

    public function handle(WhatsAppBusinessApiService $business)
    {
        $testNumber = $this->option('test');

        $status = $business->status();

        $this->table(
            ['Key', 'Value'],
            [
                ['Enabled', $status['enabled'] ? 'yes' : 'no'],
                ['Configured', $status['configured'] ? 'yes' : 'no'],
                ['Activation status (.env)', $status['activation_status']],
                ['Channel status', $status['channel_status']],
                ['Phone number id', $status['phone_number_id'] ?: '(not set)'],
                ['WABA id', $status['waba_id'] ?: '(not set)'],
                ['App id', $status['app_id'] ?: '(not set)'],
                ['Graph version', $status['graph_version']],
            ]
        );

        if ($status['error']) {
            $this->error('Could not reach the Graph API: ' . $status['error']);
        }

        if (empty($status['numbers'])) {
            if (! $status['enabled'] || $status['waba_id'] === '') {
                $this->line('No phone numbers to show. Enable WHATSAPP_CLOUD_ENABLED and set WHATSAPP_CLOUD_WABA_ID/WHATSAPP_CLOUD_ACCESS_TOKEN.');
            }
        } else {
            $rows = array_map(function ($number) {
                return [
                    $number['display_phone_number'],
                    $number['verified_name'],
                    $number['status'],
                    $number['code_verification_status'],
                    $number['quality_rating'],
                ];
            }, $status['numbers']);

            $this->info('Registered phone numbers:');
            $this->table(['Number', 'Verified name', 'Status', 'Code verification', 'Quality'], $rows);

            $pending = collect($status['numbers'])->contains(function ($number) {
                return ($number['code_verification_status'] ?? 'NOT_VERIFIED') !== 'VERIFIED'
                    || ($number['status'] ?? '') !== 'ACTIVE';
            });

            if ($pending) {
                $this->warn('Your number is still in pending activation. Complete setup at business.facebook.com, verify the webhook (GET /api/whatsapp/webhook), then set WHATSAPP_CLOUD_ACTIVATION_STATUS=active in your .env.');
            } else {
                $this->info('Your number is VERIFIED/ACTIVE on Meta. Set WHATSAPP_CLOUD_ACTIVATION_STATUS=active in your .env to start sending.');
            }
        }

        if ($testNumber) {
            $this->line('Sending a test message via the Cloud API...');
            $sent = $business->sendTestMessage($testNumber);

            if ($sent) {
                $this->info('Test message sent to ' . $testNumber . '.');
            } else {
                $this->error('Test message failed. Check that WHATSAPP_CLOUD_ACTIVATION_STATUS=active and the phone number is activated on Meta.');
            }
        }

        return 0;
    }
}