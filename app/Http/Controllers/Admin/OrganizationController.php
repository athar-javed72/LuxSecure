<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Organization::class);

        $user = $request->user();
        $query = Organization::query()->with(['country', 'owner'])->orderBy('name');

        if ($user->hasRole('organization_admin')) {
            $query->where('id', $user->organization_id);
        } elseif ($user->isCountryScoped()) {
            $query->where('country_id', $user->country_id);
        }

        return view('admin.organizations.index', [
            'organizations' => $query->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Organization::class);

        return view('admin.organizations.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Organization::class);

        $data = $this->validated($request);
        if ($request->user()->isCountryScoped()) {
            $data['country_id'] = $request->user()->country_id;
        }

        Organization::create($data);

        return redirect()->route('admin.organizations.index')->with('success', 'Organization created.');
    }

    public function edit(Organization $organization): View
    {
        $this->authorize('update', $organization);

        return view('admin.organizations.edit', array_merge(
            ['organization' => $organization],
            $this->formData()
        ));
    }

    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $this->authorize('update', $organization);

        $data = $this->validated($request, $organization);
        if ($request->user()->isCountryScoped()) {
            $data['country_id'] = $request->user()->country_id;
        }

        $organization->update($data);

        return redirect()->route('admin.organizations.index')->with('success', 'Organization updated.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $this->authorize('delete', $organization);

        if ($organization->branches()->exists()) {
            return back()->with('error', 'Cannot delete an organization that still has branches.');
        }

        $organization->delete();

        return redirect()->route('admin.organizations.index')->with('success', 'Organization deleted.');
    }

    /**
     * @return array{countries: Collection, owners: Collection}
     */
    private function formData(): array
    {
        $user = request()->user();
        $countries = Country::query()->where('is_active', true)->orderBy('name');
        if ($user?->isCountryScoped()) {
            $countries->where('id', $user->country_id);
        }

        return [
            'countries' => $countries->pluck('name', 'id'),
            'owners' => User::query()->forUserCountry($user)->orderBy('name')->limit(200)->pluck('name', 'id'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Organization $organization = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:160', 'unique:organizations,slug,'.($organization?->id ?? 'NULL')],
            'description' => ['nullable', 'string', 'max:5000'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
