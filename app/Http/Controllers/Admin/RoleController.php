<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * RoleController — Admin manages RBAC roles.
 */
class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::with(['permissions'])->withCount('users', 'permissions')->get();

        $selectedSlug = $request->query('role', $roles->first()?->slug ?? 'admin');
        $selectedRole = $roles->firstWhere('slug', $selectedSlug) ?? $roles->first();

        if ($request->ajax() || $request->wantsJson() || $request->query('partial')) {
            return view('admin.roles._role_details', compact('selectedRole'));
        }

        return view('admin.roles.index', compact('roles', 'selectedRole'));
    }

    public function create(): View
    {
        abort(403, 'Permission management is read-only.');
    }

    public function store(Request $request): RedirectResponse
    {
        abort(403, 'Permission management is read-only.');
    }

    public function show(Role $role): View
    {
        $role->load('users', 'permissions');

        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role): View
    {
        abort(403, 'Permission management is read-only.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort(403, 'Permission management is read-only.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort(403, 'Permission management is read-only.');
    }
}
