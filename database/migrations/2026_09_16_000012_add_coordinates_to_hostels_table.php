<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoordinatesToHostelsTable extends Migration
{
    public function up()
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('distance');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down()
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
}