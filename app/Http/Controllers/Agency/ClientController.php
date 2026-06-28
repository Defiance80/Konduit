<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientHealthScore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = Client::withCount(['projects', 'tickets'])
            ->with(['retainers' => fn($q) => $q->where('status', 'active'), 'healthScore'])
            ->latest()
            ->paginate(12);

        return view('agency.clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('agency.clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['nullable', 'email', 'max:255'],
            'phone'                 => ['nullable', 'string', 'max:50'],
            'website'               => ['nullable', 'url', 'max:255'],
            'industry'              => ['nullable', 'string', 'max:100'],
            'address'               => ['nullable', 'string', 'max:255'],
            'contact_person'        => ['nullable', 'string', 'max:100'],
            'contact_person_email'  => ['nullable', 'email', 'max:255'],
            'contact_person_phone'  => ['nullable', 'string', 'max:50'],
            'services_interested'   => ['nullable', 'array'],
            'notes'                 => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);

        Client::create($validated);

        return redirect()->route('agency.clients.index')
            ->with('success', 'Client created successfully.');
    }

    public function show(Client $client): View
    {
        $client->load([
            'retainers',
            'projects'  => fn($q) => $q->with('client')->orderByDesc('created_at'),
            'tickets'   => fn($q) => $q->orderByDesc('created_at')->limit(10),
            'invoices'  => fn($q) => $q->orderByDesc('created_at')->limit(10),
            'documents' => fn($q) => $q->with('uploader')->latest(),
            'healthScore',
        ]);

        $aiSummary  = $client->aiSummaries()->latest()->first();
        $retainer   = $client->retainers->where('status', 'active')->first();
        $deliverables = \App\Models\Deliverable::where('client_id', $client->id)
            ->whereIn('status', ['pending_approval', 'in_review'])
            ->with('project')
            ->latest()
            ->limit(5)
            ->get();

        return view('agency.clients.show', compact('client', 'aiSummary', 'retainer', 'deliverables'));
    }

    public function edit(Client $client): View
    {
        return view('agency.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['nullable', 'email'],
            'phone'                 => ['nullable', 'string', 'max:50'],
            'website'               => ['nullable', 'url'],
            'industry'              => ['nullable', 'string', 'max:100'],
            'status'                => ['required', 'in:active,inactive,prospect'],
            'address'               => ['nullable', 'string', 'max:255'],
            'contact_person'        => ['nullable', 'string', 'max:100'],
            'contact_person_email'  => ['nullable', 'email', 'max:255'],
            'contact_person_phone'  => ['nullable', 'string', 'max:50'],
            'services_interested'   => ['nullable', 'array'],
            'notes'                 => ['nullable', 'string'],
        ]);

        $client->update($validated);

        return redirect()->route('agency.clients.show', $client)
            ->with('success', 'Client updated.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('agency.clients.index')
            ->with('success', 'Client removed.');
    }
}
