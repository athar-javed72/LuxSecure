<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    public function index(): View
    {
        Gate::authorize('currencies.view');

        $currencies = Currency::query()->orderBy('code')->paginate(20);

        return view('admin.currencies.index', compact('currencies'));
    }

    public function create(): View
    {
        Gate::authorize('currencies.create');

        return view('admin.currencies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('currencies.create');

        Currency::create($this->validated($request));

        return redirect()->route('admin.currencies.index')->with('success', 'Currency created.');
    }

    public function edit(Currency $currency): View
    {
        Gate::authorize('currencies.update');

        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency): RedirectResponse
    {
        Gate::authorize('currencies.update');

        $currency->update($this->validated($request, $currency));

        return redirect()->route('admin.currencies.index')->with('success', 'Currency updated.');
    }

    public function destroy(Currency $currency): RedirectResponse
    {
        Gate::authorize('currencies.delete');

        $currency->delete();

        return redirect()->route('admin.currencies.index')->with('success', 'Currency deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Currency $currency = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'size:3', 'unique:currencies,code,'.($currency?->id ?? 'NULL')],
            'symbol' => ['required', 'string', 'max:8'],
            'decimals' => ['required', 'integer', 'min:0', 'max:6'],
            'rate_to_base' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
