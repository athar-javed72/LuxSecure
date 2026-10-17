@php
    $selectedRoles = collect(old('roles', $selectedRoles ?? []));
    $selectedPermissions = collect(old('permissions', $selectedPermissions ?? []));
@endphp

@can('users.assign_roles')
    <x-panel class="md:col-span-2 !p-4">
        <h3 class="text-sm font-semibold text-fs-primary mb-3">Roles</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach($assignableRoles as $role)
                <label class="inline-flex items-center gap-2 min-h-10 text-sm">
                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand" @checked($selectedRoles->contains($role->name))>
                    <span>{{ $role->name }}</span>
                </label>
            @endforeach
        </div>
        @error('roles')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
    </x-panel>
@endcan

@can('users.assign_permissions')
    <div class="md:col-span-2 space-y-3">
        <h3 class="text-sm font-semibold text-fs-primary">Direct permissions</h3>
        @foreach($groupedPermissions as $group => $permissions)
            <x-panel class="!p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-fs-muted mb-2">{{ $group }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($permissions as $permission)
                        <label class="inline-flex items-center gap-2 min-h-10 text-sm text-fs-secondary">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand" @checked($selectedPermissions->contains($permission->name))>
                            <span>{{ $permission->name }}</span>
                        </label>
                    @endforeach
                </div>
            </x-panel>
        @endforeach
        @error('permissions')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
    </div>
@endcan
