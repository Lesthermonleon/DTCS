<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$doctor = App\Models\User::where('email', 'doctor@ditc.com')->first();
if ($doctor) {
    $doctor->password = bcrypt('password');
    $doctor->save();
    echo "Doctor password set to 'password'\n";
}

$admin = App\Models\User::where('email', 'admin@ditc.com')->first();
if ($admin) {
    $admin->password = bcrypt('password');
    $admin->save();
    echo "Admin password set to 'password'\n";
}
