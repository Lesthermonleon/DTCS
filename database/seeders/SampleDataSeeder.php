<?php

namespace Database\Seeders;

use App\Models\DietPlan;
use App\Models\DietRequest;
use App\Models\LabRequest;
use App\Models\LabRequestItem;
use App\Models\LabResult;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\RadiologyRequest;
use App\Models\SurgeryRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Creates 20 sample patients plus sample clinical request records
 * for demonstration and testing purposes.
 */
class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create 20 sample patients
        Patient::factory(20)->create();

        $patients = Patient::all();
        $doctor   = User::whereHas('roles', fn($q) => $q->where('slug', 'doctor'))->first();
        $medTech  = User::whereHas('roles', fn($q) => $q->where('slug', 'med-tech'))->first();

        if (! $doctor || ! $medTech || $patients->isEmpty()) {
            return;
        }

        $labTests = LabTest::take(4)->get();

        // Create 10 sample lab requests
        foreach ($patients->take(10) as $patient) {
            $labRequest = LabRequest::create([
                'request_no'    => 'LR-2026-' . str_pad(LabRequest::count() + 1, 4, '0', STR_PAD_LEFT),
                'patient_id'    => $patient->id,
                'doctor_id'     => $doctor->id,
                'priority'      => fake()->randomElement(['Routine', 'Urgent']),
                'status'        => 'Pending',
                'specimen_type' => 'Blood',
                'clinical_notes'=> 'Sample lab request for ' . $patient->first_name,
                'requested_at'  => now()->subDays(rand(1, 7)),
            ]);

            // Attach 2 random tests
            foreach ($labTests->random(min(2, $labTests->count())) as $test) {
                LabRequestItem::create([
                    'lab_request_id' => $labRequest->id,
                    'lab_test_id'    => $test->id,
                    'status'         => 'Pending',
                ]);
            }
        }

        // Create 5 sample radiology requests
        foreach ($patients->skip(10)->take(5) as $patient) {
            RadiologyRequest::create([
                'request_no'          => 'RR-2026-' . str_pad(RadiologyRequest::count() + 1, 4, '0', STR_PAD_LEFT),
                'patient_id'          => $patient->id,
                'doctor_id'           => $doctor->id,
                'modality'            => fake()->randomElement(['X-Ray', 'CT Scan', 'Ultrasound', 'MRI']),
                'body_part'           => fake()->randomElement(['Chest', 'Abdomen', 'Brain', 'Spine']),
                'clinical_information'=> 'Evaluate for ' . fake()->word(),
                'priority'            => 'Routine',
                'status'              => 'Pending',
                'requested_at'        => now()->subDays(rand(1, 5)),
            ]);
        }

        // Create 5 sample prescriptions
        foreach ($patients->take(5) as $patient) {
            $prescription = Prescription::create([
                'prescription_no' => 'RX-2026-' . str_pad(Prescription::count() + 1, 4, '0', STR_PAD_LEFT),
                'patient_id'      => $patient->id,
                'doctor_id'       => $doctor->id,
                'status'          => 'Pending',
                'diagnosis'       => fake()->randomElement(['Hypertension', 'Diabetes', 'URTI', 'UTI']),
                'prescribed_at'   => now()->subDays(rand(1, 3)),
            ]);

            PrescriptionItem::create([
                'prescription_id' => $prescription->id,
                'medication_name' => fake()->randomElement(['Amoxicillin 500mg', 'Metformin 500mg', 'Amlodipine 5mg', 'Losartan 50mg']),
                'dosage'          => '500mg',
                'route'           => 'Oral',
                'frequency'       => 'TID',
                'duration'        => '7 days',
                'quantity'        => 21,
            ]);
        }

        // Create 3 sample surgery requests
        foreach ($patients->take(3) as $patient) {
            SurgeryRequest::create([
                'request_no'   => 'SR-2026-' . str_pad(SurgeryRequest::count() + 1, 4, '0', STR_PAD_LEFT),
                'patient_id'   => $patient->id,
                'doctor_id'    => $doctor->id,
                'procedure_name' => fake()->randomElement(['Appendectomy', 'Cholecystectomy', 'Herniorrhaphy']),
                'diagnosis'    => 'Acute ' . fake()->word(),
                'urgency'      => fake()->randomElement(['Elective', 'Urgent']),
                'status'       => 'Pending',
                'anesthesia_type' => 'General',
                'estimated_duration' => rand(60, 180),
                'requested_at' => now()->subDays(rand(1, 4)),
            ]);
        }

        // Create 3 sample diet requests
        foreach ($patients->skip(5)->take(3) as $patient) {
            $dietRequest = DietRequest::create([
                'request_no'  => 'DR-2026-' . str_pad(DietRequest::count() + 1, 4, '0', STR_PAD_LEFT),
                'patient_id'  => $patient->id,
                'doctor_id'   => $doctor->id,
                'diet_type'   => fake()->randomElement(['Diabetic', 'Low-Sodium', 'Renal', 'Cardiac']),
                'allergies'   => fake()->optional(0.3)->words(2, true),
                'status'      => 'Pending',
                'requested_at'=> now()->subDays(rand(1, 3)),
            ]);
        }
    }
}
