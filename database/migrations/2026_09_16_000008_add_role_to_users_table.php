<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRoleToUsersTable extends Migration
{
    const ROLES = ['student', 'manager', 'admin'];

    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('student')->after('remember_token');
        });

        DB::table('users')->where('is_admin', true)->update(['role' => 'admin']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
        });

        DB::table('users')->where('role', 'admin')->update(['is_admin' => true]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}