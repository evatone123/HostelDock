<?php

namespace Database\Seeders;

use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentRoomSeeder extends Seeder
{
    public function run()
    {
        $student = User::firstOrCreate(
            ['email' => 'student@demo.com'],
            [
                'name' => 'Demo Student',
                'phone' => '0240000000',
                'password' => Hash::make('password'),
                'role' => User::ROLE_STUDENT,
            ]
        );

        $hostel = Hostel::firstOrCreate(
            ['slug' => 'demo-student-hostel'],
            [
                'name' => 'Demo Student Hostel',
                'institution' => 'Demo University',
                'institution_name' => 'Demo University, Accra',
                'location' => 'Accra, Ghana',
                'distance' => 'Near campus',
                'latitude' => 5.603717,
                'longitude' => -0.186964,
                'rating' => 4.5,
                'description' => 'A demo hostel seeded to test the booking and token flow.',
                'starting_price' => 500.00,
                'is_active' => true,
            ]
        );

        $rooms = [
            ['name' => 'One in a room', 'room_type' => 'One in a room', 'capacity' => 1, 'price' => 1200.00, 'total_rooms' => 10],
            ['name' => 'Two in a room', 'room_type' => 'Two in a room', 'capacity' => 2, 'price' => 900.00, 'total_rooms' => 20],
            ['name' => 'Three in a room', 'room_type' => 'Three in a room', 'capacity' => 3, 'price' => 700.00, 'total_rooms' => 15],
            ['name' => 'Four in a room', 'room_type' => 'Four in a room', 'capacity' => 4, 'price' => 500.00, 'total_rooms' => 12],
        ];

        foreach ($rooms as $room) {
            Room::firstOrCreate(
                ['hostel_id' => $hostel->id, 'name' => $room['name']],
                $room
            );
        }

        $this->command->info('Demo student seeded: student@demo.com / password');
        $this->command->info("Demo hostel seeded: {$hostel->name} with " . count($rooms) . ' room types.');
    }
}
