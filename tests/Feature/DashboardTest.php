<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed the 8 system roles
        $this->seed(RoleSeeder::class);
    }

    /**
     * Helper to create a user and attach a role.
     */
    protected function createUserWithRole(string $roleSlug): User
    {
        $user = User::factory()->create();
        $role = Role::where('slug', $roleSlug)->firstOrFail();
        $user->roles()->attach($role);
        return $user;
    }

    /**
     * Test mapping for all role dashboards.
     */
    public static function roleDashboardProvider(): array
    {
        return [
            ['admin', '/admin/dashboard'],
            ['doctor', '/doctor/dashboard'],
            ['med-tech', '/lab/dashboard'],
            ['rad-tech', '/radiology/dashboard'],
            ['radiologist', '/radiology/dashboard'],
            ['pharmacist', '/pharmacy/dashboard'],
            ['or-coordinator', '/surgery/dashboard'],
            ['dietitian', '/diet/dashboard'],
        ];
    }

    #[DataProvider('roleDashboardProvider')]
    public function test_dashboards_render_successfully_for_authorized_roles(string $role, string $dashboardUrl): void
    {
        $user = $this->createUserWithRole($role);

        $response = $this->actingAs($user)->get($dashboardUrl);

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }
}
