@props(['steps' => []])

<section class="mg-section mh-how" aria-labelledby="mh-how-heading">
    <div class="mg-container">
        <h2 class="mg-h2" id="mh-how-heading">From dates to check-in</h2>
        <div class="mh-steps">
            @foreach($steps as $step)
                <div>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['body'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
