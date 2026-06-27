@extends('layouts.app')
@section('title', 'New Project Template')

@section('content')
<div class="max-w-3xl space-y-6" x-data="templateBuilder()">
    <div class="flex items-center gap-4">
        <a href="{{ route('agency.project-templates.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">New Project Blueprint</h1>
    </div>

    <form action="{{ route('agency.project-templates.store') }}" method="POST" class="space-y-5">
        @csrf
        {{-- Basic info --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 dark:text-white">Template Details</h2>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Template name *</label>
                <input type="text" name="name" required placeholder="e.g. Website Build, SEO Retainer Onboarding"
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                    <input type="text" name="description" placeholder="When to use this template"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Estimated duration (days)</label>
                    <input type="number" name="estimated_days" min="1" placeholder="30"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>
            </div>
        </div>

        {{-- Task sections --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-gray-900 dark:text-white">Task Sections</h2>
                <button type="button" @click="addSection()"
                    class="text-sm text-brand-500 hover:text-brand-600 font-medium">+ Add section</button>
            </div>

            <template x-for="(section, si) in sections" :key="si">
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-800 px-4 py-2.5">
                        <input type="text" :name="`sections[${si}][name]`" x-model="section.name"
                            placeholder="Section name (e.g. Discovery)"
                            class="flex-1 bg-transparent text-sm font-medium text-gray-900 dark:text-white border-0 focus:ring-0 p-0">
                        <button type="button" @click="removeSection(si)" class="text-gray-300 hover:text-error-500">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="px-4 py-2 space-y-2">
                        <template x-for="(task, ti) in section.tasks" :key="ti">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-300 dark:text-gray-600">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                </span>
                                <input type="text" :name="`sections[${si}][tasks][${ti}][title]`" x-model="task.title"
                                    placeholder="Task title"
                                    class="flex-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm dark:text-white">
                                <input type="number" :name="`sections[${si}][tasks][${ti}][estimated_hours]`" x-model="task.hours"
                                    placeholder="hrs" min="0" step="0.5"
                                    class="w-16 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1.5 text-sm text-center dark:text-white">
                                <button type="button" @click="removeTask(si, ti)" class="text-gray-300 hover:text-error-500 flex-shrink-0">
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                        <button type="button" @click="addTask(si)"
                            class="text-xs text-gray-400 hover:text-brand-500 py-1">+ Add task</button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Deliverables --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6 space-y-3">
            <h2 class="font-semibold text-gray-900 dark:text-white">Deliverables</h2>
            <p class="text-xs text-gray-400">One deliverable name per line. These will be pre-created on the project.</p>
            <textarea name="deliverable_names" rows="4" placeholder="Final website&#10;SEO audit report&#10;Brand style guide"
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('agency.project-templates.index') }}" class="rounded-lg border border-gray-200 px-6 py-2.5 text-sm text-gray-600 dark:border-gray-700 dark:text-gray-400">Cancel</a>
            <button type="submit" class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Save Template</button>
        </div>
    </form>
</div>

<script>
function templateBuilder() {
    return {
        sections: [{ name: '', tasks: [{ title: '', hours: '' }] }],
        addSection() { this.sections.push({ name: '', tasks: [{ title: '', hours: '' }] }); },
        removeSection(i) { this.sections.splice(i, 1); },
        addTask(si) { this.sections[si].tasks.push({ title: '', hours: '' }); },
        removeTask(si, ti) { this.sections[si].tasks.splice(ti, 1); },
    }
}
</script>
@endsection
