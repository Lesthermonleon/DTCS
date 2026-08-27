<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ActivityLog;

$t = ActivityLog::count();
$a = ActivityLog::where('module', ActivityLog::MODULE_AUTH)->count();
$c = ActivityLog::whereIn('module', ActivityLog::CLINICAL_MODULES)->count();
$s = ActivityLog::whereIn('module', ActivityLog::ADMIN_MODULES)->count();
$o = $t - $a - $c - $s;

echo "=== Audit Log Category Reconciliation ===" . PHP_EOL;
echo "Total Events     : {$t}" . PHP_EOL;
echo "Authentication   : {$a}" . PHP_EOL;
echo "Clinical         : {$c}" . PHP_EOL;
echo "System/Admin     : {$s}" . PHP_EOL;
echo "Uncategorized    : {$o}" . PHP_EOL;
echo "Sum (A+C+S+O)    : " . ($a + $c + $s + $o) . PHP_EOL;
echo PHP_EOL;

$sev = ActivityLog::groupBy('severity')->selectRaw('severity, count(*) as cnt')->pluck('cnt', 'severity')->toArray();
echo "=== Severity Breakdown ===" . PHP_EOL;
foreach ($sev as $k => $v) {
    echo "  {$k}: {$v}" . PHP_EOL;
}

$cols = \Illuminate\Support\Facades\Schema::getColumnListing('activity_logs');
echo PHP_EOL . "=== activity_logs columns ===" . PHP_EOL;
echo implode(', ', $cols) . PHP_EOL;
