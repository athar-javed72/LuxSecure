@props([
    'items' => [],
])

@if(count($items) > 0)
    <nav {{ $attributes->merge(['class' => 'mb-4', 'aria-label' => 'Breadcrumb']) }}>
        <ol class="flex flex-wrap items-center gap-1 text-xs text-fs-muted">
            @foreach($items as $index => $item)
                @php
                    $label = $item['label'] ?? '';
                    $href = $item['href'] ?? null;
                    $isLast = $index === array_key_last($items);
                @endphp
                <li class="inline-flex items-center gap-1 min-w-0">
                    @if(!$isLast && $href)
                        <a href="{{ $href }}" class="truncate text-fs-secondary hover:text-fs-brand transition-colors duration-150">{{ $label }}</a>
                        <span class="text-fs-muted" aria-hidden="true">/</span>
                    @else
                        <span class="truncate font-medium text-fs-primary" @if($isLast) aria-current="page" @endif>{{ $label }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
