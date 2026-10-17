@props([
    'label' => 'Upload file',
    'name',
    'required' => false,
    'hint' => null,
    'accept' => null,
    'id' => null,
])

@php
    $inputId = $id ?? $name;
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'w-full']) }}>
    <label for="{{ $inputId }}" class="block text-xs font-semibold text-fs-primary mb-1">
        {{ $label }}@if($required) <span class="text-fs-danger" aria-hidden="true">*</span>@endif
    </label>
    <div class="relative rounded-sm border border-dashed border-black/15 bg-white hover:border-fs-brand/50 transition-colors duration-150 focus-within:border-fs-brand focus-within:ring-2 focus-within:ring-fs-brand/20">
        <input
            type="file"
            name="{{ $name }}"
            id="{{ $inputId }}"
            @if($required) required @endif
            @if($accept) accept="{{ $accept }}" @endif
            {{ $attributes->except('class')->merge([
                'class' => 'block w-full text-sm text-fs-secondary file:mr-4 file:h-11 file:px-4 file:rounded-sm file:border-0 file:bg-fs-brand-light file:text-fs-primary file:font-semibold hover:file:bg-fs-brand/20 cursor-pointer px-3 py-2',
            ]) }}
        >
    </div>
    @if($hint)
        <p class="mt-1 text-xs text-fs-muted">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs text-fs-danger" id="{{ $inputId }}-error">{{ $message }}</p>
    @enderror
</div>
