<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\Retainer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::with(['client', 'owner'])
            ->latest()
            ->paginate(12);

        $statusCounts = [
            'all'       => Project::count(),
            'active'    => Project::where('status', 'active')->count(),
            'on_hold'   => Project::where('status', 'on_hold')->count(),
            'completed' => Project::where('status', 'completed')->count(),
        ];

        return view('agency.projects.index', compact('projects', 'statusCounts'));
    }

    public function create(): View
    {
        $clients  = Client::orderBy('name')->get();
        $retainers = Retainer::with('client')->where('status', 'active')->get();

        return view('agency.projects.create', compact('clients', 'retainers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id'   => ['required', 'exists:clients,id'],
            'retainer_id' => ['nullable', 'exists:retainers,id'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status'      => ['required', 'in:draft,active,on_hold,completed,cancelled'],
            'priority'    => ['required', 'in:low,medium,high,urgent'],
            'budget'      => ['nullable', 'numeric', 'min:0'],
            'start_date'  => ['nullable', 'date'],
            'due_date'    => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        Project::create($validated);

        return redirect()->route('agency.projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project): View
    {
        $project->load(['client', 'retainer', 'owner', 'tickets', 'deliverables']);

        return view('agency.projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        $clients   = Client::orderBy('name')->get();
        $retainers = Retainer::where('status', 'active')->get();

        return view('agency.projects.edit', compact('project', 'clients', 'retainers'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status'      => ['required', 'in:draft,active,on_hold,completed,cancelled'],
            'priority'    => ['required', 'in:low,medium,high,urgent'],
            'budget'      => ['nullable', 'numeric', 'min:0'],
            'progress'    => ['nullable', 'integer', 'min:0', 'max:100'],
            'due_date'    => ['nullable', 'date'],
        ]);

        $project->update($validated);

        return redirect()->route('agency.projects.show', $project)
            ->with('success', 'Project updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('agency.projects.index')
            ->with('success', 'Project deleted.');
    }
}
