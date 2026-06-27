@extends('layouts.app')
@section('title', 'Relationship Intelligence')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Relationship Intelligence</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Client health scores calculated from engagement signals</p>
        </div>
        <a href="{{ route('agency.executive.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">← Executive Dashboard</a>
    </div>

    @if(session('success'))
    <div class="rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    {{-- Summary row --}}
    @php
    $byLevel = $scores->groupBy('churn_risk_level');
    $levelCounts = ['low' => 0, 'medium' => 0, 'high' => 0, 'critical' => 0];
    foreach($byLevel as $level => $items) $levelCounts[$level] = $items->count();
    @endphp
    <div class="grid grid-cols-4 gap-4">
        @foreach([['low','success','Healthy'],['medium','blue-light','Watch'],['high','warning','At Risk'],['critical','error','Critical']] as [$lvl,$color,$label])
        <div class="rounded-2xl border border-{{ $color }}-200 bg-{{ $color }}-50 dark:border-{{ $color }}-800/40 dark:bg-{{ $color }}-500/5 px-5 py-4 text-center">
            <p class="text-2xl font-bold text-{{ $color }}-700 dark:text-{{ $color }}-400">{{ $levelCounts[$lvl] }}</p>
            <p class="text-xs font-medium text-{{ $color }}-600 dark:text-{{ $color }}-400 mt-0.5">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    {{-- Client cards --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($scores as $score)
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            {{-- Risk band --}}
            <div class="h-1 bg-{{ $score->risk_color }}-400"></div>
            <div class="px-5 py-4">
                <div class="flex items-start justify-between mb-4">
                    <div class="min-w-0">
                        <a href="{{ route('agency.clients.show', $score->client) }}"
                            class="font-semibold text-gray-900 dark:text-white hover:text-brand-500 truncate block">{{ $score->client->name }}</a>
                        <span class="inline-flex items-center rounded-full mt-1 px-2 py-0.5 text-xs font-medium
                            bg-{{ $score->risk_color }}-50 text-{{ $score->risk_color }}-700
                            dark:bg-{{ $score->risk_color }}-500/10 dark:text-{{ $score->risk_color }}-400">
                            {{ ucfirst($score->churn_risk_level) }} churn risk
                        </span>
                    </div>
                    <form action="{{ route('agency.relationship.recalculate', $score->client) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-300 hover:text-brand-500 dark:text-gray-600 dark:hover:text-brand-400" title="Recalculate">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </form>
                </div>

                {{-- Engagement score --}}
                <div class="mb-3">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500 dark:text-gray-400">Engagement</span>
                        <span class="font-medium text-{{ $score->engagement_color }}-600 dark:text-{{ $score->engagement_color }}-400">{{ $score->engagement_score }}%</span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-800">
                        <div class="h-2 rounded-full bg-{{ $score->engagement_color }}-400 transition-all" style="width: {{ $score->engagement_score }}%"></div>
                    </div>
                </div>

                {{-- Factors --}}
                @if($score->factors)
                <div class="space-y-1.5 mt-3">
                    @foreach($score->factors as $key => $factor)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500 dark:text-gray-400">{{ $factor['label'] }}</span>
                        <span class="font-medium {{ $factor['earned'] >= ($factor['weight'] * 0.5) ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400' }}">
                            {{ $factor['earned'] }}/{{ $factor['weight'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($score->calculated_at)
                <p class="text-xs text-gray-400 mt-3">Calculated {{ $score->calculated_at->diffForHumans() }}</p>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-3 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 px-6 py-12 text-center">
            <p class="text-gray-400">No client health scores yet. Add clients to get started.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
