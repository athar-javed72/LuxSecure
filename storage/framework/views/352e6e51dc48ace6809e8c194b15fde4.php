<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'question',
    'answer',
    'open' => false,
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
    'question',
    'answer',
    'open' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div
    data-faq
    <?php echo e($attributes->merge(['class' => 'rounded-[var(--radius-card)] border border-[var(--color-border)] bg-white px-5 py-4'])); ?>

>
    <button
        type="button"
        data-faq-toggle
        class="flex w-full items-center justify-between gap-4 text-left min-h-11 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-forest)]"
        aria-expanded="<?php echo e($open ? 'true' : 'false'); ?>"
    >
        <h3 class="text-base font-semibold text-[var(--color-ink)]"><?php echo e($question); ?></h3>
        <svg
            data-faq-chevron
            class="fs-faq-chevron w-5 h-5 shrink-0 text-[var(--color-muted)] <?php echo e($open ? 'is-open' : ''); ?>"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div data-faq-panel class="fs-faq-panel <?php echo e($open ? 'is-open' : ''); ?>">
        <div>
            <p class="pt-3 text-[15px] md:text-base leading-relaxed text-[var(--color-muted-strong)] max-w-prose">
                <?php echo e($answer); ?>

            </p>
        </div>
    </div>
</div>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/faq-item.blade.php ENDPATH**/ ?>