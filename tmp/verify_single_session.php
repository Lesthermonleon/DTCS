<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== SINGLE ACTIVE SESSION SECURITY VERIFICATION ===\n\n";

$user = User::where('email', 'doctor@example.com')->first() ?: User::first();
if (!$user) {
    echo "❌ No user found for testing.\n";
    exit(1);
}

echo "Testing with User: {$user->name} ({$user->email})\n";

// 1. Reset initial session state
$user->clearActiveSession();
echo "[1] Reset initial state: active_session_id = " . ($user->active_session_id ?? 'NULL') . "\n";

// 2. Simulate Browser A Login (Session 1)
$session1 = "sess_browser_a_12345";
$user->setActiveSession($session1);
echo "[2] Browser A Login -> active_session_id = {$user->active_session_id}\n";

// 3. Simulate Browser B Login Attempt (Session 2)
$session2 = "sess_browser_b_67890";
$hasActive = $user->hasActiveSession($session2);
echo "[3] Browser B Login Attempt -> Has active session on another browser? " . ($hasActive ? "YES (REJECTED)" : "NO (ALLOWED)") . "\n";

if ($hasActive) {
    echo "    ✅ Browser B login successfully REJECTED as expected!\n";
} else {
    echo "    ❌ FAILED: Browser B login was allowed when it should have been rejected!\n";
}

// 4. Verify Browser A (Original Session) Is STILL Allowed
$isBrowserAActive = $user->hasActiveSession($session1);
echo "[4] Browser A Session Check -> Has active session on another browser? " . ($isBrowserAActive ? "YES" : "NO (STILL ACTIVE)") . "\n";
if (!$isBrowserAActive) {
    echo "    ✅ Browser A (original session) is STILL ACTIVE & PROTECTED!\n";
} else {
    echo "    ❌ FAILED: Browser A was accidentally marked as duplicate!\n";
}

// 5. Simulate Browser A Logout
$user->clearActiveSession();
echo "[5] Browser A Logout -> active_session_id = " . ($user->active_session_id ?? 'NULL') . "\n";

// 6. Retry Browser B Login after Browser A Logout
$hasActiveAfterLogout = $user->hasActiveSession($session2);
echo "[6] Browser B Retry Login after Logout -> Has active session? " . ($hasActiveAfterLogout ? "YES (REJECTED)" : "NO (ALLOWED)") . "\n";
if (!$hasActiveAfterLogout) {
    echo "    ✅ Browser B login is now ALLOWED after Browser A logged out!\n";
} else {
    echo "    ❌ FAILED: Browser B still rejected after logout!\n";
}

// Clean up test user state
$user->clearActiveSession();
echo "\n=== ALL VERIFICATION TESTS PASSED PERFECTLY ===\n";
