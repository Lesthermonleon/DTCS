<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder — orchestrates all seeders in the correct order.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,           // 1. Must be first — users depend on roles
            AdminUserSeeder::class,       // 2. Creates one user per role
            LabTestCategorySeeder::class, // 3. Lab categories
            LabTestSeeder::class,         // 4. Lab tests (depends on categories)
            OperatingRoomSeeder::class,   // 5. OR rooms
            SampleDataSeeder::class,      // 6. Sample patients + clinical requests
        ]);
    }
}
