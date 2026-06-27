@extends('layouts.app')
@section('title', $deliverable->name)

@section('content')
<div x-data="{ showEdit: false, showUpload: false }" class="space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('agency.deliverables.index') }}" class="hover:text-gray-600 dark:hover:text-gray-300">Deliverables</a>
        @if($deliverable->project)
        <span>/</span>
        <a href="{{ route('agency.projects.show', $deliverable->project) }}" class="hover:text-gray-600 dark:hover:text-gray-300">{{ $deliverable->project->name }}</a>
        @endif
        <span>/</span>
        <span class="text-gray-600 dark:text-gray-300 truncate max-w-xs">{{ $deliverable->name }}</span>
    </div>

    @if(session('success'))
    <div class="rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ── LEFT: Main content ── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Header card --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $deliverable->status_color }}">
                                    {{ $deliverable->status_label }}
                                </span>
                                <span class="text-xs text-gray-400">v{{ $deliverable->version }}</span>
                                @if($deliverable->isOverdue())
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-error-50 text-error-600 dark:bg-error-500/10 dark:text-error-400">Overdue</span>
                                @endif
                            </div>
                            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $deliverable->name }}</h1>
                            @if($deliverable->description)
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">{{ $deliverable->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Action bar --}}
                <div class="flex flex-wrap items-center gap-2 px-6 py-3 border-t border-gray-100 dark:border-gray-800">
                    {{-- Submit for review --}}
                    @if($deliverable->isPending() || $deliverable->isRejected())
                    <form method="POST" action="{{ route('agency.deliverables.submit', $deliverable) }}">
                        @csrf @method('PATCH')
                        <button class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            Send for Client Approval
                        </button>
                    </form>
                    @endif

                    {{-- Mark delivered --}}
                    @if($deliverable->isApproved())
                    <form method="POST" action="{{ route('agency.deliverables.deliver', $deliverable) }}">
                        @csrf @method('PATCH')
                        <button class="inline-flex items-center gap-1.5 rounded-lg bg-success-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-success-600">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Mark Delivered
                        </button>
                    </form>
                    @endif

                    {{-- Manual approve --}}
                    @if($deliverable->isInReview())
                    <form method="POST" action="{{ route('agency.deliverables.approve', $deliverable) }}">
                        @csrf @method('PATCH')
                        <button class="inline-flex items-center gap-1.5 rounded-lg bg-success-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-success-600">
                            Approve (Manual)
                        </button>
                    </form>
                    @endif

                    <button @click="showEdit=!showEdit"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </button>

                    <form method="POST" action="{{ route('agency.deliverables.destroy', $deliverable) }}"
                        onsubmit="return confirm('Delete this deliverable?')" class="ml-auto">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-gray-400 hover:text-error-500 transition-colors">Delete</button>
                    </form>
                </div>
            </div>

            {{-- Edit form --}}
            <div x-show="showEdit" x-cloak class="rounded-2xl border border-brand-200 bg-white dark:border-brand-800 dark:bg-gray-900">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Edit Deliverable</h2>
                </div>
                <form method="POST" action="{{ route('agency.deliverables.update', $deliverable) }}" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf @method('PATCH')
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Name</label>
                        <input type="text" name="name" value="{{ old('name', $deliverable->name) }}" required
                            class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white resize-none">{{ old('description', $deliverable->description) }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Due Date</label>
                            <input type="date" name="due_date" value="{{ old('due_date', $deliverable->due_date?->format('Y-m-d')) }}"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">External Link</label>
                            <input type="url" name="file_url" value="{{ old('file_url', $deliverable->file_url) }}" placeholder="https://figma.com/…"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Replace File <span class="text-gray-300">(uploading creates a new version)</span></label>
                        <input type="file" name="file"
                            class="w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-medium file:text-brand-600 hover:file:bg-brand-100">
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="showEdit=false" class="flex-1 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Cancel</button>
                        <button type="submit" class="flex-1 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Save Changes</button>
                    </div>
                </form>
            </div>

            {{-- File / Link --}}
            @if($deliverable->hasFile())
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Attachment</h2>
                @if($deliverable->file_url)
                <a href="{{ $deliverable->file_url }}" target="_blank" rel="noopener noreferrer"
                    class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50 p-4 hover:border-brand-200 hover:bg-brand-50/50 transition-colors dark:border-gray-800 dark:bg-gray-800/50 dark:hover:border-brand-800">
                    <div class="size-10 rounded-lg bg-brand-100 dark:bg-brand-500/10 flex items-center justify-center flex-shrink-0">
                        <svg class="size-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Open External Link</p>
                        <p class="text-xs text-gray-400 truncate max-w-xs">{{ $deliverable->file_url }}</p>
                    </div>
                </a>
                @endif
                @if($deliverable->file_path)
                <a href="{{ Storage::url($deliverable->file_path) }}" target="_blank"
                    class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50 p-4 hover:border-brand-200 hover:bg-brand-50/50 transition-colors dark:border-gray-800 dark:bg-gray-800/50 mt-3">
                    <div class="size-10 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                        <svg class="size-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $deliverable->file_name ?? 'Download File' }}</p>
                        @if($deliverable->file_size_formatted)
                        <p class="text-xs text-gray-400">{{ $deliverable->file_size_formatted }}</p>
                        @endif
                    </div>
                </a>
                @endif
            </div>
            @endif

            {{-- Client feedback / rejection --}}
            @if($deliverable->rejection_reason)
            <div class="rounded-2xl border border-error-200 bg-error-50 p-5 dark:border-error-800 dark:bg-error-900/20">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="size-4 text-error-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-sm font-semibold text-error-700 dark:text-error-400">Changes Requested by Client</h3>
                </div>
                <p class="text-sm text-error-600 dark:text-error-300 leading-relaxed">{{ $deliverable->rejection_reason }}</p>
            </div>
            @elseif($deliverable->client_feedback && $deliverable->isApproved())
            <div class="rounded-2xl border border-success-200 bg-success-50 p-5 dark:border-success-800 dark:bg-success-900/20">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="size-4 text-success-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-sm font-semibold text-success-700 dark:text-success-400">Client Feedback</h3>
                </div>
                <p class="text-sm text-success-600 dark:text-success-300 leading-relaxed">{{ $deliverable->client_feedback }}</p>
            </div>
            @endif
        </div>

        {{-- ── RIGHT: Meta sidebar ── --}}
        <div class="space-y-4">
            {{-- Approval timeline --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Approval Timeline</h2>
                <ol class="relative border-l border-gray-200 dark:border-gray-700 space-y-4 ml-2">
                    <li class="ml-4">
                        <div class="absolute -left-1.5 mt-1 size-3 rounded-full bg-brand-500"></div>
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Created</p>
                        <p class="text-xs text-gray-400">{{ $deliverable->created_at->format('M j, Y g:ia') }}</p>
                    </li>
                    @if($deliverable->submitted_at)
                    <li class="ml-4">
                        <div class="absolute -left-1.5 mt-1 size-3 rounded-full bg-blue-500"></div>
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Sent for Review</p>
                        <p class="text-xs text-gray-400">{{ $deliverable->submitted_at->format('M j, Y g:ia') }}</p>
                    </li>
                    @endif
                    @if($deliverable->approved_at)
                    <li class="ml-4">
                        <div class="absolute -left-1.5 mt-1 size-3 rounded-full bg-success-500"></div>
                        <p class="text-xs font-medium text-success-600 dark:text-success-400">Approved</p>
                        <p class="text-xs text-gray-400">{{ $deliverable->approved_at->format('M j, Y g:ia') }}</p>
                    </li>
                    @endif
                    @if($deliverable->status === 'delivered')
                    <li class="ml-4">
                        <div class="absolute -left-1.5 mt-1 size-3 rounded-full bg-gray-400"></div>
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Delivered</p>
                        <p class="text-xs text-gray-400">{{ $deliverable->updated_at->format('M j, Y g:ia') }}</p>
                    </li>
                    @endif
                </ol>
            </div>

            {{-- Details --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 space-y-3">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Details</h2>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Project</span>
                    @if($deliverable->project)
                    <a href="{{ route('agency.projects.show', $deliverable->project) }}" class="text-brand-500 hover:text-brand-600 truncate max-w-[140px]">{{ $deliverable->project->name }}</a>
                    @else
                    <span class="text-gray-400">—</span>
                    @endif
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Client</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ $deliverable->client->name ?? '—' }}</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Assigned to</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ $deliverable->reviewer->name ?? '—' }}</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Due Date</span>
                    <span class="{{ $deliverable->isOverdue() ? 'text-error-500 font-medium' : 'text-gray-700 dark:text-gray-300' }}">
                        {{ $deliverable->due_date ? $deliverable->due_date->format('M j, Y') : '—' }}
                    </span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Version</span>
                    <span class="text-gray-700 dark:text-gray-300">v{{ $deliverable->version }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
