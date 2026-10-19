<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['terms' => null, 'privacy' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['terms' => null, 'privacy' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if($terms || $privacy): ?>
    <div <?php echo e($attributes->merge(['class' => 'space-y-2 rounded-sm border border-black/10 bg-fs-surface/60 p-3 text-sm'])); ?>>
        <?php if($terms): ?>
            <label class="flex items-start gap-2 min-h-11 cursor-pointer">
                <input type="checkbox" name="accept_terms" value="1" <?php if(old('accept_terms')): echo 'checked'; endif; ?> required class="mt-1 rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
                <span class="text-fs-secondary">
                    I accept the
                    <a href="<?php echo e(route('pages.show', $terms->slug)); ?>" target="_blank" rel="noopener" class="text-fs-brand font-medium hover:underline"><?php echo e($terms->title); ?></a>
                    <span class="text-fs-muted">(v<?php echo e($terms->version); ?>)</span>
                </span>
            </label>
            <?php $__errorArgs = ['accept_terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-xs text-fs-danger"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <?php endif; ?>
        <?php if($privacy): ?>
            <label class="flex items-start gap-2 min-h-11 cursor-pointer">
                <input type="checkbox" name="accept_privacy" value="1" <?php if(old('accept_privacy')): echo 'checked'; endif; ?> required class="mt-1 rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
                <span class="text-fs-secondary">
                    I accept the
                    <a href="<?php echo e(route('pages.show', $privacy->slug)); ?>" target="_blank" rel="noopener" class="text-fs-brand font-medium hover:underline"><?php echo e($privacy->title); ?></a>
                    <span class="text-fs-muted">(v<?php echo e($privacy->version); ?>)</span>
                </span>
            </label>
            <?php $__errorArgs = ['accept_privacy'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-xs text-fs-danger"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/legal-acceptances.blade.php ENDPATH**/ ?>