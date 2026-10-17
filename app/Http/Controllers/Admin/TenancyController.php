<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTenancyRequest;
use App\Http\Requests\Admin\UpdateTenancyRequest;
use App\Mail\TenancyCreatedMail;
use App\Models\Listing;
use App\Models\Tenancy;
use App\Models\User;
use App\Services\Rent\RentScheduleGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class TenancyController extends Controller
{
    public function __construct(
        private readonly RentScheduleGenerator $schedule
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Tenancy::class);

        $query = Tenancy::query()->with(['listing', 'tenant', 'landlord']);

        if (! $request->user()->can('tenancies.view.any')) {
            $query->where('landlord_id', $request->user()->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tenancies = $query->latest()->paginate(15)->withQueryString();

        return view('admin.tenancies.index', compact('tenancies'));
    }

    public function overdue(Request $request): View
    {
        $this->authorize('viewAny', Tenancy::class);

        $query = Tenancy::query()
            ->with(['listing', 'tenant', 'payments' => fn ($q) => $q->whereIn('status', ['pending', 'overdue'])->whereDate('due_date', '<', now()->toDateString())])
            ->whereHas('payments', fn ($q) => $q->whereIn('status', ['pending', 'overdue'])->whereDate('due_date', '<', now()->toDateString()));

        if (! $request->user()->can('tenancies.view.any')) {
            $query->where('landlord_id', $request->user()->id);
        }

        $tenancies = $query->latest()->paginate(15);

        return view('admin.tenancies.overdue', compact('tenancies'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Tenancy::class);

        $listings = $this->scopedListings($request->user());
        $tenants = User::role('client')->orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.tenancies.create', compact('listings', 'tenants'));
    }

    public function store(StoreTenancyRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $listing = Listing::findOrFail($data['listing_id']);

        if (! $request->user()->can('listings.view.any') && ! $request->user()->ownsListing($listing)) {
            abort(403);
        }

        $tenancy = Tenancy::create([
            'listing_id' => $listing->id,
            'tenant_id' => $data['tenant_id'],
            'landlord_id' => $data['landlord_id'] ?? $listing->owner_id ?? $request->user()->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'monthly_rent' => $data['monthly_rent'],
            'security_deposit' => $data['security_deposit'] ?? 0,
            'status' => $data['status'] ?? 'active',
        ]);

        $this->schedule->createFor($tenancy);

        $tenancy->load(['tenant', 'listing']);
        Mail::to($tenancy->tenant->email)->queue(new TenancyCreatedMail($tenancy));

        return redirect()
            ->route('admin.tenancies.show', $tenancy)
            ->with('success', 'Tenancy created and rent schedule generated.');
    }

    public function show(Tenancy $tenancy): View
    {
        $this->authorize('view', $tenancy);
        $tenancy->load(['listing', 'tenant', 'landlord', 'payments']);

        return view('admin.tenancies.show', compact('tenancy'));
    }

    public function edit(Tenancy $tenancy): View
    {
        $this->authorize('update', $tenancy);

        return view('admin.tenancies.edit', compact('tenancy'));
    }

    public function update(UpdateTenancyRequest $request, Tenancy $tenancy): RedirectResponse
    {
        $tenancy->update($request->validated());

        return redirect()
            ->route('admin.tenancies.show', $tenancy)
            ->with('success', 'Tenancy updated.');
    }

    public function destroy(Tenancy $tenancy): RedirectResponse
    {
        $this->authorize('delete', $tenancy);
        $tenancy->delete();

        return redirect()
            ->route('admin.tenancies.index')
            ->with('success', 'Tenancy deleted.');
    }

    private function scopedListings(User $user)
    {
        $query = Listing::query()->orderBy('title');

        if (! $user->can('listings.view.any')) {
            $query->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)->orWhere('agent_id', $user->id);
            });
        }

        return $query->get(['id', 'title', 'owner_id', 'agent_id']);
    }
}
