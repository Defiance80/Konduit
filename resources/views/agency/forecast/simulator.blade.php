@extends('layouts.app')
@section('title', 'Agency Simulator')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('agency.forecast.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Agency Simulator</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Model the impact of a new client or engagement before you commit</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Input form --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-5">Proposed Engagement</h2>
            <form action="{{ route('agency.forecast.simulate') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Monthly Retainer Value ($)</label>
                    <input type="number" name="retainer_value" value="{{ $retainerValue ?? '' }}" min="0" step="100"
                        placeholder="e.g. 3500"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Estimated Weekly Team Hours</label>
                    <input type="number" name="weekly_hours" value="{{ $weeklyHours ?? '' }}" min="0" step="1"
                        placeholder="e.g. 20"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Project Duration (weeks)</label>
                    <input type="number" name="project_duration_weeks" value="{{ $projectDuration ?? 12 }}" min="1"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
                <button type="submit" class="w-full rounded-lg bg-brand-500 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                    Run Simulation
                </button>
            </form>
        </div>

        {{-- Results --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-5">Impact Analysis</h2>
            @isset($simulation)
            <div class="space-y-4">
                {{-- Revenue impact --}}
                <div class="rounded-xl bg-success-50 dark:bg-success-500/10 border border-success-100 dark:border-success-800/40 px-4 py-4">
                    <p class="text-xs font-semibold text-success-700 dark:text-success-400 uppercase tracking-wide mb-2">Revenue Impact</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-success-600 dark:text-success-500">Current MRR</p>
                            <p class="text-lg font-bold text-success-800 dark:text-success-300">${{ number_format($current['mrr'], 0) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-success-600 dark:text-success-500">New MRR</p>
                            <p class="text-lg font-bold text-success-800 dark:text-success-300">${{ number_format($simulation['new_mrr'], 0) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-success-600 dark:text-success-500">New ARR</p>
                            <p class="text-lg font-bold text-success-800 dark:text-success-300">${{ number_format($simulation['new_arr'], 0) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-success-600 dark:text-success-500">3-Month Total</p>
                            <p class="text-lg font-bold text-success-800 dark:text-success-300">${{ number_format($simulation['new_3m'], 0) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Capacity impact --}}
                <div class="rounded-xl bg-{{ $simulation['avg_load_after'] >= 80 ? 'warning' : 'blue-light' }}-50 dark:bg-{{ $simulation['avg_load_after'] >= 80 ? 'warning' : 'blue-light' }}-500/10 border border-{{ $simulation['avg_load_after'] >= 80 ? 'warning' : 'blue-light' }}-100 dark:border-{{ $simulation['avg_load_after'] >= 80 ? 'warning' : 'blue-light' }}-800/40 px-4 py-4">
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">Capacity Impact</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-500">Current Load</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ round($simulation['avg_load_now']) }}%</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Projected Load</p>
                            <p class="text-lg font-bold {{ $simulation['avg_load_after'] >= 80 ? 'text-warning-600 dark:text-warning-400' : 'text-gray-900 dark:text-white' }}">{{ round($simulation['avg_load_after']) }}%</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total Hours</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($simulation['total_hours'], 0) }}h</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Weekly Commitment</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $simulation['team_hours_per_week'] }}h/wk</p>
                        </div>
                    </div>
                </div>

                {{-- Recommendation --}}
                <div class="rounded-xl border border-brand-200 dark:border-brand-800/60 bg-brand-50/50 dark:bg-brand-500/5 px-4 py-3">
                    <p class="text-xs font-semibold text-brand-700 dark:text-brand-300 uppercase tracking-wide mb-1.5">Recommendation</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $simulation['recommendation'] }}</p>
                </div>
            </div>
            @else
            <div class="flex flex-col items-center justify-center h-48 text-center">
                <svg class="size-12 text-gray-200 dark:text-gray-700 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <p class="text-sm text-gray-400">Fill in the engagement details and run the simulation to see the projected impact on revenue and team capacity.</p>
            </div>
            @endisset
        </div>
    </div>
</div>
@endsection
