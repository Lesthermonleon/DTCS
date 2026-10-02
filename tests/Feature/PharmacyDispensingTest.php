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

    public function test_pharmacist_can_batch_dispense_multiple_items_in_single_transaction(): void
    {
        $prescription = $this->createVerifiedPrescription(3);
        $itemIds = $prescription->items->pluck('id')->toArray();

        $response = $this->actingAs($this->pharmacist)
                         ->post(route('pharmacy.dispensing.store'), [
                             'prescription_id'      => $prescription->id,
                             'prescription_item_ids'=> $itemIds,
                             'lot_number'           => 'LOT-BATCH-2026',
                             'expiry_date'          => now()->addYear()->format('Y-m-d'),
                             'notes'                => 'Batch dispensed 3 items.',
                         ]);

        $response->assertRedirect();

        // Assert all 3 items are marked Dispensed
        foreach ($prescription->items as $item) {
            $this->assertEquals('Dispensed', $item->fresh()->status);
            $this->assertDatabaseHas('dispensing_records', [
                'prescription_item_id' => $item->id,
                'pharmacist_id'        => $this->pharmacist->id,
                'lot_number'           => 'LOT-BATCH-2026',
            ]);
        }

        // Assert prescription status is now Dispensed
        $this->assertEquals('Dispensed', $prescription->fresh()->status);
    }

    public function test_batch_dispense_prevents_re_dispensing_already_dispensed_items(): void
    {
        $prescription = $this->createVerifiedPrescription(2);
        $items = $prescription->items;
        $item1 = $items->first();
        $item2 = $items->last();

        // First dispense item 1
        $this->actingAs($this->pharmacist)
             ->post(route('pharmacy.dispensing.store'), [
                 'prescription_item_id' => $item1->id,
                 'quantity_dispensed'   => 14,
                 'lot_number'           => 'LOT-PREV-001',
                 'expiry_date'          => now()->addYear()->format('Y-m-d'),
             ]);

        // Attempt to batch dispense both item1 and item2
        $response = $this->actingAs($this->pharmacist)
                         ->post(route('pharmacy.dispensing.store'), [
                             'prescription_id'      => $prescription->id,
                             'prescription_item_ids'=> [$item1->id, $item2->id],
                             'lot_number'           => 'LOT-PREV-002',
                             'expiry_date'          => now()->addYear()->format('Y-m-d'),
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals('Pending', $item2->fresh()->status);
    }

    public function test_batch_dispense_rejects_items_from_different_prescriptions(): void
    {
        $p1 = $this->createVerifiedPrescription(1);
        $p2 = $this->createVerifiedPrescription(1);

        $response = $this->actingAs($this->pharmacist)
                         ->post(route('pharmacy.dispensing.store'), [
                             'prescription_item_ids'=> [$p1->items->first()->id, $p2->items->first()->id],
                             'lot_number'           => 'LOT-MISMATCH-999',
                             'expiry_date'          => now()->addYear()->format('Y-m-d'),
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_auto_batch_dispense_by_prescription_id_alone(): void
    {
        $prescription = $this->createVerifiedPrescription(3);

        // Submit prescription_id alone without explicit item IDs
        $response = $this->actingAs($this->pharmacist)
                         ->post(route('pharmacy.dispensing.store'), [
                             'prescription_id' => $prescription->id,
                             'lot_number'      => 'LOT-AUTO-2026',
                             'expiry_date'     => now()->addYear()->format('Y-m-d'),
                             'notes'           => 'Auto-dispensed all eligible pending items.',
                         ]);

        $response->assertRedirect();

        // Assert all items are now Dispensed
        foreach ($prescription->items as $item) {
            $this->assertEquals('Dispensed', $item->fresh()->status);
        }

        $this->assertEquals('Dispensed', $prescription->fresh()->status);
    }

    public function test_batch_dispense_with_item_specific_lot_numbers_and_expiry_dates(): void
    {
        $prescription = $this->createVerifiedPrescription(2);
        $items = $prescription->items;
        $item1 = $items->first();
        $item2 = $items->last();

        $itemsPayload = [
            $item1->id => [
                'lot_number'  => 'LOT-PARA-991',
                'expiry_date' => now()->addYears(2)->format('Y-m-d'),
            ],
            $item2->id => [
                'lot_number'  => 'LOT-SOLM-005',
                'expiry_date' => now()->addMonths(18)->format('Y-m-d'),
            ],
        ];

        $response = $this->actingAs($this->pharmacist)
                         ->post(route('pharmacy.dispensing.store'), [
                             'prescription_id' => $prescription->id,
                             'items'           => $itemsPayload,
                             'notes'           => 'Dispensed with item-specific lot numbers.',
                         ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('dispensing_records', [
            'prescription_item_id' => $item1->id,
            'lot_number'           => 'LOT-PARA-991',
        ]);

        $this->assertDatabaseHas('dispensing_records', [
            'prescription_item_id' => $item2->id,
            'lot_number'           => 'LOT-SOLM-005',
        ]);

        $this->assertEquals('Dispensed', $prescription->fresh()->status);
    }

    public function test_batch_dispense_rejects_expired_medication_item(): void
    {
        $prescription = $this->createVerifiedPrescription(2);
        $items = $prescription->items;
        $item1 = $items->first();
        $item2 = $items->last();

        $itemsPayload = [
            $item1->id => [
                'lot_number'  => 'LOT-VALID-100',
                'expiry_date' => now()->addYear()->format('Y-m-d'),
            ],
            $item2->id => [
                'lot_number'  => 'LOT-EXPIRED-999',
                'expiry_date' => now()->subDay()->format('Y-m-d'), // Expired date
            ],
        ];

        $response = $this->actingAs($this->pharmacist)
                         ->post(route('pharmacy.dispensing.store'), [
                             'prescription_id' => $prescription->id,
                             'items'           => $itemsPayload,
                         ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
        $this->assertEquals('Pending', $item1->fresh()->status);
        $this->assertEquals('Pending', $item2->fresh()->status);
    }

    public function test_stock_provider_insufficient_stock_blocks_dispensing(): void
    {
        $prescription = $this->createVerifiedPrescription(1);
        $item = $prescription->items->first();
        $item->update(['quantity' => 150]); // Prescribed 150, but MockMedicationStockProvider has 100 for Paracetamol or default

        \App\Services\Pharmacy\MockMedicationStockProvider::setMockStock(
            $item->id,
            new \App\Services\Pharmacy\DTOs\MedicationStockData(
                medicationName: $item->medication_name,
                availableQuantity: 10,
                lotNumber: 'LOT-LOW-001',
                expiryDate: now()->addYear()->format('Y-m-d')
            )
        );

        $response = $this->actingAs($this->pharmacist)
                         ->post(route('pharmacy.dispensing.store'), [
                             'prescription_id' => $prescription->id,
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Insufficient stock', session('error'));
        $this->assertEquals('Pending', $item->fresh()->status);

        \App\Services\Pharmacy\MockMedicationStockProvider::reset();
    }

    public function test_stock_provider_expired_batch_blocks_dispensing(): void
    {
        $prescription = $this->createVerifiedPrescription(1);
        $item = $prescription->items->first();

        \App\Services\Pharmacy\MockMedicationStockProvider::setMockStock(
            $item->id,
            new \App\Services\Pharmacy\DTOs\MedicationStockData(
                medicationName: $item->medication_name,
                availableQuantity: 100,
                lotNumber: 'LOT-EXP-888',
                expiryDate: '2020-01-01' // Expired date
            )
        );

        $response = $this->actingAs($this->pharmacist)
                         ->post(route('pharmacy.dispensing.store'), [
                             'prescription_id' => $prescription->id,
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('expired', session('error'));
        $this->assertEquals('Pending', $item->fresh()->status);

        \App\Services\Pharmacy\MockMedicationStockProvider::reset();
    }
}

