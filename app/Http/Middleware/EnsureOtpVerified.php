<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureOtpVerified — Prevents authenticated users from accessing protected routes
 * while their OTP verification is still pending.
 *
 * This is the primary OTP bypass-prevention mechanism.
 *
 * After a successful password login, the controller stores 'otp_pending_user_id'
 * in the session WITHOUT calling Auth::login(). This middleware checks for that
 * marker on fully-authenticated sessions (which should not occur in normal flow,
 * but is a safety net against edge cases).
 *
 * The middleware only takes action when:
 *   - The user IS fully authenticated (Auth::check() === true)
 *   - AND session has 'otp_pending_user_id' still set
 *
 * In practice, Auth::login() is only called AFTER OTP verification, so this
 * middleware primarily catches any edge case where those two states coexist.
 */
class EnsureOtpVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only intercept fully-authenticated sessions
        if (Auth::check()) {
            // If an OTP pending marker still exists on this session,
            // the user has not completed verification — redirect them back.
            if ($request->session()->has('otp_pending_user_id')) {
                return redirect()->route('otp.verify');
            }
        }

        return $next($request);
    }
}
