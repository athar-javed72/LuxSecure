<p>Hello <?php echo e($booking->user->name); ?>,</p>
<p>Your booking at <strong><?php echo e($booking->listing->title); ?></strong> is <strong><?php echo e($booking->status); ?></strong>.</p>
<ul>
    <li>Room type: <?php echo e($booking->roomType->name); ?></li>
    <li>Check-in: <?php echo e($booking->check_in->toDateString()); ?></li>
    <li>Check-out: <?php echo e($booking->check_out->toDateString()); ?></li>
    <li>Guests: <?php echo e($booking->guests); ?></li>
    <li>Total: <?php echo e(number_format((float) $booking->total, 2)); ?></li>
    <li>Cancellation policy: <?php echo e($booking->cancellation_policy); ?></li>
</ul>
<p>— <?php echo e(config('app.name')); ?></p>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/emails/booking-confirmation.blade.php ENDPATH**/ ?>