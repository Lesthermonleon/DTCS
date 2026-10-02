<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * SECURITY: Prevents account enumeration by returning the exact same neutral status message
     * regardless of whether the email address exists in the system database.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            Password::sendResetLink($request->only('email'));

            ActivityLog::create([
                'user_id'     => $user->id,
                'action'      => 'Password Reset Requested',
                'module'      => 'Authentication',
                'severity'    => ActivityLog::SEVERITY_INFO,
                'result'      => ActivityLog::RESULT_SUCCESS,
                'description' => "Password reset link requested for email [{$user->email}].",
                'ip_address'  => $request->ip(),
                'logged_at'   => now(),
            ]);
        }

        return back()->with('status', 'If an account exists for this email address, a password reset link has been sent.');
    }
}
