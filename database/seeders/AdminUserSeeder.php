<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
/**
 * Seeds one user per role for development/testing.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'        => 'System Administrator',
                'email'       => 'admin@ditc.com',
                'password'    => 'password',
                'employee_id' => 'EMP-0001',
                'department'  => 'Administration',
                'phone'       => '09000000001',
                'role_slug'   => 'admin',
            ],
            [
                'name'        => 'Dr. Juan Dela Cruz',
                'email'       => 'doctor@ditc.com',
                'password'    => 'password',
                'employee_id' => 'EMP-0002',
                'department'  => 'Internal Medicine',
                'phone'       => '09000000002',
                'role_slug'   => 'doctor',
            ],
            [
                'name'        => 'Maria Santos',
                'email'       => 'medtech@ditc.com',
                'password'    => 'password',
                'employee_id' => 'EMP-0003',
                'department'  => 'Laboratory',
                'phone'       => '09000000003',
                'role_slug'   => 'med-tech',
            ],
            [
                'name'        => 'Jose Reyes',
                'email'       => 'radtech@ditc.com',
                'password'    => 'password',
                'employee_id' => 'EMP-0004',
                'department'  => 'Radiology',
                'phone'       => '09000000004',
                'role_slug'   => 'rad-tech',
            ],
            [
                'name'        => 'Dr. Ana Lim',
                'email'       => 'radiologist@ditc.com',
                'password'    => 'password',
                'employee_id' => 'EMP-0005',
                'department'  => 'Radiology',
                'phone'       => '09000000005',
                'role_slug'   => 'radiologist',
            ],
            [
                'name'        => 'Pedro Garcia',
                'email'       => 'pharmacist@ditc.com',
                'password'    => 'password',
                'employee_id' => 'EMP-0006',
                'department'  => 'Pharmacy',
                'phone'       => '09000000006',
                'role_slug'   => 'pharmacist',
            ],
            [
                'name'        => 'Rosa Mendoza',
                'email'       => 'dietitian@ditc.com',
                'password'    => 'password',
                'employee_id' => 'EMP-0007',
                'department'  => 'Nutrition & Dietetics',
                'phone'       => '09000000007',
                'role_slug'   => 'dietitian',
            ],
            [
                'name'        => 'Carlos Torres',
                'email'       => 'orcoord@ditc.com',
                'password'    => 'password',
                'employee_id' => 'EMP-0008',
                'department'  => 'Operating Room',
                'phone'       => '09000000008',
                'role_slug'   => 'or-coordinator',
            ],
        ];

        foreach ($users as $data) {
            $roleSlug = $data['role_slug'];
            unset($data['role_slug']);

            $user = User::updateOrCreate(['email' => $data['email']], $data);

            // Attach role if not already attached
            $role = Role::where('slug', $roleSlug)->first();
            if ($role && ! $user->roles()->where('role_user.role_id', $role->id)->exists()) {
                $user->roles()->attach($role->id);
            }
        }
    }
}
