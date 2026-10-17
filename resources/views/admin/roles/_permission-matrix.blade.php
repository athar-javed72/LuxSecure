@php
    $selected = collect(old('permissions', $selected ?? []));
@endphp

<div
    class="space-y-4"
    x-data="permissionMatrix"
>
    @foreach($groupedPermissions as $group => $permissions)
        @php
            $groupLabel = str_replace('_', ' ', $group);
            $allSelected = collect($permissions)->every(fn ($p) => $selected->contains($p->name));
        @endphp
        <fieldset class="rounded-md border border-black/5 bg-fs-surface/40 p-4 sm:p-5">
            <legend class="sr-only">{{ $groupLabel }} permissions</legend>
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <h3 class="fs-display text-lg text-fs-primary capitalize">{{ $groupLabel }}</h3>
                <label class="inline-flex items-center gap-2 min-h-10 text-xs font-semibold uppercase tracking-wide text-fs-secondary cursor-pointer">
                    <input
                        type="checkbox"
                        data-select-all
                        class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand"
                        @checked($allSelected)
                        @change="toggleGroup($el, $event.target.checked)"
                        aria-label="Select all {{ $groupLabel }} permissions"
                    >
                    Select all
                </label>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach($permissions as $permission)
                    <label class="inline-flex items-center gap-2 min-h-11 px-3 rounded-sm bg-white border border-black/5 text-sm text-fs-secondary hover:border-fs-brand/40 cursor-pointer transition-colors duration-150">
                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="{{ $permission->name }}"
                            data-perm
                            class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand"
                            @checked($selected->contains($permission->name))
                            @change="syncSelectAll($el)"
                        >
                        <span class="font-mono text-xs sm:text-sm break-all">{{ $permission->name }}</span>
                    </label>
                @endforeach
            </div>
        </fieldset>
    @endforeach
</div>
