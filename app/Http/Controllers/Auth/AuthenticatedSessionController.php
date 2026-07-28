<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * Post-login redirect is driven by the `dashboard_route` field on the
     * user's primary role record — no code change is needed when new roles
     * are added. Simply set the `dashboard_route` value on the role row.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Prevent session fixation by regenerating the session ID on login.
        $request->session()->regenerate();

        $user = $request->user();

        // ── Generate encrypted login token ───────────────────────────────
        $plainToken    = Str::random(64);
        $encryptedToken = encrypt($plainToken);

        $user->update([
            'login_token' => $encryptedToken,
        ]);

        // ── Reset any previous failed attempts ──────────────────────────
        $user->resetLoginAttempts();

        // Resolve the target route from the role record itself.
        // Future roles just need a valid `dashboard_route` value in the DB.
        $role           = $user->roles()->first();
        $dashboardRoute = $role?->dashboard_route;

        if ($dashboardRoute && Route::has($dashboardRoute)) {
            return redirect()->route($dashboardRoute);
        }

        // Ultimate fallback: generic dashboard (redirects by role in DashboardController)
        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     *
     * Invalidating the session removes only THIS browser's session.
     * Other authenticated sessions on other browsers/devices are unaffected.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // ── Clear login token before logging out ─────────────────────────
        $user = $request->user();
        if ($user) {
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
