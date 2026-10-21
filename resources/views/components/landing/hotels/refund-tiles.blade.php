@props(['tiles' => []])

<div class="mh-tiles" aria-label="Cancellation refunds">
    @foreach($tiles as $tile)
        <div>
            <b>{{ $tile['title'] }}</b>
            <span>{{ $tile['body'] }}</span>
        </div>
    @endforeach
</div>
