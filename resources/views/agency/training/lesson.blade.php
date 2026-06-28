@extends('layouts.app')
@section('title', $trainingLesson->title)

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('agency.training.index') }}" class="hover:text-brand-500 transition-colors">Training</a>
        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('agency.training.show', $trainingCourse) }}" class="hover:text-brand-500 transition-colors">{{ $trainingCourse->title }}</a>
        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-600 dark:text-gray-300 truncate max-w-48">{{ $trainingLesson->title }}</span>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">

        {{-- Main content --}}
        <div class="lg:col-span-3 space-y-6">

            {{-- Lesson header --}}
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
                <div class="flex items-start justify-between gap-4 mb-6">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            @if($trainingLesson->type === 'video')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 dark:bg-sky-500/10 px-2.5 py-0.5 text-xs font-medium text-sky-600 dark:text-sky-400">
                                <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Video Lesson
                            </span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-gray-50 dark:bg-gray-800 px-2.5 py-0.5 text-xs font-medium text-gray-500 dark:text-gray-400">
                                Written Lesson
                            </span>
                            @endif
                            @if($trainingLesson->duration_minutes)
                            <span class="text-xs text-gray-400">{{ $trainingLesson->duration_minutes }} min</span>
                            @endif
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $trainingLesson->title }}</h1>
                    </div>
                    @if($isCompleted)
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-success-50 dark:bg-success-500/10 px-3 py-1.5 text-sm font-medium text-success-600 dark:text-success-400 shrink-0">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Completed
                    </div>
                    @endif
                </div>

                {{-- Course progress bar --}}
                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800">
                    <div class="flex-1 h-1.5 rounded-full bg-gray-200 dark:bg-gray-700">
                        <div class="h-1.5 rounded-full bg-brand-400 transition-all" style="width: {{ $progress }}%"></div>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 shrink-0">{{ $progress }}% of course</span>
                </div>
            </div>

            {{-- VIDEO EMBED --}}
            @if($trainingLesson->type === 'video' && $trainingLesson->embedUrl)
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden bg-black">
                <div class="relative" style="padding-bottom: 56.25%">
                    <iframe
                        src="{{ $trainingLesson->embedUrl }}"
                        class="absolute inset-0 w-full h-full"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                        loading="lazy">
                    </iframe>
                </div>
            </div>
            @elseif($trainingLesson->type === 'video' && $trainingLesson->video_url)
            {{-- Fallback for unrecognised video URL --}}
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 text-center">
                <a href="{{ $trainingLesson->video_url }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-3 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Watch Video (opens externally)
                </a>
            </div>
            @endif

            {{-- WRITTEN CONTENT --}}
            @if($trainingLesson->content)
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-8">
                <div class="prose prose-sm dark:prose-invert max-w-none
                            prose-headings:text-gray-900 dark:prose-headings:text-white
                            prose-p:text-gray-600 dark:prose-p:text-gray-400
                            prose-a:text-brand-500 prose-a:no-underline hover:prose-a:underline
                            prose-strong:text-gray-800 dark:prose-strong:text-gray-200
                            prose-code:text-brand-600 dark:prose-code:text-brand-400
                            prose-pre:bg-gray-50 dark:prose-pre:bg-gray-800">
                    {!! nl2br(e($trainingLesson->content)) !!}
                </div>
            </div>
            @endif

            {{-- Mark complete + navigation --}}
            <div class="flex items-center gap-3 justify-between">
                <div>
                    @if($prev)
                    <a href="{{ route('agency.training.lesson', [$trainingCourse, $prev]) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Previous
                    </a>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    @if(!$isCompleted)
                    <form method="POST" action="{{ route('agency.training.complete', [$trainingCourse, $trainingLesson]) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-lg bg-success-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-success-600 transition-colors">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Mark Complete
                        </button>
                    </form>
                    @endif

                    @if($next)
                    <a href="{{ route('agency.training.lesson', [$trainingCourse, $next]) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                        Next
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @else
                    <a href="{{ route('agency.training.show', $trainingCourse) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Back to Course
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar: lesson list --}}
        <div class="lg:col-span-1">
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-hidden sticky top-6">
                <div class="px-4 py-3.5 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Course Lessons</h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800 max-h-[calc(100vh-12rem)] overflow-y-auto">
                    @foreach($lessons as $index => $l)
                    <a href="{{ route('agency.training.lesson', [$trainingCourse, $l]) }}"
                       class="flex items-center gap-3 px-4 py-3 transition-colors
                              {{ $l->id === $trainingLesson->id ? 'bg-brand-50 dark:bg-brand-500/10' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                        <div class="shrink-0">
                            @if($l->isCompletedByUser(auth()->id()))
                            <div class="flex size-6 items-center justify-center rounded-full bg-success-50 dark:bg-success-500/10">
                                <svg class="size-3.5 text-success-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            @elseif($l->id === $trainingLesson->id)
                            <div class="flex size-6 items-center justify-center rounded-full bg-brand-100 dark:bg-brand-500/20">
                                <div class="size-2 rounded-full bg-brand-500"></div>
                            </div>
                            @else
                            <div class="flex size-6 items-center justify-center rounded-full border-2 border-gray-200 dark:border-gray-700 text-gray-400 text-xs">
                                {{ $index + 1 }}
                            </div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium leading-snug truncate {{ $l->id === $trainingLesson->id ? 'text-brand-600 dark:text-brand-400' : 'text-gray-700 dark:text-gray-300' }}">
                                {{ $l->title }}
                            </p>
                            @if($l->duration_minutes)
                            <p class="text-xs text-gray-400">{{ $l->duration_minutes }}min</p>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
