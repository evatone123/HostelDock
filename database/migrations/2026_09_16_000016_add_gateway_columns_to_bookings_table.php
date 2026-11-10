<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGatewayColumnsToBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('gateway')->nullable()->after('payment_status');
            $table->string('momo_request_reference')->nullable()->unique()->after('paystack_transaction_ref');
            $table->string('momo_phone')->nullable()->after('momo_request_reference');
            $table->string('provider_reference')->nullable()->after('momo_phone');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['gateway', 'momo_request_reference', 'momo_phone', 'provider_reference']);
        });
    }
}