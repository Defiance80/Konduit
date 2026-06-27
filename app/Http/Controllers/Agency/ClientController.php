<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = Client::withCount(['projects', 'tickets'])
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
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['nullable', 'email', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:50'],
            'website'  => ['nullable', 'url', 'max:255'],
            'industry' => ['nullable', 'string', 'max:100'],
            'notes'    => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        Client::create($validated);

        return redirect()->route('agency.clients.index')
            ->with('success', 'Client created successfully.');
    }

    public function show(Client $client): View
    {
        $client->load([
            'retainers',
            'projects' => fn($q) => $q->latest()->limit(5),
            'tickets'  => fn($q) => $q->latest()->limit(5),
        ]);

        $aiSummary = $client->aiSummaries()->latest()->first();

        return view('agency.clients.show', compact('client', 'aiSummary'));
    }

    public function edit(Client $client): View
    {
        return view('agency.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['nullable', 'email'],
            'phone'    => ['nullable', 'string', 'max:50'],
            'website'  => ['nullable', 'url'],
            'industry' => ['nullable', 'string', 'max:100'],
            'status'   => ['required', 'in:active,inactive,prospect'],
            'notes'    => ['nullable', 'string'],
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
