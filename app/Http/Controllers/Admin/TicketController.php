<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Notifications\TicketReplyNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Ticket::class);

        $query = Ticket::query()->with(['user', 'assignee'])->latest();

        if (! $request->user()->can('tickets.view.any')) {
            $query->where(function ($q) use ($request) {
                $q->where('user_id', $request->user()->id)
                    ->orWhere('assigned_to', $request->user()->id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return view('admin.tickets.index', [
            'tickets' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Ticket::class);

        return view('admin.tickets.create', [
            'supportUsers' => User::role('support')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Ticket::class);

        $data = $request->validate([
            'subject' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'priority' => ['required', 'in:'.implode(',', Ticket::PRIORITIES)],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $ticket = Ticket::create([
            ...$data,
            'user_id' => $request->user()->id,
            'status' => 'open',
        ]);

        return redirect()->route('admin.tickets.show', $ticket)->with('success', 'Ticket created.');
    }

    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);
        $ticket->load(['user', 'assignee', 'replies.user']);

        return view('admin.tickets.show', [
            'ticket' => $ticket,
            'supportUsers' => User::role(['support', 'admin', 'super_admin'])->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('update', $ticket);

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', Ticket::STATUSES)],
            'priority' => ['required', 'in:'.implode(',', Ticket::PRIORITIES)],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $ticket->update($data);

        return back()->with('success', 'Ticket updated.');
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('update', $ticket);

        $data = $request->validate([
            'body' => ['required', 'string'],
            'is_internal' => ['sometimes', 'boolean'],
        ]);

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'body' => $data['body'],
            'is_internal' => $request->boolean('is_internal'),
        ]);

        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'pending']);
        }

        if (! $reply->is_internal && (int) $ticket->user_id !== (int) $request->user()->id) {
            $ticket->user?->notify(new TicketReplyNotification($ticket, $reply));
        }

        return back()->with('success', 'Reply added.');
    }
}
