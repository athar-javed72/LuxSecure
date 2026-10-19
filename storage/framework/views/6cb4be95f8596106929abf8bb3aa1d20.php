<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label' => 'Upload file',
    'name',
    'required' => false,
    'hint' => null,
    'accept' => null,
    'id' => null,
]));

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

foreach (array_filter(([
    'label' => 'Upload file',
    'name',
    'required' => false,
    'hint' => null,
    'accept' => null,
    'id' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $inputId = $id ?? $name;
?>

<div <?php echo e($attributes->only('class')->merge(['class' => 'w-full'])); ?>>
    <label for="<?php echo e($inputId); ?>" class="block text-xs font-semibold text-fs-primary mb-1">
        <?php echo e($label); ?><?php if($required): ?> <span class="text-fs-danger" aria-hidden="true">*</span><?php endif; ?>
    </label>
    <div class="relative rounded-sm border border-dashed border-black/15 bg-white hover:border-fs-brand/50 transition-colors duration-150 focus-within:border-fs-brand focus-within:ring-2 focus-within:ring-fs-brand/20">
        <input
            type="file"
            name="<?php echo e($name); ?>"
            id="<?php echo e($inputId); ?>"
            <?php if($required): ?> required <?php endif; ?>
            <?php if($accept): ?> accept="<?php echo e($accept); ?>" <?php endif; ?>
            <?php echo e($attributes->except('class')->merge([
                'class' => 'block w-full text-sm text-fs-secondary file:mr-4 file:h-11 file:px-4 file:rounded-sm file:border-0 file:bg-fs-brand-light file:text-fs-primary file:font-semibold hover:file:bg-fs-brand/20 cursor-pointer px-3 py-2',
            ])); ?>

        >
    </div>
    <?php if($hint): ?>
        <p class="mt-1 text-xs text-fs-muted"><?php echo e($hint); ?></p>
    <?php endif; ?>
    <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="mt-1 text-xs text-fs-danger" id="<?php echo e($inputId); ?>-error"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/file-upload.blade.php ENDPATH**/ ?>