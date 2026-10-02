<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * UserController — Admin manages hospital system users.
 */
class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $role = $request->input('role');
        $status = $request->input('status');

        // 1. Query regular users (excludes soft-deleted by default)
        $query = User::with('roles');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $query->whereHas('roles', fn($q) => $q->where('slug', $role));
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        // 2. Query archived users (soft-deleted only)
        $archiveQuery = User::onlyTrashed()->with('roles');

        if ($search) {
            $archiveQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $archiveQuery->whereHas('roles', fn($q) => $q->where('slug', $role));
        }

        $archivedUsers = $archiveQuery->latest()->get();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'archivedUsers', 'roles'));
    }

    public function show(User $user): RedirectResponse
    {
        return redirect()->route('admin.users.edit', $user);
    }

    public function edit(User $user): View
    {
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => "required|email|unique:users,email,{$user->id}",
            'employee_id' => "nullable|string|unique:users,employee_id,{$user->id}",
            'department'  => 'nullable|string|max:100',
            'phone'       => 'nullable|string|max:20',
            'is_active'   => 'boolean',
            'role_id'     => 'required|exists:roles,id',
        ]);

        if ($user->id === Auth::id()) {
            $currentRoleId = $user->roles()->first()?->id;
            if ((int) $data['role_id'] !== (int) $currentRoleId) {
                return back()->withErrors(['role_id' => 'You cannot change your own role.']);
            }
            if ($request->has('is_active') && !$request->boolean('is_active')) {
                return back()->withErrors(['is_active' => 'You cannot deactivate your own account.']);
            }
        }

        $user->update([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'employee_id' => $data['employee_id'] ?? null,
            'department'  => $data['department'] ?? null,
            'phone'       => $data['phone'] ?? null,
            'is_active'   => $request->boolean('is_active'),
        ]);

        // Sync role
        $user->roles()->sync([$data['role_id']]);

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'User Updated',
            'module'      => 'User Management',
            'severity'    => ActivityLog::SEVERITY_INFO,
            'result'      => ActivityLog::RESULT_SUCCESS,
            'description' => "User account [{$user->email}] ({$user->name}) was updated by admin.",
            'ip_address'  => request()->ip(),
            'logged_at'   => now(),
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($user->id === Auth::id(), 403, 'You cannot delete your own account.');

        $request->validate([
            'comment' => ['required', 'string', 'filled', 'max:1000'],
        ], [
            'comment.required' => 'A reason / comment is required before archiving a user account.',
            'comment.filled'   => 'The reason / comment cannot be blank or whitespace only.',
            'comment.max'      => 'The reason / comment must not exceed 1,000 characters.',
        ]);

        $comment = trim($request->input('comment'));
        $email   = $user->email;
        $name    = $user->name;
        $user->delete();

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'User Archived',
            'module'      => 'User Management',
            'severity'    => ActivityLog::SEVERITY_WARNING,
            'result'      => ActivityLog::RESULT_SUCCESS,
            'description' => "User account [{$email}] ({$name}) was archived by admin. Reason: {$comment}",
            'ip_address'  => $request->ip(),
            'logged_at'   => now(),
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User account archived successfully.');
    }

    public function restore(Request $request, int|string $id): RedirectResponse
    {
        $request->validate([
            'comment' => ['required', 'string', 'filled', 'max:1000'],
        ], [
            'comment.required' => 'A reason / comment is required before restoring a user account.',
            'comment.filled'   => 'The reason / comment cannot be blank or whitespace only.',
            'comment.max'      => 'The reason / comment must not exceed 1,000 characters.',
        ]);

        $comment = trim($request->input('comment'));
        $user    = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'User Restored',
            'module'      => 'User Management',
            'severity'    => ActivityLog::SEVERITY_WARNING,
            'result'      => ActivityLog::RESULT_SUCCESS,
            'description' => "User account [{$user->email}] ({$user->name}) was restored by admin. Reason: {$comment}",
            'ip_address'  => $request->ip(),
            'logged_at'   => now(),
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User account restored successfully.');
    }

    public function showArchived(int|string $id): View
    {
        $user = User::onlyTrashed()->with('roles')->findOrFail($id);

        // Latest 'User Archived' event for this user — drives the Deletion Information card
        $deletionLog = ActivityLog::where('action', 'User Archived')
            ->where('description', 'LIKE', "%[{$user->email}]%")
            ->with('user')
            ->latest('logged_at')
            ->first();

        // Full archive + restore history in reverse chronological order — drives Account History
        $history = ActivityLog::whereIn('action', ['User Archived', 'User Restored'])
            ->where('description', 'LIKE', "%[{$user->email}]%")
            ->with('user')
            ->latest('logged_at')
            ->get();

        return view('admin.users.archived', compact('user', 'deletionLog', 'history'));
    }

    public function assignRole(Request $request, User $user): RedirectResponse
    {
        abort_if($user->id === Auth::id(), 403, 'You cannot change your own role.');
        $request->validate(['role_id' => 'required|exists:roles,id']);
        $user->roles()->sync([$request->role_id]);

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Role Assignment Changed',
            'module'      => 'User Management',
            'severity'    => ActivityLog::SEVERITY_WARNING,
            'result'      => ActivityLog::RESULT_SUCCESS,
            'description' => "Role assignment changed for user [{$user->email}] ({$user->name}) by admin.",
            'ip_address'  => request()->ip(),
            'logged_at'   => now(),
        ]);

        return back()->with('success', 'Role assigned successfully.');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $user->update([
            'password' => Hash::make('password'),
        ]);

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Password Reset',
            'module'      => 'User Management',
            'severity'    => ActivityLog::SEVERITY_WARNING,
            'result'      => ActivityLog::RESULT_SUCCESS,
            'description' => "Password for user [{$user->email}] ({$user->name}) was reset by admin.",
            'ip_address'  => request()->ip(),
            'logged_at'   => now(),
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User password reset successfully to: password');
    }

    public function unlockAccount(User $user): RedirectResponse
    {
        $user->update([
            'failed_attempts' => 0,
            'locked_at'       => null,
            'lockout_until'   => null,
            'is_active'       => true,
        ]);

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Account Unlocked',
            'module'      => 'User Management',
            'severity'    => ActivityLog::SEVERITY_INFO,
            'result'      => ActivityLog::RESULT_SUCCESS,
            'description' => "Locked account for user [{$user->email}] ({$user->name}) was unlocked by admin.",
            'ip_address'  => request()->ip(),
            'logged_at'   => now(),
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', "User account for {$user->name} has been unlocked successfully.");
    }
}
