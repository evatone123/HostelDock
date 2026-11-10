<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('room_type');
            $table->string('gender_category')->default('Unisex');
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('total_rooms')->default(1);
            $table->unsignedInteger('available_rooms')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}