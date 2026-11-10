<?php

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMtnMomoToHostelPaymentDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('hostel_payment_details', function (Blueprint $table) {
            $table->boolean('momo_api_enabled')->default(false)->after('momo_account_name');
            $table->string('momo_api_user')->nullable()->after('momo_api_enabled');
            $table->text('momo_api_key')->nullable()->after('momo_api_user');
            $table->text('momo_subscription_key')->nullable()->after('momo_api_key');
            $table->string('momo_target_environment')->default('sandbox')->after('momo_subscription_key');
            $table->string('merchant_number')->nullable()->after('momo_target_environment');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `hostel_payment_details` MODIFY `paystack_secret_key` TEXT NULL');
        }

        DB::table('hostel_payment_details')
            ->whereNotNull('paystack_secret_key')
            ->orderBy('id')
            ->get(['id', 'paystack_secret_key'])
            ->each(function ($row) {
                $key = $row->paystack_secret_key;

                if ($key === '' || $key === null) {
                    return;
                }

                try {
                    Crypt::decryptString($key);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    DB::table('hostel_payment_details')
                        ->where('id', $row->id)
                        ->update(['paystack_secret_key' => Crypt::encryptString($key)]);
                }
            });
    }

    public function down()
    {
        Schema::table('hostel_payment_details', function (Blueprint $table) {
            $table->dropColumn([
                'momo_api_enabled',
                'momo_api_user',
                'momo_api_key',
                'momo_subscription_key',
                'momo_target_environment',
                'merchant_number',
            ]);
        });
    }
}