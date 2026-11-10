<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingTokensTable extends Migration
{
    public function up()
    {
        Schema::create('booking_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token_ref')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->default(70.00);
            $table->string('status')->default('pending');
            $table->string('gateway')->nullable();
            $table->string('paystack_reference')->nullable()->unique();
            $table->string('paystack_transaction_ref')->nullable();
            $table->unsignedBigInteger('used_by_booking_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->foreign('used_by_booking_id')->references('id')->on('bookings')->onDelete('set null');
            $table->index(['user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_tokens');
    }
}