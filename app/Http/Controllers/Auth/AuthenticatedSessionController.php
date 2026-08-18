<?php

namespace App\Http\Controllers\Auth;

use App\Events\SessionReplaced;
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
     * Single Active Session with Session Replacement Policy:
     * When a user logs in from another browser or device, the new login proceeds normally,
     * becomes the active session, and broadcasts a SessionReplaced event to immediately
     * terminate the previous active session.
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

        // 4. User credentials are VALID. Authenticate user.
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

        $hadPreviousSession = false;

        DB::transaction(function () use ($user, $newSessionId, &$hadPreviousSession) {
            // Lock user record for update to eliminate simultaneous login race conditions
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            if ($lockedUser) {
                if (! empty($lockedUser->active_session_id) && $lockedUser->active_session_id !== $newSessionId) {
                    $hadPreviousSession = true;
                }
                $lockedUser->setActiveSession($newSessionId);
            }
        });

        // Broadcast real-time SessionReplaced event so the old device/browser logs out immediately via WebSockets
        if ($hadPreviousSession) {
            try {
                broadcast(new SessionReplaced($user->id))->toOthers();
            } catch (\Throwable $e) {
                // Log broadcasting error gracefully without crashing the login flow if Reverb server is offline
                logger()->warning('Failed to broadcast SessionReplaced event (Reverb server might be offline): ' . $e->getMessage());
            }

            ActivityLog::create([
                'user_id'      => $user->id,
                'action'       => 'Session Replaced',
                'module'       => 'Authentication',
                'description'  => "Account {$user->email} logged in on a new device/browser. Previous active session was replaced.",
                'ip_address'   => $request->ip(),
                'logged_at'    => now(),
            ]);
        }

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
        $user             = $request->user();
        $currentSessionId = $request->session()->getId();

        if ($user) {
            $user->clearActiveSession($currentSessionId);
            if ($user->active_session_id === null) {
                $user->update(['login_token' => null]);
            }
        }

        Auth::guard('web')->logout();

        // Destroy the current browser's session data
        $request->session()->invalidate();

        // Rotate the CSRF token so old tokens cannot be reused
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
