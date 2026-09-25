<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (App\Models\User::all() as $u) {
    if (in_array($u->primary_role, ['doctor', 'admin'])) {
        file_put_contents('tmp/users_out.txt', "ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Role: {$u->primary_role}\n", FILE_APPEND);
    }
}
