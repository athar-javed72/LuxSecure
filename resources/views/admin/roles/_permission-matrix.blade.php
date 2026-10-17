@php
    $selected = collect(old('permissions', $selected ?? []));
@endphp

<div class="space-y-4">
    @foreach($groupedPermissions as $group => $permissions)
        <div class="rounded-md border border-black/5 p-4">
            <h3 class="text-sm font-semibold text-fs-primary mb-3 capitalize">{{ str_replace('_', ' ', $group) }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach($permissions as $permission)
                    <label class="inline-flex items-center gap-2 min-h-10 text-sm text-fs-secondary">
                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="{{ $permission->name }}"
                            class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand"
                            @checked($selected->contains($permission->name))
                        >
                        <span>{{ $permission->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
