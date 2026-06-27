@extends('layouts.app')
@section('title', 'Support')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Support</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Submit and track requests, questions, and feedback.</p>
        </div>
        <a href="{{ route('client.tickets.create') }}"
            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Request
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-center">
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['open'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Open</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-center">
            <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">{{ $stats['waiting'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Waiting on You</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-center">
            <p class="text-2xl font-bold text-success-600 dark:text-success-400">{{ $stats['resolved'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Resolved</p>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    {{-- Tickets --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
            @forelse($tickets as $ticket)
            @php
                $statusColors = ['open'=>'blue','in_progress'=>'warning','waiting'=>'orange','resolved'=>'success','closed'=>'gray'];
                $sc = $statusColors[$ticket->status] ?? 'gray';
                $typeIcons = ['bug'=>'🐛','feature'=>'✨','question'=>'❓','design'=>'🎨','content'=>'📝','general'=>'💬'];
            @endphp
            <a href="{{ route('client.tickets.show', $ticket) }}"
                class="flex items-start gap-4 px-6 py-4 hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors group">
                <div class="text-lg mt-0.5 flex-shrink-0">{{ $typeIcons[$ticket->type] ?? '💬' }}</div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-brand-500 transition-colors">{{ $ticket->subject }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-400">
                        <span>{{ $ticket->ticket_number }}</span>
                        @if($ticket->project)<span>{{ $ticket->project->name }}</span>@endif
                        <span>{{ $ticket->created_at->diffForHumans() }}</span>
                        @if($ticket->assignee)<span>Assigned to {{ $ticket->assignee->name }}</span>@endif
                    </div>
                </div>
                <span class="flex-shrink-0 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-{{ $sc }}-50 text-{{ $sc }}-700 dark:bg-{{ $sc }}-500/10 dark:text-{{ $sc }}-400">
                    {{ str_replace('_',' ',ucfirst($ticket->status)) }}
                </span>
            </a>
            @empty
            <div class="py-16 text-center">
                <svg class="mx-auto size-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                <p class="text-gray-500 dark:text-gray-400 font-medium">No requests yet</p>
                <a href="{{ route('client.tickets.create') }}" class="mt-3 inline-block text-sm text-brand-500 hover:text-brand-600 font-medium">Submit your first request</a>
            </div>
            @endforelse
        </div>
        @if($tickets->hasPages())
        <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-800">{{ $tickets->links() }}</div>
        @endif
    </div>
</div>
@endsection
