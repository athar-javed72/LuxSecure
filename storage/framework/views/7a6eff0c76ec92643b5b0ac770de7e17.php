<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo e($invoice->number); ?></title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .muted { color: #666; }
        .totals { margin-top: 16px; width: 40%; margin-left: auto; }
    </style>
</head>
<body>
    <h1><?php echo e($siteName); ?></h1>
    <p class="muted">Invoice <?php echo e($invoice->number); ?></p>
    <p>Customer: <?php echo e($invoice->payment->user?->name); ?> (<?php echo e($invoice->payment->user?->email); ?>)</p>
    <p>Payment: <?php echo e($invoice->payment->uuid); ?> · Gateway: <?php echo e($invoice->payment->gateway); ?> · Status: <?php echo e($invoice->payment->status); ?></p>

    <table>
        <thead>
            <tr><th>Description</th><th>Amount</th></tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = ($invoice->line_items ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($item['description'] ?? 'Item'); ?></td>
                    <td><?php echo e(number_format((float) ($item['amount'] ?? 0), 2)); ?> <?php echo e($invoice->payment->currency); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td><?php echo e(number_format((float) $invoice->subtotal, 2)); ?></td></tr>
        <tr><td>Tax</td><td><?php echo e(number_format((float) $invoice->tax, 2)); ?></td></tr>
        <tr><td><strong>Total</strong></td><td><strong><?php echo e(number_format((float) $invoice->total, 2)); ?> <?php echo e($invoice->payment->currency); ?></strong></td></tr>
    </table>
</body>
</html>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/pdf/invoice.blade.php ENDPATH**/ ?>