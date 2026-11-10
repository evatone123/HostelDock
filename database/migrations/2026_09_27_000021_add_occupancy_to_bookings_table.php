<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddOccupancyToBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('academic_year');
            $table->timestamp('renewal_locked_at')->nullable()->after('expires_at');
            $table->unsignedBigInteger('renewal_count')->default(0)->after('renewal_locked_at');
            $table->timestamp('renewal_window_notified_at')->nullable()->after('renewal_count');
            $table->string('renewal_paystack_reference')->nullable()->after('renewal_window_notified_at');
            $table->string('renewal_paystack_transaction_ref')->nullable()->after('renewal_paystack_reference');
            $table->string('renewal_momo_reference')->nullable()->after('renewal_paystack_transaction_ref');
            $table->string('renewal_momo_transaction_ref')->nullable()->after('renewal_momo_reference');
            $table->string('renewal_phone')->nullable()->after('renewal_momo_transaction_ref');
        });

        // Backfill existing confirmed bookings with a 365-day occupancy from their
        // confirmation moment (paid_at, falling back to the last updated_at).
        $rows = DB::table('bookings')
            ->where('status', 'confirmed')
            ->whereNull('expires_at')
            ->select(['id', 'paid_at', 'updated_at'])
            ->get();

        foreach ($rows as $row) {
            $anchor = $row->paid_at ? Carbon::parse($row->paid_at) : Carbon::parse($row->updated_at);

            DB::table('bookings')
                ->where('id', $row->id)
                ->update(['expires_at' => $anchor->copy()->addDays(\App\Models\Booking::OCCUPANCY_DAYS)]);
        }
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'expires_at',
                'renewal_locked_at',
                'renewal_count',
                'renewal_window_notified_at',
                'renewal_paystack_reference',
                'renewal_paystack_transaction_ref',
                'renewal_momo_reference',
                'renewal_momo_transaction_ref',
                'renewal_phone',
            ]);
        });
    }
}