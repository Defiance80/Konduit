<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $client = auth()->user()->client;

        $tickets = Ticket::where('client_id', $client->id)
            ->with(['project', 'assignee'])
            ->latest()
            ->paginate(15);

        $stats = [
            'open'        => Ticket::where('client_id', $client->id)->whereIn('status', ['open','in_progress'])->count(),
            'waiting'     => Ticket::where('client_id', $client->id)->where('status', 'waiting')->count(),
            'resolved'    => Ticket::where('client_id', $client->id)->whereIn('status', ['resolved','closed'])->count(),
        ];

        return view('client.tickets.index', compact('tickets', 'stats'));
    }

    public function create()
    {
        $client   = auth()->user()->client;
        $projects = Project::where('client_id', $client->id)->where('status', 'active')->get();
        return view('client.tickets.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $client = auth()->user()->client;

        $validated = $request->validate([
            'subject'    => 'required|string|max:255',
            'description'=> 'required|string',
            'type'       => 'required|in:bug,feature,question,design,content,general',
            'priority'   => 'required|in:low,medium,high,urgent',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $validated['client_id']    = $client->id;
        $validated['tenant_id']    = auth()->user()->tenant_id;
        $validated['submitted_by'] = auth()->id();
        $validated['status']       = 'open';

        Ticket::create($validated);

        return redirect()->route('client.tickets.index')
            ->with('success', 'Your request has been submitted. We\'ll be in touch shortly.');
    }

    public function show(Ticket $ticket)
    {
        abort_unless($ticket->client_id === auth()->user()->client_id, 403);

        // Strip internal_notes — clients never see these
        $ticket->load(['project', 'assignee', 'comments.user']);

        // Only show comments not marked internal
        $publicComments = $ticket->comments->where('is_internal', false);

        return view('client.tickets.show', compact('ticket', 'publicComments'));
    }

    public function comment(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->client_id === auth()->user()->client_id, 403);

        $request->validate(['body' => 'required|string|max:5000']);

        TicketComment::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => auth()->id(),
            'body'        => $request->body,
            'is_internal' => false,
        ]);

        return back()->with('success', 'Reply sent.');
    }
}
