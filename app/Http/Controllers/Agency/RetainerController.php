<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Retainer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RetainerController extends Controller
{
    public function index(): View
    {
        $retainers = Retainer::with('client')->latest()->paginate(12);

        $stats = [
            'active'   => Retainer::where('status', 'active')->count(),
            'monthly'  => Retainer::where('status', 'active')->sum('monthly_value'),
            'paused'   => Retainer::where('status', 'paused')->count(),
        ];

        return view('agency.retainers.index', compact('retainers', 'stats'));
    }

    public function create(): View
    {
        $clients = Client::orderBy('name')->get();

        return view('agency.retainers.create', compact('clients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id'     => ['required', 'exists:clients,id'],
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'monthly_value' => ['nullable', 'numeric', 'min:0'],
            'hours_included'=> ['nullable', 'integer', 'min:0'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['nullable', 'date', 'after:start_date'],
            'status'        => ['required', 'in:draft,active,paused,cancelled,completed'],
            'billing_cycle' => ['required', 'in:monthly,quarterly,annually'],
        ]);

        Retainer::create($validated);

        return redirect()->route('agency.retainers.index')
            ->with('success', 'Retainer created.');
    }

    public function show(Retainer $retainer): View
    {
        $retainer->load(['client', 'projects']);

        return view('agency.retainers.show', compact('retainer'));
    }

    public function edit(Retainer $retainer): View
    {
        $clients = Client::orderBy('name')->get();

        return view('agency.retainers.edit', compact('retainer', 'clients'));
    }

    public function update(Request $request, Retainer $retainer): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'monthly_value' => ['nullable', 'numeric', 'min:0'],
            'hours_included'=> ['nullable', 'integer', 'min:0'],
            'end_date'      => ['nullable', 'date'],
            'status'        => ['required', 'in:draft,active,paused,cancelled,completed'],
        ]);

        $retainer->update($validated);

        return redirect()->route('agency.retainers.show', $retainer)
            ->with('success', 'Retainer updated.');
    }

    public function destroy(Retainer $retainer): RedirectResponse
    {
        $retainer->delete();

        return redirect()->route('agency.retainers.index')
            ->with('success', 'Retainer deleted.');
    }
}
