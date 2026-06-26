@extends('layouts.app')
@section('title', 'Tickets')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Tickets</h1>
            <p class="text-sm text-gray-500 mt-1">Manage all client support and tasks</p>
        </div>
        <a href="{{ route('agency.tickets.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Ticket
        </a>
    </div>

    <!-- Status Tabs -->
    <div class="flex gap-2 flex-wrap">
        @php
        $statuses = ['all' => 'All', 'open' => 'Open', 'in_progress' => 'In Progress', 'waiting' => 'Waiting', 'resolved' => 'Resolved'];
        $current = request('status', 'all');
        @endphp
        @foreach($statuses as $value => $label)
        <a href="{{ request()->fullUrlWithQuery(['status' => $value === 'all' ? null : $value]) }}"
           class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium {{ $current === $value || ($value === 'all' && !request('status')) ? 'bg-brand-500 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300' }}">
            {{ $label }}
            <span class="text-xs {{ $current === $value || ($value === 'all' && !request('status')) ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }} rounded-full px-1.5 py-0.5">{{ $statusCounts[$value] }}</span>
        </a>
        @endforeach
    </div>

    <!-- Filters -->
    <form method="GET" class="flex gap-3 flex-wrap">
        @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tickets..."
               class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 w-64">
        <select name="priority" class="h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-700 focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">All priorities</option>
            @foreach(['urgent', 'high', 'medium', 'low'] as $p)
            <option value="{{ $p }}" {{ request('priority') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
            @endforeach
        </select>
        <button type="submit" class="h-10 rounded-lg bg-gray-100 px-4 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">Filter</button>
    </form>

    <!-- Table -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-6 py-3.5 text-left text-xs font-medium uppercase text-gray-400">#</th>
                        <th class="px-6 py-3.5 text-left text-xs font-medium uppercase text-gray-400">Subject</th>
                        <th class="px-6 py-3.5 text-left text-xs font-medium uppercase text-gray-400">Client</th>
                        <th class="px-6 py-3.5 text-left text-xs font-medium uppercase text-gray-400">Status</th>
                        <th class="px-6 py-3.5 text-left text-xs font-medium uppercase text-gray-400">Priority</th>
                        <th class="px-6 py-3.5 text-left text-xs font-medium uppercase text-gray-400">Assignee</th>
                        <th class="px-6 py-3.5 text-left text-xs font-medium uppercase text-gray-400">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($tickets as $ticket)
                    @php
                    $statusColors = ['open' => 'blue-light', 'in_progress' => 'warning', 'waiting' => 'orange', 'resolved' => 'success', 'closed' => 'gray'];
                    $priorityColors = ['urgent' => 'error', 'high' => 'warning', 'medium' => 'blue-light', 'low' => 'gray'];
                    $sc = $statusColors[$ticket->status] ?? 'gray';
                    $pc = $priorityColors[$ticket->priority] ?? 'gray';
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                        <td class="px-6 py-4 text-xs text-gray-400 font-mono">{{ $ticket->ticket_number }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('agency.tickets.show', $ticket) }}" class="font-medium text-gray-900 dark:text-white hover:text-brand-500">{{ Str::limit($ticket->subject, 50) }}</a>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $ticket->client->name }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full bg-{{ $sc }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $sc }}-700 dark:bg-{{ $sc }}-500/10 dark:text-{{ $sc }}-400">
                                {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full bg-{{ $pc }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $pc }}-700 dark:bg-{{ $pc }}-500/10 dark:text-{{ $pc }}-400">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($ticket->assignee)
                                <div class="flex items-center gap-2">
                                    <img src="{{ $ticket->assignee->avatar_url }}" class="size-6 rounded-full" alt="{{ $ticket->assignee->name }}">
                                    <span class="text-gray-600 dark:text-gray-400 text-xs">{{ $ticket->assignee->name }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">Unassigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $ticket->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-sm text-gray-400">No tickets found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
        <div class="border-t border-gray-100 dark:border-gray-800 px-6 py-4">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
