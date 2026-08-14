<?php

namespace Tests\Feature;

use App\Models\DispensingRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PharmacyDispensingTest extends TestCase
{
    use DatabaseTransactions;

    protected User $pharmacist;
    protected User $doctor;
    protected User $radTech;
    protected Patient $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pharmacist = $this->createUserWithRole('pharmacist');
        $this->doctor     = $this->createUserWithRole('doctor');
        $this->radTech    = $this->createUserWithRole('rad-tech');

        $this->patient = Patient::create([
            'patient_no'    => 'P-TEST-' . uniqid(),
            'first_name'    => 'Jane',
            'last_name'     => 'Doe',
            'date_of_birth' => '1992-05-15',
            'gender'        => 'Female',
            'patient_type'  => 'Outpatient',
        ]);
    }

    protected function createUserWithRole(string $roleSlug): User
    {
        $user = User::create([
            'name'        => 'Test ' . ucfirst($roleSlug),
            'email'       => "{$roleSlug}_" . uniqid() . "@hospital.test",
            'password'    => bcrypt('password'),
            'employee_id' => 'EMP-' . rand(10000, 99999),
            'department'  => 'Clinical',
        ]);
        $role = Role::where('slug', $roleSlug)->firstOrFail();
        $user->roles()->attach($role->id);

        return $user;
    }

    protected function createVerifiedPrescription(int $itemCount = 2): Prescription
    {
        $prescription = Prescription::create([
            'prescription_no' => 'RX-' . date('Y') . '-' . rand(1000, 9999),
            'patient_id'      => $this->patient->id,
            'doctor_id'       => $this->doctor->id,
            'diagnosis'       => 'Acute Bronchitis',
            'status'          => 'Verified',
            'prescribed_at'   => now(),
            'verified_by'     => $this->pharmacist->id,
            'verified_at'     => now(),
        ]);

        for ($i = 1; $i <= $itemCount; $i++) {
            PrescriptionItem::create([
                'prescription_id' => $prescription->id,
                'medication_name' => "Medication Test {$i}",
                'dosage'          => '500mg',
                'route'           => 'Oral',
                'frequency'       => 'Twice daily',
                'duration'        => '7 days',
                'quantity'        => 14,
                'status'          => 'Pending',
            ]);
        }

        return $prescription;
    }

    public function test_pharmacist_can_view_dispensing_index(): void
    {
        $response = $this->actingAs($this->pharmacist)
                         ->get(route('pharmacy.dispensing.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pharmacy.dispensing.index');
    }

    public function test_pharmacist_can_view_create_dispensing_form(): void
    {
        $prescription = $this->createVerifiedPrescription();

        $response = $this->actingAs($this->pharmacist)
                         ->get(route('pharmacy.dispensing.create', ['rx' => $prescription->id]));

        $response->assertStatus(200);
        $response->assertSee($prescription->prescription_no);
    }

    public function test_pharmacist_can_dispense_single_item_and_partially_dispense_prescription(): void
    {
        $prescription = $this->createVerifiedPrescription(2);
        $item1 = $prescription->items->first();
        $item2 = $prescription->items->last();

        $lotNumber = 'LOT-TEST-' . rand(1000, 9999);

        $response = $this->actingAs($this->pharmacist)
                         ->post(route('pharmacy.dispensing.store'), [
                             'prescription_item_id' => $item1->id,
                             'quantity_dispensed'   => 14,
                             'lot_number'           => $lotNumber,
                             'expiry_date'          => now()->addYear()->format('Y-m-d'),
                             'notes'                => 'Dispensed first medication batch.',
                         ]);

        $response->assertRedirect();

        // Assert database state
        $this->assertDatabaseHas('dispensing_records', [
            'prescription_item_id' => $item1->id,
            'pharmacist_id'        => $this->pharmacist->id,
            'quantity_dispensed'   => 14,
            'lot_number'           => $lotNumber,
        ]);

        $this->assertEquals('Dispensed', $item1->fresh()->status);
        $this->assertEquals('Pending', $item2->fresh()->status);
        $this->assertEquals('Partially Dispensed', $prescription->fresh()->status);
    }

    public function test_dispensing_all_items_marks_prescription_as_dispensed(): void
    {
        $prescription = $this->createVerifiedPrescription(1);
        $item = $prescription->items->first();

        $response = $this->actingAs($this->pharmacist)
                         ->post(route('pharmacy.dispensing.store'), [
                             'prescription_item_id' => $item->id,
                             'quantity_dispensed'   => 14,
                             'lot_number'           => 'LOT-FINAL-999',
                             'expiry_date'          => now()->addYear()->format('Y-m-d'),
                         ]);

        $response->assertRedirect();
        $this->assertEquals('Dispensed', $item->fresh()->status);
        $this->assertEquals('Dispensed', $prescription->fresh()->status);
    }

    public function test_unauthorized_roles_cannot_dispense_medications(): void
    {
        $prescription = $this->createVerifiedPrescription(1);
        $item = $prescription->items->first();

        // Radiologic Technologist attempting to dispense
        $response = $this->actingAs($this->radTech)
                         ->post(route('pharmacy.dispensing.store'), [
                             'prescription_item_id' => $item->id,
                             'quantity_dispensed'   => 14,
                             'lot_number'           => 'LOT-UNAUTH-123',
                             'expiry_date'          => now()->addYear()->format('Y-m-d'),
                         ]);

        $response->assertStatus(403);
    }
}
