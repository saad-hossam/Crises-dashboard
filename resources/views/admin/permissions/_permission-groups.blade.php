@php
    $groupLabels = [
        'dashboard'   => __('permissions.groups.dashboard'),
        'users'       => __('permissions.groups.users'),
        'roles'       => __('permissions.groups.roles'),
        'permissions' => __('permissions.groups.permissions'),
        'imports'     => __('permissions.groups.imports'),
        'data'        => __('permissions.groups.data'),
        'reports'     => __('permissions.groups.reports'),
        'settings'    => __('permissions.groups.settings'),
        'activity_logs' => __('permissions.groups.activity_logs'),
    ];
@endphp

<div class="mb-3">
    <label class="form-label fw-semibold">{{ __('roles.permissions') }}</label>
    <div class="text-muted small mb-3">{{ __('roles.permissions_hint') }}</div>

    <div class="row g-3">
        @foreach ($permissions as $group => $groupPermissions)
            <div class="col-md-6">
                <div class="border rounded p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="fw-semibold mb-0">
                            {{ $groupLabels[$group] ?? ucfirst($group) }}
                        </label>
                        <button type="button"
                                class="btn btn-sm btn-link p-0 text-decoration-none"
                                onclick="toggleGroup('{{ $group }}', this)">
                            {{ __('roles.select_all') }}
                        </button>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        @foreach ($groupPermissions as $permission)
                            <div class="form-check">
                                <input class="form-check-input group-{{ $group }}"
                                       type="checkbox"
                                       name="permissions[]"
                                       id="perm_{{ $permission->id }}"
                                       value="{{ $permission->name }}"
                                       @checked(in_array($permission->name, $selectedPermissions))>
                                <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                    <code>{{ $permission->name }}</code>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
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