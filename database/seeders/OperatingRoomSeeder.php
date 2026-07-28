<?php

namespace Database\Seeders;

use App\Models\OperatingRoom;
use Illuminate\Database\Seeder;

/**
 * Seeds sample operating rooms.
 */
class OperatingRoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            ['name' => 'OR-1', 'location' => '3rd Floor, Surgical Wing',  'status' => 'Available'],
            ['name' => 'OR-2', 'location' => '3rd Floor, Surgical Wing',  'status' => 'Available'],
            ['name' => 'OR-3', 'location' => '4th Floor, Cardiac Center', 'status' => 'Available'],
            ['name' => 'OR-4', 'location' => '4th Floor, Cardiac Center', 'status' => 'Available'],
            ['name' => 'OR-5 (Trauma)',   'location' => 'Ground Floor, ER', 'status' => 'Available'],
        ];

        foreach ($rooms as $room) {
            OperatingRoom::firstOrCreate(['name' => $room['name']], $room);
        }
    }
}
