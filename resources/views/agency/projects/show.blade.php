@extends('layouts.app')
@section('title', $project->name)

@section('content')
<div x-data="{ tab: 'overview', showDeliverable: false }" class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('agency.projects.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white truncate">{{ $project->name }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $project->client->name }}
                @if($project->retainer) &middot; {{ $project->retainer->name }} @endif
            </p>
        </div>
        <a href="{{ route('agency.projects.edit', $project) }}"
            class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
            Edit
        </a>
    </div>

    @if(session('success'))
    <div class="rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:border-error-800 dark:bg-error-900/20 dark:text-error-400">{{ session('error') }}</div>
    @endif

    {{-- AI Summary --}}
    @include('partials.ai-summary-card', [
        'summary'        => $aiSummary,
        'generateRoute'  => 'agency.projects.ai-summary',
        'generateParam'  => $project,
        'label'          => 'Project',
    ])

    {{-- Tabs --}}
    <div class="flex gap-1 border-b border-gray-200 dark:border-gray-800">
        @foreach([
            ['key'=>'overview',     'label'=>'Overview'],
            ['key'=>'deliverables', 'label'=>'Deliverables', 'count'=>$project->deliverables->count()],
            ['key'=>'tasks',        'label'=>'Tasks',        'count'=>$project->tasks->count()],
            ['key'=>'tickets',      'label'=>'Tickets',      'count'=>$project->tickets->count()],
        ] as $t)
        <button @click="tab='{{ $t['key'] }}'"
            :class="tab==='{{ $t['key'] }}' ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
            class="flex items-center gap-1.5 border-b-2 px-4 py-2.5 text-sm font-medium transition-colors -mb-px">
            {{ $t['label'] }}
            @if(isset($t['count']) && $t['count'] > 0)
            <span class="text-xs bg-gray-100 dark:bg-gray-800 rounded-full px-1.5">{{ $t['count'] }}</span>
            @endif
        </button>
        @endforeach
    </div>

    {{-- ══════ OVERVIEW TAB ══════ --}}
    <div x-show="tab==='overview'">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-5">

                {{-- Progress --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Progress</h2>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-500">Completion</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $project->progress }}%</span>
                    </div>
                    <div class="h-2.5 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                        <div class="h-2.5 rounded-full bg-brand-500 transition-all" style="width: {{ $project->progress }}%"></div>
                    </div>
                    @if($project->budget)
                    <div class="mt-5 grid grid-cols-3 gap-4 text-sm">
                        <div><p class="text-gray-400 text-xs mb-0.5">Budget</p><p class="font-semibold text-gray-900 dark:text-white">${{ number_format($project->budget, 0) }}</p></div>
                        <div><p class="text-gray-400 text-xs mb-0.5">Spent</p><p class="font-semibold text-gray-900 dark:text-white">${{ number_format($project->budget_spent ?? 0, 0) }}</p></div>
                        <div><p class="text-gray-400 text-xs mb-0.5">Remaining</p><p class="font-semibold text-gray-900 dark:text-white">${{ number_format($project->budget - ($project->budget_spent ?? 0), 0) }}</p></div>
                    </div>
                    @endif
                </div>

                {{-- Quick stats --}}
                <div class="grid grid-cols-3 gap-3">
                    @php
                        $delivApproved = $project->deliverables->where('status','approved')->count();
                        $delivTotal    = $project->deliverables->count();
                        $tasksDone     = $project->tasks->where('status','done')->count();
                        $tasksTotal    = $project->tasks->count();
                    @endphp
                    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-center">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $delivApproved }}/{{ $delivTotal }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Deliverables</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-center">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $tasksDone }}/{{ $tasksTotal }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Tasks Done</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-center">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $project->tickets->count() }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Open Tickets</p>
                    </div>
                </div>

                @if($project->description)
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="font-semibold text-gray-900 dark:text-white mb-2">Description</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $project->description }}</p>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 space-y-3">
                    <h2 class="font-semibold text-gray-900 dark:text-white text-sm">Details</h2>
                    @php $sc = ['active'=>'success','on_hold'=>'warning','completed'=>'gray','cancelled'=>'error','draft'=>'blue-light'][$project->status] ?? 'gray'; @endphp
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Status</span>
                        <span class="inline-flex items-center rounded-full bg-{{ $sc }}-50 px-2 py-0.5 text-xs font-medium text-{{ $sc }}-700 dark:bg-{{ $sc }}-500/10 dark:text-{{ $sc }}-400">{{ str_replace('_',' ',ucfirst($project->status)) }}</span>
                    </div>
                    @php $pc = ['urgent'=>'error','high'=>'warning','medium'=>'blue','low'=>'gray'][$project->priority] ?? 'gray'; @endphp
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Priority</span>
                        <span class="inline-flex items-center rounded-full bg-{{ $pc }}-50 px-2 py-0.5 text-xs font-medium text-{{ $pc }}-700 dark:bg-{{ $pc }}-500/10 dark:text-{{ $pc }}-400">{{ ucfirst($project->priority) }}</span>
                    </div>
                    @if($project->start_date)<div class="flex justify-between text-sm"><span class="text-gray-400">Start</span><span class="text-gray-700 dark:text-gray-300">{{ $project->start_date->format('M j, Y') }}</span></div>@endif
                    @if($project->due_date)<div class="flex justify-between text-sm"><span class="text-gray-400">Due</span><span class="text-gray-700 dark:text-gray-300">{{ $project->due_date->format('M j, Y') }}</span></div>@endif
                    @if($project->owner)<div class="flex justify-between text-sm"><span class="text-gray-400">Owner</span><span class="text-gray-700 dark:text-gray-300">{{ $project->owner->name }}</span></div>@endif
                </div>
            </div>
        </div>
    </div>

    {{-- ══════ DELIVERABLES TAB ══════ --}}
    <div x-show="tab==='deliverables'" x-cloak>
        <div class="flex justify-between items-center mb-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $project->deliverables->count() }} deliverable(s) for this project.</p>
            <button @click="showDeliverable=true"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Deliverable
            </button>
        </div>

        <div class="space-y-3">
            @forelse($project->deliverables->sortByDesc('created_at') as $d)
            <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-white px-5 py-4 hover:shadow-sm transition-shadow dark:border-gray-800 dark:bg-gray-900 group">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    {{-- Status icon --}}
                    <div class="size-9 rounded-lg flex items-center justify-center flex-shrink-0 {{ match($d->status) {
                        'approved'  => 'bg-success-50 dark:bg-success-500/10',
                        'in_review' => 'bg-blue-50 dark:bg-blue-500/10',
                        'rejected'  => 'bg-error-50 dark:bg-error-500/10',
                        'delivered' => 'bg-gray-100 dark:bg-gray-800',
                        default     => 'bg-warning-50 dark:bg-warning-500/10',
                    } }}">
                        @if($d->status === 'approved' || $d->status === 'delivered')
                        <svg class="size-4 text-success-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @elseif($d->status === 'in_review')
                        <svg class="size-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        @elseif($d->status === 'rejected')
                        <svg class="size-4 text-error-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                        <svg class="size-4 text-warning-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <a href="{{ route('agency.deliverables.show', $d) }}" class="font-medium text-gray-900 dark:text-white hover:text-brand-500 truncate block">{{ $d->name }}</a>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $d->status_color }}">{{ $d->status_label }}</span>
                            <span class="text-xs text-gray-400">v{{ $d->version }}</span>
                            @if($d->due_date)<span class="text-xs {{ $d->isOverdue() ? 'text-error-500' : 'text-gray-400' }}">Due {{ $d->due_date->format('M j') }}</span>@endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                    @if($d->isPending() || $d->isRejected())
                    <form method="POST" action="{{ route('agency.deliverables.submit', $d) }}">
                        @csrf @method('PATCH')
                        <button class="text-xs text-brand-500 hover:text-brand-600 font-medium whitespace-nowrap">Send for Review</button>
                    </form>
                    @endif
                    <a href="{{ route('agency.deliverables.show', $d) }}"
                        class="opacity-0 group-hover:opacity-100 transition-opacity text-gray-400 hover:text-gray-600">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            @empty
            <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 py-12 text-center">
                <svg class="mx-auto size-10 text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-gray-500 dark:text-gray-400 font-medium">No deliverables yet</p>
                <button @click="showDeliverable=true" class="mt-3 text-sm text-brand-500 hover:text-brand-600 font-medium">Add the first deliverable</button>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ══════ TASKS TAB ══════ --}}
    <div x-show="tab==='tasks'" x-cloak>
        <div class="flex justify-between items-center mb-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $project->tasks->count() }} task(s) for this project.</p>
            <a href="{{ route('agency.tasks.index') }}?project={{ $project->id }}"
                class="text-sm text-brand-500 hover:text-brand-600 font-medium">View all →</a>
        </div>
        <div class="space-y-2">
            @forelse($project->tasks->sortBy('due_date') as $task)
            <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900 group">
                <form method="POST" action="{{ route('agency.tasks.update', $task) }}" class="flex-shrink-0">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="{{ $task->status === 'done' ? 'todo' : 'done' }}">
                    <button class="size-4 rounded border-2 flex items-center justify-center {{ $task->status === 'done' ? 'bg-success-500 border-success-500 text-white' : 'border-gray-300 dark:border-gray-600 hover:border-brand-500' }}">
                        @if($task->status === 'done')<svg class="size-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@endif
                    </button>
                </form>
                <span class="size-2 rounded-full flex-shrink-0 {{ match($task->priority) { 'urgent'=>'bg-error-500','high'=>'bg-warning-500','medium'=>'bg-blue-400',default=>'bg-transparent' } }}"></span>
                <a href="{{ route('agency.tasks.show', $task) }}" class="flex-1 text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500 {{ $task->status === 'done' ? 'line-through text-gray-400' : '' }}">{{ $task->title }}</a>
                @if($task->due_date)<span class="text-xs {{ $task->isOverdue() ? 'text-error-500' : 'text-gray-400' }} flex-shrink-0">{{ $task->due_date->format('M j') }}</span>@endif
                @if($task->assignee)<img src="{{ $task->assignee->avatar_url }}" alt="" class="size-6 rounded-full flex-shrink-0">@endif
            </div>
            @empty
            <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 py-10 text-center">
                <p class="text-gray-500 dark:text-gray-400 text-sm">No tasks yet.</p>
                <a href="{{ route('agency.tasks.index') }}" class="mt-2 inline-block text-sm text-brand-500 hover:text-brand-600 font-medium">Go to Tasks →</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ══════ TICKETS TAB ══════ --}}
    <div x-show="tab==='tickets'" x-cloak>
        <div class="flex justify-between items-center mb-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $project->tickets->count() }} ticket(s) for this project.</p>
            <a href="{{ route('agency.tickets.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">+ New Ticket</a>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 divide-y divide-gray-50 dark:divide-gray-800/50">
            @forelse($project->tickets as $ticket)
            <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50/60 dark:hover:bg-gray-800/40">
                <a href="{{ route('agency.tickets.show', $ticket) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500">{{ $ticket->subject }}</a>
                <span class="text-xs text-gray-400">{{ $ticket->ticket_number }}</span>
            </div>
            @empty
            <p class="px-5 py-8 text-center text-sm text-gray-400">No tickets for this project.</p>
            @endforelse
        </div>
    </div>

    {{-- Add Deliverable Modal --}}
    <div x-show="showDeliverable" x-cloak class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-16" style="background: rgba(0,0,0,0.5);">
        <div @click.away="showDeliverable=false" class="w-full max-w-lg rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-900">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Add Deliverable</h3>
                <button @click="showDeliverable=false" class="text-gray-400 hover:text-gray-600">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('agency.deliverables.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <input type="hidden" name="client_id" value="{{ $project->client_id }}">
                <input type="hidden" name="redirect" value="{{ url()->current() }}">

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Name</label>
                    <input type="text" name="name" required placeholder="Homepage mockup v1"
                        class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Description</label>
                    <textarea name="description" rows="2"
                        class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Due Date</label>
                        <input type="date" name="due_date"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">External Link</label>
                        <input type="url" name="file_url" placeholder="https://figma.com/…"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Upload File</label>
                    <input type="file" name="file"
                        class="w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-medium file:text-brand-600 hover:file:bg-brand-100">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showDeliverable=false"
                        class="flex-1 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Cancel</button>
                    <button type="submit"
                        class="flex-1 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Add Deliverable</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
