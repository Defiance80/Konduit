@extends('layouts.app')
@section('title', 'Tasks')

@section('content')
<div x-data="{
    view: 'list',
    showCreate: false,
    createInGroup: '',
    openTask: null,
}" class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Tasks</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">All tasks across projects and clients.</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- View toggle --}}
            <div class="flex rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <button @click="view='list'" :class="view==='list' ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium transition-colors">
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    List
                </button>
                <button @click="view='board'" :class="view==='board' ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium transition-colors border-l border-gray-200 dark:border-gray-700">
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                    Board
                </button>
            </div>
            <button @click="showCreate=true; createInGroup=''"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Task
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        @foreach([
            ['label'=>'To Do', 'count'=>$stats['todo'], 'color'=>'gray'],
            ['label'=>'In Progress', 'count'=>$stats['in_progress'], 'color'=>'blue'],
            ['label'=>'In Review', 'count'=>$stats['review'], 'color'=>'warning'],
            ['label'=>'Done', 'count'=>$stats['done'], 'color'=>'success'],
            ['label'=>'Overdue', 'count'=>$stats['overdue'], 'color'=>'error'],
        ] as $s)
        <div class="rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ $s['label'] }}</p>
            <p class="text-2xl font-bold text-{{ $s['color'] }}-600 dark:text-{{ $s['color'] }}-400">{{ $s['count'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('agency.tasks.index') }}" class="flex flex-wrap gap-3 items-center">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search tasks…"
            class="rounded-lg border border-gray-200 bg-white px-3.5 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white w-44 focus:outline-none focus:ring-1 focus:ring-brand-500">

        <select name="status" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="">All Statuses</option>
            @foreach(['todo'=>'To Do','in_progress'=>'In Progress','review'=>'In Review','done'=>'Done'] as $val=>$label)
            <option value="{{ $val }}" @selected(request('status')===$val)>{{ $label }}</option>
            @endforeach
        </select>

        <select name="priority" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="">All Priorities</option>
            @foreach(['urgent','high','medium','low','none'] as $p)
            <option value="{{ $p }}" @selected(request('priority')===$p)>{{ ucfirst($p) }}</option>
            @endforeach
        </select>

        <select name="assignee" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="">All Assignees</option>
            @foreach($teamMembers as $m)
            <option value="{{ $m->id }}" @selected(request('assignee')==$m->id)>{{ $m->name }}</option>
            @endforeach
        </select>

        <select name="project" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="">All Projects</option>
            @foreach($projects as $p)
            <option value="{{ $p->id }}" @selected(request('project')==$p->id)>{{ $p->name }}</option>
            @endforeach
        </select>

        <button type="submit" class="rounded-lg bg-gray-100 hover:bg-gray-200 px-3 py-2 text-sm font-medium dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-white">Filter</button>
        @if(request()->hasAny(['q','status','priority','assignee','project']))
        <a href="{{ route('agency.tasks.index') }}" class="text-sm text-gray-400 hover:text-gray-600">Clear</a>
        @endif
    </form>

    {{-- Flash message --}}
    @if(session('success'))
    <div class="rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    {{-- ══════════ LIST VIEW ══════════ --}}
    <div x-show="view==='list'" class="space-y-4">
        @forelse($grouped as $groupName => $tasks)
        @if($tasks->isNotEmpty() || !request()->hasAny(['status','priority','assignee','project','q']))
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            {{-- Group header --}}
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-gray-800"
                style="background: {{ match($groupName) { 'In Progress' => 'rgba(59,130,246,0.04)', 'In Review' => 'rgba(245,158,11,0.04)', 'Done' => 'rgba(16,185,129,0.04)', default => 'transparent' } }}">
                <div class="flex items-center gap-2.5">
                    <span class="inline-block size-2.5 rounded-full {{ match($groupName) {
                        'In Progress' => 'bg-blue-500',
                        'In Review'   => 'bg-warning-500',
                        'Done'        => 'bg-success-500',
                        default       => 'bg-gray-300 dark:bg-gray-600'
                    } }}"></span>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $groupName }}</span>
                    <span class="text-xs text-gray-400">{{ $tasks->count() }}</span>
                </div>
                <button @click="showCreate=true; createInGroup='{{ strtolower(str_replace(' ','_',$groupName)) }}'"
                    class="text-xs text-brand-500 hover:text-brand-600 font-medium">+ Add Task</button>
            </div>

            {{-- Task rows --}}
            <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
                @forelse($tasks as $task)
                <div class="flex items-start gap-3 px-5 py-3 hover:bg-gray-50/60 dark:hover:bg-gray-800/40 group transition-colors">
                    {{-- Complete checkbox --}}
                    <form method="POST" action="{{ route('agency.tasks.update', $task) }}" class="mt-0.5 flex-shrink-0">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $task->status === 'done' ? 'todo' : 'done' }}">
                        <button type="submit" class="size-4 rounded border-2 flex items-center justify-center transition-colors
                            {{ $task->status === 'done' ? 'bg-success-500 border-success-500 text-white' : 'border-gray-300 dark:border-gray-600 hover:border-brand-500' }}">
                            @if($task->status === 'done')
                            <svg class="size-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </button>
                    </form>

                    {{-- Priority dot --}}
                    <span class="mt-1.5 size-2 rounded-full flex-shrink-0 {{ match($task->priority) {
                        'urgent' => 'bg-error-500',
                        'high'   => 'bg-warning-500',
                        'medium' => 'bg-blue-400',
                        'low'    => 'bg-gray-300',
                        default  => 'bg-transparent',
                    } }}"></span>

                    {{-- Task title & meta --}}
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('agency.tasks.show', $task) }}"
                            class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500 {{ $task->status === 'done' ? 'line-through text-gray-400' : '' }}">
                            {{ $task->title }}
                        </a>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                            @if($task->project)
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $task->project->name }}</span>
                            @elseif($task->client)
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $task->client->name }}</span>
                            @endif
                            @if($task->subtasks->count() > 0)
                            <span class="text-xs text-gray-400">
                                <svg class="size-3 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                {{ $task->subtasks->where('status','done')->count() }}/{{ $task->subtasks->count() }}
                            </span>
                            @endif
                            @if($task->tags)
                            @foreach(array_slice($task->tags, 0, 2) as $tag)
                            <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ $tag }}</span>
                            @endforeach
                            @endif
                        </div>
                    </div>

                    {{-- Right meta --}}
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @if($task->estimated_hours)
                        <span class="text-xs text-gray-400">{{ $task->estimated_hours }}h</span>
                        @endif

                        @if($task->due_date)
                        <span class="text-xs {{ $task->isOverdue() ? 'text-error-500 font-medium' : 'text-gray-400' }}">
                            {{ $task->due_date->format('M j') }}
                        </span>
                        @endif

                        @if($task->assignee)
                        <img src="{{ $task->assignee->avatar_url }}" alt="{{ $task->assignee->name }}"
                            title="{{ $task->assignee->name }}"
                            class="size-6 rounded-full object-cover ring-1 ring-white dark:ring-gray-900">
                        @endif

                        <a href="{{ route('agency.tasks.show', $task) }}"
                            class="opacity-0 group-hover:opacity-100 transition-opacity text-gray-400 hover:text-gray-600">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                @empty
                <p class="px-5 py-4 text-sm text-gray-400 dark:text-gray-500 italic">No tasks in this group.</p>
                @endforelse
            </div>
        </div>
        @endif
        @empty
        <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 py-16 text-center">
            <svg class="mx-auto size-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <p class="text-gray-500 dark:text-gray-400 font-medium">No tasks found</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Create your first task to get started.</p>
            <button @click="showCreate=true" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Task
            </button>
        </div>
        @endforelse
    </div>

    {{-- ══════════ BOARD VIEW ══════════ --}}
    <div x-show="view==='board'" x-cloak class="overflow-x-auto pb-4">
        <div class="flex gap-4 min-w-max">
            @foreach($grouped as $groupName => $tasks)
            <div class="w-72 flex-shrink-0">
                <div class="flex items-center justify-between mb-3 px-1">
                    <div class="flex items-center gap-2">
                        <span class="size-2.5 rounded-full {{ match($groupName) {
                            'In Progress' => 'bg-blue-500', 'In Review' => 'bg-warning-500',
                            'Done' => 'bg-success-500', default => 'bg-gray-300 dark:bg-gray-600'
                        } }}"></span>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $groupName }}</span>
                        <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-800 rounded-full px-1.5">{{ $tasks->count() }}</span>
                    </div>
                    <button @click="showCreate=true; createInGroup='{{ strtolower(str_replace(' ','_',$groupName)) }}'"
                        class="text-gray-400 hover:text-brand-500 transition-colors">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>
                </div>

                <div class="space-y-2.5">
                    @foreach($tasks as $task)
                    <a href="{{ route('agency.tasks.show', $task) }}"
                        class="block rounded-xl border border-gray-200 bg-white p-3.5 hover:shadow-sm hover:border-brand-200 transition-all dark:border-gray-800 dark:bg-gray-900 dark:hover:border-brand-800">
                        {{-- Priority bar --}}
                        @if($task->priority !== 'none')
                        <div class="h-0.5 rounded-full mb-3 {{ match($task->priority) {
                            'urgent' => 'bg-error-400',
                            'high'   => 'bg-warning-400',
                            'medium' => 'bg-blue-400',
                            default  => 'bg-gray-200'
                        } }}"></div>
                        @endif

                        <p class="text-sm font-medium text-gray-900 dark:text-white leading-snug {{ $task->status === 'done' ? 'line-through text-gray-400' : '' }}">
                            {{ $task->title }}
                        </p>

                        @if($task->project)
                        <p class="text-xs text-gray-400 mt-1.5">{{ $task->project->name }}</p>
                        @endif

                        <div class="flex items-center justify-between mt-3">
                            <div class="flex items-center gap-2">
                                @if($task->due_date)
                                <span class="text-xs {{ $task->isOverdue() ? 'text-error-500' : 'text-gray-400' }}">
                                    {{ $task->due_date->format('M j') }}
                                </span>
                                @endif
                                @if($task->subtasks->count() > 0)
                                <span class="text-xs text-gray-400">
                                    {{ $task->subtasks->where('status','done')->count() }}/{{ $task->subtasks->count() }}
                                </span>
                                @endif
                            </div>
                            @if($task->assignee)
                            <img src="{{ $task->assignee->avatar_url }}" alt="{{ $task->assignee->name }}"
                                class="size-6 rounded-full object-cover">
                            @endif
                        </div>
                    </a>
                    @endforeach

                    @if($tasks->isEmpty())
                    <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 p-4 text-center">
                        <p class="text-xs text-gray-400">No tasks</p>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ══════════ CREATE TASK MODAL ══════════ --}}
    <div x-show="showCreate" x-cloak class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-16" style="background: rgba(0,0,0,0.5);">
        <div @click.away="showCreate=false" class="w-full max-w-lg rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-900">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">New Task</h3>
                <button @click="showCreate=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('agency.tasks.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="redirect" value="{{ url()->current() }}">

                <div>
                    <input type="text" name="title" required placeholder="Task title…" autofocus
                        class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm font-medium text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>

                <div>
                    <textarea name="description" rows="2" placeholder="Description (optional)…"
                        class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                        <select name="status" x-model="createInGroup"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="todo">To Do</option>
                            <option value="in_progress">In Progress</option>
                            <option value="review">In Review</option>
                            <option value="done">Done</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Priority</label>
                        <select name="priority"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="none">None</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Project</label>
                        <select name="project_id"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="">No project</option>
                            @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Assignee</label>
                        <select name="assignee_id"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="">Unassigned</option>
                            @foreach($teamMembers as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Due Date</label>
                        <input type="date" name="due_date"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Est. Hours</label>
                        <input type="number" name="estimated_hours" step="0.5" min="0" placeholder="0"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tags (comma-separated)</label>
                    <input type="text" name="tags" placeholder="design, urgent, Q3"
                        class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showCreate=false"
                        class="flex-1 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                        Create Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
