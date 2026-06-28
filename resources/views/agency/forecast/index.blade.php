@extends('layouts.app')
@section('title', 'Resource Forecasting')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Resource Forecasting</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Revenue projections and capacity outlook</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('agency.forecast.simulator') }}" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Agency Simulator</a>
            <a href="{{ route('agency.executive.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400">← Dashboard</a>
        </div>
    </div>

    {{-- Revenue cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach([
            ['MRR', '$' . number_format($revenue['mrr'], 0), 'Monthly recurring', 'success'],
            ['ARR', '$' . number_format($revenue['arr'], 0), 'Annual run-rate', 'brand'],
            ['Pipeline', '$' . number_format($revenue['pipeline'], 0), 'Pending invoices', 'blue-light'],
            ['3-Month Projection', '$' . number_format($revenue['projected_3m'], 0), 'MRR + pipeline', 'success'],
        ] as [$label, $value, $sub, $color])
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 px-5 py-4">
            <p class="text-xs text-gray-400">{{ $label }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $value }}</p>
            <p class="text-xs text-{{ $color }}-600 dark:text-{{ $color }}-400 mt-0.5">{{ $sub }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- 3-month revenue bars --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="font-semibold text-gray-900 dark:text-white">Revenue Forecast — Next 3 Months</h2>
            </div>
            <div class="px-6 py-6">
                @php $maxVal = max(array_merge(array_column($revenue['months'], 'total'), [1])); @endphp
                <div class="flex items-end gap-4 h-40">
                    @foreach($revenue['months'] as $month)
                    @php $pct = max(4, round(($month['total'] / $maxVal) * 100)); @endphp
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">${{ number_format($month['total'], 0) }}</span>
                        <div class="w-full rounded-t-lg bg-brand-400/80" style="height: {{ $pct }}%"></div>
                        <span class="text-xs text-gray-400">{{ $month['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Team load --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="font-semibold text-gray-900 dark:text-white">Team Load</h2>
                <span class="text-sm text-gray-400">Avg {{ round($capacity['avg_load']) }}%</span>
            </div>
            <div class="px-6 py-4 space-y-4">
                @forelse($capacity['team_load'] as $member)
                @php
                    $loadColor = $member['load'] >= 80 ? 'error' : ($member['load'] >= 60 ? 'warning' : 'success');
                @endphp
                <div>
                    <div class="flex items-center justify-between text-sm mb-1.5">
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $member['name'] }}</span>
                        <span class="text-xs text-{{ $loadColor }}-600 dark:text-{{ $loadColor }}-400">{{ $member['tasks'] }} tasks · {{ $member['load'] }}%</span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-800">
                        <div class="h-2 rounded-full bg-{{ $loadColor }}-400 transition-all" style="width: {{ $member['load'] }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 py-4 text-center">No team members found.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 8-week capacity --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-white">8-Week Capacity Outlook</h2>
        </div>
        <div class="px-6 py-4">
            <div class="flex items-end gap-2 h-40">
                @foreach($capacity['weeks'] as $week)
                @php
                    $h = max(4, $week['capacity']);
                    $color = $week['status'] === 'critical' ? 'error' : ($week['status'] === 'high' ? 'warning' : 'brand');
                @endphp
                <div class="flex-1 flex flex-col items-center gap-1.5">
                    <span class="text-xs font-medium text-gray-500">{{ $week['capacity'] }}%</span>
                    <div class="w-full rounded-t bg-{{ $color }}-400/80 hover:bg-{{ $color }}-500 transition-colors" style="height: {{ $h }}%"></div>
                    <span class="text-xs text-gray-400 truncate w-full text-center">{{ $week['label'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Upcoming deadlines --}}
    @if($deadlines->count() > 0)
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-white">Upcoming Project Deadlines</h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($deadlines as $dl)
            <div class="flex items-center gap-4 px-6 py-3">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('agency.projects.show', $dl['id']) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500">{{ $dl['name'] }}</a>
                    <p class="text-xs text-gray-400">{{ $dl['client'] }}</p>
                </div>
                <div class="flex items-center gap-4 flex-shrink-0">
                    <div class="h-1.5 w-24 rounded-full bg-gray-100 dark:bg-gray-800">
                        <div class="h-1.5 rounded-full {{ $dl['at_risk'] ? 'bg-error-400' : 'bg-brand-400' }}" style="width: {{ $dl['progress'] }}%"></div>
                    </div>
                    <span class="text-sm font-medium {{ $dl['days_left'] < 7 ? 'text-error-600 dark:text-error-400' : 'text-gray-700 dark:text-gray-300' }} w-14 text-right">
                        {{ $dl['days_left'] < 0 ? abs($dl['days_left']) . 'd late' : $dl['days_left'] . 'd left' }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
