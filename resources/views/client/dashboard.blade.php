@extends('layouts.app')
@section('title', 'My Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Welcome banner --}}
    <div class="rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 px-6 py-6 text-white">
        <p class="text-sm font-medium text-brand-100 mb-1">Welcome back</p>
        <h1 class="text-2xl font-bold">{{ auth()->user()->name }}</h1>
        <p class="text-sm text-brand-200 mt-1">Here's what's happening with your account today.</p>
    </div>

    {{-- Action items --}}
    @if($pendingDeliverables->isNotEmpty())
    <div class="rounded-2xl border border-warning-200 bg-warning-50 px-5 py-4 dark:border-warning-800/50 dark:bg-warning-500/10">
        <div class="flex items-center gap-3">
            <div class="size-9 rounded-lg bg-warning-100 dark:bg-warning-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="size-5 text-warning-600 dark:text-warning-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-warning-900 dark:text-warning-200">You have {{ $pendingDeliverables->count() }} item{{ $pendingDeliverables->count() > 1 ? 's' : '' }} waiting for your review</p>
                <p class="text-xs text-warning-700 dark:text-warning-400 mt-0.5">These need your approval before we can move forward.</p>
            </div>
            <a href="{{ route('client.deliverables.index') }}"
                class="ml-auto flex-shrink-0 rounded-lg bg-warning-500 px-4 py-2 text-xs font-semibold text-white hover:bg-warning-600">
                Review Now
            </a>
        </div>
    </div>
    @endif

    {{-- KPI row --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3">Active Projects</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['active_projects'] }}</p>
            <p class="text-xs text-gray-400 mt-1">in progress right now</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3">Open Requests</p>
            <p class="text-3xl font-bold {{ $stats['open_tickets'] > 0 ? 'text-warning-600 dark:text-warning-400' : 'text-gray-900 dark:text-white' }}">{{ $stats['open_tickets'] }}</p>
            <p class="text-xs text-gray-400 mt-1">support tickets</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3">Awaiting Review</p>
            <p class="text-3xl font-bold {{ $stats['pending_deliverables'] > 0 ? 'text-brand-600 dark:text-brand-400' : 'text-gray-900 dark:text-white' }}">{{ $stats['pending_deliverables'] }}</p>
            <p class="text-xs text-gray-400 mt-1">items need your sign-off</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3">Monthly Plan</p>
            @if($retainer)
            <p class="text-2xl font-bold text-success-600 dark:text-success-400">${{ number_format($retainer->monthly_value) }}</p>
            <p class="text-xs text-success-600 dark:text-success-400 mt-1">Active plan</p>
            @else
            <p class="text-xl font-bold text-gray-400">None</p>
            <p class="text-xs text-gray-400 mt-1">no active plan</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Projects with progress rings --}}
        <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Your Projects</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Where things stand right now.</p>
                </div>
                <a href="{{ route('client.projects.index') }}" class="text-xs text-brand-600 dark:text-brand-400 hover:underline">View all</a>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($activeProjects as $project)
                @php
                    $pct = $project->progress ?? 0;
                    $circumference = round(2 * 3.14159 * 15.9155, 2);
                    $offset = round($circumference * (1 - $pct / 100), 2);
                    $color = $pct >= 80 ? '#10B981' : ($pct >= 40 ? '#465FFF' : '#F59E0B');
                    $daysLeft = $project->due_date ? now()->diffInDays($project->due_date, false) : null;
                @endphp
                <a href="{{ route('client.projects.show', $project) }}"
                    class="flex items-center gap-5 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">

                    {{-- Progress ring --}}
                    <div class="relative flex-shrink-0">
                        <svg class="size-14 -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#E5E7EB" stroke-width="3" class="dark:stroke-gray-700"/>
                            <circle cx="18" cy="18" r="15.9155" fill="none"
                                stroke="{{ $color }}" stroke-width="3"
                                stroke-dasharray="{{ $circumference }}"
                                stroke-dashoffset="{{ $offset }}"
                                stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $pct }}%</span>
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:text-brand-600 dark:group-hover:text-brand-400">{{ $project->name }}</p>
                            <svg class="size-4 text-gray-300 dark:text-gray-600 flex-shrink-0 mt-0.5 group-hover:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            @if($daysLeft !== null)
                                @if($daysLeft < 0)
                                <span class="text-error-600 dark:text-error-400">Overdue by {{ abs($daysLeft) }} days</span>
                                @elseif($daysLeft <= 7)
                                <span class="text-warning-600 dark:text-warning-400">Due in {{ $daysLeft }} days</span>
                                @else
                                Due {{ $project->due_date->format('M j') }}
                                @endif
                            @else
                            In progress
                            @endif
                        </p>
                        {{-- Progress bar --}}
                        <div class="mt-2 h-1 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-1 rounded-full transition-all" style="width:{{ $pct }}%; background:{{ $color }}"></div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="px-6 py-12 text-center">
                    <svg class="size-10 mx-auto text-gray-200 dark:text-gray-700 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-sm text-gray-400">No active projects at the moment.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Right column: Retainer + Recent tickets --}}
        <div class="space-y-5">

            {{-- Retainer card with pie chart --}}
            @if($retainer)
            @php
                $used = min(100, $retainer->hours_used ?? 0);
                $total = $retainer->hours_included ?? 0;
                $usedPct = $total > 0 ? round(($used / $total) * 100) : 0;
                $circ = round(2 * 3.14159 * 15.9155, 2);
                $retOffset = round($circ * (1 - $usedPct / 100), 2);
                $retColor = $usedPct >= 90 ? '#EF4444' : ($usedPct >= 75 ? '#F59E0B' : '#10B981');
            @endphp
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Monthly Plan</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $retainer->name }}</p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-success-50 px-2 py-0.5 text-[10px] font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
                        <span class="mr-1 size-1.5 rounded-full bg-success-500"></span> Active
                    </span>
                </div>

                {{-- Budget pie chart --}}
                <div class="flex items-center gap-4">
                    <div class="relative flex-shrink-0">
                        <svg class="size-20 -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#E5E7EB" stroke-width="3.5" class="dark:stroke-gray-700"/>
                            <circle cx="18" cy="18" r="15.9155" fill="none"
                                stroke="{{ $retColor }}" stroke-width="3.5"
                                stroke-dasharray="{{ $circ }}"
                                stroke-dashoffset="{{ $retOffset }}"
                                stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $usedPct }}%</span>
                            <span class="text-[8px] text-gray-400">used</span>
                        </div>
                    </div>
                    <div class="space-y-2 flex-1">
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-gray-500 dark:text-gray-400">Budget</span>
                                <span class="font-medium text-gray-900 dark:text-white">${{ number_format($retainer->monthly_value) }}/mo</span>
                            </div>
                        </div>
                        @if($total > 0)
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-gray-500 dark:text-gray-400">Hours</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $used }}/{{ $total }}</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="h-1.5 rounded-full transition-all" style="width:{{ $usedPct }}%; background:{{ $retColor }}"></div>
                            </div>
                        </div>
                        @endif
                        <a href="{{ route('client.retainer.index') }}"
                            class="block text-center rounded-lg border border-brand-200 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-50 dark:border-brand-800 dark:text-brand-400 dark:hover:bg-brand-500/10 mt-2">
                            View full plan
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Recent support requests --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Recent Requests</h3>
                    <a href="{{ route('client.tickets.create') }}" class="inline-flex items-center gap-1 text-xs text-brand-600 dark:text-brand-400 hover:underline">
                        <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New
                    </a>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($recentTickets as $ticket)
                    @php
                        $statusMap = ['open'=>['bg'=>'bg-blue-100','text'=>'text-blue-700','label'=>'Open'],'in_progress'=>['bg'=>'bg-warning-100','text'=>'text-warning-700','label'=>'In Progress'],'resolved'=>['bg'=>'bg-success-100','text'=>'text-success-700','label'=>'Resolved'],'closed'=>['bg'=>'bg-gray-100','text'=>'text-gray-600','label'=>'Closed']];
                        $sc = $statusMap[$ticket->status] ?? ['bg'=>'bg-gray-100','text'=>'text-gray-600','label'=>ucfirst($ticket->status)];
                    @endphp
                    <a href="{{ route('client.tickets.show', $ticket) }}" class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 dark:text-white truncate">{{ $ticket->subject }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $ticket->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="flex-shrink-0 rounded-full px-2 py-0.5 text-[10px] font-medium {{ $sc['bg'] }} {{ $sc['text'] }} dark:bg-opacity-20">{{ $sc['label'] }}</span>
                    </a>
                    @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-400">
                        <p>No requests yet.</p>
                        <a href="{{ route('client.tickets.create') }}" class="text-brand-600 dark:text-brand-400 hover:underline mt-1 block">Submit your first request</a>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent reports (if any visible to client) --}}
            @if($recentReports->isNotEmpty())
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Latest Reports</h3>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-gray-800">
                    @foreach($recentReports as $report)
                    <a href="{{ route('client.reports.index') }}" class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <div class="size-8 rounded-lg bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center flex-shrink-0">
                            <svg class="size-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 dark:text-white truncate">{{ $report->title ?? 'AI Report' }}</p>
                            <p class="text-xs text-gray-400">{{ $report->created_at->format('M j') }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- Quick links --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach([
            ['route' => 'client.projects.index', 'label' => 'All Projects', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => '#465FFF'],
            ['route' => 'client.deliverables.index', 'label' => 'Files to Approve', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0', 'color' => '#10B981'],
            ['route' => 'client.tickets.index', 'label' => 'Support', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'color' => '#F59E0B'],
            ['route' => 'client.reports.index', 'label' => 'Reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => '#8B5CF6'],
        ] as $link)
        <a href="{{ route($link['route']) }}"
            class="flex items-center gap-3 rounded-2xl border border-gray-200 bg-white p-4 hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-gray-800/50 transition-colors group">
            <div class="size-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:{{ $link['color'] }}15">
                <svg class="size-5" style="color:{{ $link['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $link['icon'] }}"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">{{ $link['label'] }}</span>
            <svg class="size-4 ml-auto text-gray-300 dark:text-gray-600 group-hover:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endforeach
    </div>

</div>
@endsection
