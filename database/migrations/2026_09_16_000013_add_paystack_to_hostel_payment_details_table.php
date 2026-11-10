<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaystackToHostelPaymentDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('hostel_payment_details', function (Blueprint $table) {
            $table->boolean('paystack_enabled')->default(false)->after('instructions');
            $table->string('paystack_public_key')->nullable()->after('paystack_enabled');
            $table->string('paystack_secret_key')->nullable()->after('paystack_public_key');
        });
    }

    public function down()
    {
        Schema::table('hostel_payment_details', function (Blueprint $table) {
            $table->dropColumn(['paystack_enabled', 'paystack_public_key', 'paystack_secret_key']);
        });
    }
}