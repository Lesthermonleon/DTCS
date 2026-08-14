<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use App\Services\GoogleWebSearchService;
use App\Services\HimsToolProvider;
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

        $this->seed(\Database\Seeders\RoleSeeder::class);

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

    public function test_guest_cannot_access_medisense_workspace(): void
    {
        $response = $this->get(route('medisense.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_doctor_can_access_medisense_workspace(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->doctor)->get(route('medisense.index'));

        $response->assertStatus(200);
        $response->assertSee('MediSense AI');
    }

    public function test_hims_tool_provider_searches_patients(): void
    {
        $patient = Patient::create([
            'patient_no'    => 'P-TEST-101',
            'first_name'    => 'Maria',
            'last_name'     => 'Santos',
            'date_of_birth' => '1990-01-01',
            'gender'        => 'Female',
            'blood_type'    => 'A+',
        ]);

        $provider = new HimsToolProvider();
        $result = $provider->executeTool('searchPatients', ['query' => 'Maria'], $this->doctor);

        $this->assertTrue($result['success']);
        $this->assertGreaterThanOrEqual(1, $result['count']);
        $this->assertEquals('HIMS Database', $result['source']);
    }

    public function test_web_search_anonymizes_patient_identifiers(): void
    {
        $service = new GoogleWebSearchService();
        $rawQuery = "Find WHO guidelines for Maria Santos, Patient ID P-12345, phone 555-1234";
        $patientContext = [
            'name'       => 'Maria Santos',
            'patient_no' => 'P-12345',
            'phone'      => '555-1234',
        ];

        $anonymized = $service->anonymizeQuery($rawQuery, $patientContext);

        $this->assertStringNotContainsString('Maria Santos', $anonymized);
        $this->assertStringNotContainsString('P-12345', $anonymized);
        $this->assertStringNotContainsString('555-1234', $anonymized);
    }

    public function test_create_lab_request_tool_requires_confirmation_when_unconfirmed(): void
    {
        $patient = Patient::create([
            'patient_no'    => 'P-TEST-102',
            'first_name'    => 'Juan',
            'last_name'     => 'Dela Cruz',
            'date_of_birth' => '1982-03-20',
            'gender'        => 'Male',
        ]);

        $provider = new HimsToolProvider();
        $result = $provider->executeTool('createLabRequest', [
            'patientId' => $patient->id,
            'testName'  => 'Complete Blood Count (CBC)',
            'confirmed' => false,
        ], $this->doctor);

        $this->assertFalse($result['success']);
        $this->assertTrue($result['requires_confirm']);
        $this->assertArrayHasKey('action_details', $result);
    }

    public function test_create_lab_request_tool_executes_when_confirmed(): void
    {
        $patient = Patient::create([
            'patient_no'    => 'P-TEST-103',
            'first_name'    => 'Anna',
            'last_name'     => 'Reyes',
            'date_of_birth' => '1995-07-10',
            'gender'        => 'Female',
        ]);

        $provider = new HimsToolProvider();
        $result = $provider->executeTool('createLabRequest', [
            'patientId' => $patient->id,
            'testName'  => 'Lipid Panel',
            'confirmed' => true,
        ], $this->doctor);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('lab_requests', [
            'patient_id' => $patient->id,
            'doctor_id'  => $this->doctor->id,
            'status'     => 'pending',
        ]);
    }

    public function test_open_ended_unseen_lab_workload_paraphrase(): void
    {
        config(['medisense.api_key' => 'fake-test-key']);
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Laboratory Workload details from Gemini AI']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($this->doctor)->postJson(route('medisense.chat'), [
            'prompt' => 'What is still unfinished in the lab?',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertStringContainsString('Laboratory Workload', $response->json('ai_response'));
    }

    public function test_open_ended_unseen_workload_focus_query(): void
    {
        config(['medisense.api_key' => 'fake-test-key']);
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'HIMS Operational Workload Summary from Gemini AI']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($this->doctor)->postJson(route('medisense.chat'), [
            'prompt' => 'What should I focus on today?',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertStringContainsString('HIMS Operational Workload Summary', $response->json('ai_response'));
    }

    public function test_open_ended_unseen_general_knowledge_query(): void
    {
        config(['medisense.api_key' => 'fake-test-key']);
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'An MRI (Magnetic Resonance Imaging) is a non-invasive medical imaging test...']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($this->doctor)->postJson(route('medisense.chat'), [
            'prompt' => 'What is an MRI and how does magnetic resonance imaging work?',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertContains('✨ AI Analysis', $response->json('sources'));
    }

    public function test_open_ended_unseen_action_intent(): void
    {
        config(['medisense.api_key' => 'fake-test-key']);
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'functionCall' => [
                                        'name' => 'createLabRequest',
                                        'args' => [
                                            'patientId' => 1,
                                            'testName'  => 'Complete Blood Count (CBC)',
                                            'confirmed' => false
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $patient = Patient::create([
            'patient_no'    => 'P-TEST-105',
            'first_name'    => 'Carlos',
            'last_name'     => 'Mendoza',
            'date_of_birth' => '1988-11-15',
            'gender'        => 'Male',
        ]);

        $response = $this->actingAs($this->doctor)->postJson(route('medisense.chat'), [
            'prompt'     => 'Can you order a CBC request for this patient?',
            'patient_id' => $patient->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('requires_confirm', true);
    }

    public function test_unconfigured_api_key_returns_clear_error_status(): void
    {
        config(['medisense.api_key' => '']);

        $response = $this->actingAs($this->doctor)->postJson(route('medisense.chat'), [
            'prompt' => 'What is paracetamol used for?',
        ]);

        $response->assertStatus(400);
        $response->assertJsonPath('success', false);
        $this->assertStringContainsString('unable to connect', $response->json('error'));
    }
}
