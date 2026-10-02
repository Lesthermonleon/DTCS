<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionUnlocked
{
    /**
     * Handle an incoming request.
     *
     * Enforces server-side session lock when session('hims_session_locked') is true.
     * Prevents locked users from executing authenticated actions or API requests.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && $request->session()->get('hims_session_locked', false) === true) {
            // Exclude lock lifecycle endpoints and logout from being blocked
            if ($request->routeIs('session.lock', 'session.unlock', 'session.lock-status', 'session.locked', 'logout')) {
                return $next($request);
            }

            // For JSON or AJAX requests, return HTTP 423 (Locked)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'locked'   => true,
                    'message'  => 'Session is locked due to inactivity.',
                    'redirect' => route('session.locked'),
                ], 423);
            }

            // For web GET/POST requests, redirect to the locked view
            return redirect()->route('session.locked');
        }

        return $next($request);
    }
}
