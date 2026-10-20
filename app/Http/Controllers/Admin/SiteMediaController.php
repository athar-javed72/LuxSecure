<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\MediaAsset;
use App\Services\MediaResolver;
use App\Services\MediaUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteMediaController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', MediaAsset::class);

        $slots = config('media_slots.slots', []);
        $grouped = [];
        foreach ($slots as $key => $meta) {
            $page = $meta['page'] ?? 'Other';
            $grouped[$page][$key] = $meta;
        }

        $assets = MediaAsset::query()->orderBy('slot')->orderBy('sort')->orderBy('id')->get()->groupBy('slot');
        $cities = City::query()->active()->orderBy('name')->limit(40)->get();

        return view('admin.site-media.index', compact('grouped', 'assets', 'cities'));
    }

    public function updateCityCover(Request $request, City $city, MediaUploadService $uploads): RedirectResponse
    {
        abort_unless($request->user()?->can('cms.update'), 403);

        $data = $request->validate([
            'cover' => ['nullable', 'file'],
            'cover_focal_x' => ['nullable', 'integer', 'min:0', 'max:100'],
            'cover_focal_y' => ['nullable', 'integer', 'min:0', 'max:100'],
            'remove_cover' => ['sometimes', 'boolean'],
        ]);

        if ($request->boolean('remove_cover')) {
            $city->cover_path = null;
            $city->save();

            return back()->with('success', 'City cover removed.');
        }

        if ($request->hasFile('cover')) {
            $stored = $uploads->storeImage($request->file('cover'), 'media/cities');
            $city->cover_path = $stored['path'];
        }

        $city->cover_focal_x = $data['cover_focal_x'] ?? $city->cover_focal_x ?? 50;
        $city->cover_focal_y = $data['cover_focal_y'] ?? $city->cover_focal_y ?? 50;
        $city->save();

        return back()->with('success', 'City cover saved.');
    }

    public function store(Request $request, MediaUploadService $uploads): RedirectResponse
    {
        $this->authorize('create', MediaAsset::class);

        $data = $request->validate([
            'slot' => ['required', 'string', 'max:64'],
            'type' => ['required', 'in:image,video,embed'],
            'file' => ['nullable', 'file'],
            'poster' => ['nullable', 'file'],
            'embed_url' => ['nullable', 'string', 'max:500'],
            'alt' => ['nullable', 'string', 'max:255'],
            'decorative' => ['sometimes', 'boolean'],
            'caption' => ['nullable', 'string', 'max:255'],
            'focal_x' => ['nullable', 'integer', 'min:0', 'max:100'],
            'focal_y' => ['nullable', 'integer', 'min:0', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $slot = $data['slot'];
        $slotMeta = config('media_slots.slots')[$slot] ?? null;
        if (! is_array($slotMeta)) {
            return back()->with('error', 'Unknown media slot.');
        }

        $max = (int) ($slotMeta['max_items'] ?? 1);
        $count = MediaAsset::query()->where('slot', $slot)->count();
        if ($count >= $max) {
            return back()->with('error', "This slot allows at most {$max} item(s). Remove one first.");
        }

        $types = $slotMeta['types'] ?? [];
        if (! in_array($data['type'], $types, true) && ! ($data['type'] === 'embed' && in_array('embed', $types, true))) {
            return back()->with('error', 'This slot does not accept that media type.');
        }

        $decorative = $request->boolean('decorative');
        if (! $decorative && ($slotMeta['alt_required'] ?? false) && blank($data['alt'] ?? null)) {
            return back()->withErrors(['alt' => 'Alt text is required unless marked decorative.'])->withInput();
        }

        $asset = new MediaAsset([
            'slot' => $slot,
            'type' => $data['type'],
            'alt' => $data['alt'] ?? null,
            'decorative' => $decorative,
            'caption' => $data['caption'] ?? null,
            'focal_x' => $data['focal_x'] ?? 50,
            'focal_y' => $data['focal_y'] ?? 50,
            'sort' => $count,
            'is_active' => $request->boolean('is_active', true),
            'is_demo' => false,
            'disk' => 'public',
        ]);

        if ($data['type'] === 'embed') {
            $parsed = $uploads->parseEmbedUrl((string) ($data['embed_url'] ?? ''));
            $asset->embed_provider = $parsed['provider'];
            $asset->embed_id = $parsed['id'];
            if ($request->hasFile('poster')) {
                $poster = $uploads->storeImage($request->file('poster'), 'media/posters');
                $asset->poster_path = $poster['path'];
            } else {
                return back()->withErrors(['poster' => 'A poster image is required for embeds.'])->withInput();
            }
        } elseif ($data['type'] === 'video') {
            if (! $request->hasFile('file')) {
                return back()->withErrors(['file' => 'Please choose a video file.'])->withInput();
            }
            $stored = $uploads->storeVideo($request->file('file'));
            $asset->path = $stored['path'];
            $asset->meta = $stored['meta'];
            if ($request->hasFile('poster')) {
                $poster = $uploads->storeImage($request->file('poster'), 'media/posters');
                $asset->poster_path = $poster['path'];
            } else {
                return back()->withErrors(['poster' => 'A poster image is required for videos.'])->withInput();
            }
        } else {
            if (! $request->hasFile('file')) {
                return back()->withErrors(['file' => 'Please choose an image file.'])->withInput();
            }
            $stored = $uploads->storeImage($request->file('file'));
            $asset->path = $stored['path'];
            $asset->meta = $stored['meta'];
        }

        $asset->save();
        MediaResolver::forgetSlot($slot);

        return back()->with('success', 'Media uploaded.');
    }

    public function update(Request $request, MediaAsset $media, MediaUploadService $uploads): RedirectResponse
    {
        $this->authorize('update', $media);

        $data = $request->validate([
            'alt' => ['nullable', 'string', 'max:255'],
            'decorative' => ['sometimes', 'boolean'],
            'caption' => ['nullable', 'string', 'max:255'],
            'focal_x' => ['nullable', 'integer', 'min:0', 'max:100'],
            'focal_y' => ['nullable', 'integer', 'min:0', 'max:100'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:99'],
            'is_active' => ['sometimes', 'boolean'],
            'file' => ['nullable', 'file'],
            'poster' => ['nullable', 'file'],
            'embed_url' => ['nullable', 'string', 'max:500'],
        ]);

        $slotMeta = config('media_slots.slots')[$media->slot] ?? [];
        $decorative = $request->boolean('decorative');
        if (! $decorative && ($slotMeta['alt_required'] ?? false) && blank($data['alt'] ?? $media->alt)) {
            return back()->withErrors(['alt' => 'Alt text is required unless marked decorative.'])->withInput();
        }

        $media->fill([
            'alt' => $data['alt'] ?? $media->alt,
            'decorative' => $decorative,
            'caption' => $data['caption'] ?? $media->caption,
            'focal_x' => $data['focal_x'] ?? $media->focal_x,
            'focal_y' => $data['focal_y'] ?? $media->focal_y,
            'sort' => $data['sort'] ?? $media->sort,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $media->is_active,
        ]);

        if ($request->hasFile('file') && $media->type === 'image') {
            $uploads->deleteFiles($media);
            $stored = $uploads->storeImage($request->file('file'));
            $media->path = $stored['path'];
            $media->meta = $stored['meta'];
        }

        if ($request->hasFile('poster')) {
            $poster = $uploads->storeImage($request->file('poster'), 'media/posters');
            $media->poster_path = $poster['path'];
        }

        if ($media->type === 'embed' && filled($data['embed_url'] ?? null)) {
            $parsed = $uploads->parseEmbedUrl($data['embed_url']);
            $media->embed_provider = $parsed['provider'];
            $media->embed_id = $parsed['id'];
        }

        $media->save();
        MediaResolver::forgetSlot($media->slot);

        return back()->with('success', 'Media updated.');
    }

    public function destroy(MediaAsset $media, MediaUploadService $uploads): RedirectResponse
    {
        $this->authorize('delete', $media);
        $slot = $media->slot;
        $uploads->deleteFiles($media);
        $media->delete();
        MediaResolver::forgetSlot($slot);

        return back()->with('success', 'Media removed.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('cms.update'), 403);

        $data = $request->validate([
            'slot' => ['required', 'string'],
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        foreach ($data['order'] as $sort => $id) {
            MediaAsset::query()->where('slot', $data['slot'])->whereKey($id)->update(['sort' => $sort]);
        }
        MediaResolver::forgetSlot($data['slot']);

        return back()->with('success', 'Order saved.');
    }
}
