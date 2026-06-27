<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskSection;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['project', 'client', 'assignee', 'subtasks'])
            ->topLevel()
            ->orderBy('due_date')
            ->orderBy('priority');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('assignee')) {
            $query->where('assignee_id', $request->assignee);
        }
        if ($request->filled('project')) {
            $query->where('project_id', $request->project);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        // Group by status for list view
        $tasks = $query->get();

        $grouped = $tasks->groupBy(fn ($t) => match ($t->status) {
            'in_progress' => 'In Progress',
            'review'      => 'In Review',
            'done'        => 'Done',
            default       => 'To Do',
        });

        // Order the groups
        $groupOrder = ['To Do', 'In Progress', 'In Review', 'Done'];
        $grouped = collect($groupOrder)->mapWithKeys(fn ($g) => [$g => $grouped->get($g, collect())]);

        $teamMembers = User::where('tenant_id', auth()->user()->tenant_id)
            ->where('user_type', 'agency_user')
            ->orderBy('name')
            ->get();

        $projects = Project::orderBy('name')->get();
        $clients  = Client::orderBy('name')->get();

        $stats = [
            'todo'        => Task::topLevel()->where('status', 'todo')->count(),
            'in_progress' => Task::topLevel()->where('status', 'in_progress')->count(),
            'review'      => Task::topLevel()->where('status', 'review')->count(),
            'done'        => Task::topLevel()->where('status', 'done')->count(),
            'overdue'     => Task::topLevel()->where('status', '!=', 'done')->whereNotNull('due_date')->where('due_date', '<', now()->toDateString())->count(),
        ];

        return view('agency.tasks.index', compact('grouped', 'teamMembers', 'projects', 'clients', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:500',
            'description'    => 'nullable|string',
            'project_id'     => 'nullable|exists:projects,id',
            'section_id'     => 'nullable|exists:task_sections,id',
            'client_id'      => 'nullable|exists:clients,id',
            'assignee_id'    => 'nullable|exists:users,id',
            'parent_task_id' => 'nullable|exists:tasks,id',
            'status'         => 'required|in:todo,in_progress,review,done',
            'priority'       => 'required|in:none,low,medium,high,urgent',
            'due_date'       => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999',
            'tags'           => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        if (!empty($validated['tags'])) {
            $validated['tags'] = array_filter(array_map('trim', explode(',', $validated['tags'])));
        }

        $task = Task::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['task' => $task->load(['assignee', 'project', 'client'])]);
        }

        $redirectTo = $request->input('redirect', route('agency.tasks.index'));
        return redirect($redirectTo)->with('success', 'Task created.');
    }

    public function show(Task $task)
    {
        $task->load(['project', 'client', 'assignee', 'creator', 'section', 'subtasks.assignee', 'parent']);

        $teamMembers = User::where('tenant_id', auth()->user()->tenant_id)
            ->where('user_type', 'agency_user')
            ->orderBy('name')
            ->get();

        $projects = Project::orderBy('name')->get();
        $clients  = Client::orderBy('name')->get();

        return view('agency.tasks.show', compact('task', 'teamMembers', 'projects', 'clients'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title'           => 'sometimes|required|string|max:500',
            'description'     => 'nullable|string',
            'project_id'      => 'nullable|exists:projects,id',
            'section_id'      => 'nullable|exists:task_sections,id',
            'client_id'       => 'nullable|exists:clients,id',
            'assignee_id'     => 'nullable|exists:users,id',
            'status'          => 'sometimes|in:todo,in_progress,review,done',
            'priority'        => 'sometimes|in:none,low,medium,high,urgent',
            'due_date'        => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999',
            'tags'            => 'nullable|string',
        ]);

        if (isset($validated['tags']) && is_string($validated['tags'])) {
            $validated['tags'] = array_filter(array_map('trim', explode(',', $validated['tags'])));
        }

        // Auto-set completed_at when status becomes done
        if (isset($validated['status'])) {
            if ($validated['status'] === 'done' && !$task->completed_at) {
                $validated['completed_at'] = now();
            } elseif ($validated['status'] !== 'done') {
                $validated['completed_at'] = null;
            }
        }

        $task->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['task' => $task->fresh()->load(['assignee', 'project'])]);
        }

        return back()->with('success', 'Task updated.');
    }

    public function destroy(Task $task)
    {
        $redirectTo = $task->project_id
            ? route('agency.projects.show', $task->project)
            : route('agency.tasks.index');

        $task->delete();

        return redirect($redirectTo)->with('success', 'Task deleted.');
    }

    // Quick status toggle via AJAX
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate(['status' => 'required|in:todo,in_progress,review,done']);

        $task->update([
            'status'       => $request->status,
            'completed_at' => $request->status === 'done' ? now() : null,
        ]);

        return response()->json(['task' => $task->fresh()]);
    }

    // Project board: tasks grouped by section
    public function board(Project $project)
    {
        $sections = TaskSection::where('project_id', $project->id)
            ->with(['tasks' => fn ($q) => $q->with(['assignee'])->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        // Create default sections if none exist
        if ($sections->isEmpty()) {
            $defaults = ['To Do', 'In Progress', 'Review', 'Done'];
            foreach ($defaults as $i => $name) {
                TaskSection::create([
                    'project_id' => $project->id,
                    'name'       => $name,
                    'sort_order' => $i,
                ]);
            }
            $sections = TaskSection::where('project_id', $project->id)
                ->with(['tasks.assignee'])
                ->orderBy('sort_order')
                ->get();
        }

        $teamMembers = User::where('tenant_id', auth()->user()->tenant_id)
            ->where('user_type', 'agency_user')
            ->orderBy('name')
            ->get();

        return view('agency.tasks.board', compact('project', 'sections', 'teamMembers'));
    }

    // Reorder task sections
    public function reorder(Request $request)
    {
        $request->validate(['items' => 'required|array', 'items.*.id' => 'required|integer', 'items.*.sort_order' => 'required|integer']);
        foreach ($request->items as $item) {
            Task::where('id', $item['id'])->update(['sort_order' => $item['sort_order'], 'section_id' => $item['section_id'] ?? null]);
        }
        return response()->json(['ok' => true]);
    }
}
