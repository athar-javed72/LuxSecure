<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Tenancy;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenancyController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Tenancy::class);

        $tenancies = Tenancy::query()
            ->with(['listing', 'landlord', 'payments'])
            ->where('tenant_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('client.tenancies.index', compact('tenancies'));
    }

    public function show(Request $request, Tenancy $tenancy): View
    {
        $this->authorize('view', $tenancy);
        abort_unless((int) $tenancy->tenant_id === (int) $request->user()->id, 403);

        $tenancy->load(['listing', 'landlord', 'payments']);

        return view('client.tenancies.show', compact('tenancy'));
    }
}
