@php
    $groupLabels = [
        'dashboard'     => __('permissions.groups.dashboard'),
        'users'         => __('permissions.groups.users'),
        'roles'         => __('permissions.groups.roles'),
        'permissions'   => __('permissions.groups.permissions'),
        'imports'       => __('permissions.groups.imports'),
        'data'          => __('permissions.groups.data'),
        'reports'       => __('permissions.groups.reports'),
        'settings'      => __('permissions.groups.settings'),
        'activity_logs' => __('permissions.groups.activity_logs'),
        'units'         => __('permissions.groups.units') ?? 'Units',
    ];

    $selectedPermissions = $selectedPermissions ?? [];
    $selectedPermissions = array_map('intval', (array) $selectedPermissions);
@endphp

<div class="mb-3">
    <label class="form-label fw-semibold">{{ __('roles.permissions') }}</label>
    <div class="text-muted small mb-3">{{ __('roles.permissions_hint') }}</div>

    @if ($errors->has('permissions') || $errors->has('permissions.*'))
        <div class="alert alert-danger py-2 small">
            {{ $errors->first('permissions') ?: $errors->first('permissions.*') }}
        </div>
    @endif

    <div class="row g-3">
        @forelse ($permissions as $group => $groupPermissions)
            @php $groupKey = $group ?: 'other'; @endphp
            <div class="col-md-6">
                <div class="border rounded p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="fw-semibold mb-0">
                            {{ $groupLabels[$group] ?? ucfirst($groupKey) }}
                        </label>
                        <button type="button"
                                class="btn btn-sm btn-link p-0 text-decoration-none"
                                onclick="toggleGroup('{{ $groupKey }}', this)">
                            {{ __('roles.select_all') }}
                        </button>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        @foreach ($groupPermissions as $permission)
                            <div class="form-check">
                                <input class="form-check-input group-{{ $groupKey }}"
                                       type="checkbox"
                                       name="permissions[]"
                                       id="perm_{{ $permission->id }}"
                                       value="{{ $permission->id }}"
                                       @checked(in_array($permission->id, $selectedPermissions, true))>
                                <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                    <code>{{ $permission->name }}</code>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info mb-0">{{ __('roles.no_permissions') }}</div>
            </div>
        @endforelse
    </div>
</div>

<script>
function toggleGroup(group, btn) {
    const boxes = document.querySelectorAll('.group-' + group);
    const anyUnchecked = Array.from(boxes).some(b => !b.checked);
    boxes.forEach(b => b.checked = anyUnchecked);
    btn.textContent = anyUnchecked ? '{{ __('roles.deselect_all') }}' : '{{ __('roles.select_all') }}';
}
</script>