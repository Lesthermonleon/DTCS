<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PATIENT CLINICAL FINDINGS IN DATABASE ===\n";
App\Models\Patient::all()->each(function($p) {
    echo "ID: {$p->id} | Patient No: {$p->patient_no} | Name: {$p->full_name}\n";
    echo "  Clinical Findings: " . ($p->clinical_findings ? '"' . $p->clinical_findings . '"' : '[NULL / EMPTY]') . "\n\n";
});
