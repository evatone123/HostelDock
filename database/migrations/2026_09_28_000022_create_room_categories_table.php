<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRoomCategoriesTable extends Migration
{
    protected const DEFAULTS = [
        'One in a room' => 1,
        'Two in a room' => 2,
        'Three in a room' => 3,
        'Four in a room' => 4,
        'Five in a room' => 5,
        'Six in a room' => 6,
    ];

    public function up()
    {
        Schema::create('room_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedSmallInteger('capacity')->default(1);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();

        foreach (self::DEFAULTS as $name => $capacity) {
            DB::table('room_categories')->insert([
                'name' => $name,
                'capacity' => $capacity,
                'sort_order' => $capacity,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->backfillStrayRoomTypes($now);
    }

    protected function backfillStrayRoomTypes($now): void
    {
        if (! Schema::hasTable('rooms') || ! Schema::hasColumn('rooms', 'room_type')) {
            return;
        }

        $known = DB::table('room_categories')->pluck('name')->all();

        $strays = DB::table('rooms')
            ->whereNotIn('room_type', $known)
            ->distinct()
            ->pluck('room_type')
            ->filter()
            ->reject(function ($type) use ($known) {
                return in_array($type, $known, true);
            });

        foreach ($strays as $type) {
            $capacity = preg_match('/^(\d+)/', $type, $matches) ? (int) $matches[1] : 1;

            DB::table('room_categories')->insert([
                'name' => $type,
                'capacity' => max(1, $capacity),
                'sort_order' => max(1, $capacity),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('room_categories');
    }
}
