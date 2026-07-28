<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for generating sample Patient records.
 *
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        return [
            'patient_no'               => 'P-2026-' . str_pad($counter, 4, '0', STR_PAD_LEFT),
            'first_name'               => $this->faker->firstName(),
            'last_name'                => $this->faker->lastName(),
            'middle_name'              => $this->faker->optional(0.7)->firstName(),
            'date_of_birth'            => $this->faker->dateTimeBetween('-80 years', '-5 years')->format('Y-m-d'),
            'gender'                   => $this->faker->randomElement(['Male', 'Female']),
            'blood_type'               => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'address'                  => $this->faker->address(),
            'phone'                    => '09' . $this->faker->numerify('#########'),
            'email'                    => $this->faker->optional(0.5)->safeEmail(),
            'emergency_contact_name'   => $this->faker->name(),
            'emergency_contact_phone'  => '09' . $this->faker->numerify('#########'),
            'patient_type'             => $this->faker->randomElement(['Inpatient', 'Outpatient']),
            'ward'                     => $this->faker->optional(0.4)->randomElement(['Ward A', 'Ward B', 'ICU', 'Pediatrics', 'OB-Gyne']),
            'bed_number'               => $this->faker->optional(0.4)->numerify('BED-###'),
        ];
    }
}
