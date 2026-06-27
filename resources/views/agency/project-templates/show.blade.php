@extends('layouts.app')
@section('title', $template->name)

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('agency.project-templates.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $template->name }}</h1>
            <p class="text-sm text-gray-400 mt-0.5">
                {{ $template->section_count }} sections · {{ $template->task_count }} tasks · {{ count($template->deliverable_names ?? []) }} deliverables
                @if($template->estimated_days) · ~{{ $template->estimated_days }} days @endif
            </p>
        </div>
        <form action="{{ route('agency.project-templates.destroy', $template) }}" method="POST"
            onsubmit="return confirm('Delete this template?')">
            @csrf @method('DELETE')
            <button type="submit" class="rounded-lg border border-error-200 px-4 py-2 text-sm text-error-600 hover:bg-error-50 dark:border-error-800 dark:text-error-400">Delete</button>
        </form>
    </div>

    @if($template->description)
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $template->description }}</p>
    @endif

    {{-- Apply to project --}}
    @php $projects = \App\Models\Project::where('tenant_id', auth()->user()->tenant_id)->where('status', 'active')->get(); @endphp
    @if($projects->count())
    <div class="rounded-2xl border border-brand-200/60 bg-brand-50/30 dark:border-brand-800/40 dark:bg-brand-500/5 p-5">
        <h3 class="font-medium text-brand-700 dark:text-brand-300 mb-3">Apply this template to a project</h3>
        <form action="{{ route('agency.project-templates.apply', $template) }}" method="POST" class="flex gap-3">
            @csrf
            <select name="project_id" class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                @foreach($projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Apply Template</button>
        </form>
    </div>
    @endif

    {{-- Sections and tasks --}}
    @if($template->task_sections)
    <div class="space-y-4">
        @foreach($template->task_sections as $section)
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-2 px-5 py-3.5 border-b border-gray-100 dark:border-gray-800">
                <svg class="size-4 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <h3 class="font-medium text-gray-900 dark:text-white">{{ $section['name'] }}</h3>
                <span class="text-xs text-gray-400">{{ count($section['tasks'] ?? []) }} tasks</span>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800">
                @foreach($section['tasks'] ?? [] as $task)
                <div class="flex items-center justify-between px-5 py-2.5">
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $task['title'] }}</span>
                    @if(!empty($task['estimated_hours']))
                    <span class="text-xs text-gray-400">{{ $task['estimated_hours'] }}h</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Deliverables --}}
    @if($template->deliverable_names)
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5">
        <h3 class="font-medium text-gray-900 dark:text-white mb-3">Deliverables</h3>
        <div class="space-y-1.5">
            @foreach($template->deliverable_names as $name)
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <svg class="size-3.5 text-gray-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                {{ $name }}
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
