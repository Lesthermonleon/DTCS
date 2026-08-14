<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\RadiologyReport;
use App\Models\RadiologyRequest;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RadiologyWorkflowAuthorizationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $doctor;
    protected User $radTech;
    protected User $radiologist;
    protected Patient $patient;

    protected function setUp(): void
    {
        parent::setUp();

        if (Role::count() === 0) {
            $this->seed(RoleSeeder::class);
            $this->seed(PermissionSeeder::class);
        }

        $uniq = uniqid();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => "admin_{$uniq}@hospital.test",
            'password' => 'password',
            'employee_id' => 'EMP-A-' . substr(md5(uniqid()), 0, 8),
            'department' => 'Admin',
        ]);
        $this->admin->roles()->attach(Role::where('slug', 'admin')->first()->id);

        $this->doctor = User::create([
            'name' => 'Doctor Test',
            'email' => "doctor_{$uniq}@hospital.test",
            'password' => 'password',
            'employee_id' => 'EMP-D-' . substr(md5(uniqid()), 0, 8),
            'department' => 'Clinical',
        ]);
        $this->doctor->roles()->attach(Role::where('slug', 'doctor')->first()->id);

        $this->radTech = User::create([
            'name' => 'Rad Tech Test',
            'email' => "radtech_{$uniq}@hospital.test",
            'password' => 'password',
            'employee_id' => 'EMP-T-' . substr(md5(uniqid()), 0, 8),
            'department' => 'Radiology',
        ]);
        $this->radTech->roles()->attach(Role::where('slug', 'rad-tech')->first()->id);

        $this->radiologist = User::create([
            'name' => 'Radiologist Test',
            'email' => "radiologist_{$uniq}@hospital.test",
            'password' => 'password',
            'employee_id' => 'EMP-R-' . substr(md5(uniqid()), 0, 8),
            'department' => 'Radiology',
        ]);
        $this->radiologist->roles()->attach(Role::where('slug', 'radiologist')->first()->id);

        $this->patient = Patient::create([
            'patient_no'    => 'P-2026-' . rand(10000, 99999),
            'first_name'    => 'John',
            'last_name'     => 'Doe',
            'date_of_birth' => '1990-01-01',
            'gender'        => 'Male',
            'patient_type'  => 'Outpatient',
        ]);
    }

    public function test_doctor_can_create_imaging_request(): void
    {
        $response = $this->actingAs($this->doctor)->post(route('radiology.requests.store'), [
            'patient_id'           => $this->patient->id,
            'modality'             => 'X-Ray',
            'body_part'            => 'Chest PA',
            'clinical_information' => 'Persistent cough for 2 weeks',
            'priority'             => 'Routine',
        ]);

        $response->assertRedirect(route('radiology.requests.index'));
        $this->assertDatabaseHas('radiology_requests', [
            'patient_id' => $this->patient->id,
            'modality'   => 'X-Ray',
            'doctor_id'  => $this->doctor->id,
            'status'     => 'Pending',
        ]);
    }

    public function test_rad_tech_cannot_create_imaging_request(): void
    {
        $response = $this->actingAs($this->radTech)->post(route('radiology.requests.store'), [
            'patient_id' => $this->patient->id,
            'modality'   => 'CT Scan',
            'body_part'  => 'Head Plain',
            'priority'   => 'STAT',
        ]);

        $response->assertStatus(403);
    }

    public function test_radiologist_cannot_create_imaging_request(): void
    {
        $response = $this->actingAs($this->radiologist)->post(route('radiology.requests.store'), [
            'patient_id' => $this->patient->id,
            'modality'   => 'MRI',
            'body_part'  => 'Brain',
            'priority'   => 'Urgent',
        ]);

        $response->assertStatus(403);
    }

    public function test_rad_tech_can_schedule_start_upload_and_complete_procedure(): void
    {
        Storage::fake('public');

        $req = RadiologyRequest::create([
            'request_no'   => 'RR-2026-' . rand(1000, 9999),
            'patient_id'   => $this->patient->id,
            'doctor_id'    => $this->doctor->id,
            'modality'     => 'X-Ray',
            'body_part'    => 'Chest PA',
            'priority'     => 'Routine',
            'status'       => 'Pending',
            'requested_at' => now(),
        ]);

        // 1. Schedule
        $this->actingAs($this->radTech)
             ->patch(route('radiology.requests.schedule', $req))
             ->assertRedirect();
        $this->assertEquals('Scheduled', $req->fresh()->status);

        // 2. Start procedure
        $this->actingAs($this->radTech)
             ->patch(route('radiology.requests.start', $req))
             ->assertRedirect();
        $this->assertEquals('In Progress', $req->fresh()->status);

        // 3. Upload image scan
        $file = UploadedFile::fake()->create('chest_xray.png', 500, 'image/png');
        $this->actingAs($this->radTech)
             ->post(route('radiology.requests.upload', $req), ['image' => $file])
             ->assertRedirect();
        $this->assertDatabaseHas('radiology_images', ['radiology_request_id' => $req->id]);

        // 4. Complete procedure
        $this->actingAs($this->radTech)
             ->patch(route('radiology.requests.complete', $req))
             ->assertRedirect();
        $this->assertEquals('Completed', $req->fresh()->status);
        $this->assertNotNull($req->fresh()->completed_at);
    }

    public function test_radiologist_cannot_schedule_start_upload_or_complete_procedure(): void
    {
        $req = RadiologyRequest::create([
            'request_no'   => 'RR-2026-' . rand(1000, 9999),
            'patient_id'   => $this->patient->id,
            'doctor_id'    => $this->doctor->id,
            'modality'     => 'CT Scan',
            'body_part'    => 'Abdomen',
            'priority'     => 'Routine',
            'status'       => 'Pending',
            'requested_at' => now(),
        ]);

        // Radiologist attempts schedule -> 403
        $this->actingAs($this->radiologist)
             ->patch(route('radiology.requests.schedule', $req))
             ->assertStatus(403);

        $req->update(['status' => 'Scheduled']);

        // Radiologist attempts start procedure -> 403
        $this->actingAs($this->radiologist)
             ->patch(route('radiology.requests.start', $req))
             ->assertStatus(403);

        // Radiologist attempts upload scan -> 403
        $file = UploadedFile::fake()->create('ct_scan.png', 500, 'image/png');
        $this->actingAs($this->radiologist)
             ->post(route('radiology.requests.upload', $req), ['image' => $file])
             ->assertStatus(403);

        // Radiologist attempts complete procedure -> 403
        $this->actingAs($this->radiologist)
             ->patch(route('radiology.requests.complete', $req))
             ->assertStatus(403);
    }

    public function test_rad_tech_cannot_create_or_mutate_radiology_reports(): void
    {
        $req = RadiologyRequest::create([
            'request_no'   => 'RR-2026-' . rand(1000, 9999),
            'patient_id'   => $this->patient->id,
            'doctor_id'    => $this->doctor->id,
            'modality'     => 'X-Ray',
            'body_part'    => 'Hand',
            'priority'     => 'Routine',
            'status'       => 'Completed',
            'requested_at' => now(),
            'completed_at' => now(),
        ]);

        // Direct GET create report -> 403
        $this->actingAs($this->radTech)
             ->get(route('radiology.reports.create'))
             ->assertStatus(403);

        // Direct POST store report -> 403
        $this->actingAs($this->radTech)
             ->post(route('radiology.reports.store'), [
                 'radiology_request_id' => $req->id,
                 'findings'             => 'Normal bone alignment',
                 'impression'           => 'No fracture detected',
             ])
             ->assertStatus(403);

        // Create a report manually to test approve & release
        $report = RadiologyReport::create([
            'radiology_request_id' => $req->id,
            'radiologist_id'       => $this->radiologist->id,
            'findings'             => 'Draft findings',
            'impression'           => 'Draft impression',
            'status'               => 'Draft',
        ]);

        // Direct PATCH approve -> 403
        $this->actingAs($this->radTech)
             ->patch(route('radiology.reports.approve', $report))
             ->assertStatus(403);

        $report->update(['status' => 'Approved']);

        // Direct PATCH release -> 403
        $this->actingAs($this->radTech)
             ->patch(route('radiology.reports.release', $report))
             ->assertStatus(403);
    }

    public function test_radiologist_can_create_approve_and_release_diagnostic_report(): void
    {
        $req = RadiologyRequest::create([
            'request_no'   => 'RR-2026-' . rand(1000, 9999),
            'patient_id'   => $this->patient->id,
            'doctor_id'    => $this->doctor->id,
            'modality'     => 'X-Ray',
            'body_part'    => 'Chest PA',
            'priority'     => 'Routine',
            'status'       => 'Completed',
            'requested_at' => now(),
            'completed_at' => now(),
        ]);

        // 1. Create Report (Draft)
        $this->actingAs($this->radiologist)
             ->post(route('radiology.reports.store'), [
                 'radiology_request_id' => $req->id,
                 'findings'             => 'Lungs are clear bilaterally. Heart size is within normal limits.',
                 'impression'           => 'No acute cardiopulmonary disease.',
             ])
             ->assertRedirect();

        $report = RadiologyReport::where('radiology_request_id', $req->id)->first();
        $this->assertNotNull($report);
        $this->assertEquals('Draft', $report->status);

        // 2. Approve Report
        $this->actingAs($this->radiologist)
             ->patch(route('radiology.reports.approve', $report))
             ->assertRedirect();
        $this->assertEquals('Approved', $report->fresh()->status);
        $this->assertEquals($this->radiologist->id, $report->fresh()->approved_by);

        // 3. Release Report
        $this->actingAs($this->radiologist)
             ->patch(route('radiology.reports.release', $report))
             ->assertRedirect();
        $this->assertEquals('Released', $report->fresh()->status);
        $this->assertEquals($this->radiologist->id, $report->fresh()->released_by);
    }

    public function test_rad_tech_and_radiologist_cannot_access_patients_directory(): void
    {
        $this->actingAs($this->radTech)
             ->get(route('patients.index'))
             ->assertStatus(403);

        $this->actingAs($this->radiologist)
             ->get(route('patients.index'))
             ->assertStatus(403);

        // Doctor CAN access patients directory
        $this->actingAs($this->doctor)
             ->get(route('patients.index'))
             ->assertStatus(200);

        // Admin CAN access patients directory
        $this->actingAs($this->admin)
             ->get(route('patients.index'))
             ->assertStatus(200);
    }

    public function test_full_imaging_upload_and_interpretation_lifecycle(): void
    {
        Storage::fake('public');

        // 1. Doctor creates imaging request
        $this->actingAs($this->doctor)->post(route('radiology.requests.store'), [
            'patient_id'           => $this->patient->id,
            'modality'             => 'MRI',
            'body_part'            => 'Lumbar Spine',
            'clinical_information' => 'Lower back pain radiating to left leg',
            'priority'             => 'Urgent',
        ])->assertRedirect(route('radiology.requests.index'));

        $req = RadiologyRequest::where('patient_id', $this->patient->id)->where('modality', 'MRI')->first();
        $this->assertNotNull($req);
        $this->assertEquals('Pending', $req->status);

        // 2. Rad Tech schedules procedure
        $this->actingAs($this->radTech)
             ->patch(route('radiology.requests.schedule', $req))
             ->assertRedirect();
        $this->assertEquals('Scheduled', $req->fresh()->status);

        // 3. Rad Tech uploads multi-file scans with notes & completes procedure in one step
        $file1 = UploadedFile::fake()->image('mri_sagittal.jpg', 800, 600);
        $file2 = UploadedFile::fake()->image('mri_axial.jpg', 900, 700);

        $this->actingAs($this->radTech)
             ->post(route('radiology.requests.upload', $req), [
                 'images' => [$file1, $file2],
                 'notes'  => 'Lumbar spine MRI scan completed without contrast.',
                 'action' => 'upload_complete',
             ])
             ->assertSessionHasNoErrors()
             ->assertRedirect();

        $req->refresh();
        $this->assertEquals('Completed', $req->status);
        $this->assertCount(2, $req->images);

        // 4. Verify secure file access route for Technologist & Radiologist
        $firstImage = $req->images->first();
        $this->actingAs($this->radTech)
             ->get(route('radiology.images.view', $firstImage))
             ->assertStatus(200);

        $this->actingAs($this->radiologist)
             ->get(route('radiology.images.view', $firstImage))
             ->assertStatus(200);

        // 5. Radiologist creates diagnostic report
        $this->actingAs($this->radiologist)
             ->post(route('radiology.reports.store'), [
                 'radiology_request_id' => $req->id,
                 'findings'             => 'L4-L5 disc protrusion causing mild left neural foraminal stenosis.',
                 'impression'           => 'L4-L5 left paracentral disc protrusion.',
                 'recommendations'      => 'Clinical correlation and physical therapy advised.',
             ])
             ->assertRedirect();

        $report = $req->fresh()->report;
        $this->assertNotNull($report);
        $this->assertEquals('Draft', $report->status);

        // 6. Radiologist approves and releases report
        $this->actingAs($this->radiologist)->patch(route('radiology.reports.approve', $report))->assertRedirect();
        $this->actingAs($this->radiologist)->patch(route('radiology.reports.release', $report))->assertRedirect();

        $this->assertEquals('Released', $report->fresh()->status);

        // 7. Doctor views request and released report with scan images
        $this->actingAs($this->doctor)
             ->get(route('radiology.requests.show', $req))
             ->assertStatus(200)
             ->assertSee('Lumbar spine MRI scan completed without contrast.')
             ->assertSee('L4-L5 left paracentral disc protrusion.');
    }

    public function test_rad_tech_can_complete_procedure_with_existing_scans_without_attaching_new_files(): void
    {
        Storage::fake('public');

        $req = RadiologyRequest::create([
            'request_no' => 'RR-2026-9999',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'modality' => 'X-Ray',
            'body_part' => 'Chest PA',
            'priority' => 'Routine',
            'status' => 'Scheduled',
            'requested_at' => now(),
            'scheduled_at' => now(),
        ]);

        // Upload first scan file in progress
        $file = UploadedFile::fake()->image('chest_xray.jpg', 600, 600);
        $this->actingAs($this->radTech)
             ->post(route('radiology.requests.upload', $req), [
                 'images' => [$file],
                 'action' => 'upload_only',
             ])
             ->assertSessionHasNoErrors()
             ->assertRedirect();

        $this->assertEquals('In Progress', $req->fresh()->status);
        $this->assertCount(1, $req->fresh()->images);

        // Technologist now submits completion WITHOUT attaching new files
        $this->actingAs($this->radTech)
             ->post(route('radiology.requests.upload', $req), [
                 'action' => 'upload_complete',
             ])
             ->assertSessionHasNoErrors()
             ->assertRedirect();

        $this->assertEquals('Completed', $req->fresh()->status);
        $this->assertCount(1, $req->fresh()->images);
    }

    public function test_rad_tech_can_upload_dicom_imaging_file_and_pdf_supporting_document(): void
    {
        Storage::fake('public');

        $req = RadiologyRequest::create([
            'request_no'   => 'RR-2026-9998',
            'patient_id'   => $this->patient->id,
            'doctor_id'    => $this->doctor->id,
            'modality'     => 'CT Scan',
            'body_part'    => 'Abdomen',
            'priority'     => 'Urgent',
            'status'       => 'Scheduled',
            'requested_at' => now(),
            'scheduled_at' => now(),
        ]);

        $dicom = UploadedFile::fake()->create('scan001.dcm', 500, 'application/dicom');
        $pdf   = UploadedFile::fake()->create('contrast_consent.pdf', 200, 'application/pdf');

        $this->actingAs($this->radTech)
             ->post(route('radiology.requests.upload', $req), [
                 'images'    => [$dicom],
                 'documents' => [$pdf],
                 'notes'     => 'CT scan with IV contrast, consent attached.',
                 'action'    => 'upload_complete',
             ])
             ->assertSessionHasNoErrors()
             ->assertRedirect();

        $req->refresh();
        $this->assertEquals('Completed', $req->status);
        $this->assertCount(2, $req->images);

        $types = $req->images->pluck('file_type')->all();
        $this->assertContains('dcm', $types);
        $this->assertContains('pdf', $types);
    }

    public function test_upload_rejects_oversized_files_using_configurable_limit(): void
    {
        Storage::fake('public');

        // Set tight limit of 100 KB for testing configurable limit
        config(['radiology.max_upload_size_kb' => 100]);

        $req = RadiologyRequest::create([
            'request_no'   => 'RR-2026-9997',
            'patient_id'   => $this->patient->id,
            'doctor_id'    => $this->doctor->id,
            'modality'     => 'MRI',
            'body_part'    => 'Brain',
            'priority'     => 'Routine',
            'status'       => 'Scheduled',
            'requested_at' => now(),
            'scheduled_at' => now(),
        ]);

        // Create 200KB fake file (exceeds 100KB configured limit)
        $largeFile = UploadedFile::fake()->create('large_mri.jpg', 200, 'image/jpeg');

        $this->actingAs($this->radTech)
             ->post(route('radiology.requests.upload', $req), [
                 'images' => [$largeFile],
                 'action' => 'upload_only',
             ])
             ->assertSessionHasErrors(['images.0'])
             ->assertRedirect();

        $errors = session('errors')->get('images.0');
        $this->assertContains("The selected file could not be uploaded because it exceeds the server's configured upload limit.", $errors);
    }

    public function test_upload_rejects_unsupported_file_types_with_specific_error_message(): void
    {
        Storage::fake('public');

        $req = RadiologyRequest::create([
            'request_no'   => 'RR-2026-9996',
            'patient_id'   => $this->patient->id,
            'doctor_id'    => $this->doctor->id,
            'modality'     => 'X-Ray',
            'body_part'    => 'Chest',
            'priority'     => 'Routine',
            'status'       => 'Scheduled',
            'requested_at' => now(),
            'scheduled_at' => now(),
        ]);

        $invalidFile = UploadedFile::fake()->create('malicious.exe', 50, 'application/x-msdownload');

        $this->actingAs($this->radTech)
             ->post(route('radiology.requests.upload', $req), [
                 'images' => [$invalidFile],
                 'action' => 'upload_only',
             ])
             ->assertSessionHasErrors(['images.0'])
             ->assertRedirect();

        $errors = session('errors')->get('images.0');
        $this->assertContains("The selected file type is not supported.", $errors);
    }
}
