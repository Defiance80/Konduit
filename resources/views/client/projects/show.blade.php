@extends('layouts.app')
@section('title', $project->name)

@section('content')
<div class="space-y-6 max-w-4xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('client.projects.index') }}" class="hover:text-gray-600 dark:hover:text-gray-300">Projects</a>
        <span>/</span>
        <span class="text-gray-600 dark:text-gray-300">{{ $project->name }}</span>
    </div>

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            @php $statusColor = match($project->status) { 'active'=>'success','on_hold'=>'warning','completed'=>'gray','cancelled'=>'error',default=>'blue' }; @endphp
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-{{ $statusColor }}-50 text-{{ $statusColor }}-700 dark:bg-{{ $statusColor }}-500/10 dark:text-{{ $statusColor }}-400">
                    {{ str_replace('_',' ',ucfirst($project->status)) }}
                </span>
            </div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $project->name }}</h1>
            @if($project->description)
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $project->description }}</p>
            @endif
        </div>
    </div>

    {{-- Progress + Timeline confidence --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- Progress card --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Progress</h2>
            <div class="flex items-end gap-3 mb-3">
                <span class="text-4xl font-bold text-gray-900 dark:text-white">{{ $project->progress }}%</span>
                <span class="text-sm text-gray-400 mb-1">complete</span>
            </div>
            <div class="h-3 w-full rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                <div class="h-3 rounded-full bg-brand-500 transition-all" style="width: {{ $project->progress }}%"></div>
            </div>
            @if($project->budget)
            <div class="grid grid-cols-2 gap-3 text-sm pt-3 border-t border-gray-100 dark:border-gray-800">
                <div><p class="text-gray-400 text-xs mb-0.5">Budget</p><p class="font-semibold text-gray-900 dark:text-white">${{ number_format($project->budget, 0) }}</p></div>
                <div><p class="text-gray-400 text-xs mb-0.5">Spent</p><p class="font-semibold text-gray-900 dark:text-white">${{ number_format($project->budget_spent ?? 0, 0) }}</p></div>
            </div>
            @endif
        </div>

        {{-- Timeline confidence --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Timeline</h2>
            @if($timelineConfidence['score'] !== null)
            <div class="flex items-end gap-3 mb-3">
                <span class="text-4xl font-bold text-{{ $timelineConfidence['color'] }}-600 dark:text-{{ $timelineConfidence['color'] }}-400">
                    {{ $timelineConfidence['score'] }}%
                </span>
                <span class="text-sm text-gray-400 mb-1">confidence</span>
            </div>
            <div class="h-3 w-full rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                <div class="h-3 rounded-full bg-{{ $timelineConfidence['color'] }}-500 transition-all" style="width: {{ $timelineConfidence['score'] }}%"></div>
            </div>
            <p class="text-sm font-medium text-{{ $timelineConfidence['color'] }}-600 dark:text-{{ $timelineConfidence['color'] }}-400">
                {{ $timelineConfidence['label'] }}
            </p>
            @endif
            <div class="mt-3 grid grid-cols-2 gap-3 text-sm pt-3 border-t border-gray-100 dark:border-gray-800">
                @if($project->start_date)<div><p class="text-gray-400 text-xs mb-0.5">Started</p><p class="font-medium text-gray-700 dark:text-gray-300">{{ $project->start_date->format('M j, Y') }}</p></div>@endif
                @if($project->due_date)<div><p class="text-gray-400 text-xs mb-0.5">Due</p><p class="font-medium text-gray-700 dark:text-gray-300">{{ $project->due_date->format('M j, Y') }}</p></div>@endif
            </div>
        </div>
    </div>

    {{-- Deliverables --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-white">Deliverables</h2>
            @php $awaitingCount = $project->deliverables->where('status','in_review')->count(); @endphp
            @if($awaitingCount > 0)
            <span class="text-xs font-medium text-blue-600 dark:text-blue-400 flex items-center gap-1.5">
                <span class="size-2 rounded-full bg-blue-500 animate-pulse"></span>
                {{ $awaitingCount }} awaiting your review
            </span>
            @endif
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
            @forelse($project->deliverables->sortByDesc('created_at') as $d)
            <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50/60 dark:hover:bg-gray-800/40 group transition-colors">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="size-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ match($d->status) {
                        'approved','delivered' => 'bg-success-50 dark:bg-success-500/10',
                        'in_review' => 'bg-blue-50 dark:bg-blue-500/10',
                        'rejected'  => 'bg-error-50 dark:bg-error-500/10',
                        default     => 'bg-gray-100 dark:bg-gray-800',
                    } }}">
                        @if(in_array($d->status, ['approved','delivered']))
                        <svg class="size-4 text-success-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @elseif($d->status === 'in_review')
                        <svg class="size-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                        @elseif($d->status === 'rejected')
                        <svg class="size-4 text-error-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @else
                        <svg class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $d->name }}</p>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $d->status_color }}">{{ $d->status_label }}</span>
                    </div>
                </div>
                @if($d->status === 'in_review')
                <a href="{{ route('client.deliverables.show', $d) }}"
                    class="ml-4 inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600 flex-shrink-0">
                    Review
                </a>
                @elseif($d->hasFile())
                <a href="{{ $d->file_url ?? Storage::url($d->file_path) }}" target="_blank"
                    class="ml-4 text-xs text-brand-500 hover:text-brand-600 font-medium flex-shrink-0">View file</a>
                @endif
            </div>
            @empty
            <p class="px-6 py-8 text-center text-sm text-gray-400">No deliverables yet — the team is working on it.</p>
            @endforelse
        </div>
    </div>

    {{-- Retainer --}}
    @if($project->retainer)
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Retainer</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div><p class="text-gray-400 text-xs mb-0.5">Name</p><p class="font-medium text-gray-700 dark:text-gray-300">{{ $project->retainer->name }}</p></div>
            <div><p class="text-gray-400 text-xs mb-0.5">Status</p>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $project->retainer->status === 'active' ? 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($project->retainer->status) }}</span>
            </div>
            @if($project->retainer->monthly_value)<div><p class="text-gray-400 text-xs mb-0.5">Monthly Value</p><p class="font-medium text-gray-700 dark:text-gray-300">${{ number_format($project->retainer->monthly_value, 0) }}</p></div>@endif
            @if($project->retainer->renewal_date)<div><p class="text-gray-400 text-xs mb-0.5">Renews</p><p class="font-medium text-gray-700 dark:text-gray-300">{{ $project->retainer->renewal_date->format('M j, Y') }}</p></div>@endif
        </div>
    </div>
    @endif

    {{-- Point of contact --}}
    @if($project->owner)
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <h2 class="font-semibold text-gray-900 dark:text-white mb-3 text-sm">Your Point of Contact</h2>
        <div class="flex items-center gap-3">
            <img src="{{ $project->owner->avatar_url }}" alt="{{ $project->owner->name }}" class="size-10 rounded-full object-cover">
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->owner->name }}</p>
                <p class="text-xs text-gray-400">{{ $project->owner->email }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
