<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomAmenityTable extends Migration
{
    public function up()
    {
        Schema::create('room_amenity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['room_id', 'amenity_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_amenity');
    }
}