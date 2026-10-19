<?php
    $selected = collect(old('permissions', $selected ?? []));
?>

<div
    class="space-y-4"
    x-data="permissionMatrix"
>
    <?php $__currentLoopData = $groupedPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $permissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $groupLabel = str_replace('_', ' ', $group);
            $allSelected = collect($permissions)->every(fn ($p) => $selected->contains($p->name));
        ?>
        <fieldset class="rounded-md border border-black/5 bg-fs-surface/40 p-4 sm:p-5">
            <legend class="sr-only"><?php echo e($groupLabel); ?> permissions</legend>
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <h3 class="fs-display text-lg text-fs-primary capitalize"><?php echo e($groupLabel); ?></h3>
                <label class="inline-flex items-center gap-2 min-h-10 text-xs font-semibold uppercase tracking-wide text-fs-secondary cursor-pointer">
                    <input
                        type="checkbox"
                        data-select-all
                        class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand"
                        <?php if($allSelected): echo 'checked'; endif; ?>
                        @change="toggleGroup($el, $event.target.checked)"
                        aria-label="Select all <?php echo e($groupLabel); ?> permissions"
                    >
                    Select all
                </label>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="inline-flex items-center gap-2 min-h-11 px-3 rounded-sm bg-white border border-black/5 text-sm text-fs-secondary hover:border-fs-brand/40 cursor-pointer transition-colors duration-150">
                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="<?php echo e($permission->name); ?>"
                            data-perm
                            class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand"
                            <?php if($selected->contains($permission->name)): echo 'checked'; endif; ?>
                            @change="syncSelectAll($el)"
                        >
                        <span class="font-mono text-xs sm:text-sm break-all"><?php echo e($permission->name); ?></span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </fieldset>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/admin/roles/_permission-matrix.blade.php ENDPATH**/ ?>