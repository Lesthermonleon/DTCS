<?php

namespace Database\Seeders;

use App\Models\LabTestCategory;
use Illuminate\Database\Seeder;

/**
 * Seeds common lab test categories used in hospitals.
 */
class LabTestCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Hematology',            'code' => 'HEM',  'description' => 'Blood cell counts and related tests'],
            ['name' => 'Clinical Chemistry',    'code' => 'CHEM', 'description' => 'Biochemical analysis of blood and serum'],
            ['name' => 'Microbiology',          'code' => 'MICRO','description' => 'Culture and sensitivity tests'],
            ['name' => 'Urinalysis',            'code' => 'URI',  'description' => 'Urine analysis and microscopy'],
            ['name' => 'Serology / Immunology', 'code' => 'SERO', 'description' => 'Antibody and antigen tests'],
            ['name' => 'Coagulation',           'code' => 'COAG', 'description' => 'Clotting and bleeding time tests'],
            ['name' => 'Hormones / Endocrine',  'code' => 'ENDO', 'description' => 'Thyroid, hormonal panels'],
            ['name' => 'Blood Banking',         'code' => 'BB',   'description' => 'Blood type, crossmatch and transfusion tests'],
        ];

        foreach ($categories as $cat) {
            LabTestCategory::firstOrCreate(['code' => $cat['code']], $cat);
        }
    }
}
