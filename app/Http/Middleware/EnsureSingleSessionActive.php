<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleSessionActive
{
    /**
     * Handle an incoming request.
     *
     * Verifies that the authenticated user's current session matches the single registered
     * active_session_id for their account. Updates last_activity_at timestamp on valid requests.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user             = Auth::user();
            $currentSessionId = $request->session()->getId();

            // If the user's registered active_session_id does not match this session ID
            if ($user->active_session_id !== null && $user->active_session_id !== $currentSessionId) {
                Auth::guard('web')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Your session has ended because your account logged in elsewhere or session expired.');
            }

            // Update activity timestamp for active session
            $user->touchLastActivity($currentSessionId);
        }

        return $next($request);
    }
}
