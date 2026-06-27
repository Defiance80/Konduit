@extends('layouts.app')
@section('title', 'New Request')

@section('content')
<div class="max-w-2xl space-y-5">
    <div class="flex items-center gap-3">
        <a href="{{ route('client.tickets.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">New Request</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Tell us what you need and we'll get right on it.</p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <form method="POST" action="{{ route('client.tickets.store') }}" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Subject <span class="text-error-500">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                    placeholder="Briefly describe what you need…"
                    class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                @error('subject')<p class="text-xs text-error-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Type <span class="text-error-500">*</span></label>
                    <select name="type" required
                        class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="">Select type…</option>
                        <option value="bug"     @selected(old('type')==='bug')>🐛 Something is broken</option>
                        <option value="feature" @selected(old('type')==='feature')>✨ New feature or change</option>
                        <option value="design"  @selected(old('type')==='design')>🎨 Design update</option>
                        <option value="content" @selected(old('type')==='content')>📝 Content update</option>
                        <option value="question"@selected(old('type')==='question')>❓ Question</option>
                        <option value="general" @selected(old('type')==='general')>💬 General</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Priority <span class="text-error-500">*</span></label>
                    <select name="priority" required
                        class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="low"    @selected(old('priority')==='low')>Low — whenever you can</option>
                        <option value="medium" @selected(old('priority')==='medium') selected>Medium — this week</option>
                        <option value="high"   @selected(old('priority')==='high')>High — needs prompt attention</option>
                        <option value="urgent" @selected(old('priority')==='urgent')>Urgent — blocking us now</option>
                    </select>
                </div>
            </div>

            @if($projects->isNotEmpty())
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Related Project <span class="text-gray-400 font-normal">(optional)</span></label>
                <select name="project_id"
                    class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">Not project-specific</option>
                    @foreach($projects as $p)
                    <option value="{{ $p->id }}" @selected(old('project_id')==$p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description <span class="text-error-500">*</span></label>
                <textarea name="description" rows="6" required
                    placeholder="Please provide as much detail as possible. Include steps to reproduce, expected vs actual behaviour, links, etc."
                    class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white resize-none">{{ old('description') }}</textarea>
                @error('description')<p class="text-xs text-error-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('client.tickets.index') }}"
                    class="flex-1 text-center rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </a>
                <button type="submit"
                    class="flex-1 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
