@extends('layouts.app')
@section('title', 'Projects')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Projects</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $projects->total() }} total projects</p>
        </div>
        <a href="{{ route('agency.projects.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Project
        </a>
    </div>

    <!-- Status Summary -->
    <div class="flex gap-3 flex-wrap">
        @php $statusLabels = ['all' => 'All', 'active' => 'Active', 'on_hold' => 'On Hold', 'completed' => 'Completed']; @endphp
        @foreach($statusLabels as $key => $label)
        <span class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            {{ $label }}: <strong>{{ $statusCounts[$key] }}</strong>
        </span>
        @endforeach
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($projects as $project)
        @php
        $priorityColors = ['urgent' => 'error', 'high' => 'warning', 'medium' => 'blue-light', 'low' => 'gray'];
        $statusColors = ['active' => 'success', 'on_hold' => 'warning', 'cancelled' => 'error', 'completed' => 'gray', 'draft' => 'blue-light'];
        $pc = $priorityColors[$project->priority] ?? 'gray';
        $sc = $statusColors[$project->status] ?? 'gray';
        @endphp
        <div class="rounded-2xl border border-gray-200 bg-white p-5 hover:shadow-theme-md transition-shadow dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-start justify-between mb-3">
                <div class="min-w-0 flex-1">
                    <a href="{{ route('agency.projects.show', $project) }}" class="font-semibold text-gray-900 dark:text-white hover:text-brand-500 block truncate">{{ $project->name }}</a>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $project->client->name }}</p>
                </div>
                <span class="ml-2 shrink-0 inline-flex items-center rounded-full bg-{{ $sc }}-50 px-2 py-0.5 text-xs font-medium text-{{ $sc }}-700 dark:bg-{{ $sc }}-500/10 dark:text-{{ $sc }}-400">
                    {{ str_replace('_', ' ', ucfirst($project->status)) }}
                </span>
            </div>

            @if($project->description)
            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-3">{{ $project->description }}</p>
            @endif

            <!-- Progress -->
            <div class="mb-3">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>Progress</span><span>{{ $project->progress }}%</span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                    <div class="h-1.5 rounded-full bg-brand-500 transition-all" style="width: {{ $project->progress }}%"></div>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-gray-500">
                <span class="inline-flex items-center gap-1 rounded-full bg-{{ $pc }}-50 px-2 py-0.5 text-{{ $pc }}-700 dark:bg-{{ $pc }}-500/10 dark:text-{{ $pc }}-400">{{ ucfirst($project->priority) }}</span>
                @if($project->due_date)
                <span class="{{ $project->due_date->isPast() && $project->status !== 'completed' ? 'text-error-500' : 'text-gray-400' }}">
                    Due {{ $project->due_date->format('M j, Y') }}
                </span>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full rounded-2xl border border-dashed border-gray-200 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-900">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">No projects yet</h3>
            <p class="text-sm text-gray-500 mb-4">Create your first project to start tracking work</p>
            <a href="{{ route('agency.projects.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">New Project</a>
        </div>
        @endforelse
    </div>

    {{ $projects->links() }}
</div>
@endsection
