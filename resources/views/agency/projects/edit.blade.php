@extends('layouts.app')
@section('title', 'Edit Project')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('agency.projects.show', $project) }}" class="text-gray-400 hover:text-gray-600"><svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Project</h1>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <form method="POST" action="{{ route('agency.projects.update', $project) }}" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Project Name</label>
                    <input type="text" name="name" value="{{ old('name', $project->name) }}" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        @foreach(['draft','active','on_hold','completed','cancelled'] as $s)<option value="{{ $s }}" {{ old('status', $project->status) === $s ? 'selected' : '' }}>{{ str_replace('_',' ',ucfirst($s)) }}</option>@endforeach
                    </select>
                </div>
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                    <select name="priority" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        @foreach(['low','medium','high','urgent'] as $p)<option value="{{ $p }}" {{ old('priority', $project->priority) === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>@endforeach
                    </select>
                </div>
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Progress (%)</label>
                    <input type="number" name="progress" value="{{ old('progress', $project->progress) }}" min="0" max="100" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date', $project->due_date?->format('Y-m-d')) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>
                <div class="sm:col-span-2"><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('description', $project->description) }}</textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('agency.projects.show', $project) }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Cancel</a>
                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
