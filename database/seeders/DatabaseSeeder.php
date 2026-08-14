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
            PermissionSeeder::class,     // 2. Permissions & role-permission assignments
            AdminUserSeeder::class,      // 3. Creates one user per role
            LabTestCategorySeeder::class,// 4. Lab categories
            LabTestSeeder::class,        // 5. Lab tests (depends on categories)
            OperatingRoomSeeder::class,  // 6. OR rooms
        ]);
    }
}
