<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->number }}</title>
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
    <h1>{{ $siteName }}</h1>
    <p class="muted">Invoice {{ $invoice->number }}</p>
    <p>Customer: {{ $invoice->payment->user?->name }} ({{ $invoice->payment->user?->email }})</p>
    <p>Payment: {{ $invoice->payment->uuid }} · Gateway: {{ $invoice->payment->gateway }} · Status: {{ $invoice->payment->status }}</p>

    <table>
        <thead>
            <tr><th>Description</th><th>Amount</th></tr>
        </thead>
        <tbody>
            @foreach(($invoice->line_items ?? []) as $item)
                <tr>
                    <td>{{ $item['description'] ?? 'Item' }}</td>
                    <td>{{ number_format((float) ($item['amount'] ?? 0), 2) }} {{ $invoice->payment->currency }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td>{{ number_format((float) $invoice->subtotal, 2) }}</td></tr>
        <tr><td>Tax</td><td>{{ number_format((float) $invoice->tax, 2) }}</td></tr>
        <tr><td><strong>Total</strong></td><td><strong>{{ number_format((float) $invoice->total, 2) }} {{ $invoice->payment->currency }}</strong></td></tr>
    </table>
</body>
</html>
