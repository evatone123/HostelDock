<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'admin@studentroombook.com'],
            [
                'name' => 'Site Administrator',
                'phone' => '0240812739',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_ADMIN,
            ]
        );
    }
}