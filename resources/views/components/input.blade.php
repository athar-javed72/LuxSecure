@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'id' => null,
])

@php
    $inputId = $id ?? $name;
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'w-full']) }}>
    @if($label)
        <label for="{{ $inputId }}" class="block text-xs font-semibold text-fs-primary mb-1 text-left">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $inputId }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        {{ $attributes->except('class')->merge([
            'class' => 'w-full h-12 px-4 rounded-sm border border-slate-200 bg-white text-sm text-fs-primary placeholder:text-fs-muted transition-colors duration-150 focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20',
        ]) }}
    >
    @error($name)
        <p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>
    @enderror
</div>
