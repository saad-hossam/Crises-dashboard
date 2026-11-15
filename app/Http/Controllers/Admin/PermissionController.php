<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(protected PermissionService $permissionService) {}

    public function index(Request $request)
    {
        $query = Permission::withCount('roles');

        if ($group = $request->input('group')) {
            $query->where('group', $group);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $permissions = $query->orderBy('group')->orderBy('name')->get()->groupBy('group');
        $groups = Permission::knownGroups();

        return view('admin.permissions.index', compact('permissions', 'groups'));
    }

    public function create()
    {
        $groups = Permission::knownGroups();
        return view('admin.permissions.create', compact('groups'));
    }

    public function store(StorePermissionRequest $request)
    {
        $this->permissionService->create($request->validated());

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('permissions.created'));
    }

    public function show(Permission $permission)
    {
        $permission->load('roles');

        return view('admin.permissions.show', compact('permission'));
    }

    public function edit(Permission $permission)
    {
        $groups = Permission::knownGroups();
        return view('admin.permissions.edit', compact('permission', 'groups'));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $this->permissionService->update($permission, $request->validated());

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('permissions.updated'));
    }

    public function destroy(Permission $permission)
    {
        $this->permissionService->delete($permission);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('permissions.deleted'));
    }
}