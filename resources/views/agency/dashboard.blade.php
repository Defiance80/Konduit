@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
                {{ explode(' ', auth()->user()->name)[0] }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ now()->format('l, F j, Y') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('agency.tickets.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Ticket
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @php
        $statCards = [
            ['label' => 'Total Clients',     'value' => $stats['clients'],          'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'brand'],
            ['label' => 'Active Projects',   'value' => $stats['active_projects'],  'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color' => 'success'],
            ['label' => 'Open Tickets',      'value' => $stats['open_tickets'],     'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', 'color' => 'warning'],
            ['label' => 'Active Retainers',  'value' => $stats['active_retainers'], 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'blue-light'],
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

    {{-- Main Grid: Projects + Tickets --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Active Projects --}}
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

        {{-- Open Tickets --}}
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
                            @php $priorityColors = ['urgent' => 'error', 'high' => 'warning', 'medium' => 'blue-light', 'low' => 'gray']; $pc = $priorityColors[$ticket->priority] ?? 'gray'; @endphp
                            <span class="shrink-0 inline-flex items-center rounded-full bg-{{ $pc }}-50 px-2 py-0.5 text-xs font-medium text-{{ $pc }}-700 dark:bg-{{ $pc }}-500/10 dark:text-{{ $pc }}-400">
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

    {{-- Marketing Intelligence + Training --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- News Feed --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2.5">
                        <div class="flex size-7 items-center justify-center rounded-lg bg-brand-50 dark:bg-brand-500/10">
                            <svg class="size-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                        </div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Marketing Intelligence</h2>
                        <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800 px-2 py-0.5 text-xs text-gray-500 dark:text-gray-400">
                            {{ $newsBrief['generated_at'] ?? now()->format('M j') }}
                        </span>
                    </div>
                    <a href="{{ route('agency.news.index') }}" class="text-sm text-brand-500 hover:text-brand-600">View all</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @php
                        $newsTypeIcons = [
                            'trend'    => ['icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',     'color' => 'brand'],
                            'platform' => ['icon' => 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z', 'color' => 'blue-light'],
                            'industry' => ['icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z', 'color' => 'success'],
                            'tip'      => ['icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z', 'color' => 'warning'],
                        ];
                        $newsItems = array_slice($newsBrief['items'] ?? [], 0, 4);
                    @endphp
                    @forelse($newsItems as $item)
                    @php $ncfg = $newsTypeIcons[$item['type'] ?? 'industry'] ?? $newsTypeIcons['industry']; @endphp
                    <div class="flex items-start gap-4 px-6 py-4">
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-{{ $ncfg['color'] }}-50 dark:bg-{{ $ncfg['color'] }}-500/10 mt-0.5">
                            <svg class="size-4 text-{{ $ncfg['color'] }}-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $ncfg['icon'] }}"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white leading-snug mb-0.5">{{ $item['headline'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">{{ $item['summary'] }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-sm text-gray-400">
                        <a href="{{ route('agency.news.index') }}" class="text-brand-500 hover:text-brand-600">Generate your first intelligence brief →</a>
                    </div>
                    @endforelse
                </div>
                @if(count($newsBrief['items'] ?? []) > 4)
                <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ route('agency.news.index') }}" class="text-xs text-brand-500 hover:text-brand-600 font-medium">
                        + {{ count($newsBrief['items']) - 4 }} more items →
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Training Academy sidebar --}}
        <div>
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="px-6 pt-5 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-warning-50 dark:bg-warning-500/10">
                                <svg class="size-3.5 text-warning-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                            </div>
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Training Academy</h2>
                        </div>
                        <a href="{{ route('agency.training.index') }}" class="text-sm text-brand-500 hover:text-brand-600">View all</a>
                    </div>
                </div>

                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-2">
                        <span>Your progress</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $trainingProgress }}%</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                        <div class="h-2 rounded-full bg-warning-500 transition-all" style="width: {{ $trainingProgress }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-600 mt-1.5">{{ $completedLessons }} / {{ $totalLessons }} lessons done</p>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($trainingCourses as $course)
                    @php $cp = $course->user_progress; @endphp
                    <a href="{{ route('agency.training.show', $course) }}"
                       class="flex items-center gap-3 px-6 py-3.5 hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors group">
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                            @if($cp >= 100)
                            <svg class="size-4 text-success-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @elseif($cp > 0)
                            <span class="text-xs font-bold text-brand-500">{{ $cp }}%</span>
                            @else
                            <svg class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">{{ $course->title }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-600">{{ $course->lessons_count }} lessons</p>
                        </div>
                    </a>
                    @empty
                    <div class="px-6 py-6 text-center text-sm text-gray-400">No courses yet</div>
                    @endforelse
                </div>

                <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ route('agency.training.index') }}"
                       class="flex w-full items-center justify-center gap-1.5 rounded-xl bg-warning-50 dark:bg-warning-500/10 py-2.5 text-sm font-medium text-warning-700 dark:text-warning-400 hover:bg-warning-100 dark:hover:bg-warning-500/20 transition-colors">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        Browse All Courses
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Clients --}}
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
                            @php $statusColors = ['active' => 'success', 'inactive' => 'gray', 'prospect' => 'blue-light']; $sc = $statusColors[$client->status] ?? 'gray'; @endphp
                            <span class="inline-flex items-center rounded-full bg-{{ $sc }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $sc }}-700 dark:bg-{{ $sc }}-500/10 dark:text-{{ $sc }}-400">
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
