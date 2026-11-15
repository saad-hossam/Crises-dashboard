<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
        $permissions = Permission::orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');

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
                $this->normalizePermissionIds($validated['permissions'] ?? [])
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
        $role->load(['permissions', 'users']);

        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show edit role form.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');

        $role->load('permissions');

        // IDs للصلاحيات المختارة
        $selectedPermissions = $role->permissions->pluck('id')->toArray();

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
            // حماية الدور المحمي من تغيير الاسم أو الـ slug
            $isProtected = (bool) $role->is_protected;

            $role->update([
                'name' => $isProtected ? $role->name : $validated['name'],
                'slug' => $isProtected ? $role->slug : $validated['slug'],
                'description' => $validated['description'] ?? null,
            ]);

            $role->permissions()->sync(
                $this->normalizePermissionIds($validated['permissions'] ?? [])
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
        // منع حذف الأدوار المحمية (Super Admin)
        if ($role->is_protected) {
            return back()->with(
                'error',
                __('roles.cannot_delete_protected_role') ?? 'Cannot delete a protected role.'
            );
        }

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

    /**
     * تطبيع مصفوفة IDs الصلاحيات: int + إزالة القيم الفارغة والمكررة.
     */
    private function normalizePermissionIds(array $ids): array
    {
        return collect($ids)
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }
}