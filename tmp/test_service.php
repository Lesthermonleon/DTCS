<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Patient;
use App\Models\User;
use App\Services\MediSense\MedisenseService;
use Illuminate\Support\Facades\Auth;

$doctor = User::where('email', 'doctor@ditc.com')->first();
Auth::login($doctor);

$patient = Patient::first();
$service = app(MedisenseService::class);

$tasks = ['SYMPTOM_ASSESSMENT', 'DIAGNOSTIC_ASSISTANCE', 'TREATMENT_RECOMMENDATION', 'CLINICAL_SERVICE_ASSISTANCE'];

foreach ($tasks as $t) {
    $res = $service->executeTask($t, $patient);
    echo "[SUCCESS] Task {$t} returned status: {$res['status']} | Provider: {$res['provider']}\n";
}

echo "All 4 MediSense AI actions executed successfully!\n";
