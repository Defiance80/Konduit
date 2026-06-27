@extends('layouts.app')
@section('title', $task->title)

@section('content')
<div class="space-y-5" x-data="{ showSubtaskForm: false, editMode: false }">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('agency.tasks.index') }}" class="hover:text-gray-600 dark:hover:text-gray-300">Tasks</a>
        @if($task->project)
        <span>/</span>
        <a href="{{ route('agency.projects.show', $task->project) }}" class="hover:text-gray-600 dark:hover:text-gray-300">{{ $task->project->name }}</a>
        @endif
        <span>/</span>
        <span class="text-gray-600 dark:text-gray-300 truncate max-w-xs">{{ $task->title }}</span>
    </div>

    @if(session('success'))
    <div class="rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ── LEFT: Task detail ── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Title & description card --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="p-6">
                    {{-- Status + Priority badges --}}
                    <div class="flex items-center gap-2 mb-4">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $task->status_badge_color }}">
                            {{ match($task->status) { 'in_progress'=>'In Progress','review'=>'In Review','done'=>'Done',default=>'To Do' } }}
                        </span>
                        @if($task->priority !== 'none')
                        <span class="inline-flex items-center gap-1 text-xs font-medium {{ $task->priority_color }}">
                            <span class="size-1.5 rounded-full bg-current"></span>
                            {{ ucfirst($task->priority) }} Priority
                        </span>
                        @endif
                        @if($task->isOverdue())
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-error-50 text-error-600 dark:bg-error-500/10 dark:text-error-400">Overdue</span>
                        @endif
                    </div>

                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 {{ $task->status === 'done' ? 'line-through text-gray-400' : '' }}">
                        {{ $task->title }}
                    </h1>

                    @if($task->description)
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $task->description }}</p>
                    @else
                    <p class="text-sm text-gray-400 italic">No description.</p>
                    @endif

                    @if($task->tags)
                    <div class="flex flex-wrap gap-1.5 mt-4">
                        @foreach($task->tags as $tag)
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ $tag }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Action bar --}}
                <div class="flex flex-wrap items-center gap-2 px-6 py-3 border-t border-gray-100 dark:border-gray-800">
                    @if($task->status !== 'done')
                    <form method="POST" action="{{ route('agency.tasks.update', $task) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="done">
                        <button class="inline-flex items-center gap-1.5 rounded-lg bg-success-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-success-600">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Mark Complete
                        </button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('agency.tasks.update', $task) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="todo">
                        <button class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                            Reopen Task
                        </button>
                    </form>
                    @endif
                    <button @click="editMode=!editMode" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </button>
                    <form method="POST" action="{{ route('agency.tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')" class="ml-auto">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-gray-400 hover:text-error-500 transition-colors">Delete</button>
                    </form>
                </div>
            </div>

            {{-- Edit form (hidden by default) --}}
            <div x-show="editMode" x-cloak class="rounded-2xl border border-brand-200 bg-white dark:border-brand-800 dark:bg-gray-900">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Edit Task</h2>
                </div>
                <form method="POST" action="{{ route('agency.tasks.update', $task) }}" class="p-6 space-y-4">
                    @csrf @method('PATCH')
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Title</label>
                        <input type="text" name="title" value="{{ old('title', $task->title) }}" required
                            class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Description</label>
                        <textarea name="description" rows="4"
                            class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white resize-none">{{ old('description', $task->description) }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Status</label>
                            <select name="status" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                @foreach(['todo'=>'To Do','in_progress'=>'In Progress','review'=>'In Review','done'=>'Done'] as $val=>$lbl)
                                <option value="{{ $val }}" @selected($task->status===$val)>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Priority</label>
                            <select name="priority" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                @foreach(['none','low','medium','high','urgent'] as $p)
                                <option value="{{ $p }}" @selected($task->priority===$p)>{{ ucfirst($p) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Project</label>
                            <select name="project_id" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                <option value="">No project</option>
                                @foreach($projects as $p)
                                <option value="{{ $p->id }}" @selected($task->project_id==$p->id)>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Assignee</label>
                            <select name="assignee_id" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                <option value="">Unassigned</option>
                                @foreach($teamMembers as $m)
                                <option value="{{ $m->id }}" @selected($task->assignee_id==$m->id)>{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Due Date</label>
                            <input type="date" name="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Est. Hours</label>
                            <input type="number" name="estimated_hours" step="0.5" min="0" value="{{ old('estimated_hours', $task->estimated_hours) }}"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Tags</label>
                        <input type="text" name="tags" value="{{ old('tags', $task->tags ? implode(', ', $task->tags) : '') }}" placeholder="design, urgent"
                            class="w-full rounded-lg border border-gray-200 px-3.5 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="editMode=false" class="flex-1 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Cancel</button>
                        <button type="submit" class="flex-1 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Save Changes</button>
                    </div>
                </form>
            </div>

            {{-- Subtasks --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Subtasks
                        @if($task->subtasks->count() > 0)
                        <span class="text-xs text-gray-400 ml-1">{{ $task->subtasks->where('status','done')->count() }}/{{ $task->subtasks->count() }}</span>
                        @endif
                    </h2>
                    <button @click="showSubtaskForm=!showSubtaskForm" class="text-xs text-brand-500 hover:text-brand-600 font-medium">+ Add</button>
                </div>

                <div x-show="showSubtaskForm" x-cloak class="px-6 py-3 border-b border-gray-100 dark:border-gray-800">
                    <form method="POST" action="{{ route('agency.tasks.store') }}" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="parent_task_id" value="{{ $task->id }}">
                        <input type="hidden" name="project_id" value="{{ $task->project_id }}">
                        <input type="hidden" name="status" value="todo">
                        <input type="hidden" name="priority" value="none">
                        <input type="hidden" name="redirect" value="{{ url()->current() }}">
                        <input type="text" name="title" required placeholder="Subtask name…"
                            class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <button type="submit" class="rounded-lg bg-brand-500 px-3 py-2 text-sm font-medium text-white hover:bg-brand-600">Add</button>
                        <button type="button" @click="showSubtaskForm=false" class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">Cancel</button>
                    </form>
                </div>

                <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
                    @forelse($task->subtasks as $sub)
                    <div class="flex items-center gap-3 px-6 py-3">
                        <form method="POST" action="{{ route('agency.tasks.update', $sub) }}" class="flex-shrink-0">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $sub->status === 'done' ? 'todo' : 'done' }}">
                            <button type="submit" class="size-4 rounded border-2 flex items-center justify-center transition-colors
                                {{ $sub->status === 'done' ? 'bg-success-500 border-success-500 text-white' : 'border-gray-300 hover:border-brand-500 dark:border-gray-600' }}">
                                @if($sub->status === 'done')
                                <svg class="size-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </button>
                        </form>
                        <span class="flex-1 text-sm text-gray-700 dark:text-gray-300 {{ $sub->status === 'done' ? 'line-through text-gray-400' : '' }}">{{ $sub->title }}</span>
                        @if($sub->assignee)
                        <img src="{{ $sub->assignee->avatar_url }}" alt="{{ $sub->assignee->name }}" class="size-5 rounded-full">
                        @endif
                        @if($sub->due_date)
                        <span class="text-xs {{ $sub->isOverdue() ? 'text-error-500' : 'text-gray-400' }}">{{ $sub->due_date->format('M j') }}</span>
                        @endif
                    </div>
                    @empty
                    <p class="px-6 py-4 text-sm text-gray-400 italic">No subtasks yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ── RIGHT: Meta sidebar ── --}}
        <div class="space-y-4">
            {{-- Details --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 space-y-3.5">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Details</h2>

                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-400">Assignee</span>
                    @if($task->assignee)
                    <div class="flex items-center gap-2">
                        <img src="{{ $task->assignee->avatar_url }}" alt="" class="size-5 rounded-full">
                        <span class="text-gray-700 dark:text-gray-300">{{ $task->assignee->name }}</span>
                    </div>
                    @else
                    <span class="text-gray-400 italic">Unassigned</span>
                    @endif
                </div>

                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-400">Due Date</span>
                    <span class="{{ $task->isOverdue() ? 'text-error-500 font-medium' : 'text-gray-700 dark:text-gray-300' }}">
                        {{ $task->due_date ? $task->due_date->format('M j, Y') : '—' }}
                    </span>
                </div>

                @if($task->estimated_hours)
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-400">Estimated</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ $task->estimated_hours }}h</span>
                </div>
                @endif

                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-400">Created by</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ $task->creator?->name ?? '—' }}</span>
                </div>

                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-400">Created</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ $task->created_at->format('M j, Y') }}</span>
                </div>

                @if($task->completed_at)
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-400">Completed</span>
                    <span class="text-success-600 dark:text-success-400 font-medium">{{ $task->completed_at->format('M j, Y') }}</span>
                </div>
                @endif
            </div>

            {{-- Project/Client links --}}
            @if($task->project || $task->client)
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 space-y-3">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Related</h2>
                @if($task->project)
                <a href="{{ route('agency.projects.show', $task->project) }}" class="flex items-center gap-2.5 text-sm text-gray-700 hover:text-brand-500 dark:text-gray-300 dark:hover:text-brand-400">
                    <svg class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01"/></svg>
                    {{ $task->project->name }}
                </a>
                @endif
                @if($task->client)
                <a href="{{ route('agency.clients.show', $task->client) }}" class="flex items-center gap-2.5 text-sm text-gray-700 hover:text-brand-500 dark:text-gray-300 dark:hover:text-brand-400">
                    <svg class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
                    {{ $task->client->name }}
                </a>
                @endif
                @if($task->parent)
                <a href="{{ route('agency.tasks.show', $task->parent) }}" class="flex items-center gap-2.5 text-sm text-gray-700 hover:text-brand-500 dark:text-gray-300 dark:hover:text-brand-400">
                    <svg class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/></svg>
                    Parent: {{ $task->parent->title }}
                </a>
                @endif
            </div>
            @endif

            {{-- Quick status change --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Move To</h2>
                <div class="flex flex-col gap-2">
                    @foreach(['todo'=>'To Do','in_progress'=>'In Progress','review'=>'In Review','done'=>'Done'] as $val=>$lbl)
                    @if($task->status !== $val)
                    <form method="POST" action="{{ route('agency.tasks.update', $task) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $val }}">
                        <button type="submit" class="w-full text-left rounded-lg border border-gray-100 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-200 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors">
                            → {{ $lbl }}
                        </button>
                    </form>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
