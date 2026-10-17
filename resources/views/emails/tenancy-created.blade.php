<p>Hello {{ $tenancy->tenant->name }},</p>
<p>Your tenancy for <strong>{{ $tenancy->listing->title }}</strong> is now {{ $tenancy->status }}.</p>
<ul>
    <li>Start: {{ $tenancy->start_date->toDateString() }}</li>
    @if($tenancy->end_date)
        <li>End: {{ $tenancy->end_date->toDateString() }}</li>
    @endif
    <li>Monthly rent: {{ number_format((float) $tenancy->monthly_rent, 2) }}</li>
    <li>Security deposit: {{ number_format((float) $tenancy->security_deposit, 2) }}</li>
</ul>
<p>— {{ config('app.name') }}</p>
