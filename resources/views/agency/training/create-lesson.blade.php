@extends('layouts.app')
@section('title', 'Add Lesson — ' . $trainingCourse->title)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <a href="{{ route('agency.training.show', $trainingCourse) }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-brand-500 transition-colors mb-4">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to {{ $trainingCourse->title }}
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add Lesson</h1>
        <p class="text-sm text-gray-400 mt-1">Lessons can be written content, or a video from YouTube, Vimeo, or any other embeddable source.</p>
    </div>

    <form method="POST" action="{{ route('agency.training.lessons.store', $trainingCourse) }}"
          x-data="{ type: '{{ old('type', 'written') }}' }"
          class="space-y-5">
        @csrf

        @if($errors->any())
        <div class="rounded-xl border border-error-200 dark:border-error-800 bg-error-50 dark:bg-error-500/10 p-4">
            <ul class="space-y-1">
                @foreach($errors->all() as $e)
                <li class="text-sm text-error-600 dark:text-error-400">{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 space-y-5">

            {{-- Title --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Lesson Title <span class="text-error-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="e.g. Understanding Keyword Intent"
                       class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            {{-- Type toggle --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content Type <span class="text-error-500">*</span></label>
                <div class="inline-flex rounded-lg border border-gray-200 dark:border-gray-700 p-1 gap-1">
                    <button type="button"
                            @click="type = 'written'"
                            :class="type === 'written' ? 'bg-brand-500 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                            class="inline-flex items-center gap-1.5 rounded-md px-4 py-2 text-sm font-medium transition-all">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Written
                    </button>
                    <button type="button"
                            @click="type = 'video'"
                            :class="type === 'video' ? 'bg-brand-500 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                            class="inline-flex items-center gap-1.5 rounded-md px-4 py-2 text-sm font-medium transition-all">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Video
                    </button>
                </div>
                <input type="hidden" name="type" :value="type">
            </div>

            {{-- Video URL (shown when type=video) --}}
            <div x-show="type === 'video'" x-transition>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Video URL</label>
                <input type="url" name="video_url" value="{{ old('video_url') }}"
                       placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/..."
                       class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                <p class="mt-1.5 text-xs text-gray-400">Supports YouTube, Vimeo, and any other embeddable video URL. Provider is auto-detected.</p>
            </div>

            {{-- Written content (shown when type=written) --}}
            <div x-show="type === 'written'" x-transition>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Content</label>
                <textarea name="content" rows="16"
                          placeholder="Write the lesson content here. Use line breaks to separate paragraphs."
                          class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 font-mono resize-y">{{ old('content') }}</textarea>
                <p class="mt-1.5 text-xs text-gray-400">Plain text — line breaks create new paragraphs. HTML is not supported.</p>
            </div>

            {{-- Duration --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Estimated Duration (minutes) <span class="text-error-500">*</span></label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 10) }}" min="1" max="300" required
                       class="w-40 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-900 dark:text-white focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
        </div>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('agency.training.show', $trainingCourse) }}"
               class="rounded-lg border border-gray-200 dark:border-gray-700 px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                Add Lesson
            </button>
        </div>
    </form>
</div>
@endsection
