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
    public function index(): View
    {
        $roles = Role::withCount('users', 'permissions')->get();

        return view('admin.roles.index', compact('roles'));
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
