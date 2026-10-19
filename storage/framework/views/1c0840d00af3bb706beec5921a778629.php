<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'status' => 'pending',
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
    'status' => 'pending',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $key = strtolower((string) $status);
    $map = [
        'pending' => 'muted',
        'draft' => 'muted',
        'awaiting_approval' => 'warning',
        'confirmed' => 'brand',
        'published' => 'success',
        'active' => 'success',
        'paid' => 'success',
        'approved' => 'success',
        'completed' => 'success',
        'verified' => 'success',
        'failed' => 'danger',
        'rejected' => 'danger',
        'cancelled' => 'danger',
        'overdue' => 'danger',
        'refund_pending' => 'warning',
        'refunded' => 'muted',
        'inactive' => 'muted',
        'open' => 'brand',
        'closed' => 'muted',
        'in_progress' => 'warning',
        'new' => 'brand',
        'contacted' => 'brand',
        'won' => 'success',
        'lost' => 'danger',
    ];
    $tone = $map[$key] ?? 'muted';
    $tones = [
        'brand' => 'bg-fs-brand-light text-fs-primary',
        'success' => 'bg-fs-accent/10 text-fs-accent',
        'warning' => 'bg-fs-brand/15 text-fs-brand-dark',
        'danger' => 'bg-fs-danger/10 text-fs-danger',
        'muted' => 'bg-black/5 text-fs-secondary',
    ];
    $label = ucwords(str_replace('_', ' ', $key));
?>

<span <?php echo e($attributes->merge(['class' => 'inline-flex items-center px-2.5 h-7 rounded-sm text-xs font-semibold tracking-wide whitespace-nowrap '.($tones[$tone] ?? $tones['muted'])])); ?>>
    <?php echo e($slot->isEmpty() ? $label : $slot); ?>

</span>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/status-badge.blade.php ENDPATH**/ ?>