@extends('layouts.app')
@section('title', $audit->title)

@section('content')
<div x-data="{ showEditSummary: false, showAddFinding: false, showAddRec: false }" class="space-y-5">

    {{-- Back + Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div class="flex items-start gap-3">
            <a href="{{ route('agency.audits.index') }}" class="mt-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 flex-shrink-0">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $audit->title }}</h1>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        @if($audit->status==='complete') bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400
                        @elseif($audit->status==='shared') bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400
                        @else bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 @endif">
                        {{ ucfirst(str_replace('_',' ',$audit->status)) }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $audit->type_label }}</span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $audit->client->name }}
                    @if($audit->project) · {{ $audit->project->name }} @endif
                    @if($audit->audited_at) · {{ $audit->audited_at->format('M j, Y') }} @endif
                </p>
            </div>
        </div>

        <div class="flex gap-2">
            @if(!$audit->visible_to_client && in_array($audit->status, ['complete']))
            <form action="{{ route('agency.audits.share', $audit) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                    Share with Client
                </button>
            </form>
            @endif
            <button @click="showEditSummary=!showEditSummary"
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                Edit
            </button>
            <form action="{{ route('agency.audits.destroy', $audit) }}" method="POST" onsubmit="return confirm('Delete this audit?')">
                @csrf @method('DELETE')
                <button type="submit" class="rounded-lg border border-error-200 px-4 py-2 text-sm text-error-600 hover:bg-error-50 dark:border-error-800 dark:text-error-400">Delete</button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-success-50 border border-success-200 px-4 py-3 text-sm text-success-700 dark:bg-success-500/10 dark:border-success-500/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    {{-- Score + Edit form --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Score card --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 flex items-center gap-5">
            @if($audit->score !== null)
            <div class="relative size-20 flex-shrink-0">
                <svg class="size-20 -rotate-90" viewBox="0 0 80 80">
                    <circle cx="40" cy="40" r="34" fill="none" stroke="currentColor" class="text-gray-100 dark:text-gray-800" stroke-width="8"/>
                    <circle cx="40" cy="40" r="34" fill="none"
                        stroke="currentColor"
                        class="{{ $audit->score >= 80 ? 'text-success-500' : ($audit->score >= 60 ? 'text-warning-500' : 'text-error-500') }}"
                        stroke-width="8"
                        stroke-dasharray="{{ round(2 * 3.14159 * 34) }}"
                        stroke-dashoffset="{{ round(2 * 3.14159 * 34 * (1 - $audit->score/100)) }}"
                        stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xl font-bold {{ $audit->score >= 80 ? 'text-success-600 dark:text-success-400' : ($audit->score >= 60 ? 'text-warning-600 dark:text-warning-400' : 'text-error-600 dark:text-error-400') }}">{{ $audit->score }}</span>
                </div>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Overall Score</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $audit->score >= 80 ? 'Good health' : ($audit->score >= 60 ? 'Needs improvement' : 'Critical issues found') }}</p>
            </div>
            @else
            <div class="text-center w-full">
                <p class="text-sm text-gray-400">Score not set yet.</p>
            </div>
            @endif
        </div>

        {{-- Summary --}}
        <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Executive Summary</h3>
            @if($audit->executive_summary)
            <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap leading-relaxed">{{ $audit->executive_summary }}</p>
            @else
            <p class="text-sm text-gray-400 italic">No executive summary yet.</p>
            @endif
        </div>
    </div>

    {{-- Edit form --}}
    <div x-show="showEditSummary" x-cloak class="rounded-xl border border-brand-200 bg-brand-50/30 dark:border-brand-800/50 dark:bg-brand-500/5 p-5">
        <form action="{{ route('agency.audits.update', $audit) }}" method="POST" class="space-y-4">
            @csrf @method('PATCH')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Score (0–100)</label>
                    <input type="number" name="score" min="0" max="100" value="{{ $audit->score }}"
                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="draft" @selected($audit->status==='draft')>Draft</option>
                        <option value="in_progress" @selected($audit->status==='in_progress')>In Progress</option>
                        <option value="complete" @selected($audit->status==='complete')>Complete</option>
                        <option value="shared" @selected($audit->status==='shared')>Shared</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Executive Summary</label>
                    <textarea name="executive_summary" rows="4"
                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">{{ $audit->executive_summary }}</textarea>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">AI Analysis</label>
                    <textarea name="ai_analysis" rows="4" placeholder="Paste AI-generated analysis here…"
                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">{{ $audit->ai_analysis }}</textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showEditSummary=false" class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">Cancel</button>
                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Save Changes</button>
            </div>
        </form>
    </div>

    {{-- Findings + Recommendations --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Findings --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Findings
                    <span class="ml-1 text-xs font-normal text-gray-400">({{ count($audit->findings ?? []) }})</span>
                </h3>
                <button @click="showAddFinding=!showAddFinding" class="text-xs text-brand-600 dark:text-brand-400 hover:underline">+ Add</button>
            </div>

            <div x-show="showAddFinding" x-cloak class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                <form action="{{ route('agency.audits.findings.store', $audit) }}" method="POST" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <input type="text" name="title" required placeholder="Finding title"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                        </div>
                        <div>
                            <select name="severity" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                                <option value="critical">Critical</option>
                                <option value="high">High</option>
                                <option value="medium" selected>Medium</option>
                                <option value="low">Low</option>
                                <option value="info">Info</option>
                            </select>
                        </div>
                        <div>
                            <textarea name="detail" rows="2" placeholder="Details (optional)"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500"></textarea>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="showAddFinding=false" class="text-xs text-gray-500">Cancel</button>
                        <button type="submit" class="rounded bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600">Add Finding</button>
                    </div>
                </form>
            </div>

            <div class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($audit->findings ?? [] as $finding)
                <div class="flex gap-3 px-5 py-3">
                    <span class="inline-flex items-center mt-0.5 rounded px-1.5 py-0.5 text-xs font-semibold flex-shrink-0
                        @if($finding['severity']==='critical') bg-error-100 text-error-800 dark:bg-error-500/20 dark:text-error-400
                        @elseif($finding['severity']==='high') bg-orange-100 text-orange-800 dark:bg-orange-500/20 dark:text-orange-400
                        @elseif($finding['severity']==='medium') bg-warning-100 text-warning-800 dark:bg-warning-500/20 dark:text-warning-400
                        @elseif($finding['severity']==='low') bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400
                        @else bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 @endif">
                        {{ ucfirst($finding['severity']) }}
                    </span>
                    <div>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $finding['title'] }}</p>
                        @if(!empty($finding['detail']))
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $finding['detail'] }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <p class="px-5 py-4 text-sm text-gray-400 italic">No findings recorded.</p>
                @endforelse
            </div>
        </div>

        {{-- Recommendations --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Recommendations
                    <span class="ml-1 text-xs font-normal text-gray-400">({{ count($audit->recommendations ?? []) }})</span>
                </h3>
                <button @click="showAddRec=!showAddRec" class="text-xs text-brand-600 dark:text-brand-400 hover:underline">+ Add</button>
            </div>

            <div x-show="showAddRec" x-cloak class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                <form action="{{ route('agency.audits.recommendations.store', $audit) }}" method="POST" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <input type="text" name="title" required placeholder="Recommendation"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                        </div>
                        <div>
                            <select name="priority" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                                <option value="critical">Critical</option>
                                <option value="high">High</option>
                                <option value="medium" selected>Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                        <div>
                            <textarea name="detail" rows="2" placeholder="Detail (optional)"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500"></textarea>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="showAddRec=false" class="text-xs text-gray-500">Cancel</button>
                        <button type="submit" class="rounded bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600">Add</button>
                    </div>
                </form>
            </div>

            <div class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($audit->recommendations ?? [] as $rec)
                <div class="flex gap-3 px-5 py-3">
                    <span class="inline-flex items-center mt-0.5 rounded px-1.5 py-0.5 text-xs font-semibold flex-shrink-0
                        @if($rec['priority']==='critical') bg-error-100 text-error-800 dark:bg-error-500/20 dark:text-error-400
                        @elseif($rec['priority']==='high') bg-orange-100 text-orange-800 dark:bg-orange-500/20 dark:text-orange-400
                        @elseif($rec['priority']==='medium') bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400
                        @else bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 @endif">
                        {{ ucfirst($rec['priority']) }}
                    </span>
                    <div>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $rec['title'] }}</p>
                        @if(!empty($rec['detail']))
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $rec['detail'] }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <p class="px-5 py-4 text-sm text-gray-400 italic">No recommendations yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- AI Analysis block --}}
    @if($audit->ai_analysis)
    <div class="rounded-xl border border-brand-200 bg-brand-50/40 dark:border-brand-800/50 dark:bg-brand-500/5 p-5">
        <div class="flex items-center gap-2 mb-3">
            <svg class="size-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            <h3 class="text-sm font-semibold text-brand-700 dark:text-brand-300">AI Analysis</h3>
        </div>
        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ $audit->ai_analysis }}</p>
    </div>
    @endif

</div>
@endsection
