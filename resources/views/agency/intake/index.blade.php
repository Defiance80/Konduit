@extends('layouts.app')
@section('title', 'Intake Submissions')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Intake Submissions</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">All requests submitted via your intake widget</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="rounded-xl border border-dashed border-brand-300 dark:border-brand-800 bg-brand-50/40 dark:bg-brand-500/5 px-4 py-2">
                <p class="text-xs text-brand-600 dark:text-brand-400 font-medium mb-1">Your intake widget URL</p>
                <div class="flex items-center gap-2">
                    <code class="text-xs text-gray-600 dark:text-gray-400 font-mono">{{ $intakeUrl }}</code>
                    <button onclick="navigator.clipboard.writeText('{{ $intakeUrl }}')"
                        class="text-brand-400 hover:text-brand-600" title="Copy URL">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        @if($submissions->count())
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 dark:border-gray-800">
                <tr>
                    @foreach(['Submitted by', 'Company', 'Type', 'Priority', 'AI Summary', 'Ticket', 'Date'] as $h)
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @foreach($submissions as $sub)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $sub->name }}</p>
                        <p class="text-xs text-gray-400">{{ $sub->email }}</p>
                    </td>
                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $sub->company ?: '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="capitalize text-gray-700 dark:text-gray-300">{{ $sub->issue_type }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                            bg-{{ $sub->priority_color }}-50 text-{{ $sub->priority_color }}-700
                            dark:bg-{{ $sub->priority_color }}-500/10 dark:text-{{ $sub->priority_color }}-400">
                            {{ ucfirst($sub->priority) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 max-w-xs">
                        @if($sub->ai_summary)
                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">{{ $sub->ai_summary }}</p>
                        @else
                        <span class="text-xs text-gray-300">No summary</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        @if($sub->ticket)
                        <a href="{{ route('agency.tickets.show', $sub->ticket) }}" class="text-brand-500 hover:text-brand-600 text-xs font-medium">{{ $sub->ticket->ticket_number }}</a>
                        @else
                        <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400 whitespace-nowrap">{{ $sub->created_at->format('M j, g:ia') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800">
            {{ $submissions->links() }}
        </div>
        @else
        <div class="py-16 text-center">
            <p class="text-gray-400 text-sm mb-2">No intake submissions yet.</p>
            <p class="text-xs text-gray-400">Share your intake URL with clients to start receiving requests.</p>
        </div>
        @endif
    </div>
</div>
@endsection
