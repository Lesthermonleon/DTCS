<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Services\Pharmacy\Contracts\MedicationStockProviderInterface;
use App\Services\Pharmacy\DTOs\MedicationStockData;
use App\Services\Pharmacy\MockMedicationStockProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MedicineAvailabilityTest extends TestCase
{
    use DatabaseTransactions;

    protected User $pharmacist;
    protected User $doctor;
    protected User $admin;
    protected User $radTech;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pharmacist = $this->createUserWithRole('pharmacist');
        $this->doctor     = $this->createUserWithRole('doctor');
        $this->admin      = $this->createUserWithRole('admin');
        $this->radTech    = $this->createUserWithRole('rad-tech');
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

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get(route('pharmacy.medicines.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_pharmacist_can_view_medicine_availability_index(): void
    {
        $response = $this->actingAs($this->pharmacist)
                         ->get(route('pharmacy.medicines.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pharmacy.medicines.index');
        $response->assertSee('Medicine Availability');
        $response->assertSee('Paracetamol 500mg Tablet');
    }

    public function test_doctor_and_admin_can_view_medicine_availability_index(): void
    {
        $responseDoctor = $this->actingAs($this->doctor)
                               ->get(route('pharmacy.medicines.index'));
        $responseDoctor->assertStatus(200);

        $responseAdmin = $this->actingAs($this->admin)
                              ->get(route('pharmacy.medicines.index'));
        $responseAdmin->assertStatus(200);
    }

    public function test_unauthorized_role_cannot_view_medicine_availability(): void
    {
        $response = $this->actingAs($this->radTech)
                         ->get(route('pharmacy.medicines.index'));

        $response->assertStatus(403);
    }

    public function test_search_filters_medicine_by_name_or_lot_number(): void
    {
        // Search by medicine name
        $responseName = $this->actingAs($this->pharmacist)
                             ->get(route('pharmacy.medicines.index', ['search' => 'Amoxicillin']));

        $responseName->assertStatus(200);
        $responseName->assertSee('Amoxicillin 500mg Capsule');
        $responseName->assertDontSee('Paracetamol 500mg Tablet');

        // Search by batch/lot number
        $responseLot = $this->actingAs($this->pharmacist)
                            ->get(route('pharmacy.medicines.index', ['search' => 'PARA-2026-001']));

        $responseLot->assertStatus(200);
        $responseLot->assertSee('Paracetamol 500mg Tablet');
    }

    public function test_stock_status_filter_filters_in_stock_low_and_out_of_stock(): void
    {
        // Filter Low Stock
        $responseLow = $this->actingAs($this->pharmacist)
                            ->get(route('pharmacy.medicines.index', ['stock' => 'low']));

        $responseLow->assertStatus(200);
        $responseLow->assertSee('Solmoux 500mg Capsule');

        // Filter Out of Stock
        $responseOut = $this->actingAs($this->pharmacist)
                            ->get(route('pharmacy.medicines.index', ['stock' => 'out']));

        $responseOut->assertStatus(200);
        $responseOut->assertSee('Mefenamic Acid 500mg Tablet');
    }

    public function test_expiration_status_filter_filters_expiring_soon_and_expired(): void
    {
        $responseExpiring = $this->actingAs($this->pharmacist)
                                 ->get(route('pharmacy.medicines.index', ['expiry' => 'expiring_soon']));

        $responseExpiring->assertStatus(200);
        $responseExpiring->assertSee('Cefuroxime 500mg Tablet');

        $responseExpired = $this->actingAs($this->pharmacist)
                                ->get(route('pharmacy.medicines.index', ['expiry' => 'expired']));

        $responseExpired->assertStatus(200);
        $responseExpired->assertSee('Metformin 500mg Tablet');
    }

    public function test_pharmacy_dashboard_displays_pharmacy_stock_directory_section(): void
    {
        $response = $this->actingAs($this->pharmacist)
                         ->get(route('pharmacy.dashboard'));

        $response->assertStatus(200);

        // Top Operational Summary Cards
        $response->assertSee('Total Prescriptions');
        $response->assertSee('Pending Verification');
        $response->assertSee('Dispensed Today');
        $response->assertSee('Pending Dispensing');
        $response->assertDontSee('Low Stock Alert');

        // Pharmacy Stock Directory Section & Cards
        $response->assertSee('Pharmacy Stock Directory');
        $response->assertSee('Read-only medication stock status, lot/batch information, and expiration monitoring.');
        $response->assertSee('Total Monitored');
        $response->assertSee('In Stock');
        $response->assertSee('Low Stock');
        $response->assertSee('Attention Required');
        $response->assertSee('All Medicines');
        $response->assertSee('Available Stock');
        $response->assertSee('Replenishment Needed');
        $response->assertSee('Out / Expired / Expiring');
    }
}


