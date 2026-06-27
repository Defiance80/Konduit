@extends('layouts.app')
@section('title', 'My Retainer')

@section('content')
<div class="space-y-6 max-w-3xl">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Your Retainer</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Details about your ongoing agreement with us.</p>
    </div>

    @forelse($retainers as $retainer)
    @php
        $statusColor = ['active'=>'success','paused'=>'warning','cancelled'=>'error','completed'=>'gray'][$retainer->status] ?? 'blue';
        $now = now();
        $daysUntilRenewal = $retainer->end_date ? $now->diffInDays($retainer->end_date, false) : null;
    @endphp

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $retainer->name }}</h2>
                @if($retainer->description)<p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $retainer->description }}</p>@endif
            </div>
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-{{ $statusColor }}-50 text-{{ $statusColor }}-700 dark:bg-{{ $statusColor }}-500/10 dark:text-{{ $statusColor }}-400">
                {{ ucfirst($retainer->status) }}
            </span>
        </div>

        {{-- Key numbers --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y divide-gray-100 dark:divide-gray-800">
            @if($retainer->monthly_value)
            <div class="p-5 text-center">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($retainer->monthly_value, 0) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Monthly Value</p>
            </div>
            @endif
            @if($retainer->hours_included)
            <div class="p-5 text-center">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $retainer->hours_included }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Hours / Month</p>
            </div>
            @endif
            @if($retainer->start_date)
            <div class="p-5 text-center">
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $retainer->start_date->format('M Y') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Started</p>
            </div>
            @endif
            @if($daysUntilRenewal !== null)
            <div class="p-5 text-center">
                <p class="text-lg font-semibold {{ $daysUntilRenewal < 30 ? 'text-warning-600 dark:text-warning-400' : 'text-gray-900 dark:text-white' }}">
                    {{ $daysUntilRenewal > 0 ? $daysUntilRenewal . 'd' : 'Expired' }}
                </p>
                <p class="text-xs text-gray-400 mt-0.5">Until Renewal</p>
            </div>
            @endif
        </div>

        {{-- What's included --}}
        @if($retainer->services && count($retainer->services) > 0)
        <div class="px-6 py-5 border-t border-gray-100 dark:border-gray-800">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">What's Included</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($retainer->services as $service)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-medium text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">
                    <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    {{ $service }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Active projects under this retainer --}}
        @if($retainer->projects->isNotEmpty())
        <div class="border-t border-gray-100 dark:border-gray-800">
            <div class="px-6 py-4 border-b border-gray-50 dark:border-gray-800">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Projects Under This Retainer</h3>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
                @foreach($retainer->projects as $project)
                <a href="{{ route('client.projects.show', $project) }}" class="flex items-center justify-between px-6 py-3 hover:bg-gray-50/60 dark:hover:bg-gray-800/40 group transition-colors">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-brand-500 transition-colors">{{ $project->name }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="w-24 h-1.5 rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="h-1.5 rounded-full bg-brand-500" style="width: {{ $project->progress }}%"></div>
                            </div>
                            <span class="text-xs text-gray-400">{{ $project->progress }}%</span>
                        </div>
                    </div>
                    <svg class="size-4 text-gray-300 group-hover:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Renewal notice --}}
        @if($daysUntilRenewal !== null && $daysUntilRenewal > 0 && $daysUntilRenewal <= 30 && $retainer->status === 'active')
        <div class="m-5 rounded-xl border border-warning-200 bg-warning-50 p-4 dark:border-warning-800 dark:bg-warning-500/5 flex items-start gap-3">
            <svg class="size-5 text-warning-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="text-sm font-semibold text-warning-800 dark:text-warning-300">Renewal coming up</p>
                <p class="text-xs text-warning-600 dark:text-warning-400 mt-0.5">Your retainer renews in {{ $daysUntilRenewal }} days on {{ $retainer->end_date->format('M j, Y') }}. Reach out if you'd like to discuss terms.</p>
            </div>
        </div>
        @endif
    </div>
    @empty
    <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 py-16 text-center">
        <svg class="mx-auto size-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <p class="text-gray-500 dark:text-gray-400 font-medium">No active retainer</p>
        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Contact us to discuss a retainer agreement.</p>
    </div>
    @endforelse
</div>
@endsection
