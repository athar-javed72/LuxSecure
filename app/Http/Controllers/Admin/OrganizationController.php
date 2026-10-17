<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(): View
    {
        Gate::authorize('organizations.view');

        $organizations = Organization::query()
            ->with(['country', 'owner'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.organizations.index', compact('organizations'));
    }

    public function create(): View
    {
        Gate::authorize('organizations.create');

        return view('admin.organizations.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('organizations.create');

        Organization::create($this->validated($request));

        return redirect()->route('admin.organizations.index')->with('success', 'Organization created.');
    }

    public function edit(Organization $organization): View
    {
        Gate::authorize('organizations.update');

        return view('admin.organizations.edit', array_merge(
            ['organization' => $organization],
            $this->formData()
        ));
    }

    public function update(Request $request, Organization $organization): RedirectResponse
    {
        Gate::authorize('organizations.update');

        $organization->update($this->validated($request, $organization));

        return redirect()->route('admin.organizations.index')->with('success', 'Organization updated.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        Gate::authorize('organizations.delete');

        if ($organization->branches()->exists()) {
            return back()->with('error', 'Cannot delete an organization that still has branches.');
        }

        $organization->delete();

        return redirect()->route('admin.organizations.index')->with('success', 'Organization deleted.');
    }

    /**
     * @return array{countries: \Illuminate\Support\Collection, owners: \Illuminate\Support\Collection}
     */
    private function formData(): array
    {
        return [
            'countries' => Country::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id'),
            'owners' => User::query()->orderBy('name')->limit(200)->pluck('name', 'id'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Organization $organization = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:180', 'unique:organizations,slug,'.($organization?->id ?? 'NULL')],
            'description' => ['nullable', 'string', 'max:5000'],
            'logo' => ['nullable', 'string', 'max:255'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
