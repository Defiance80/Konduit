@extends('layouts.app')
@section('title', $deliverable->name)

@section('content')
<div x-data="{ showReject: false }" class="space-y-5 max-w-2xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('client.deliverables.index') }}" class="hover:text-gray-600 dark:hover:text-gray-300">Approvals</a>
        <span>/</span>
        <span class="text-gray-600 dark:text-gray-300 truncate max-w-xs">{{ $deliverable->name }}</span>
    </div>

    @if(session('success'))
    <div class="rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    {{-- Main card --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="p-6">
            <div class="flex items-center gap-2 mb-3">
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $deliverable->status_color }}">
                    {{ $deliverable->status_label }}
                </span>
                <span class="text-xs text-gray-400">v{{ $deliverable->version }}</span>
            </div>

            <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ $deliverable->name }}</h1>

            @if($deliverable->project)
            <p class="text-sm text-gray-400 mb-4">{{ $deliverable->project->name }}</p>
            @endif

            @if($deliverable->description)
            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed bg-gray-50 dark:bg-gray-800 rounded-xl p-4">{{ $deliverable->description }}</p>
            @endif
        </div>

        {{-- Attachment --}}
        @if($deliverable->hasFile())
        <div class="px-6 pb-6">
            @if($deliverable->file_url)
            <a href="{{ $deliverable->file_url }}" target="_blank" rel="noopener noreferrer"
                class="flex items-center gap-3 rounded-xl border border-brand-100 bg-brand-50 p-4 hover:bg-brand-100 transition-colors dark:border-brand-800 dark:bg-brand-500/10 dark:hover:bg-brand-500/20">
                <div class="size-10 rounded-lg bg-brand-100 dark:bg-brand-500/20 flex items-center justify-center flex-shrink-0">
                    <svg class="size-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-brand-700 dark:text-brand-300">Open Deliverable</p>
                    <p class="text-xs text-brand-500 truncate max-w-xs">{{ $deliverable->file_url }}</p>
                </div>
                <svg class="size-4 text-brand-400 ml-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            @endif
            @if($deliverable->file_path)
            <a href="{{ Storage::url($deliverable->file_path) }}" target="_blank"
                class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50 p-4 hover:bg-gray-100 transition-colors dark:border-gray-800 dark:bg-gray-800/50 mt-3">
                <div class="size-10 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                    <svg class="size-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $deliverable->file_name ?? 'Download File' }}</p>
                    @if($deliverable->file_size_formatted)<p class="text-xs text-gray-400">{{ $deliverable->file_size_formatted }}</p>@endif
                </div>
            </a>
            @endif
        </div>
        @endif

        {{-- Approval actions — only shown when in_review --}}
        @if($deliverable->isInReview())
        <div class="px-6 pb-6 space-y-3">
            <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Your Decision</p>

            <form method="POST" action="{{ route('client.deliverables.approve', $deliverable) }}">
                @csrf @method('PATCH')
                <div class="mb-3">
                    <textarea name="feedback" rows="2" placeholder="Any comments? (optional)"
                        class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white resize-none"></textarea>
                </div>
                <button type="submit"
                    class="w-full rounded-xl bg-success-500 py-3 text-sm font-semibold text-white hover:bg-success-600 transition-colors flex items-center justify-center gap-2">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Approve Deliverable
                </button>
            </form>

            <button @click="showReject=true"
                class="w-full rounded-xl border border-error-200 py-3 text-sm font-semibold text-error-600 hover:bg-error-50 transition-colors dark:border-error-800 dark:text-error-400 dark:hover:bg-error-500/10">
                Request Changes
            </button>
        </div>
        @endif

        {{-- Approved state --}}
        @if($deliverable->isApproved())
        <div class="px-6 pb-6">
            <div class="rounded-xl bg-success-50 border border-success-200 p-4 dark:bg-success-500/10 dark:border-success-800 flex items-center gap-3">
                <svg class="size-5 text-success-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="text-sm font-semibold text-success-700 dark:text-success-400">Approved</p>
                    @if($deliverable->approved_at)<p class="text-xs text-success-600 dark:text-success-500">{{ $deliverable->approved_at->format('M j, Y \a\t g:ia') }}</p>@endif
                </div>
            </div>
            @if($deliverable->client_feedback)
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-3 italic">"{{ $deliverable->client_feedback }}"</p>
            @endif
        </div>
        @endif

        {{-- Rejected state --}}
        @if($deliverable->isRejected())
        <div class="px-6 pb-6">
            <div class="rounded-xl bg-error-50 border border-error-200 p-4 dark:bg-error-500/10 dark:border-error-800">
                <p class="text-sm font-semibold text-error-700 dark:text-error-400 mb-1">Changes Requested</p>
                <p class="text-sm text-error-600 dark:text-error-300">{{ $deliverable->rejection_reason }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Details card --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 space-y-3">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Details</h2>
        @if($deliverable->reviewer)
        <div class="flex justify-between text-sm"><span class="text-gray-400">Submitted by</span><span class="text-gray-700 dark:text-gray-300">{{ $deliverable->reviewer->name }}</span></div>
        @endif
        @if($deliverable->submitted_at)
        <div class="flex justify-between text-sm"><span class="text-gray-400">Submitted</span><span class="text-gray-700 dark:text-gray-300">{{ $deliverable->submitted_at->format('M j, Y') }}</span></div>
        @endif
        @if($deliverable->due_date)
        <div class="flex justify-between text-sm"><span class="text-gray-400">Due</span><span class="{{ $deliverable->isOverdue() ? 'text-error-500 font-medium' : 'text-gray-700 dark:text-gray-300' }}">{{ $deliverable->due_date->format('M j, Y') }}</span></div>
        @endif
        <div class="flex justify-between text-sm"><span class="text-gray-400">Version</span><span class="text-gray-700 dark:text-gray-300">v{{ $deliverable->version }}</span></div>
    </div>

    {{-- Reject modal --}}
    <div x-show="showReject" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);">
        <div @click.away="showReject=false" class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-900">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Request Changes</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Please describe what needs to be changed so the team can revise accordingly.</p>
            <form method="POST" action="{{ route('client.deliverables.reject', $deliverable) }}">
                @csrf @method('PATCH')
                <textarea name="rejection_reason" rows="5" required minlength="10"
                    placeholder="Please describe what changes are needed…"
                    class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white resize-none mb-4"></textarea>
                <div class="flex gap-3">
                    <button type="button" @click="showReject=false"
                        class="flex-1 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 rounded-lg bg-error-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-error-600">
                        Submit Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
