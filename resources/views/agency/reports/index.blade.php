@extends('layouts.app')
@section('title', 'AI Reports')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">AI Reports</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">AI-generated summaries for projects and clients. Toggle visibility to share with clients.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="rounded-lg bg-brand-50 dark:bg-brand-500/10 px-3 py-1.5 flex items-center gap-2">
                <svg class="size-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                <span class="text-xs font-medium text-brand-700 dark:text-brand-300">{{ $stats['total'] }} total · {{ $stats['visible_client'] }} shared with clients</span>
            </div>
        </div>
    </div>

    @if($summaries->isEmpty())
    <div class="rounded-xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-gray-900">
        <div class="mx-auto size-14 rounded-full bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center mb-4">
            <svg class="size-7 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
        </div>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">No AI reports generated yet</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
            AI summaries are generated on projects and client profiles. Open a project or client to generate a summary.
        </p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($summaries as $summary)
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="inline-flex items-center rounded-full bg-brand-50 dark:bg-brand-500/10 px-2 py-0.5 text-xs font-medium text-brand-700 dark:text-brand-400">
                            {{ str_replace('_', ' ', ucfirst($summary->type)) }}
                        </span>
                        @if($summary->subject)
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $summary->subject }}</span>
                        @elseif($summary->summarizable)
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $summary->summarizable->name ?? $summary->summarizable->title ?? 'Unknown' }}
                        </span>
                        @endif
                        @if($summary->confidence)
                        <span class="text-xs text-gray-400">{{ round($summary->confidence * 100) }}% confidence</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed line-clamp-3">{{ $summary->content }}</p>

                    @if($summary->what_happened || $summary->why || $summary->what_next)
                    <div class="flex gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                        @if($summary->what_happened)
                        <span><span class="font-medium text-gray-700 dark:text-gray-300">What happened:</span> {{ Str::limit($summary->what_happened, 60) }}</span>
                        @endif
                        @if($summary->what_next)
                        <span><span class="font-medium text-gray-700 dark:text-gray-300">Next:</span> {{ Str::limit($summary->what_next, 60) }}</span>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    @if($summary->visible_to_client)
                    <span class="inline-flex items-center rounded-full bg-success-50 px-2 py-0.5 text-xs text-success-700 dark:bg-success-500/10 dark:text-success-400">Visible to Client</span>
                    @endif
                    <span class="text-xs text-gray-400">{{ $summary->created_at->diffForHumans() }}</span>
                </div>
            </div>

            @if($summary->client_content && $summary->visible_to_client)
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Client-facing version:</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 italic leading-relaxed">{{ $summary->client_content }}</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{ $summaries->links() }}
    @endif

</div>
@endsection
