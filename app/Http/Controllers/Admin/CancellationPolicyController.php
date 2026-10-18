<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CancellationPolicy;
use App\Models\CancellationPolicyRule;
use App\Models\Country;
use App\Models\Organization;
use App\Models\User;
use App\Services\Payments\RefundCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CancellationPolicyController extends Controller
{
    public function __construct(private readonly RefundCalculator $refunds) {}

    public function index(Request $request): View
    {
        Gate::authorize('cancellation_policies.view');

        $user = $request->user();
        $query = CancellationPolicy::query()
            ->with(['country', 'organization'])
            ->withCount('rules')
            ->orderBy('name');

        if ($user->hasRole('organization_admin') && $user->organization_id) {
            $query->where('organization_id', $user->organization_id);
        } elseif ($user->isCountryScoped()) {
            $query->where(function ($q) use ($user) {
                $q->where('country_id', $user->country_id)
                    ->orWhereNull('country_id');
            });
        }

        return view('admin.cancellation-policies.index', [
            'policies' => $query->paginate(20),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('cancellation_policies.create');

        return view('admin.cancellation-policies.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('cancellation_policies.create');

        $data = $this->validated($request);

        DB::transaction(function () use ($request, $data) {
            $policy = CancellationPolicy::create($data);
            $this->syncRules($policy, $request->input('rules', []));
        });

        return redirect()->route('admin.cancellation-policies.index')->with('success', 'Cancellation policy created.');
    }

    public function edit(CancellationPolicy $cancellation_policy): View
    {
        Gate::authorize('cancellation_policies.update');
        $this->assertPolicyAccess(request()->user(), $cancellation_policy);

        $cancellation_policy->load('rules');
        $preview = $this->refunds->previewTable($cancellation_policy);

        return view('admin.cancellation-policies.edit', array_merge(
            ['policy' => $cancellation_policy, 'preview' => $preview],
            $this->formData()
        ));
    }

    public function update(Request $request, CancellationPolicy $cancellation_policy): RedirectResponse
    {
        Gate::authorize('cancellation_policies.update');
        $this->assertPolicyAccess($request->user(), $cancellation_policy);

        $data = $this->validated($request, $cancellation_policy);

        DB::transaction(function () use ($request, $cancellation_policy, $data) {
            $cancellation_policy->update($data);
            $this->syncRules($cancellation_policy, $request->input('rules', []));
        });

        return redirect()->route('admin.cancellation-policies.edit', $cancellation_policy)
            ->with('success', 'Cancellation policy updated.');
    }

    public function destroy(CancellationPolicy $cancellation_policy): RedirectResponse
    {
        Gate::authorize('cancellation_policies.delete');
        $this->assertPolicyAccess(request()->user(), $cancellation_policy);

        $cancellation_policy->delete();

        return redirect()->route('admin.cancellation-policies.index')->with('success', 'Cancellation policy deleted.');
    }

    public function preview(CancellationPolicy $cancellation_policy): View
    {
        Gate::authorize('cancellation_policies.view');
        $this->assertPolicyAccess(request()->user(), $cancellation_policy);

        $cancellation_policy->load('rules');
        $preview = $this->refunds->previewTable($cancellation_policy);

        return view('admin.cancellation-policies.preview', [
            'policy' => $cancellation_policy,
            'preview' => $preview,
        ]);
    }

    /**
     * @return array{countries: Collection, organizations: Collection}
     */
    private function formData(): array
    {
        $user = request()->user();
        $countries = Country::query()->orderBy('name');
        $organizations = Organization::query()->orderBy('name');

        if ($user?->hasRole('organization_admin') && $user->organization_id) {
            $organizations->where('id', $user->organization_id);
        } elseif ($user?->isCountryScoped()) {
            $countries->where('id', $user->country_id);
            $organizations->where('country_id', $user->country_id);
        }

        return [
            'countries' => $countries->pluck('name', 'id'),
            'organizations' => $organizations->pluck('name', 'id'),
        ];
    }

    private function assertPolicyAccess(?User $user, CancellationPolicy $policy): void
    {
        if (! $user) {
            abort(403);
        }

        if ($user->hasRole('organization_admin')) {
            abort_unless(
                $policy->organization_id
                && (int) $policy->organization_id === (int) $user->organization_id,
                403
            );

            return;
        }

        if ($user->isCountryScoped() && $policy->country_id) {
            abort_unless((int) $policy->country_id === (int) $user->country_id, 403);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?CancellationPolicy $policy = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:120'],
            'applies_to' => ['required', 'in:hotel,rent,sale'],
            'reference_date' => ['required', 'in:check_in,move_in,agreement_date'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_default' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'rules' => ['nullable', 'array'],
            'rules.*.days_before_min' => ['required_with:rules', 'integer', 'min:0'],
            'rules.*.refund_percent' => ['required_with:rules', 'numeric', 'min:0', 'max:100'],
            'rules.*.fee_fixed' => ['nullable', 'numeric', 'min:0'],
            'rules.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_default'] = $request->boolean('is_default');
        $data['is_active'] = $request->boolean('is_active', true);

        return collect($data)->except('rules')->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function syncRules(CancellationPolicy $policy, array $rows): void
    {
        $keepIds = [];

        foreach (array_values($rows) as $index => $row) {
            if (! isset($row['days_before_min'], $row['refund_percent'])) {
                continue;
            }

            $payload = [
                'cancellation_policy_id' => $policy->id,
                'days_before_min' => (int) $row['days_before_min'],
                'refund_percent' => $row['refund_percent'],
                'fee_fixed' => $row['fee_fixed'] !== null && $row['fee_fixed'] !== '' ? $row['fee_fixed'] : null,
                'sort_order' => (int) ($row['sort_order'] ?? $index),
            ];

            if (! empty($row['id'])) {
                $rule = CancellationPolicyRule::query()
                    ->where('cancellation_policy_id', $policy->id)
                    ->where('id', $row['id'])
                    ->first();
                if ($rule) {
                    $rule->update($payload);
                    $keepIds[] = $rule->id;

                    continue;
                }
            }

            $keepIds[] = CancellationPolicyRule::create($payload)->id;
        }

        CancellationPolicyRule::query()
            ->where('cancellation_policy_id', $policy->id)
            ->whereNotIn('id', $keepIds !== [] ? $keepIds : [0])
            ->delete();
    }
}
