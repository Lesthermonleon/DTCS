<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

echo "=== DUPLICATE LOGIN RESPONSE SIMULATION ===\n\n";

$user = User::where('email', 'doctor@example.com')->first() ?: User::first();

// 1. Set Browser A active session
$browserASessionId = "sess_browser_A_999";
$user->setActiveSession($browserASessionId);
echo "[1] Set Browser A Session: active_session_id = {$user->active_session_id}\n";

// 2. Simulate Browser B POST /login request
$request = LoginRequest::create('/login', 'POST', [
    'email' => $user->email,
    'password' => 'password',
]);

// Give Browser B a different session ID
$sessionB = $app['session']->driver();
$sessionB->setId("sess_browser_B_888");
$request->setLaravelSession($sessionB);

echo "[2] Simulating Browser B POST /login request with session ID: sess_browser_B_888\n";

$controller = new AuthenticatedSessionController();
$response = $controller->store($request);

echo "[3] Response Class: " . get_class($response) . "\n";
echo "[4] HTTP Status: " . $response->getStatusCode() . "\n";

$content = $response->getContent();
$hasTitle = str_contains($content, 'Account Already Logged In');
$hasCountdown = str_contains($content, 'duplicateLoginCountdown');
$hasProgressBar = str_contains($content, 'duplicateLoginProgress');
$hasCountdownNumber = str_contains($content, '5');

echo "\n--- BLADE VIEW CONTENT CHECKS ---\n";
echo "Title 'Account Already Logged In': " . ($hasTitle ? "✅ FOUND" : "❌ MISSING") . "\n";
echo "Element 'duplicateLoginCountdown': " . ($hasCountdown ? "✅ FOUND" : "❌ MISSING") . "\n";
echo "Element 'duplicateLoginProgress': " . ($hasProgressBar ? "✅ FOUND" : "❌ MISSING") . "\n";
echo "Countdown Number '5': " . ($hasCountdownNumber ? "✅ FOUND" : "❌ MISSING") . "\n";

// Clean up
$user->clearActiveSession();
echo "\n=== SIMULATION COMPLETED SUCCESSFULLY ===\n";
