@extends('layouts.app')
@section('title', 'My Dashboard')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Welcome, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-gray-500 mt-1">Here's an overview of your work with us</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @php
        $cards = [
            ['label' => 'Active Projects', 'value' => $stats['active_projects'], 'color' => 'brand'],
            ['label' => 'Open Tickets', 'value' => $stats['open_tickets'], 'color' => 'warning'],
            ['label' => 'Awaiting Approval', 'value' => $stats['pending_deliverables'], 'color' => 'blue-light'],
            ['label' => 'Retainer', 'value' => $stats['active_retainer'] ? 'Active' : 'None', 'color' => 'success'],
        ];
        @endphp
        @foreach($cards as $card)
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-medium uppercase text-gray-500 mb-2">{{ $card['label'] }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Active Projects -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Your Projects</h2>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($activeProjects as $project)
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-medium text-gray-900 dark:text-white">{{ $project->name }}</h3>
                        <span class="text-xs text-gray-500">{{ $project->progress }}%</span>
                    </div>
                    <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                        <div class="h-1.5 rounded-full bg-brand-500" style="width: {{ $project->progress }}%"></div>
                    </div>
                    @if($project->due_date)
                    <p class="text-xs text-gray-400 mt-1.5">Due {{ $project->due_date->format('M j, Y') }}</p>
                    @endif
                </div>
                @empty
                <div class="px-6 py-10 text-center text-sm text-gray-400">No active projects</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Tickets -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Support Tickets</h2>
                <a href="{{ route('client.dashboard') }}" class="text-sm text-brand-500 hover:text-brand-600">New Ticket</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($recentTickets as $ticket)
                @php $statusColors = ['open' => 'blue-light', 'in_progress' => 'warning', 'resolved' => 'success', 'closed' => 'gray', 'waiting' => 'orange']; $c = $statusColors[$ticket->status] ?? 'gray'; @endphp
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ $ticket->subject }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $ticket->ticket_number }} &middot; {{ $ticket->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="shrink-0 inline-flex items-center rounded-full bg-{{ $c }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $c }}-700 dark:bg-{{ $c }}-500/10 dark:text-{{ $c }}-400">
                            {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="px-6 py-10 text-center text-sm text-gray-400">No tickets submitted</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Pending Approvals -->
    @if($pendingDeliverables->isNotEmpty())
    <div class="rounded-2xl border border-warning-200 bg-warning-50 p-5 dark:border-warning-800 dark:bg-warning-900/20">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex size-9 items-center justify-center rounded-lg bg-warning-100 dark:bg-warning-900/40">
                <svg class="size-5 text-warning-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h3 class="font-semibold text-warning-900 dark:text-warning-200">Action Required</h3>
                <p class="text-sm text-warning-700 dark:text-warning-400">{{ $pendingDeliverables->count() }} deliverable(s) awaiting your approval</p>
            </div>
        </div>
        <div class="space-y-2">
            @foreach($pendingDeliverables as $deliverable)
            <div class="flex items-center justify-between rounded-lg bg-white p-3 dark:bg-gray-900">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $deliverable->name }}</p>
                    <p class="text-xs text-gray-500">{{ $deliverable->project->name }}</p>
                </div>
                <button class="rounded-lg bg-warning-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-warning-600">
                    Review
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
