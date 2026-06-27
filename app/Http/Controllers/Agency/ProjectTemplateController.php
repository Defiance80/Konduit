<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Deliverable;
use App\Models\Project;
use App\Models\ProjectTemplate;
use App\Models\Task;
use App\Models\TaskSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectTemplateController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $templates = ProjectTemplate::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('agency.project-templates.index', compact('templates'));
    }

    public function create(): \Illuminate\View\View
    {
        return view('agency.project-templates.create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'estimated_days' => 'nullable|integer|min:1',
        ]);

        $sections = [];
        $rawSections = $request->input('sections', []);
        foreach ($rawSections as $sec) {
            if (empty($sec['name'])) continue;
            $tasks = [];
            foreach ($sec['tasks'] ?? [] as $task) {
                if (empty($task['title'])) continue;
                $tasks[] = [
                    'title'            => $task['title'],
                    'estimated_hours'  => (int) ($task['estimated_hours'] ?? 0),
                ];
            }
            $sections[] = ['name' => $sec['name'], 'tasks' => $tasks];
        }

        $deliverables = array_filter(
            array_map('trim', explode("\n", $request->input('deliverable_names', '')))
        );

        ProjectTemplate::create([
            'tenant_id'         => Auth::user()->tenant_id,
            'name'              => $request->name,
            'description'       => $request->description,
            'estimated_days'    => $request->estimated_days,
            'task_sections'     => $sections,
            'deliverable_names' => array_values($deliverables),
        ]);

        return redirect()->route('agency.project-templates.index')->with('success', 'Template created.');
    }

    public function show(ProjectTemplate $projectTemplate): \Illuminate\View\View
    {
        return view('agency.project-templates.show', ['template' => $projectTemplate]);
    }

    public function destroy(ProjectTemplate $projectTemplate): \Illuminate\Http\RedirectResponse
    {
        $projectTemplate->delete();
        return redirect()->route('agency.project-templates.index')->with('success', 'Template deleted.');
    }

    public function apply(Request $request, ProjectTemplate $projectTemplate): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['project_id' => 'required|exists:projects,id']);
        $project   = Project::findOrFail($request->project_id);
        $tenantId  = Auth::user()->tenant_id;
        $createdBy = Auth::id();

        foreach ($projectTemplate->task_sections ?? [] as $sectionData) {
            $section = TaskSection::create([
                'tenant_id'  => $tenantId,
                'project_id' => $project->id,
                'name'       => $sectionData['name'],
            ]);

            foreach ($sectionData['tasks'] ?? [] as $taskData) {
                Task::create([
                    'tenant_id'        => $tenantId,
                    'project_id'       => $project->id,
                    'section_id'       => $section->id,
                    'title'            => $taskData['title'],
                    'estimated_hours'  => $taskData['estimated_hours'] ?? null,
                    'status'           => 'todo',
                    'created_by'       => $createdBy,
                    'assigned_to'      => null,
                ]);
            }
        }

        foreach ($projectTemplate->deliverable_names ?? [] as $deliverableName) {
            Deliverable::create([
                'tenant_id'  => $tenantId,
                'project_id' => $project->id,
                'name'       => $deliverableName,
                'status'     => 'draft',
            ]);
        }

        return redirect()->route('agency.projects.show', $project)
            ->with('success', "Template \"{$projectTemplate->name}\" applied — {$projectTemplate->task_count} tasks and " . count($projectTemplate->deliverable_names ?? []) . " deliverables created.");
    }
}
