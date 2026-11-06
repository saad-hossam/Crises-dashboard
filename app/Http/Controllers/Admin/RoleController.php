<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display all roles.
     */
    public function index()
    {
        $roles = Role::withCount('permissions')
            ->latest()
            ->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show create role form.
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get();

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a new role.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                'unique:roles,slug',
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'required',
                'integer',
                'distinct',
                'exists:permissions,id',
            ],
        ]);

        DB::transaction(function () use ($validated) {
            $role = Role::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
            ]);

            $role->permissions()->sync(
                $validated['permissions'] ?? []
            );
        });

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('roles.created_successfully'));
    }

    /**
     * Show role details.
     */
    public function show(Role $role)
    {
        $role->load('permissions');

        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show edit role form.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();

        $role->load('permissions');

        $selectedPermissions = $role->permissions
            ->pluck('id')
            ->toArray();

        return view('admin.roles.edit', compact(
            'role',
            'permissions',
            'selectedPermissions'
        ));
    }

    /**
     * Update role.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('roles', 'slug')->ignore($role->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'required',
                'integer',
                'distinct',
                'exists:permissions,id',
            ],
        ]);

        DB::transaction(function () use ($validated, $role) {
            $role->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
            ]);

            $role->permissions()->sync(
                $validated['permissions'] ?? []
            );
        });

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('roles.updated_successfully'));
    }

    /**
     * Delete role.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting a role assigned to users.
        if ($role->users()->exists()) {
            return back()->with(
                'error',
                __('roles.cannot_delete_assigned_role')
            );
        }

        DB::transaction(function () use ($role) {
            $role->permissions()->detach();
            $role->delete();
        });

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('roles.deleted_successfully'));
    }
}
