<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;

class PaymentGatewaySeeder extends Seeder
{
    public function run()
    {
        $gateways = [
            [
                'name' => 'MTN Mobile Money',
                'type' => 'mobile_money',
                'network' => 'MTN',
                'account_name' => 'HostelDock',
                'account_number' => '0240812739',
                'instructions' => 'Send mobile money to 0240812739 (HostelDock). Reply with the sender name and reference so we can confirm your payment.',
                'is_active' => true,
                'sort' => 1,
            ],
            [
                'name' => 'Cash at Hostel',
                'type' => 'cash',
                'network' => null,
                'account_name' => null,
                'account_number' => null,
                'instructions' => 'Visit the hostel during the day to pay at the reception desk. Mention your booking reference (HSD-xxxx).',
                'is_active' => true,
                'sort' => 2,
            ],
            [
                'name' => 'Bank Transfer',
                'type' => 'bank',
                'network' => null,
                'account_name' => 'HostelDock',
                'account_number' => '00000001-00001-GHS',
                'instructions' => 'Transfer to the account above and email your payment slip to info@studentroombook.com.',
                'is_active' => false,
                'sort' => 3,
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::firstOrCreate(
                ['name' => $gateway['name']],
                $gateway
            );
        }
    }
}