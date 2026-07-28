<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSelfModificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed the system roles (which includes 'admin', 'doctor', etc.)
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $adminUser = User::factory()->create(['is_active' => true]);
        $adminUser->roles()->attach($adminRole);

        $response = $this
            ->actingAs($adminUser)
            ->put(route('admin.users.update', $adminUser), [
                'name'        => 'Admin Name',
                'email'       => $adminUser->email,
                'employee_id' => $adminUser->employee_id,
                'role_id'     => $adminRole->id,
                'is_active'   => 0, // Attempt self-deactivation
            ]);

        $response->assertSessionHasErrors(['is_active']);
        
        $adminUser->refresh();
        $this->assertTrue($adminUser->is_active);
    }

    public function test_admin_cannot_change_own_roles(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $otherRole = Role::where('slug', 'doctor')->firstOrFail();
        $adminUser = User::factory()->create();
        $adminUser->roles()->attach($adminRole);

        $response = $this
            ->actingAs($adminUser)
            ->put(route('admin.users.update', $adminUser), [
                'name'        => $adminUser->name,
                'email'       => $adminUser->email,
                'employee_id' => $adminUser->employee_id,
                'role_id'     => $otherRole->id, // Attempt self-role modification
                'is_active'   => 1,
            ]);

        $response->assertSessionHasErrors(['role_id']);
        
        $adminUser->refresh();
        $this->assertSame('admin', $adminUser->roles()->first()->slug);
    }

    public function test_admin_cannot_self_assign_role(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $otherRole = Role::where('slug', 'doctor')->firstOrFail();
        $adminUser = User::factory()->create();
        $adminUser->roles()->attach($adminRole);

        $response = $this
            ->actingAs($adminUser)
            ->post(route('admin.users.assign-role', $adminUser), [
                'role_id' => $otherRole->id,
            ]);

        $response->assertForbidden();
        
        $adminUser->refresh();
        $this->assertSame('admin', $adminUser->roles()->first()->slug);
    }

    public function test_admin_can_reset_user_password(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $adminUser = User::factory()->create();
        $adminUser->roles()->attach($adminRole);

        $otherUser = User::factory()->create([
            'password' => \Illuminate\Support\Facades\Hash::make('some_old_secret_password'),
        ]);

        $response = $this
            ->actingAs($adminUser)
            ->post(route('admin.users.reset-password', $otherUser));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'User password reset successfully to: password');

        $otherUser->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('password', $otherUser->password));
    }

    public function test_admin_can_archive_user(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $adminUser = User::factory()->create();
        $adminUser->roles()->attach($adminRole);

        $otherUser = User::factory()->create();

        $response = $this
            ->actingAs($adminUser)
            ->delete(route('admin.users.destroy', $otherUser));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'User account archived successfully.');

        $this->assertSoftDeleted('users', [
            'id' => $otherUser->id,
        ]);
    }

    public function test_admin_can_restore_archived_user(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $adminUser = User::factory()->create();
        $adminUser->roles()->attach($adminRole);

        $otherUser = User::factory()->create();
        $otherUser->delete();

        $this->assertSoftDeleted('users', [
            'id' => $otherUser->id,
        ]);

        $response = $this
            ->actingAs($adminUser)
            ->post(route('admin.users.restore', $otherUser->id));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'User account restored successfully.');

        $this->assertNotSoftDeleted('users', [
            'id' => $otherUser->id,
        ]);
    }

    public function test_admin_can_filter_archived_users(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $adminUser = User::factory()->create();
        $adminUser->roles()->attach($adminRole);

        $otherUser = User::factory()->create(['name' => 'Archived Guy']);
        $otherUser->delete();

        $response = $this
            ->actingAs($adminUser)
            ->get(route('admin.users.index', ['search' => 'Random Search Term That Does Not Match']));

        $response->assertStatus(200);
        $response->assertDontSee('Archived Guy');

        $response = $this
            ->actingAs($adminUser)
            ->get(route('admin.users.index', ['search' => 'Archived Guy']));

        $response->assertStatus(200);
        $response->assertSee('Archived Guy');
    }

    public function test_admin_can_view_permissions_index(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $adminUser = User::factory()->create();
        $adminUser->roles()->attach($adminRole);

        $response = $this
            ->actingAs($adminUser)
            ->get(route('admin.roles.index'));

        $response->assertStatus(200);
        $response->assertSee('Doctor');
        $response->assertSee('Medical Technologist');
    }

    public function test_admin_can_view_permission_details(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $adminUser = User::factory()->create();
        $adminUser->roles()->attach($adminRole);

        $doctorRole = Role::where('slug', 'doctor')->firstOrFail();

        $response = $this
            ->actingAs($adminUser)
            ->get(route('admin.roles.show', $doctorRole));

        $response->assertStatus(200);
        $response->assertSee('LIS');
        $response->assertSee('RIS');
        $response->assertSee('PMS');
        $response->assertSee('SORS');
        $response->assertSee('DNMS');
    }

    public function test_admin_cannot_modify_permissions(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $adminUser = User::factory()->create();
        $adminUser->roles()->attach($adminRole);

        // Try access create
        $this->actingAs($adminUser)->get(route('admin.roles.create'))->assertStatus(403);

        // Try access edit
        $role = Role::first();
        $this->actingAs($adminUser)->get(route('admin.roles.edit', $role))->assertStatus(403);

        // Try access store
        $this->actingAs($adminUser)->post(route('admin.roles.store'), ['name' => 'Failed'])->assertStatus(403);

        // Try access update
        $this->actingAs($adminUser)->put(route('admin.roles.update', $role), ['name' => 'Failed'])->assertStatus(403);

        // Try access destroy
        $this->actingAs($adminUser)->delete(route('admin.roles.destroy', $role))->assertStatus(403);
    }
}
