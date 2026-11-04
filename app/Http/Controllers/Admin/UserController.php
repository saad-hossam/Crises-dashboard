<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function index(Request $request)
    {
        // Gate::authorize('users.view');

        $query = User::with('roles');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($role = $request->input('role')) {
            $query->role($role);
        }

        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['id', 'name', 'email', 'status', 'last_login_at', 'created_at'];
        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc');
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        // Gate::authorize('users.create');
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->createUser($request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', __('users.created'));
    }

    public function show(User $user)
    {
        // Gate::authorize('users.view');
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Gate::authorize('users.edit');
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->updateUser($user, $request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', __('users.updated'));
    }

    public function destroy(User $user)
    {
        // Gate::authorize('users.delete');

        $this->userService->deleteUser($user, auth()->user());

        return redirect()->route('admin.users.index')
            ->with('success', __('users.deleted'));
    }
}