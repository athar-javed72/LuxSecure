<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentBlock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentBlockController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', ContentBlock::class);

        $keys = array_keys(trans('content'));
        $existing = ContentBlock::query()->where('locale', 'en')->get()->keyBy('key');

        return view('admin.content-blocks.index', compact('keys', 'existing'));
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('cms.update'), 403);

        $data = $request->validate([
            'blocks' => ['required', 'array'],
            'blocks.*.title' => ['nullable', 'string', 'max:255'],
            'blocks.*.body' => ['nullable', 'string', 'max:5000'],
        ]);

        foreach ($data['blocks'] as $key => $fields) {
            if (! array_key_exists($key, trans('content'))) {
                continue;
            }
            ContentBlock::query()->updateOrCreate(
                ['key' => $key, 'locale' => 'en'],
                [
                    'title' => $fields['title'] ?? null,
                    'body' => $fields['body'] ?? null,
                ]
            );
            ContentBlock::forgetCache($key, 'en');
        }

        return back()->with('success', 'Content blocks saved.');
    }
}
