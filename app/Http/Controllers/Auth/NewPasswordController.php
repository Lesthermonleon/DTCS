<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view if token is valid and unexpired (<= 5 minutes).
     */
    public function create(Request $request): View|RedirectResponse
    {
        $token = $request->route('token');
        $email = $request->query('email');

        $user = $email ? User::where('email', $email)->first() : null;

        if (!$user || !$token || !Password::getRepository()->exists($user, $token)) {
            return redirect()->route('password.request')
                ->with('status', 'This password reset link has expired or is invalid. Please request a new password reset link.');
        }

        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Attempt to reset the user's password using Laravel's password broker.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password'       => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Clear active session registration so any open sessions across browsers/devices are invalidated
                $user->clearActiveSession();

                // Log audit event
                ActivityLog::create([
                    'user_id'     => $user->id,
                    'action'      => 'Password Reset Completed',
                    'module'      => 'Authentication',
                    'severity'    => ActivityLog::SEVERITY_INFO,
                    'result'      => ActivityLog::RESULT_SUCCESS,
                    'description' => "Password reset successfully completed for account [{$user->email}].",
                    'ip_address'  => $request->ip(),
                    'logged_at'   => now(),
                ]);

                event(new PasswordReset($user));
            }
        );

        // If successful, redirect to login page with status message (no auto-auth, no OTP bypass)
        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('status', 'Your password has been reset successfully. Please sign in with your new password.');
        }

        return redirect()->route('password.request')
            ->with('status', 'This password reset link has expired or is invalid. Please request a new password reset link.');
    }
}
