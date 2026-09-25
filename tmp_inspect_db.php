<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PATIENT CLINICAL FINDINGS IN DATABASE ===\n";
$patients = App\Models\Patient::all();
echo "Total Patients: " . $patients->count() . "\n";
foreach ($patients as $p) {
    echo "ID: {$p->id} | Patient No: {$p->patient_no} | Name: {$p->full_name}\n";
    echo "  clinical_findings in DB: " . ($p->clinical_findings ? '"' . $p->clinical_findings . '"' : '[NULL / EMPTY]') . "\n";
}
