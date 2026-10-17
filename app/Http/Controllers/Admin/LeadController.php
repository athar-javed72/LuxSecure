<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use App\Models\Lead;
use App\Models\User;
use App\Support\CsvExporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Lead::class);

        $query = Lead::query()->with(['listing', 'assignedAgent'])->latest();

        if (! $request->user()->can('leads.view.any')) {
            $query->where('assigned_agent_id', $request->user()->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return view('admin.leads.index', [
            'leads' => $query->paginate(20)->withQueryString(),
            'agents' => User::role('agent')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function convert(ContactInquiry $inquiry): RedirectResponse
    {
        $this->authorize('create', Lead::class);

        $lead = Lead::query()->firstOrCreate(
            ['contact_inquiry_id' => $inquiry->id],
            [
                'listing_id' => $inquiry->listing_id,
                'assigned_agent_id' => $inquiry->listing?->agent_id,
                'name' => $inquiry->name,
                'email' => $inquiry->email,
                'status' => 'new',
                'notes' => $inquiry->message,
            ]
        );

        return redirect()->route('admin.leads.edit', $lead)->with('success', 'Inquiry converted to lead.');
    }

    public function edit(Lead $lead): View
    {
        $this->authorize('view', $lead);
        $lead->load(['listing', 'assignedAgent', 'inquiry']);

        return view('admin.leads.edit', [
            'lead' => $lead,
            'agents' => User::role('agent')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', Lead::STATUSES)],
            'assigned_agent_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
            'follow_up_at' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        $lead->update($data);

        return back()->with('success', 'Lead updated.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $this->authorize('delete', $lead);
        $lead->delete();

        return redirect()->route('admin.leads.index')->with('success', 'Lead deleted.');
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Lead::class);
        abort_unless($request->user()->can('reports.export') || $request->user()->can('leads.view.any') || $request->user()->can('leads.view.own'), 403);

        $query = Lead::query()->with(['listing', 'assignedAgent']);
        if (! $request->user()->can('leads.view.any')) {
            $query->where('assigned_agent_id', $request->user()->id);
        }

        $rows = $query->latest()->get()->map(fn (Lead $l) => [
            'id' => $l->id,
            'name' => $l->name,
            'email' => $l->email,
            'phone' => $l->phone,
            'status' => $l->status,
            'listing' => $l->listing?->title,
            'agent' => $l->assignedAgent?->name,
            'follow_up_at' => optional($l->follow_up_at)->toDateTimeString(),
        ]);

        return CsvExporter::download('leads.csv', ['id', 'name', 'email', 'phone', 'status', 'listing', 'agent', 'follow_up_at'], $rows);
    }

    public function bulk(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', Lead::class);

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:leads,id'],
            'action' => ['required', 'in:delete,status,assign'],
            'status' => ['nullable', 'in:'.implode(',', Lead::STATUSES)],
            'assigned_agent_id' => ['nullable', 'exists:users,id'],
        ]);

        $leads = Lead::query()->whereIn('id', $data['ids'])->get();

        foreach ($leads as $lead) {
            if (! $request->user()->can('update', $lead) && $data['action'] !== 'delete') {
                continue;
            }
            if ($data['action'] === 'delete' && $request->user()->can('delete', $lead)) {
                $lead->delete();
            } elseif ($data['action'] === 'status' && ! empty($data['status'])) {
                $lead->update(['status' => $data['status']]);
            } elseif ($data['action'] === 'assign') {
                $lead->update(['assigned_agent_id' => $data['assigned_agent_id'] ?? null]);
            }
        }

        return back()->with('success', 'Bulk action applied.');
    }
}
