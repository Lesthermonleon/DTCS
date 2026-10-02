<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SessionLockController extends Controller
{
    /**
     * Lock the current authenticated session due to inactivity.
     */
    public function lock(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user) {
            $alreadyLocked = $request->session()->get('hims_session_locked', false);

            $request->session()->put('hims_session_locked', true);
            if (!$alreadyLocked) {
                $request->session()->put('hims_unlock_attempts', 0);

                ActivityLog::create([
                    'user_id'     => $user->id,
                    'action'      => 'Session Locked',
                    'module'      => ActivityLog::MODULE_AUTH,
                    'severity'    => ActivityLog::SEVERITY_WARNING,
                    'result'      => ActivityLog::RESULT_SUCCESS,
                    'description' => "User session locked due to inactivity for account [{$user->email}].",
                    'ip_address'  => $request->ip(),
                    'logged_at'   => now(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Session locked successfully.',
        ]);
    }

    /**
     * Get the current session lock status.
     */
    public function status(Request $request): JsonResponse
    {
        return response()->json([
            'locked' => (bool) $request->session()->get('hims_session_locked', false),
        ]);
    }

    /**
     * Display full-page locked view when navigating while locked.
     */
    public function showLocked(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('hims_session_locked', false)) {
            return redirect()->route('dashboard');
        }

        return view('auth.session-locked');
    }

    /**
     * Attempt to unlock the current session using the account password.
     */
    public function unlock(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success'  => false,
                'message'  => 'Unauthenticated.',
                'redirect' => route('login'),
            ], 401);
        }

        // Verify password against user hash
        if (Hash::check($request->input('password'), $user->password)) {
            // Unlock successful
            $request->session()->forget(['hims_session_locked', 'hims_unlock_attempts']);

            ActivityLog::create([
                'user_id'     => $user->id,
                'action'      => 'Session Unlocked',
                'module'      => ActivityLog::MODULE_AUTH,
                'severity'    => ActivityLog::SEVERITY_INFO,
                'result'      => ActivityLog::RESULT_SUCCESS,
                'description' => "Session unlocked with correct password for account [{$user->email}].",
                'ip_address'  => $request->ip(),
                'logged_at'   => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Session unlocked successfully.',
            ]);
        }

        // Password wrong — increment unlock attempt counter
        $attempts = (int) $request->session()->get('hims_unlock_attempts', 0) + 1;
        $request->session()->put('hims_unlock_attempts', $attempts);

        ActivityLog::create([
            'user_id'     => $user->id,
            'action'      => 'Session Unlock Failed',
            'module'      => ActivityLog::MODULE_AUTH,
            'severity'    => ActivityLog::SEVERITY_WARNING,
            'result'      => ActivityLog::RESULT_FAILED,
            'description' => "Failed session unlock attempt ({$attempts}/3) for account [{$user->email}].",
            'ip_address'  => $request->ip(),
            'logged_at'   => now(),
        ]);

        // Max 3 attempts reached → Force full logout
        if ($attempts >= 3) {
            ActivityLog::create([
                'user_id'     => $user->id,
                'action'      => 'Session Logout After Failed Unlock',
                'module'      => ActivityLog::MODULE_AUTH,
                'severity'    => ActivityLog::SEVERITY_CRITICAL,
                'result'      => ActivityLog::RESULT_BLOCKED,
                'description' => "Maximum unlock attempts (3) exceeded for account [{$user->email}]. Session terminated.",
                'ip_address'  => $request->ip(),
                'logged_at'   => now(),
            ]);

            $currentSessionId = $request->session()->getId();
            $user->clearActiveSession($currentSessionId);

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success'               => false,
                'max_attempts_exceeded' => true,
                'message'               => 'Maximum unlock attempts exceeded. You have been signed out.',
                'redirect'              => route('login'),
            ], 422);
        }

        $remaining = 3 - $attempts;

        return response()->json([
            'success'            => false,
            'attempts_remaining' => $remaining,
            'message'            => "Incorrect password. {$remaining} attempt(s) remaining.",
        ], 422);
    }
}
