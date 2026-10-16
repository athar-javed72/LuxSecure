@props([
    'label' => null,
    'name',
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'required' => false,
    'id' => null,
])

@php
    $selectId = $id ?? $name;
    $current = old($name, $selected);
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'w-full']) }}>
    @if($label)
        <label for="{{ $selectId }}" class="block text-xs font-semibold text-fs-primary mb-1 text-left">{{ $label }}</label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $selectId }}"
        @if($required) required @endif
        {{ $attributes->except('class')->merge([
            'class' => 'w-full h-12 px-4 rounded-sm border border-slate-200 bg-white text-sm text-fs-primary transition-colors duration-150 focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20',
        ]) }}
    >
        @if($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $value => $text)
            @if(is_int($value))
                <option value="{{ $text }}" @selected($current == $text)>{{ $text }}</option>
            @else
                <option value="{{ $value }}" @selected((string) $current === (string) $value)>{{ $text }}</option>
            @endif
        @endforeach
    </select>
    @error($name)
        <p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>
    @enderror
</div>
