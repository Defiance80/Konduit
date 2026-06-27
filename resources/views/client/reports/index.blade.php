@extends('layouts.app')
@section('title', 'Reports')

@section('content')
<div class="space-y-6 max-w-3xl">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Reports</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Summaries and updates from your team.</p>
    </div>

    {{-- Summary cards --}}
    @forelse($summaries as $summary)
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="px-6 py-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="size-7 rounded-lg bg-brand-100 dark:bg-brand-500/10 flex items-center justify-center">
                        <svg class="size-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-brand-600 dark:text-brand-400 uppercase tracking-wide">{{ str_replace('_',' ',ucfirst($summary->type ?? 'Summary')) }}</span>
                </div>
                <span class="text-xs text-gray-400">{{ $summary->created_at->format('M j, Y') }}</span>
            </div>
            @if($summary->subject)
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ $summary->subject }}</h2>
            @endif
            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $summary->content }}</p>
        </div>
        @if($summary->summarizable)
        <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-400">
            Related to: {{ $summary->summarizable->name ?? $summary->summarizable->subject ?? '—' }}
        </div>
        @endif
    </div>
    @empty
    <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 py-20 text-center">
        <div class="size-14 rounded-2xl bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center mx-auto mb-4">
            <svg class="size-7 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
        </div>
        <p class="text-gray-500 dark:text-gray-400 font-medium">No reports yet</p>
        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1 max-w-xs mx-auto">AI-generated summaries and reports will appear here once your projects are underway.</p>
    </div>
    @endforelse
</div>
@endsection
