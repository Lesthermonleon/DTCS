<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$patients = App\Models\Patient::all();
foreach ($patients as $p) {
    file_put_contents('tmp_output.txt', "PATIENT {$p->id} | {$p->patient_no} | {$p->full_name}\nCF: " . var_export($p->clinical_findings, true) . "\n-------------------\n", FILE_APPEND);
}
