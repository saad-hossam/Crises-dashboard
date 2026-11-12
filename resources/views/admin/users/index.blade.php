@extends('layouts.admin')

@section('title', __('users.title'))
@section('page_title', __('users.title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>{{ __('users.title') }}</h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> {{ __('users.create') }}
        </a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control"
               placeholder="{{ __('users.search_placeholder') }}"
               value="{{ request('search') }}">
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">{{ __('users.all_statuses') }}</option>
            <option value="active" @selected(request('status') === 'active')>{{ __('users.active') }}</option>
            <option value="inactive" @selected(request('status') === 'inactive')>{{ __('users.inactive') }}</option>
        </select>
    </div>
    <div class="col-md-3">
        <select name="role" class="form-select">
            <option value="">{{ __('users.all_roles') }}</option>
            @foreach ($roles as $role)
                <option value="{{ $role->name }}" @selected(request('role') === $role->name)>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-outline-secondary w-100">
            <i class="bi bi-search"></i> {{ __('common.search') }}
        </button>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ __('users.name') }}</th>
                    <th>{{ __('users.email') }}</th>
                    <th>{{ __('users.phone') }}</th>
                    <th>{{ __('users.roles') }}</th>
                    <th>{{ __('users.status') }}</th>
                    <th>{{ __('users.last_login') }}</th>
                    <th>{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '—' }}</td>
                        <td>
                            @forelse ($user->roles as $role)
                                <span class="badge bg-primary-subtle text-primary-emphasis">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="text-muted small">—</span>
                            @endforelse
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                {{ __('users.' . $user->status) }}
                            </span>
                        </td>
                        <td>{{ $user->last_login_at?->diffForHumans() ?? '—' }}</td>
                        <td>
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('{{ __('users.delete_confirm') }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">{{ __('common.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $users->links() }}</div>
@endsection