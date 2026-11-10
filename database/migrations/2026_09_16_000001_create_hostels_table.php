<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostelsTable extends Migration
{
    public function up()
    {
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('institution')->nullable();
            $table->string('institution_name')->nullable();
            $table->string('location');
            $table->string('distance')->nullable();
            $table->decimal('rating', 3, 1)->default(0);
            $table->longText('description')->nullable();
            $table->decimal('starting_price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hostels');
    }
}