@extends('admin.layout')

@section('title', 'Site media')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Site media'],
]" />

<x-page-header title="Site media" subtitle="Upload images and videos into named page slots. Alt text is required unless decorative." />

@foreach($grouped as $page => $slots)
    <section class="mb-10">
        <h2 class="text-lg font-semibold text-fs-primary mb-4">{{ $page }}</h2>
        <div class="grid gap-4 lg:grid-cols-2">
            @foreach($slots as $slotKey => $meta)
                @php $items = $assets->get($slotKey, collect()); @endphp
                <x-panel class="!p-4">
                    <div class="flex flex-wrap items-start justify-between gap-2 mb-3">
                        <div>
                            <h3 class="text-sm font-semibold text-fs-primary">{{ $meta['label'] }}</h3>
                            <p class="text-xs text-fs-muted mt-1">{{ $meta['guidance'] }}</p>
                            <p class="text-xs text-fs-muted mt-1">{{ $meta['recommended'] }} · max {{ $meta['max_items'] }}</p>
                        </div>
                        <code class="text-[10px] text-fs-muted">{{ $slotKey }}</code>
                    </div>

                    @if($items->isNotEmpty())
                        <ul class="space-y-3 mb-4">
                            @foreach($items as $asset)
                                <li class="border border-black/10 rounded-sm p-3">
                                    <div class="flex gap-3">
                                        <div class="w-24 h-16 rounded-sm overflow-hidden bg-fs-surface shrink-0 relative">
                                            @if($asset->type === 'image' && $asset->path)
                                                <img src="{{ $asset->url() }}" alt="" class="w-full h-full object-cover" style="object-position: {{ $asset->objectPosition() }}">
                                            @elseif($asset->poster_path)
                                                <img src="{{ $asset->posterUrl() }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-[10px] p-2 block">{{ strtoupper($asset->type) }}</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <form method="POST" action="{{ route('admin.site-media.update', $asset) }}" enctype="multipart/form-data" class="space-y-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="alt" value="{{ old('alt', $asset->alt) }}" placeholder="Alt text" class="w-full text-xs border border-black/10 rounded-sm px-2 py-1.5">
                                                <label class="inline-flex items-center gap-2 text-xs"><input type="checkbox" name="decorative" value="1" @checked($asset->decorative)> Decorative</label>
                                                <div class="grid grid-cols-3 gap-2">
                                                    <label class="text-xs">Focal X <input type="number" min="0" max="100" name="focal_x" value="{{ $asset->focal_x }}" class="w-full border border-black/10 rounded-sm px-2 py-1"></label>
                                                    <label class="text-xs">Focal Y <input type="number" min="0" max="100" name="focal_y" value="{{ $asset->focal_y }}" class="w-full border border-black/10 rounded-sm px-2 py-1"></label>
                                                    <label class="text-xs">Sort <input type="number" min="0" name="sort" value="{{ $asset->sort }}" class="w-full border border-black/10 rounded-sm px-2 py-1"></label>
                                                </div>
                                                <label class="inline-flex items-center gap-2 text-xs"><input type="checkbox" name="is_active" value="1" @checked($asset->is_active)> Active</label>
                                                @if(in_array($asset->type, ['video', 'embed'], true))
                                                    <input type="file" name="poster" accept="image/jpeg,image/png,image/webp" class="text-xs w-full">
                                                @endif
                                                @if($asset->type === 'embed')
                                                    <input type="text" name="embed_url" value="{{ $asset->embed_id }}" placeholder="YouTube/Vimeo ID or URL" class="w-full text-xs border border-black/10 rounded-sm px-2 py-1.5">
                                                @endif
                                                <div class="flex flex-wrap gap-2">
                                                    <x-button type="submit" variant="primary" class="!text-xs !min-h-9">Save</x-button>
                                                </div>
                                            </form>
                                            <form method="POST" action="{{ route('admin.site-media.destroy', $asset) }}" class="mt-2" onsubmit="return confirm('Remove this media?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-fs-danger hover:underline">Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if($items->count() < ($meta['max_items'] ?? 1))
                        <form method="POST" action="{{ route('admin.site-media.store') }}" enctype="multipart/form-data" class="space-y-2 border-t border-black/10 pt-3">
                            @csrf
                            <input type="hidden" name="slot" value="{{ $slotKey }}">
                            <label class="block text-xs font-semibold">Type
                                <select name="type" class="w-full mt-1 border border-black/10 rounded-sm px-2 py-1.5 text-sm">
                                    @foreach($meta['types'] as $t)
                                        <option value="{{ $t }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <input type="file" name="file" class="text-xs w-full">
                            <input type="file" name="poster" accept="image/jpeg,image/png,image/webp" class="text-xs w-full" title="Poster (videos/embeds)">
                            <input type="text" name="embed_url" placeholder="YouTube/Vimeo URL or ID" class="w-full text-xs border border-black/10 rounded-sm px-2 py-1.5">
                            <input type="text" name="alt" placeholder="Alt text" class="w-full text-xs border border-black/10 rounded-sm px-2 py-1.5">
                            <label class="inline-flex items-center gap-2 text-xs"><input type="checkbox" name="decorative" value="1"> Decorative</label>
                            <x-button type="submit" variant="primary" class="!text-xs !min-h-9">Upload</x-button>
                        </form>
                    @endif
                </x-panel>
            @endforeach
        </div>
    </section>
@endforeach

<section class="mb-10">
    <h2 class="text-lg font-semibold text-fs-primary mb-4">City covers</h2>
    <p class="text-sm text-fs-muted mb-4">Optional destination tiles for division and home destination grids (4:3, 1200×900 recommended).</p>
    <div class="grid gap-3 md:grid-cols-2">
        @foreach($cities as $city)
            <x-panel class="!p-3">
                <form method="POST" action="{{ route('admin.site-media.city-cover', $city) }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                    @csrf
                    @method('PUT')
                    <div class="w-20 h-14 rounded-sm overflow-hidden bg-fs-surface shrink-0">
                        @if($city->cover_path)
                            <img src="{{ \App\Support\StorageDisk::url($city->cover_path) }}" alt="" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold">{{ $city->displayLabel() }}</p>
                        <input type="file" name="cover" accept="image/jpeg,image/png,image/webp" class="text-xs mt-1 w-full">
                        <div class="flex gap-2 mt-1">
                            <input type="number" name="cover_focal_x" min="0" max="100" value="{{ $city->cover_focal_x ?? 50 }}" class="w-16 text-xs border border-black/10 rounded-sm px-1 py-1" title="Focal X">
                            <input type="number" name="cover_focal_y" min="0" max="100" value="{{ $city->cover_focal_y ?? 50 }}" class="w-16 text-xs border border-black/10 rounded-sm px-1 py-1" title="Focal Y">
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <x-button type="submit" variant="primary" class="!text-xs !min-h-9">Save</x-button>
                        <button type="submit" name="remove_cover" value="1" class="text-xs text-fs-danger">Remove</button>
                    </div>
                </form>
            </x-panel>
        @endforeach
    </div>
</section>
@endsection
