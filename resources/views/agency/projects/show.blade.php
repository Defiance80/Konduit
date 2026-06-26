@extends('layouts.app')
@section('title', $project->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('agency.projects.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $project->name }}</h1>
            <p class="text-sm text-gray-500">{{ $project->client->name }} @if($project->retainer) &middot; {{ $project->retainer->name }} @endif</p>
        </div>
        <a href="{{ route('agency.projects.edit', $project) }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Edit</a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-4">
            <!-- Progress -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Progress</h2>
                <div class="flex justify-between text-sm mb-2"><span class="text-gray-500">Completion</span><span class="font-medium text-gray-900 dark:text-white">{{ $project->progress }}%</span></div>
                <div class="h-3 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                    <div class="h-3 rounded-full bg-brand-500" style="width: {{ $project->progress }}%"></div>
                </div>
                @if($project->budget)
                <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                    <div><p class="text-gray-400 text-xs">Budget</p><p class="font-medium text-gray-900 dark:text-white">${{ number_format($project->budget, 0) }}</p></div>
                    <div><p class="text-gray-400 text-xs">Spent</p><p class="font-medium text-gray-900 dark:text-white">${{ number_format($project->budget_spent, 0) }}</p></div>
                </div>
                @endif
            </div>

            <!-- Tickets -->
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Tickets ({{ $project->tickets->count() }})</h2>
                    <a href="{{ route('agency.tickets.create') }}" class="text-xs text-brand-500 hover:text-brand-600">+ New</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($project->tickets->take(5) as $ticket)
                    <div class="flex items-center justify-between px-6 py-3">
                        <a href="{{ route('agency.tickets.show', $ticket) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500">{{ $ticket->subject }}</a>
                        <span class="text-xs text-gray-400">{{ $ticket->ticket_number }}</span>
                    </div>
                    @empty
                    <p class="px-6 py-4 text-sm text-gray-400">No tickets</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Project Details -->
        <div class="space-y-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 space-y-3">
                <h2 class="font-semibold text-gray-900 dark:text-white">Details</h2>
                @php $sc = ['active' => 'success', 'on_hold' => 'warning', 'completed' => 'gray', 'cancelled' => 'error', 'draft' => 'blue-light'][$project->status] ?? 'gray'; @endphp
                <div class="flex justify-between text-sm"><span class="text-gray-400">Status</span><span class="inline-flex items-center rounded-full bg-{{ $sc }}-50 px-2 py-0.5 text-xs font-medium text-{{ $sc }}-700">{{ str_replace('_', ' ', ucfirst($project->status)) }}</span></div>
                @php $pc = ['urgent' => 'error', 'high' => 'warning', 'medium' => 'blue-light', 'low' => 'gray'][$project->priority] ?? 'gray'; @endphp
                <div class="flex justify-between text-sm"><span class="text-gray-400">Priority</span><span class="inline-flex items-center rounded-full bg-{{ $pc }}-50 px-2 py-0.5 text-xs font-medium text-{{ $pc }}-700">{{ ucfirst($project->priority) }}</span></div>
                @if($project->start_date) <div class="flex justify-between text-sm"><span class="text-gray-400">Start</span><span class="text-gray-700 dark:text-gray-300">{{ $project->start_date->format('M j, Y') }}</span></div> @endif
                @if($project->due_date) <div class="flex justify-between text-sm"><span class="text-gray-400">Due</span><span class="text-gray-700 dark:text-gray-300">{{ $project->due_date->format('M j, Y') }}</span></div> @endif
                @if($project->owner) <div class="flex justify-between text-sm"><span class="text-gray-400">Owner</span><span class="text-gray-700 dark:text-gray-300">{{ $project->owner->name }}</span></div> @endif
            </div>

            @if($project->description)
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="font-semibold text-gray-900 dark:text-white mb-2">Description</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $project->description }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
