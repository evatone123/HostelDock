<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCapacityToRoomsTable extends Migration
{
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->unsignedSmallInteger('capacity')->default(1)->after('room_type');
        });

        $this->backfillCapacity();

        Schema::table('rooms', function (Blueprint $table) {
            $table->index('room_type');
        });
    }

    protected function backfillCapacity(): void
    {
        if (! Schema::hasTable('room_categories')) {
            return;
        }

        $capacities = DB::table('room_categories')->pluck('capacity', 'name')->all();

        if (empty($capacities)) {
            return;
        }

        foreach (DB::table('rooms')->select('id', 'room_type')->get() as $room) {
            $capacity = $capacities[$room->room_type] ?? 1;

            DB::table('rooms')
                ->where('id', $room->id)
                ->update(['capacity' => max(1, (int) $capacity)]);
        }
    }

    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex(['room_type']);
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('capacity');
        });
    }
}
