<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_system_administrator_can_access_admin_dashboard()
    {
        $admin = User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))->first()
                 ?? User::factory()->create();

        if (!$admin->roles()->where('slug', 'admin')->exists()) {
            $role = Role::where('slug', 'admin')->first() ?? Role::create(['name' => 'System Administrator', 'slug' => 'admin']);
            $admin->roles()->attach($role);
        }

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.admin');
        $response->assertViewHasAll([
            'stats',
            'usersByRole',
            'moduleStats',
            'systemAlerts',
            'recentActivity',
            'recentPatients',
        ]);
    }

    public function test_non_admin_role_cannot_access_admin_dashboard()
    {
        $role = Role::where('slug', 'doctor')->first() ?? Role::create(['name' => 'Doctor', 'slug' => 'doctor']);
        $doctor = User::create([
            'name'        => 'Pure Doctor Test',
            'email'       => 'pure_doctor_' . uniqid() . '@hospital.test',
            'password'    => 'password',
            'employee_id' => 'DOC-' . rand(1000, 9999),
        ]);
        $doctor->roles()->attach($role->id);

        $response = $this->actingAs($doctor)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }
}
