@extends('layouts.app')
@section('title', 'Approvals')

@section('content')
<div class="space-y-5">

    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Approvals</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Review and approve work submitted by your team.</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-center">
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['awaiting'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Awaiting Review</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-center">
            <p class="text-2xl font-bold text-success-600 dark:text-success-400">{{ $stats['approved'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Approved</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-center">
            <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">{{ $stats['changes'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Changes Requested</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-center">
            <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['delivered'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Delivered</p>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    {{-- Awaiting approval — shown first and prominently --}}
    @if($grouped->has('in_review') && $grouped['in_review']->isNotEmpty())
    <div class="rounded-2xl border border-blue-200 bg-blue-50/50 dark:border-blue-800 dark:bg-blue-500/5">
        <div class="flex items-center gap-2 px-5 py-4 border-b border-blue-200 dark:border-blue-800">
            <span class="size-2.5 rounded-full bg-blue-500 animate-pulse"></span>
            <h2 class="text-sm font-semibold text-blue-700 dark:text-blue-400">Awaiting Your Approval</h2>
        </div>
        <div class="divide-y divide-blue-100 dark:divide-blue-800/50">
            @foreach($grouped['in_review'] as $d)
            <div class="flex items-center justify-between px-5 py-4">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('client.deliverables.show', $d) }}" class="font-medium text-gray-900 dark:text-white hover:text-brand-500 block truncate">{{ $d->name }}</a>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $d->project->name ?? '' }} @if($d->due_date) · Due {{ $d->due_date->format('M j') }} @endif</p>
                </div>
                <a href="{{ route('client.deliverables.show', $d) }}"
                    class="ml-4 inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600 flex-shrink-0">
                    Review
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- All deliverables list --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">All Deliverables</h2>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
            @forelse($deliverables as $d)
            <div class="flex items-center justify-between px-5 py-4 hover:bg-gray-50/60 dark:hover:bg-gray-800/40 group transition-colors">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <span class="size-2.5 rounded-full flex-shrink-0 {{ match($d->status) {
                        'approved'  => 'bg-success-500',
                        'in_review' => 'bg-blue-500',
                        'rejected'  => 'bg-error-500',
                        'delivered' => 'bg-gray-400',
                        default     => 'bg-warning-500',
                    } }}"></span>
                    <div class="min-w-0">
                        <a href="{{ route('client.deliverables.show', $d) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500 block truncate">{{ $d->name }}</a>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $d->project->name ?? '' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $d->status_color }}">{{ $d->status_label }}</span>
                    @if($d->due_date)<span class="text-xs text-gray-400 hidden sm:block">{{ $d->due_date->format('M j, Y') }}</span>@endif
                    <a href="{{ route('client.deliverables.show', $d) }}" class="opacity-0 group-hover:opacity-100 transition-opacity text-gray-400 hover:text-brand-500">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            @empty
            <div class="py-12 text-center">
                <p class="text-gray-500 dark:text-gray-400 text-sm">No deliverables to review yet.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
