<?php

namespace Database\Seeders;

use App\Models\LabTest;
use App\Models\LabTestCategory;
use Illuminate\Database\Seeder;

/**
 * Seeds common laboratory tests under each category.
 */
class LabTestSeeder extends Seeder
{
    public function run(): void
    {
        $tests = [
            'HEM' => [
                ['name' => 'Complete Blood Count (CBC)',        'code' => 'CBC',   'normal_range' => '4.5–11.0',  'unit' => 'x10³/µL',   'price' => 250],
                ['name' => 'Hemoglobin',                       'code' => 'HGB',   'normal_range' => '12.0–17.5', 'unit' => 'g/dL',       'price' => 150],
                ['name' => 'Hematocrit',                       'code' => 'HCT',   'normal_range' => '36–50',     'unit' => '%',          'price' => 150],
                ['name' => 'Platelet Count',                   'code' => 'PLT',   'normal_range' => '150–400',   'unit' => 'x10³/µL',   'price' => 200],
                ['name' => 'Erythrocyte Sedimentation Rate',   'code' => 'ESR',   'normal_range' => '0–20',      'unit' => 'mm/hr',      'price' => 180],
            ],
            'CHEM' => [
                ['name' => 'Fasting Blood Sugar (FBS)',         'code' => 'FBS',   'normal_range' => '70–99',    'unit' => 'mg/dL',      'price' => 150],
                ['name' => 'Blood Urea Nitrogen (BUN)',         'code' => 'BUN',   'normal_range' => '7–20',     'unit' => 'mg/dL',      'price' => 200],
                ['name' => 'Serum Creatinine',                  'code' => 'CREA',  'normal_range' => '0.7–1.2',  'unit' => 'mg/dL',      'price' => 200],
                ['name' => 'SGPT (ALT)',                        'code' => 'SGPT',  'normal_range' => '7–56',     'unit' => 'U/L',        'price' => 250],
                ['name' => 'SGOT (AST)',                        'code' => 'SGOT',  'normal_range' => '10–40',    'unit' => 'U/L',        'price' => 250],
                ['name' => 'Total Cholesterol',                 'code' => 'CHOL',  'normal_range' => '<200',     'unit' => 'mg/dL',      'price' => 280],
                ['name' => 'Triglycerides',                     'code' => 'TRIG',  'normal_range' => '<150',     'unit' => 'mg/dL',      'price' => 280],
                ['name' => 'Uric Acid',                         'code' => 'UA',    'normal_range' => '3.5–7.2',  'unit' => 'mg/dL',      'price' => 200],
            ],
            'URI' => [
                ['name' => 'Routine Urinalysis',                'code' => 'RUA',   'normal_range' => 'Refer to form', 'unit' => '',       'price' => 120],
                ['name' => 'Urine Culture & Sensitivity',       'code' => 'UCS',   'normal_range' => 'No growth', 'unit' => '',           'price' => 450],
            ],
            'SERO' => [
                ['name' => 'Hepatitis B Surface Antigen (HBsAg)','code' => 'HBSAG','normal_range' => 'Non-reactive','unit' => '',          'price' => 350],
                ['name' => 'Anti-HIV',                           'code' => 'HIV',   'normal_range' => 'Non-reactive','unit' => '',          'price' => 450],
                ['name' => 'Dengue NS1 Antigen',                 'code' => 'NS1',   'normal_range' => 'Negative',   'unit' => '',          'price' => 600],
            ],
            'COAG' => [
                ['name' => 'Prothrombin Time (PT)',              'code' => 'PT',    'normal_range' => '11–13',    'unit' => 'seconds',    'price' => 300],
                ['name' => 'Activated Partial Thromboplastin Time','code' => 'APTT','normal_range' => '25–35',   'unit' => 'seconds',    'price' => 300],
            ],
            'ENDO' => [
                ['name' => 'Thyroid Stimulating Hormone (TSH)', 'code' => 'TSH',   'normal_range' => '0.4–4.0',  'unit' => 'mIU/L',     'price' => 550],
                ['name' => 'Free T4 (FT4)',                     'code' => 'FT4',   'normal_range' => '0.8–1.8',  'unit' => 'ng/dL',     'price' => 550],
            ],
        ];

        foreach ($tests as $categoryCode => $testList) {
            $category = LabTestCategory::where('code', $categoryCode)->first();
            if (! $category) {
                continue;
            }

            foreach ($testList as $test) {
                LabTest::firstOrCreate(
                    ['code' => $test['code']],
                    array_merge($test, [
                        'lab_test_category_id' => $category->id,
                        'method'               => 'Automated',
                    ])
                );
            }
        }
    }
}
