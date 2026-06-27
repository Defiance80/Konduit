@extends('layouts.app')
@section('title', 'Project Templates')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Project Blueprints</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Reusable project templates with pre-built task structures</p>
        </div>
        <a href="{{ route('agency.project-templates.create') }}" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">+ New Template</a>
    </div>

    @if(session('success'))
    <div class="rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    @if($templates->count())
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($templates as $template)
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            <div class="px-5 py-4">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $template->name }}</h3>
                    @if($template->estimated_days)
                    <span class="text-xs text-gray-400 ml-2 flex-shrink-0">~{{ $template->estimated_days }}d</span>
                    @endif
                </div>
                @if($template->description)
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">{{ $template->description }}</p>
                @endif
                <div class="flex gap-3 text-xs text-gray-400">
                    <span>{{ $template->section_count }} sections</span>
                    <span>{{ $template->task_count }} tasks</span>
                    <span>{{ count($template->deliverable_names ?? []) }} deliverables</span>
                </div>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-800 px-5 py-3 bg-gray-50/50 dark:bg-gray-800/30 flex items-center gap-2">
                <a href="{{ route('agency.project-templates.show', $template) }}"
                    class="flex-1 rounded-lg border border-gray-200 py-1.5 text-xs text-center text-gray-600 hover:bg-white dark:border-gray-700 dark:text-gray-400">View</a>
                {{-- Apply to project --}}
                @php $projects = \App\Models\Project::where('tenant_id', auth()->user()->tenant_id)->where('status', 'active')->get(); @endphp
                @if($projects->count())
                <div x-data="{ open: false }" class="flex-1 relative">
                    <button @click="open=!open" class="w-full rounded-lg bg-brand-500 py-1.5 text-xs font-medium text-white hover:bg-brand-600">Apply</button>
                    <div x-show="open" x-cloak @click.outside="open=false"
                        class="absolute right-0 mt-1 w-56 rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-lg z-10 py-1">
                        @foreach($projects as $proj)
                        <form action="{{ route('agency.project-templates.apply', $template) }}" method="POST">
                            @csrf
                            <input type="hidden" name="project_id" value="{{ $proj->id }}">
                            <button type="submit" class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                                {{ $proj->name }}
                            </button>
                        </form>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 py-16 text-center">
        <p class="text-gray-400 text-sm">No templates yet. Create your first project blueprint to speed up project setup.</p>
        <a href="{{ route('agency.project-templates.create') }}" class="mt-3 inline-block rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Create Template</a>
    </div>
    @endif
</div>
@endsection
