<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentColumnsToBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status');
            $table->string('paystack_reference')->nullable()->unique()->after('payment_status');
            $table->string('paystack_transaction_ref')->nullable()->after('paystack_reference');
            $table->timestamp('paid_at')->nullable()->after('paystack_transaction_ref');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'paystack_reference', 'paystack_transaction_ref', 'paid_at']);
        });
    }
}