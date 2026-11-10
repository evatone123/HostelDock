<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostelUserTable extends Migration
{
    public function up()
    {
        Schema::create('hostel_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hostel_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'hostel_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('hostel_user');
    }
}