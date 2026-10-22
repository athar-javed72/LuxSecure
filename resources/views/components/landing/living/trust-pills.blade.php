@props(['pills' => []])

<div class="mg-container">
    <div class="mh-pills" aria-label="Living assurances">
        @foreach($pills as $pill)
            <span>{{ $pill }}</span>
        @endforeach
    </div>
</div>
