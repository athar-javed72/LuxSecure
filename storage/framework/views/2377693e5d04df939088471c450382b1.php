<?php
    $selectedRoles = collect(old('roles', $selectedRoles ?? []));
    $selectedPermissions = collect(old('permissions', $selectedPermissions ?? []));
?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('users.assign_roles')): ?>
    <?php if (isset($component)) { $__componentOriginal36665f0dc0e45320e21db1e20a989acf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36665f0dc0e45320e21db1e20a989acf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.panel','data' => ['class' => 'md:col-span-2 !p-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'md:col-span-2 !p-4']); ?>
        <h3 class="text-sm font-semibold text-fs-primary mb-3">Roles</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            <?php $__currentLoopData = $assignableRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="inline-flex items-center gap-2 min-h-10 text-sm">
                    <input type="checkbox" name="roles[]" value="<?php echo e($role->name); ?>" class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand" <?php if($selectedRoles->contains($role->name)): echo 'checked'; endif; ?>>
                    <span><?php echo e($role->name); ?></span>
                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php $__errorArgs = ['roles'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-fs-danger"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal36665f0dc0e45320e21db1e20a989acf)): ?>
<?php $attributes = $__attributesOriginal36665f0dc0e45320e21db1e20a989acf; ?>
<?php unset($__attributesOriginal36665f0dc0e45320e21db1e20a989acf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal36665f0dc0e45320e21db1e20a989acf)): ?>
<?php $component = $__componentOriginal36665f0dc0e45320e21db1e20a989acf; ?>
<?php unset($__componentOriginal36665f0dc0e45320e21db1e20a989acf); ?>
<?php endif; ?>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('users.assign_permissions')): ?>
    <div class="md:col-span-2 space-y-3">
        <h3 class="text-sm font-semibold text-fs-primary">Direct permissions</h3>
        <?php $__currentLoopData = $groupedPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $permissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if (isset($component)) { $__componentOriginal36665f0dc0e45320e21db1e20a989acf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36665f0dc0e45320e21db1e20a989acf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.panel','data' => ['class' => '!p-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => '!p-4']); ?>
                <p class="text-xs font-semibold uppercase tracking-wide text-fs-muted mb-2"><?php echo e($group); ?></p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="inline-flex items-center gap-2 min-h-10 text-sm text-fs-secondary">
                            <input type="checkbox" name="permissions[]" value="<?php echo e($permission->name); ?>" class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand" <?php if($selectedPermissions->contains($permission->name)): echo 'checked'; endif; ?>>
                            <span><?php echo e($permission->name); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal36665f0dc0e45320e21db1e20a989acf)): ?>
<?php $attributes = $__attributesOriginal36665f0dc0e45320e21db1e20a989acf; ?>
<?php unset($__attributesOriginal36665f0dc0e45320e21db1e20a989acf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal36665f0dc0e45320e21db1e20a989acf)): ?>
<?php $component = $__componentOriginal36665f0dc0e45320e21db1e20a989acf; ?>
<?php unset($__componentOriginal36665f0dc0e45320e21db1e20a989acf); ?>
<?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php $__errorArgs = ['permissions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-fs-danger"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
<?php endif; ?>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/admin/users/_rbac-fields.blade.php ENDPATH**/ ?>