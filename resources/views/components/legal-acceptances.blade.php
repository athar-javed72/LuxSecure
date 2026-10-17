@props(['terms' => null, 'privacy' => null])

@if($terms || $privacy)
    <div {{ $attributes->merge(['class' => 'space-y-2 rounded-sm border border-black/10 bg-fs-surface/60 p-3 text-sm']) }}>
        @if($terms)
            <label class="flex items-start gap-2 min-h-11 cursor-pointer">
                <input type="checkbox" name="accept_terms" value="1" @checked(old('accept_terms')) required class="mt-1 rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
                <span class="text-fs-secondary">
                    I accept the
                    <a href="{{ route('pages.show', $terms->slug) }}" target="_blank" rel="noopener" class="text-fs-brand font-medium hover:underline">{{ $terms->title }}</a>
                    <span class="text-fs-muted">(v{{ $terms->version }})</span>
                </span>
            </label>
            @error('accept_terms')
                <p class="text-xs text-fs-danger">{{ $message }}</p>
            @enderror
        @endif
        @if($privacy)
            <label class="flex items-start gap-2 min-h-11 cursor-pointer">
                <input type="checkbox" name="accept_privacy" value="1" @checked(old('accept_privacy')) required class="mt-1 rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand">
                <span class="text-fs-secondary">
                    I accept the
                    <a href="{{ route('pages.show', $privacy->slug) }}" target="_blank" rel="noopener" class="text-fs-brand font-medium hover:underline">{{ $privacy->title }}</a>
                    <span class="text-fs-muted">(v{{ $privacy->version }})</span>
                </span>
            </label>
            @error('accept_privacy')
                <p class="text-xs text-fs-danger">{{ $message }}</p>
            @enderror
        @endif
    </div>
@endif
