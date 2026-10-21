@props(['tenancy' => []])

<aside class="ml-tenancy" aria-label="{{ $tenancy['heading'] ?? 'My tenancy' }} preview">
    <div class="ml-tenancy__head">
        <span>{{ $tenancy['heading'] ?? 'My tenancy' }}</span>
        <span>{{ $tenancy['property'] ?? '' }}</span>
    </div>
    <div class="ml-tenancy__next">
        <p class="ml-tenancy__next-label">{{ $tenancy['next_label'] ?? 'Next rent due' }}</p>
        <p class="ml-tenancy__next-amount">{{ $tenancy['next_amount'] ?? '' }}</p>
        <p class="ml-tenancy__next-date">{{ $tenancy['next_date'] ?? '' }}</p>
    </div>
    <ul class="ml-tenancy__rows">
        @foreach(($tenancy['rows'] ?? []) as $row)
            <li>
                <span>{{ $row['label'] }}</span>
                @if(($row['tone'] ?? '') === 'link')
                    <a href="{{ $row['href'] ?? '#' }}">{{ $row['value'] }}</a>
                @else
                    <b class="ml-tenancy__status">{{ $row['value'] }}</b>
                @endif
            </li>
        @endforeach
    </ul>
</aside>
