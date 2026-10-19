
<?php
    $tag = $tag ?? 'span';
    $brandingClass = $brandingClass ?? 'text-gray-400 text-sm';
    $strongClass = $strongClass ?? '';
?>
<<?php echo e($tag); ?> class="<?php echo e($brandingClass); ?>">
    Developed by <strong class="<?php echo e($strongClass); ?>">Nexora Labs</strong>
</<?php echo e($tag); ?>>
<?php /**PATH D:\Projects\Laravel Projects\LuxSecure-main\resources\views/include/branding.blade.php ENDPATH**/ ?>