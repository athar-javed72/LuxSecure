<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\KycDocumentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KycDocumentTypeController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('countries.view');

        $countryId = $request->integer('country_id') ?: null;

        $types = KycDocumentType::query()
            ->with('country')
            ->when($countryId, fn ($q) => $q->where('country_id', $countryId))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.kyc-document-types.index', [
            'types' => $types,
            'countries' => Country::query()->orderBy('name')->pluck('name', 'id'),
            'countryId' => $countryId,
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('countries.update');

        return view('admin.kyc-document-types.create', [
            'countries' => Country::query()->orderBy('name')->pluck('name', 'id'),
            'countryId' => $request->integer('country_id') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('countries.update');

        $type = KycDocumentType::create($this->validated($request));

        return redirect()
            ->route('admin.kyc-document-types.index', ['country_id' => $type->country_id])
            ->with('success', 'KYC document type created.');
    }

    public function edit(KycDocumentType $kyc_document_type): View
    {
        Gate::authorize('countries.update');

        return view('admin.kyc-document-types.edit', [
            'type' => $kyc_document_type,
            'countries' => Country::query()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, KycDocumentType $kyc_document_type): RedirectResponse
    {
        Gate::authorize('countries.update');

        $kyc_document_type->update($this->validated($request, $kyc_document_type));

        return redirect()
            ->route('admin.kyc-document-types.index', ['country_id' => $kyc_document_type->country_id])
            ->with('success', 'KYC document type updated.');
    }

    public function destroy(KycDocumentType $kyc_document_type): RedirectResponse
    {
        Gate::authorize('countries.update');

        $countryId = $kyc_document_type->country_id;
        $kyc_document_type->delete();

        return redirect()
            ->route('admin.kyc-document-types.index', ['country_id' => $countryId])
            ->with('success', 'KYC document type deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?KycDocumentType $type = null): array
    {
        $data = $request->validate([
            'country_id' => ['required', 'exists:countries,id'],
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:60'],
            'for_roles' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_required' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $roles = array_values(array_filter(array_map('trim', explode(',', (string) ($data['for_roles'] ?? '')))));
        $data['for_roles'] = $roles !== [] ? $roles : null;
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_required'] = $request->boolean('is_required');
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
