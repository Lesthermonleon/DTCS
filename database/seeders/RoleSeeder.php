<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Seeds the 8 hospital system roles.
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'System Administrator',    'slug' => 'admin',          'description' => 'Full access to all modules and system settings'],
            ['name' => 'Doctor',                  'slug' => 'doctor',         'description' => 'Order tests, prescriptions, surgeries, and diet requests'],
            ['name' => 'Medical Technologist',    'slug' => 'med-tech',       'description' => 'Receive and process laboratory requests, encode results'],
            ['name' => 'Radiologic Technologist', 'slug' => 'rad-tech',       'description' => 'Perform imaging procedures and upload images'],
            ['name' => 'Radiologist',             'slug' => 'radiologist',    'description' => 'Interpret imaging results and approve radiology reports'],
            ['name' => 'Pharmacist',              'slug' => 'pharmacist',     'description' => 'Verify and dispense prescriptions'],
            ['name' => 'Dietitian / Nutritionist','slug' => 'dietitian',      'description' => 'Create and manage therapeutic diet plans'],
            ['name' => 'OR Coordinator',          'slug' => 'or-coordinator', 'description' => 'Schedule surgeries and assign operating rooms'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
