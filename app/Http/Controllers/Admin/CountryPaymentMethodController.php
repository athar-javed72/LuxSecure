<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CountryPaymentMethod;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CountryPaymentMethodController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('countries.view');

        $countries = Country::query()->orderBy('name')->get();
        $countryId = $request->integer('country_id') ?: $countries->first()?->id;

        $methods = collect();
        if ($countryId) {
            $existing = CountryPaymentMethod::query()
                ->where('country_id', $countryId)
                ->get()
                ->keyBy('gateway');

            $methods = collect(Payment::GATEWAYS)->map(function (string $gateway) use ($countryId, $existing) {
                return $existing->get($gateway) ?? new CountryPaymentMethod([
                    'country_id' => $countryId,
                    'gateway' => $gateway,
                    'is_enabled' => false,
                ]);
            });
        }

        return view('admin.country-payment-methods.index', [
            'countries' => $countries->pluck('name', 'id'),
            'countryId' => $countryId,
            'methods' => $methods,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        Gate::authorize('countries.update');

        $data = $request->validate([
            'country_id' => ['required', 'exists:countries,id'],
            'methods' => ['required', 'array'],
            'methods.*.gateway' => ['required', 'string', 'in:'.implode(',', Payment::GATEWAYS)],
            'methods.*.is_enabled' => ['sometimes', 'boolean'],
        ]);

        foreach ($data['methods'] as $row) {
            CountryPaymentMethod::query()->updateOrCreate(
                [
                    'country_id' => $data['country_id'],
                    'gateway' => $row['gateway'],
                ],
                [
                    'is_enabled' => filter_var($row['is_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ]
            );
        }

        return redirect()
            ->route('admin.country-payment-methods.index', ['country_id' => $data['country_id']])
            ->with('success', 'Payment methods updated.');
    }
}
