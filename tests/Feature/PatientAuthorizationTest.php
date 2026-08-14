<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PatientAuthorizationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        if (Role::count() === 0) {
            $this->seed(RoleSeeder::class);
        }
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Helper to create a user attached to a specific role by slug.
     */
    protected function createUserWithRole(string $roleSlug): User
    {
        $uniq = uniqid();
        $user = User::create([
            'name'        => 'Test User ' . ucfirst($roleSlug),
            'email'       => "{$roleSlug}_{$uniq}@hospital.test",
            'password'    => 'password',
            'employee_id' => 'EMP-' . rand(1000, 99999),
            'department'  => 'Clinical',
        ]);
        $role = Role::where('slug', $roleSlug)->firstOrFail();
        $user->roles()->attach($role->id);

        return $user;
    }

    /**
     * Helper to create a dummy patient.
     */
    protected function createPatient(): Patient
    {
        $uniq = rand(10000, 99999);
        return Patient::create([
            'patient_no'    => 'P-2026-' . $uniq,
            'first_name'    => 'John',
            'last_name'     => 'Doe',
            'date_of_birth' => '1990-01-01',
            'gender'        => 'Male',
            'patient_type'  => 'Outpatient',
        ]);
    }

    /**
     * Non-authorized roles list.
     */
    protected function unauthorizedRoles(): array
    {
        return [
            'med-tech',
            'rad-tech',
            'radiologist',
            'pharmacist',
            'dietitian',
            'or-coordinator',
        ];
    }

    // ──────────────────────────── Admin Tests ────────────────────────────

    public function test_admin_can_access_patient_information(): void
    {
        $admin = $this->createUserWithRole('admin');
        $patient = $this->createPatient();

        $this->actingAs($admin)->get(route('patients.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('patients.show', $patient))->assertStatus(200);
        $this->actingAs($admin)->get(route('patients.create'))->assertStatus(200);
        $this->actingAs($admin)->get(route('patients.edit', $patient))->assertStatus(200);

        $this->actingAs($admin)->post(route('patients.store'), [
            'first_name'    => 'Jane',
            'last_name'     => 'Smith',
            'date_of_birth' => '1995-05-05',
            'gender'        => 'Female',
            'patient_type'  => 'Inpatient',
        ])->assertRedirect(route('patients.index'));

        $this->actingAs($admin)->put(route('patients.update', $patient), [
            'first_name'    => 'John',
            'last_name'     => 'Updated',
            'date_of_birth' => '1990-01-01',
            'gender'        => 'Male',
            'patient_type'  => 'Outpatient',
        ])->assertRedirect(route('patients.show', $patient));

        $this->actingAs($admin)->delete(route('patients.destroy', $patient))->assertRedirect(route('patients.index'));
    }

    // ──────────────────────────── Doctor Tests ────────────────────────────

    public function test_doctor_can_access_patient_information(): void
    {
        $doctor = $this->createUserWithRole('doctor');
        $patient = $this->createPatient();

        $this->actingAs($doctor)->get(route('patients.index'))->assertStatus(200);
        $this->actingAs($doctor)->get(route('patients.show', $patient))->assertStatus(200);
        $this->actingAs($doctor)->get(route('patients.create'))->assertStatus(200);
        $this->actingAs($doctor)->get(route('patients.edit', $patient))->assertStatus(200);

        $this->actingAs($doctor)->post(route('patients.store'), [
            'first_name'    => 'Alice',
            'last_name'     => 'Brown',
            'date_of_birth' => '1988-08-08',
            'gender'        => 'Female',
            'patient_type'  => 'Outpatient',
        ])->assertRedirect(route('patients.index'));

        $this->actingAs($doctor)->put(route('patients.update', $patient), [
            'first_name'    => 'John',
            'last_name'     => 'DoctorEdit',
            'date_of_birth' => '1990-01-01',
            'gender'        => 'Male',
            'patient_type'  => 'Outpatient',
        ])->assertRedirect(route('patients.show', $patient));

        $this->actingAs($doctor)->delete(route('patients.destroy', $patient))->assertRedirect(route('patients.index'));
    }

    // ──────────────────────── Unauthorized Roles Tests ────────────────────────

    public function test_unauthorized_roles_cannot_access_patient_index(): void
    {
        foreach ($this->unauthorizedRoles() as $roleSlug) {
            $user = $this->createUserWithRole($roleSlug);
            $this->actingAs($user)
                 ->get(route('patients.index'))
                 ->assertStatus(403);
        }
    }

    public function test_unauthorized_roles_cannot_view_patient_profile(): void
    {
        $patient = $this->createPatient();

        foreach ($this->unauthorizedRoles() as $roleSlug) {
            $user = $this->createUserWithRole($roleSlug);
            $this->actingAs($user)
                 ->get(route('patients.show', $patient))
                 ->assertStatus(403);
        }
    }

    public function test_unauthorized_roles_cannot_create_patient(): void
    {
        foreach ($this->unauthorizedRoles() as $roleSlug) {
            $user = $this->createUserWithRole($roleSlug);

            $this->actingAs($user)
                 ->get(route('patients.create'))
                 ->assertStatus(403);

            $this->actingAs($user)
                 ->post(route('patients.store'), [
                     'first_name'    => 'Hacker',
                     'last_name'     => 'Attempt',
                     'date_of_birth' => '2000-01-01',
                     'gender'        => 'Male',
                     'patient_type'  => 'Outpatient',
                 ])
                 ->assertStatus(403);
        }
    }

    public function test_unauthorized_roles_cannot_update_patient(): void
    {
        $patient = $this->createPatient();

        foreach ($this->unauthorizedRoles() as $roleSlug) {
            $user = $this->createUserWithRole($roleSlug);

            $this->actingAs($user)
                 ->get(route('patients.edit', $patient))
                 ->assertStatus(403);

            $this->actingAs($user)
                 ->put(route('patients.update', $patient), [
                     'first_name'    => 'John',
                     'last_name'     => 'Hacked',
                     'date_of_birth' => '1990-01-01',
                     'gender'        => 'Male',
                     'patient_type'  => 'Outpatient',
                 ])
                 ->assertStatus(403);
        }
    }

    public function test_unauthorized_roles_cannot_delete_patient(): void
    {
        $patient = $this->createPatient();

        foreach ($this->unauthorizedRoles() as $roleSlug) {
            $user = $this->createUserWithRole($roleSlug);

            $this->actingAs($user)
                 ->delete(route('patients.destroy', $patient))
                 ->assertStatus(403);
        }
    }

    // ──────────────────────── Global Search Protection ────────────────────────

    public function test_admin_and_doctor_can_search_patients_in_global_search(): void
    {
        $admin = $this->createUserWithRole('admin');
        $doctor = $this->createUserWithRole('doctor');
        $this->createPatient();

        $responseAdmin = $this->actingAs($admin)->getJson(route('global-search', ['q' => 'John']));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertJsonStructure(['results' => ['patients']]);

        $responseDoctor = $this->actingAs($doctor)->getJson(route('global-search', ['q' => 'John']));
        $responseDoctor->assertStatus(200);
        $responseDoctor->assertJsonStructure(['results' => ['patients']]);
    }

    public function test_unauthorized_roles_cannot_search_patients_in_global_search(): void
    {
        $this->createPatient();

        foreach ($this->unauthorizedRoles() as $roleSlug) {
            $user = $this->createUserWithRole($roleSlug);
            $response = $this->actingAs($user)->getJson(route('global-search', ['q' => 'John']));
            $response->assertStatus(200);
            $this->assertArrayNotHasKey('patients', $response->json('results'));
        }
    }
}
