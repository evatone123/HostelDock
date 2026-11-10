<?php

namespace Database\Seeders;

use App\Models\Hostel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManagerUserSeeder extends Seeder
{
    public function run()
    {
        $manager = User::firstOrCreate(
            ['email' => 'manager@studentroombook.com'],
            [
                'name' => 'Demo Hostel Manager',
                'phone' => '0200000000',
                'password' => Hash::make('manager123'),
                'role' => User::ROLE_MANAGER,
            ]
        );

        $hostels = Hostel::orderBy('name')->take(3)->pluck('id');
        $manager->managedHostels()->syncWithoutDetaching($hostels);
    }
}