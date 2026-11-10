<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CampusSeeder::class,
            HostelSeeder::class,
            AdminUserSeeder::class,
            ManagerUserSeeder::class,
            PaymentGatewaySeeder::class,
            StudentRoomSeeder::class,
        ]);
    }
}
