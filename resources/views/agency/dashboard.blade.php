@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Good morning, {{ auth()->user()->name }} 👋</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Here's what's happening across your agency today.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('agency.tickets.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Ticket
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @php
        $statCards = [
            ['label' => 'Total Clients', 'value' => $stats['clients'], 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'brand'],
            ['label' => 'Active Projects', 'value' => $stats['active_projects'], 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color' => 'success'],
            ['label' => 'Open Tickets', 'value' => $stats['open_tickets'], 'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', 'color' => 'warning'],
            ['label' => 'Active Retainers', 'value' => $stats['active_retainers'], 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'blue-light'],
        ];
        @endphp

        @foreach($statCards as $card)
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $card['label'] }}</span>
                <div class="flex size-10 items-center justify-center rounded-lg bg-{{ $card['color'] }}-50 text-{{ $card['color'] }}-500 dark:bg-{{ $card['color'] }}-500/10">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $card['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $card['value'] }}</div>
        </div>
        @endforeach
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Active Projects -->
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Active Projects</h2>
                    <a href="{{ route('agency.projects.index') }}" class="text-sm text-brand-500 hover:text-brand-600">View all</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($activeProjects as $project)
                    <div class="flex items-center gap-4 px-6 py-4">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                            <svg class="size-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('agency.projects.show', $project) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500 truncate block">{{ $project->name }}</a>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $project->client->name }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-24">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-gray-500">{{ $project->progress }}%</span>
                                </div>
                                <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                                    <div class="h-1.5 rounded-full bg-brand-500" style="width: {{ $project->progress }}%"></div>
                                </div>
                            </div>
                            @if($project->due_date)
                                <span class="text-xs text-gray-400">{{ $project->due_date->format('M j') }}</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-10 text-center text-sm text-gray-400">
                        No active projects. <a href="{{ route('agency.projects.create') }}" class="text-brand-500">Create one</a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Tickets -->
        <div>
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Open Tickets</h2>
                    <a href="{{ route('agency.tickets.index') }}" class="text-sm text-brand-500 hover:text-brand-600">View all</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($recentTickets as $ticket)
                    <div class="px-6 py-4">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <a href="{{ route('agency.tickets.show', $ticket) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500 leading-tight line-clamp-1">{{ $ticket->subject }}</a>
                            @php
                            $priorityColors = ['urgent' => 'error', 'high' => 'warning', 'medium' => 'blue-light', 'low' => 'gray'];
                            $color = $priorityColors[$ticket->priority] ?? 'gray';
                            @endphp
                            <span class="shrink-0 inline-flex items-center rounded-full bg-{{ $color }}-50 px-2 py-0.5 text-xs font-medium text-{{ $color }}-700 dark:bg-{{ $color }}-500/10 dark:text-{{ $color }}-400">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500">{{ $ticket->client->name }} &middot; {{ $ticket->ticket_number }}</div>
                    </div>
                    @empty
                    <div class="px-6 py-10 text-center text-sm text-gray-400">No open tickets</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Clients -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Recent Clients</h2>
            <a href="{{ route('agency.clients.index') }}" class="text-sm text-brand-500 hover:text-brand-600">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-400">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-400">Industry</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-400">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($recentClients as $client)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $client->logo_url }}" class="size-8 rounded-full object-cover" alt="{{ $client->name }}">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $client->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $client->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $client->industry ?: '—' }}</td>
                        <td class="px-6 py-4">
                            @php $statusColors = ['active' => 'success', 'inactive' => 'gray', 'prospect' => 'blue-light']; $c = $statusColors[$client->status] ?? 'gray'; @endphp
                            <span class="inline-flex items-center rounded-full bg-{{ $c }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $c }}-700 dark:bg-{{ $c }}-500/10 dark:text-{{ $c }}-400">
                                {{ ucfirst($client->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('agency.clients.show', $client) }}" class="text-brand-500 hover:text-brand-600 text-xs font-medium">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-10 text-center text-sm text-gray-400">No clients yet. <a href="{{ route('agency.clients.create') }}" class="text-brand-500">Add one</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
