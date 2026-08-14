<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * Enforces Single Active Session Policy:
     * A user account may have ONLY ONE active authenticated session at a time.
     * If an active session exists on another browser/device, the second login is rejected
     * and a 5-second security countdown page is displayed without kicking out the original session.
     */
    public function store(LoginRequest $request): RedirectResponse|View|Response
    {
        // 1. Run basic request validation (email + password format)
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = $request->input('email');
        $user  = User::where('email', $email)->first();

        // 2. Check if user is locked out or in cooldown using existing LoginRequest logic
        if ($user) {
            if ($user->isLockedOut() || $user->isCoolingDown()) {
                $request->authenticate(); // Throws validation exception with lockout/cooldown rules
            }
        }

        // 3. Verify credentials with Hash check before logging in
        if (! $user || ! Hash::check($request->input('password'), $user->password) || ! $user->is_active) {
            // Trigger standard Breeze lockout handling for invalid credentials
            $request->authenticate();
        }

        // 4. User credentials are VALID. Perform atomic check for existing active session.
        $currentSessionId   = $request->session()->getId();
        $isDuplicateSession = false;

        DB::transaction(function () use ($user, $currentSessionId, &$isDuplicateSession) {
            // Lock user record for update to eliminate simultaneous login race conditions
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            if ($lockedUser && $lockedUser->hasActiveSession($currentSessionId)) {
                $isDuplicateSession = true;
            }
        });

        // 5. IF a valid active session ALREADY exists on another browser/device:
        if ($isDuplicateSession) {
            // Log security audit trail event
            ActivityLog::create([
                'user_id'      => $user->id,
                'action'       => 'Duplicate Login Attempt Rejected',
                'module'       => 'Authentication',
                'description'  => "Duplicate login attempt for account {$user->email} was rejected due to an active session on another device/browser.",
                'ip_address'   => $request->ip(),
                'logged_at'    => now(),
            ]);

            // Return security view with 5-second countdown. User remains 100% unauthenticated.
            return response()->view('auth.account-already-logged-in', [
                'loginUrl' => route('login'),
            ]);
        }

        // 6. NO active session exists — proceed with normal authentication
        Auth::login($user, $request->boolean('remember'));

        // Handle Remember Account email persistence cookie (30 days)
        if ($request->boolean('remember')) {
            cookie()->queue('remember_hims_email', $user->email, 43200);
        } else {
            cookie()->queue(cookie()->forget('remember_hims_email'));
        }

        // Prevent session fixation by regenerating the session ID on login.
        $request->session()->regenerate();
        $newSessionId = $request->session()->getId();

        // Register active session & touch last activity timestamp
        $user->setActiveSession($newSessionId);

        // Generate encrypted login token
        $plainToken     = Str::random(64);
        $encryptedToken = encrypt($plainToken);

        $user->update([
            'login_token' => $encryptedToken,
        ]);

        // Reset any previous failed attempts
        $user->resetLoginAttempts();

        // Resolve the target route from the user's primary role record
        $role           = $user->roles()->first();
        $dashboardRoute = $role?->dashboard_route;

        if ($dashboardRoute && Route::has($dashboardRoute)) {
            return redirect()->route($dashboardRoute);
        }

        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     *
     * Invalidating the session removes active session registration on the user record.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user) {
            $user->clearActiveSession();
            $user->update(['login_token' => null]);
        }

        Auth::guard('web')->logout();

        // Destroy the current browser's session data
        $request->session()->invalidate();

        // Rotate the CSRF token so old tokens cannot be reused
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
