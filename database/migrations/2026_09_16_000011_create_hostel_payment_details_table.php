<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostelPaymentDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('hostel_payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->unique()->constrained('hostels')->cascadeOnDelete();

            $table->string('momo_provider')->nullable();
            $table->string('momo_number')->nullable();
            $table->string('momo_account_name')->nullable();

            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();

            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hostel_payment_details');
    }
}