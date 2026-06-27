@extends('layouts.app')
@section('title', 'Executive Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Executive Dashboard</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Agency intelligence — updated now</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('agency.forecast.index') }}" class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">Forecasting</a>
            <a href="{{ route('agency.relationship.index') }}" class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">Relationships</a>
        </div>
    </div>

    {{-- Strategy Intelligence Brief --}}
    @if($strategyBrief)
    @php
        $briefHealth = $strategyBrief['health'] ?? 'good';
        $briefColor  = match($briefHealth) { 'critical' => 'error', 'caution' => 'warning', default => 'success' };
    @endphp
    <div class="rounded-xl border border-{{ $briefColor }}-200/60 bg-gradient-to-r from-{{ $briefColor }}-50/60 to-white dark:from-{{ $briefColor }}-500/5 dark:to-gray-900 dark:border-{{ $briefColor }}-800/40 px-5 py-4">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="size-4 text-{{ $briefColor }}-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span class="text-xs font-semibold text-{{ $briefColor }}-700 dark:text-{{ $briefColor }}-400 uppercase tracking-wide">Strategy Intelligence</span>
                    @if(!empty($strategyBrief['generated_at']))
                    <span class="text-xs text-gray-400">· {{ \Carbon\Carbon::parse($strategyBrief['generated_at'])->diffForHumans() }}</span>
                    @endif
                </div>
                <p class="text-sm font-medium text-gray-900 dark:text-white mb-3">{{ $strategyBrief['headline'] }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach([
                        ['Revenue', $strategyBrief['revenue_note'] ?? null],
                        ['Risk', $strategyBrief['risk_note'] ?? null],
                        ['Opportunity', $strategyBrief['opportunity'] ?? null],
                    ] as [$label, $note])
                    @if($note)
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-0.5">{{ $label }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">{{ $note }}</p>
                    </div>
                    @endif
                    @endforeach
                </div>
                @if(!empty($strategyBrief['priorities']))
                <div class="mt-3 flex flex-wrap gap-1.5">
                    @foreach($strategyBrief['priorities'] as $i => $priority)
                    <span class="inline-flex items-center gap-1 rounded-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 px-2.5 py-0.5 text-xs text-gray-600 dark:text-gray-400">
                        <span class="font-semibold text-brand-500">{{ $i + 1 }}.</span>
                        {{ $priority }}
                    </span>
                    @endforeach
                </div>
                @endif
            </div>
            <form action="{{ route('agency.executive.brief') }}" method="POST" class="flex-shrink-0">
                @csrf
                <button type="submit" class="rounded-lg border border-gray-200 dark:border-gray-700 px-2.5 py-1.5 text-xs text-gray-400 hover:text-brand-500 hover:border-brand-200" title="Regenerate brief">
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 px-5 py-3 flex items-center justify-between">
        <p class="text-sm text-gray-400">No strategy brief yet.</p>
        <form action="{{ route('agency.executive.brief') }}" method="POST">
            @csrf
            <button type="submit" class="rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600">Generate AI Brief</button>
        </form>
    </div>
    @endif

    {{-- KPI Row --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @php
        $kpis = [
            ['label' => 'Monthly Recurring Revenue', 'value' => '$' . number_format($revenue['mrr'], 0), 'sub' => '$' . number_format($revenue['arr'], 0) . ' ARR', 'color' => 'success', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Active Projects', 'value' => $activeProjects->count(), 'sub' => $atRiskProjects->count() . ' at risk', 'color' => $atRiskProjects->count() > 0 ? 'warning' : 'brand', 'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z'],
            ['label' => 'Pipeline', 'value' => '$' . number_format($revenue['pipeline'], 0), 'sub' => '$' . number_format($revenue['overdue'], 0) . ' overdue', 'color' => $overdueInvoices > 0 ? 'error' : 'brand', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['label' => 'Team Members', 'value' => $teamCount, 'sub' => round($capacity['avg_load']) . '% avg load', 'color' => $capacity['avg_load'] >= 80 ? 'warning' : 'brand', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 px-5 py-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500">{{ $kpi['label'] }}</p>
                    <p class="mt-1.5 text-2xl font-bold text-gray-900 dark:text-white">{{ $kpi['value'] }}</p>
                    <p class="mt-0.5 text-xs text-{{ $kpi['color'] }}-600 dark:text-{{ $kpi['color'] }}-400">{{ $kpi['sub'] }}</p>
                </div>
                <div class="rounded-xl bg-{{ $kpi['color'] }}-50 dark:bg-{{ $kpi['color'] }}-500/10 p-2.5">
                    <svg class="size-5 text-{{ $kpi['color'] }}-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $kpi['icon'] }}"/></svg>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Middle row: At-risk projects + Client health --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- At-risk projects --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2">
                    <div class="size-2 rounded-full bg-error-400 animate-pulse"></div>
                    <h2 class="font-semibold text-gray-900 dark:text-white">At-Risk Projects</h2>
                </div>
                <a href="{{ route('agency.projects.index') }}" class="text-xs text-brand-500 hover:text-brand-600">View all</a>
            </div>
            @forelse($atRiskProjects as $project)
            <div class="flex items-center justify-between px-6 py-3 border-b border-gray-50 dark:border-gray-800/50 last:border-0">
                <div class="min-w-0 flex-1">
                    <a href="{{ route('agency.projects.show', $project) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500 truncate block">{{ $project->name }}</a>
                    <p class="text-xs text-gray-400">{{ $project->client->name }}</p>
                </div>
                <div class="flex items-center gap-3 ml-4 flex-shrink-0">
                    <div class="text-right">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $project->progress }}%</p>
                        @if($project->due_date)
                        @php $daysLeft = now()->diffInDays($project->due_date, false); @endphp
                        <p class="text-xs {{ $daysLeft < 0 ? 'text-error-500' : 'text-warning-500' }}">
                            {{ $daysLeft < 0 ? abs((int)$daysLeft) . 'd overdue' : (int)$daysLeft . 'd left' }}
                        </p>
                        @endif
                    </div>
                    <div class="h-8 w-1.5 rounded-full bg-gray-100 dark:bg-gray-800">
                        <div class="rounded-full bg-error-400" style="height: {{ $project->progress }}%; margin-top: {{ 100 - $project->progress }}%;"></div>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center">
                <p class="text-sm text-gray-400">No at-risk projects. All clear.</p>
            </div>
            @endforelse
        </div>

        {{-- Client health heatmap --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="font-semibold text-gray-900 dark:text-white">Client Relationship Health</h2>
                <a href="{{ route('agency.relationship.index') }}" class="text-xs text-brand-500 hover:text-brand-600">View all</a>
            </div>
            @forelse($clientScores as $score)
            <div class="flex items-center gap-4 px-6 py-3 border-b border-gray-50 dark:border-gray-800/50 last:border-0">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1.5">
                        <a href="{{ route('agency.clients.show', $score->client) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500 truncate">{{ $score->client->name }}</a>
                        <span class="ml-2 flex-shrink-0 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                            bg-{{ $score->risk_color }}-50 text-{{ $score->risk_color }}-700
                            dark:bg-{{ $score->risk_color }}-500/10 dark:text-{{ $score->risk_color }}-400">
                            {{ ucfirst($score->churn_risk_level) }} risk
                        </span>
                    </div>
                    <div class="h-1.5 rounded-full bg-gray-100 dark:bg-gray-800">
                        <div class="h-1.5 rounded-full bg-{{ $score->engagement_color }}-400 transition-all"
                            style="width: {{ $score->engagement_score }}%"></div>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">{{ $score->engagement_score }}% engagement</p>
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center">
                <p class="text-sm text-gray-400">No client health data yet.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Capacity + Intelligence Feed --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- 8-week capacity chart --}}
        <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="font-semibold text-gray-900 dark:text-white">8-Week Capacity Forecast</h2>
                <a href="{{ route('agency.forecast.index') }}" class="text-xs text-brand-500 hover:text-brand-600">Full forecast</a>
            </div>
            <div class="px-6 py-4">
                <div class="flex items-end gap-2 h-32">
                    @foreach($capacity['weeks'] as $week)
                    @php
                        $h = max(4, $week['capacity']);
                        $color = $week['status'] === 'critical' ? 'error' : ($week['status'] === 'high' ? 'warning' : 'brand');
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1" title="{{ $week['capacity'] }}% — {{ $week['tasks'] }} tasks">
                        <div class="w-full rounded-t bg-{{ $color }}-400/80 hover:bg-{{ $color }}-500 transition-colors"
                            style="height: {{ $h }}%"></div>
                        <span class="text-xs text-gray-400 truncate w-full text-center">{{ $week['label'] }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="flex items-center gap-4 mt-3 text-xs text-gray-400">
                    <span class="flex items-center gap-1"><span class="size-2 rounded-full bg-brand-400 inline-block"></span>Normal</span>
                    <span class="flex items-center gap-1"><span class="size-2 rounded-full bg-warning-400 inline-block"></span>High (70%+)</span>
                    <span class="flex items-center gap-1"><span class="size-2 rounded-full bg-error-400 inline-block"></span>Critical (90%+)</span>
                </div>
            </div>
        </div>

        {{-- AI Intelligence Feed --}}
        <div class="rounded-2xl border border-brand-200/60 bg-gradient-to-br from-brand-50/40 to-white dark:from-brand-500/5 dark:to-gray-900 dark:border-brand-800/40">
            <div class="flex items-center gap-2 px-5 py-4 border-b border-brand-100 dark:border-brand-800/30">
                <svg class="size-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                <h3 class="text-sm font-semibold text-brand-700 dark:text-brand-300">Intelligence Feed</h3>
            </div>
            <div class="px-4 py-3 space-y-3 max-h-72 overflow-y-auto">
                @forelse($intelligenceFeed as $item)
                <div class="rounded-lg bg-white dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 p-3">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center rounded-full bg-brand-50 px-1.5 py-0.5 text-xs text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                            {{ str_replace('_', ' ', $item->type) }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $item->updated_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed line-clamp-3">{{ $item->content }}</p>
                </div>
                @empty
                <div class="py-4 text-center">
                    <p class="text-xs text-gray-400">Generate AI summaries on projects and clients to populate the feed.</p>
                    <a href="{{ route('agency.projects.index') }}" class="mt-2 inline-block text-xs text-brand-500 hover:underline">Go to projects →</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Upcoming deadlines --}}
    @if($deadlines->count() > 0)
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-white">Upcoming Deadlines <span class="text-sm font-normal text-gray-400">(next 45 days)</span></h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($deadlines as $dl)
            <div class="flex items-center justify-between px-6 py-3">
                <div class="flex items-center gap-3 min-w-0">
                    @if($dl['at_risk'])
                    <div class="size-1.5 rounded-full bg-error-400 flex-shrink-0"></div>
                    @else
                    <div class="size-1.5 rounded-full bg-gray-300 dark:bg-gray-600 flex-shrink-0"></div>
                    @endif
                    <div class="min-w-0">
                        <a href="{{ route('agency.projects.show', $dl['id']) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500 truncate block">{{ $dl['name'] }}</a>
                        <p class="text-xs text-gray-400">{{ $dl['client'] }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-6 flex-shrink-0 ml-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Progress</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $dl['progress'] }}%</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Due</p>
                        <p class="text-sm font-semibold {{ $dl['days_left'] < 7 ? 'text-error-600 dark:text-error-400' : 'text-gray-900 dark:text-white' }}">
                            {{ $dl['days_left'] }}d
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
