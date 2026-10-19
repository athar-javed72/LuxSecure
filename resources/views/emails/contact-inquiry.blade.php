<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Inquiry | {{ config('app.name') }}</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #142A25;">New Contact Inquiry — {{ config('app.name') }}</h2>
    <div style="height: 3px; width: 48px; background: #876A2F; margin: 0 0 16px;"></div>
    <p><strong>From:</strong> {{ $inquiry->name }}</p>
    <p><strong>Email:</strong> {{ $inquiry->email }}</p>
    <p><strong>Message:</strong></p>
    <div style="background: #f5f5f5; padding: 15px; border-radius: 8px; margin: 10px 0;">
        {{ $inquiry->message }}
    </div>
    <p style="color: #666; font-size: 12px;">Received at {{ $inquiry->created_at->format('F j, Y g:i A') }}</p>
    <p style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 11px;">{{ config('app.name') }} — Developed by <strong style="color: #6b7280;">Nexora Labs</strong></p>
</body>
</html>
