<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VirtualMediSenseTest extends TestCase
{
    use DatabaseTransactions;

    protected User $doctor;
    protected User $medTech;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureRolesExist();

        $this->doctor  = $this->createUserWithRole('doctor', 'Internal Medicine');
        $this->medTech = $this->createUserWithRole('med-tech', 'Laboratory');
    }

    protected function createUserWithRole(string $roleSlug, string $department): User
    {
        $user = User::create([
            'name'        => 'Test ' . ucfirst($roleSlug) . ' ' . rand(100, 999),
            'email'       => "{$roleSlug}_" . uniqid() . "@hospital.test",
            'password'    => bcrypt('password'),
            'employee_id' => 'EMP-' . rand(10000, 99999),
            'department'  => $department,
            'is_active'   => true,
        ]);

        $role = Role::where('slug', $roleSlug)->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }

        return $user;
    }

    protected function ensureRolesExist(): void
    {
        $roles = [
            ['name' => 'System Administrator',    'slug' => 'admin',          'dashboard_route' => 'admin.dashboard'],
            ['name' => 'Doctor',                  'slug' => 'doctor',         'dashboard_route' => 'doctor.dashboard'],
            ['name' => 'Medical Technologist',    'slug' => 'med-tech',       'dashboard_route' => 'lab.dashboard'],
            ['name' => 'Radiologic Technologist', 'slug' => 'rad-tech',       'dashboard_route' => 'radiology.dashboard'],
            ['name' => 'Radiologist',             'slug' => 'radiologist',    'dashboard_route' => 'radiology.dashboard'],
            ['name' => 'Pharmacist',              'slug' => 'pharmacist',     'dashboard_route' => 'pharmacy.dashboard'],
            ['name' => 'Dietitian / Nutritionist','slug' => 'dietitian',      'dashboard_route' => 'diet.dashboard'],
            ['name' => 'OR Coordinator',          'slug' => 'or-coordinator', 'dashboard_route' => 'surgery.dashboard'],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['slug' => $r['slug']], $r);
        }
    }

    public function test_guest_cannot_access_medisense_workspace(): void
    {
        $response = $this->get(route('medisense.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_doctor_can_access_medisense_workspace(): void
    {
        $response = $this->actingAs($this->doctor)->get(route('medisense.index'));

        $response->assertStatus(200);
        $response->assertSee('MediSense AI');
    }

    public function test_chat_returns_unavailable_status_without_ai_execution(): void
    {
        $response = $this->actingAs($this->doctor)->postJson(route('medisense.chat'), [
            'prompt' => 'What is paracetamol used for?',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', false);
        $this->assertStringContainsString('temporarily unavailable', $response->json('error'));
    }

    public function test_capabilities_endpoint_returns_user_role_capabilities(): void
    {
        $response = $this->actingAs($this->doctor)->getJson(route('medisense.capabilities'));

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('role', 'doctor');
    }
}
