<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use DatabaseTransactions;

    private function createUserWithRole(string $slug): User
    {
        $role = Role::firstOrCreate(
            ['slug' => $slug],
            ['name' => ucfirst($slug), 'slug' => $slug, 'dashboard_route' => $slug . '.dashboard']
        );

        $user = User::create([
            'name'        => 'Test ' . ucfirst($slug),
            'email'       => $slug . '_' . uniqid() . '@test.hospital',
            'password'    => bcrypt('password'),
            'employee_id' => 'EMP-' . rand(10000, 99999),
            'department'  => 'Test',
            'is_active'   => true,
        ]);
        $user->roles()->attach($role->id);
        return $user;
    }

    public function test_system_administrator_can_access_admin_dashboard()
    {
        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.admin');
        $response->assertViewHasAll([
            'stats',
            'usersByRole',
            'moduleStats',
            'systemAlerts',
            'recentPatients',
        ]);
        $response->assertSee('Inactive Users');
        $response->assertDontSee('Pending Tasks');
    }

    public function test_non_admin_role_cannot_access_admin_dashboard()
    {
        $doctor = $this->createUserWithRole('doctor');

        $response = $this->actingAs($doctor)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }
}
