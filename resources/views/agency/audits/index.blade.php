@extends('layouts.app')
@section('title', 'Audit Engine')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Audit Engine</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Run and share SEO, website, social, and performance audits with clients.</p>
        </div>
        <a href="{{ route('agency.audits.create') }}"
            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Audit
        </a>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-success-50 border border-success-200 px-4 py-3 text-sm text-success-700 dark:bg-success-500/10 dark:border-success-500/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs text-gray-400 mb-1">Total Audits</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs text-gray-400 mb-1">Complete</p>
            <p class="text-2xl font-bold text-success-600 dark:text-success-400">{{ $stats['complete'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs text-gray-400 mb-1">Shared with Clients</p>
            <p class="text-2xl font-bold text-brand-600 dark:text-brand-400">{{ $stats['shared'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs text-gray-400 mb-1">Avg. Score</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['avg_score'] ?: '—' }}{{ $stats['avg_score'] ? '/100' : '' }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Audit</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                    <th class="relative px-4 py-3"><span class="sr-only">View</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($audits as $audit)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                    <td class="px-4 py-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $audit->title }}</p>
                        @if($audit->audited_at)
                        <p class="text-xs text-gray-400">{{ $audit->audited_at->format('M j, Y') }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $audit->client->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $audit->type_label }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            @if($audit->status==='complete') bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400
                            @elseif($audit->status==='shared') bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400
                            @elseif($audit->status==='in_progress') bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400
                            @else bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 @endif">
                            {{ ucfirst(str_replace('_', ' ', $audit->status)) }}
                        </span>
                        @if($audit->visible_to_client)
                        <span class="ml-1 inline-flex items-center rounded-full bg-brand-50 px-1.5 py-0.5 text-xs text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">Shared</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($audit->score !== null)
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold {{ $audit->score >= 80 ? 'text-success-600 dark:text-success-400' : ($audit->score >= 60 ? 'text-warning-600 dark:text-warning-400' : 'text-error-600 dark:text-error-400') }}">
                                {{ $audit->score }}
                            </span>
                            <div class="h-1.5 w-16 rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="h-1.5 rounded-full {{ $audit->score >= 80 ? 'bg-success-500' : ($audit->score >= 60 ? 'bg-warning-500' : 'bg-error-500') }}"
                                    style="width:{{ $audit->score }}%"></div>
                            </div>
                        </div>
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('agency.audits.show', $audit) }}"
                            class="text-sm text-brand-600 dark:text-brand-400 hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-400 italic">
                        No audits yet. <a href="{{ route('agency.audits.create') }}" class="text-brand-600 hover:underline">Create your first audit</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $audits->links() }}

</div>
@endsection
