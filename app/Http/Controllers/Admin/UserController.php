<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
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

    public function create(): View
    {
        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => ['required', Password::defaults()],
            'employee_id' => 'nullable|string|unique:users,employee_id',
            'department'  => 'nullable|string|max:100',
            'phone'       => 'nullable|string|max:20',
            'role_id'     => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'employee_id' => $data['employee_id'] ?? null,
            'department'  => $data['department'] ?? null,
            'phone'       => $data['phone'] ?? null,
            'is_active'   => true,
        ]);

        $user->roles()->attach($data['role_id']);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User created successfully.');
    }

    public function show(User $user): View
    {
        $user->load('roles');

        return view('admin.users.show', compact('user'));
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

        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === Auth::id(), 403, 'You cannot delete your own account.');
        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User account archived successfully.');
    }

    public function restore(int|string $id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User account restored successfully.');
    }

    public function assignRole(Request $request, User $user): RedirectResponse
    {
        abort_if($user->id === Auth::id(), 403, 'You cannot change your own role.');
        $request->validate(['role_id' => 'required|exists:roles,id']);
        $user->roles()->sync([$request->role_id]);

        return back()->with('success', 'Role assigned successfully.');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $user->update([
            'password' => Hash::make('password'),
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

        return redirect()->route('admin.users.index')
                         ->with('success', "User account for {$user->name} has been unlocked successfully.");
    }
}
