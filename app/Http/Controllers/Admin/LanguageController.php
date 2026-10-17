<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LanguageController extends Controller
{
    public function index(): View
    {
        Gate::authorize('languages.view');

        $languages = Language::query()->orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('admin.languages.index', compact('languages'));
    }

    public function create(): View
    {
        Gate::authorize('languages.create');

        return view('admin.languages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('languages.create');

        Language::create($this->validated($request));

        return redirect()->route('admin.languages.index')->with('success', 'Language created.');
    }

    public function edit(Language $language): View
    {
        Gate::authorize('languages.update');

        return view('admin.languages.edit', compact('language'));
    }

    public function update(Request $request, Language $language): RedirectResponse
    {
        Gate::authorize('languages.update');

        $language->update($this->validated($request, $language));

        return redirect()->route('admin.languages.index')->with('success', 'Language updated.');
    }

    public function destroy(Language $language): RedirectResponse
    {
        Gate::authorize('languages.delete');

        $language->delete();

        return redirect()->route('admin.languages.index')->with('success', 'Language deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Language $language = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:12', 'unique:languages,code,'.($language?->id ?? 'NULL')],
            'name' => ['required', 'string', 'max:120'],
            'native_name' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_rtl' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['code'] = strtolower($data['code']);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_rtl'] = $request->boolean('is_rtl');
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
