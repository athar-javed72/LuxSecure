<p>Hello {{ $booking->user->name }},</p>
<p>Your booking at <strong>{{ $booking->listing->title }}</strong> is <strong>{{ $booking->status }}</strong>.</p>
<ul>
    <li>Room type: {{ $booking->roomType->name }}</li>
    <li>Check-in: {{ $booking->check_in->toDateString() }}</li>
    <li>Check-out: {{ $booking->check_out->toDateString() }}</li>
    <li>Guests: {{ $booking->guests }}</li>
    <li>Total: {{ number_format((float) $booking->total, 2) }}</li>
    <li>Cancellation policy: {{ $booking->cancellation_policy }}</li>
</ul>
<p>— LuxSecure</p>
