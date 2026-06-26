<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $query = Ticket::with(['client', 'assignee', 'project']);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }
        if ($request->search) {
            $query->where(fn($q) => $q
                ->where('subject', 'like', "%{$request->search}%")
                ->orWhere('ticket_number', 'like', "%{$request->search}%")
            );
        }

        $tickets = $query->latest()->paginate(20);

        $statusCounts = [
            'all'         => Ticket::count(),
            'open'        => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'waiting'     => Ticket::where('status', 'waiting')->count(),
            'resolved'    => Ticket::where('status', 'resolved')->count(),
        ];

        return view('agency.tickets.index', compact('tickets', 'statusCounts'));
    }

    public function create(): View
    {
        $clients  = Client::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $agents   = User::where('user_type', 'agency_user')->orderBy('name')->get();

        return view('agency.tickets.create', compact('clients', 'projects', 'agents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id'   => ['required', 'exists:clients,id'],
            'project_id'  => ['nullable', 'exists:projects,id'],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'subject'     => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type'        => ['required', 'in:bug,feature,question,task,change_request'],
            'priority'    => ['required', 'in:low,medium,high,urgent'],
        ]);

        $validated['submitted_by'] = auth()->id();

        Ticket::create($validated);

        return redirect()->route('agency.tickets.index')
            ->with('success', 'Ticket created.');
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['client', 'project', 'assignee', 'submittedBy', 'comments.user']);
        $agents = User::where('user_type', 'agency_user')->orderBy('name')->get();

        return view('agency.tickets.show', compact('ticket', 'agents'));
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'status'      => ['sometimes', 'in:open,in_progress,waiting,resolved,closed'],
            'priority'    => ['sometimes', 'in:low,medium,high,urgent'],
            'assignee_id' => ['nullable', 'exists:users,id'],
        ]);

        if (isset($validated['status']) && $validated['status'] === 'resolved' && ! $ticket->resolved_at) {
            $validated['resolved_at'] = now();
        }

        $ticket->update($validated);

        return back()->with('success', 'Ticket updated.');
    }

    public function comment(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'body'        => ['required', 'string'],
            'is_internal' => ['boolean'],
        ]);

        $validated['user_id'] = auth()->id();

        $ticket->comments()->create($validated);

        return back()->with('success', 'Comment added.');
    }
}
