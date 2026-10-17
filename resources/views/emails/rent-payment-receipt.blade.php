<p>Hello {{ $payment->tenancy->tenant->name }},</p>
<p>We recorded a rent payment.</p>
<ul>
    <li>Receipt: {{ $payment->receipt_no }}</li>
    <li>Amount: {{ number_format((float) $payment->amount, 2) }}</li>
    <li>Due: {{ $payment->due_date->toDateString() }}</li>
    <li>Paid at: {{ optional($payment->paid_at)->toDateTimeString() }}</li>
    <li>Method: {{ $payment->method }}</li>
</ul>
<p>— LuxSecure</p>
