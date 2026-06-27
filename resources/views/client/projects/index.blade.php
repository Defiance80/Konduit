@extends('layouts.app')
@section('title', 'My Projects')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Your Projects</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Track the progress of all work being done for you.</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-center">
            <p class="text-2xl font-bold text-brand-600 dark:text-brand-400">{{ $stats['active'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Active</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-center">
            <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">{{ $stats['on_hold'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">On Hold</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-center">
            <p class="text-2xl font-bold text-success-600 dark:text-success-400">{{ $stats['completed'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Completed</p>
        </div>
    </div>

    {{-- Projects --}}
    <div class="space-y-4">
        @forelse($projects as $project)
        @php
            $statusColor = match($project->status) {
                'active'    => 'success',
                'on_hold'   => 'warning',
                'completed' => 'gray',
                'cancelled' => 'error',
                default     => 'blue',
            };
            $delivTotal    = $project->deliverables->count();
            $delivApproved = $project->deliverables->where('status','approved')->count();
            $tasksDone     = $project->tasks->where('status','done')->count();
            $tasksTotal    = $project->tasks->count();
        @endphp
        <a href="{{ route('client.projects.show', $project) }}"
            class="block rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 hover:shadow-md hover:border-brand-200 dark:hover:border-brand-800 transition-all group">
            <div class="p-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-{{ $statusColor }}-50 text-{{ $statusColor }}-700 dark:bg-{{ $statusColor }}-500/10 dark:text-{{ $statusColor }}-400">
                                {{ str_replace('_', ' ', ucfirst($project->status)) }}
                            </span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-brand-500 transition-colors">{{ $project->name }}</h3>
                        @if($project->description)
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $project->description }}</p>
                        @endif
                    </div>
                    <svg class="size-5 text-gray-300 group-hover:text-brand-400 transition-colors flex-shrink-0 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>

                {{-- Progress bar --}}
                <div class="mb-4">
                    <div class="flex justify-between text-xs text-gray-400 mb-1.5">
                        <span>Progress</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $project->progress }}%</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                        <div class="h-2 rounded-full bg-brand-500 transition-all" style="width: {{ $project->progress }}%"></div>
                    </div>
                </div>

                {{-- Meta row --}}
                <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400">
                    @if($project->due_date)
                    <span class="flex items-center gap-1.5">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $project->due_date->format('M j, Y') }}
                    </span>
                    @endif
                    @if($delivTotal > 0)
                    <span class="flex items-center gap-1.5">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $delivApproved }}/{{ $delivTotal }} approved
                    </span>
                    @endif
                    @if($tasksTotal > 0)
                    <span class="flex items-center gap-1.5">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        {{ $tasksDone }}/{{ $tasksTotal }} tasks
                    </span>
                    @endif
                    @if($project->budget)
                    <span class="flex items-center gap-1.5">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        ${{ number_format($project->budget, 0) }} budget
                    </span>
                    @endif
                </div>
            </div>

            {{-- Awaiting approval banner --}}
            @php $awaitingApproval = $project->deliverables->where('status','in_review')->count(); @endphp
            @if($awaitingApproval > 0)
            <div class="border-t border-blue-100 bg-blue-50 dark:border-blue-800/50 dark:bg-blue-500/5 px-6 py-2.5 rounded-b-2xl flex items-center gap-2">
                <span class="size-2 rounded-full bg-blue-500 animate-pulse"></span>
                <span class="text-xs font-medium text-blue-600 dark:text-blue-400">{{ $awaitingApproval }} deliverable{{ $awaitingApproval > 1 ? 's' : '' }} awaiting your approval</span>
            </div>
            @endif
        </a>
        @empty
        <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 py-16 text-center">
            <svg class="mx-auto size-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01"/></svg>
            <p class="text-gray-500 dark:text-gray-400 font-medium">No projects yet</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Projects will appear here once your team gets started.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
