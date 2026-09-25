<?php
// Quick DB verification script for MediSense Stage 1 audit
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$patient = App\Models\Patient::where('patient_no', 'like', '%0001%')->orderBy('patient_no')->first();
if (!$patient) {
    echo "No patient with '0001' in patient_no found. Listing first 3:\n";
    App\Models\Patient::take(3)->get()->each(fn($p) => print("{$p->patient_no} | {$p->full_name}\n"));
    exit;
}

echo "=== Patient Found ===\n";
echo "Name      : {$patient->full_name}\n";
echo "Patient No: {$patient->patient_no}\n";
echo "Type      : {$patient->patient_type}\n\n";

echo "=== Clinical Records ===\n";
echo "Lab Requests    : " . $patient->labRequests()->count() . "\n";
echo "Radiology Reqs  : " . $patient->radiologyRequests()->count() . "\n";
echo "Prescriptions   : " . $patient->prescriptions()->count() . "\n";
echo "Surgery Requests: " . $patient->surgeryRequests()->count() . "\n";
echo "Diet Requests   : " . $patient->dietRequests()->count() . "\n";

// Check released/approved specifically (what ClinicalContextBuilder filters for)
$labCount = 0;
foreach ($patient->labRequests()->with('items.results')->get() as $req) {
    foreach ($req->items as $item) {
        if ($item->results) {
            foreach ($item->results as $res) {
                if (in_array($res->status, ['Released','Validated'])) $labCount++;
            }
        }
    }
}

$radCount = 0;
foreach ($patient->radiologyRequests()->with('report')->get() as $req) {
    if ($req->report && in_array($req->report->status, ['Approved','Released'])) $radCount++;
}

$rxCount = 0;
foreach ($patient->prescriptions()->with('items')->get() as $rx) {
    if (in_array($rx->status, ['Verified','Pending','Partially Dispensed','Dispensed'])) $rxCount++;
}

echo "\n=== ClinicalContextBuilder Filtered Counts ===\n";
echo "Released Lab Results  : {$labCount}\n";
echo "Approved Rad Reports  : {$radCount}\n";
echo "Active Prescriptions  : {$rxCount}\n";
echo "Surgery Requests      : " . $patient->surgeryRequests()->count() . "\n";
echo "Diet Requests         : " . $patient->dietRequests()->count() . "\n";
