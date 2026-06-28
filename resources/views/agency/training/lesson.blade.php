@extends('layouts.app')
@section('title', $trainingLesson->title)

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 flex-wrap">
        <a href="{{ route('agency.training.index') }}" class="hover:text-brand-500">Training Academy</a>
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('agency.training.show', $trainingCourse) }}" class="hover:text-brand-500">{{ $trainingCourse->title }}</a>
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-900 dark:text-white">{{ $trainingLesson->title }}</span>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">

        {{-- Lesson sidebar --}}
        <div class="space-y-4 lg:order-last">
            {{-- Course progress --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5">
                <h3 class="text-xs font-medium uppercase text-gray-400 dark:text-gray-600 tracking-wider mb-3">Course Progress</h3>
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-2">
                    <span>{{ $trainingCourse->title }}</span>
                    <span>{{ $progress }}%</span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                    <div class="h-1.5 rounded-full {{ $progress >= 100 ? 'bg-success-500' : 'bg-brand-500' }}" style="width: {{ $progress }}%"></div>
                </div>
                <div class="space-y-1">
                    @foreach($lessons as $i => $l)
                    <a href="{{ route('agency.training.lesson', [$trainingCourse, $l]) }}"
                       class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-xs transition-colors
                           {{ $l->id === $trainingLesson->id
                               ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400 font-medium'
                               : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <div class="flex size-5 shrink-0 items-center justify-center rounded-full
                            {{ $l->is_completed ?? $l->isCompletedByUser(auth()->id())
                                ? 'bg-success-100 dark:bg-success-500/10 text-success-600 dark:text-success-400'
                                : ($l->id === $trainingLesson->id ? 'bg-brand-100 dark:bg-brand-500/20 text-brand-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-400') }}">
                            @if($l->is_completed ?? $l->isCompletedByUser(auth()->id()))
                            <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @else
                            <span class="text-[10px] font-bold">{{ $i + 1 }}</span>
                            @endif
                        </div>
                        <span class="truncate">{{ $l->title }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- Lesson header --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 px-8 py-6">
                <div class="flex items-start justify-between gap-4 mb-1">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $trainingLesson->title }}</h1>
                    @if($isCompleted)
                    <span class="shrink-0 inline-flex items-center gap-1.5 rounded-full bg-success-50 dark:bg-success-500/10 px-3 py-1 text-xs font-medium text-success-700 dark:text-success-400">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Completed
                    </span>
                    @endif
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-600">
                    {{ $trainingCourse->title }} &middot; {{ $trainingLesson->duration_minutes }} min read
                </p>
            </div>

            {{-- Flash --}}
            @if(session('success'))
            <div class="rounded-xl bg-success-50 dark:bg-success-500/10 border border-success-200 dark:border-success-500/20 px-4 py-3 text-sm text-success-700 dark:text-success-400 flex items-center gap-2">
                <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
            @endif

            {{-- Lesson content --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 px-8 py-8">
                <div class="prose prose-sm prose-gray dark:prose-invert max-w-none
                    prose-h2:text-lg prose-h2:font-semibold prose-h2:text-gray-900 dark:prose-h2:text-white prose-h2:mb-3
                    prose-h3:text-sm prose-h3:font-semibold prose-h3:text-gray-800 dark:prose-h3:text-gray-200 prose-h3:mt-5 prose-h3:mb-2
                    prose-p:text-gray-600 dark:prose-p:text-gray-400 prose-p:leading-relaxed prose-p:mb-3
                    prose-ul:text-gray-600 dark:prose-ul:text-gray-400 prose-ul:space-y-1.5
                    prose-ol:text-gray-600 dark:prose-ol:text-gray-400 prose-ol:space-y-2
                    prose-strong:text-gray-800 dark:prose-strong:text-gray-200
                    prose-li:leading-relaxed">
                    {!! $trainingLesson->content !!}
                </div>
            </div>

            {{-- Navigation + Complete button --}}
            <div class="flex items-center justify-between gap-4">
                <div>
                    @if($prev)
                    <a href="{{ route('agency.training.lesson', [$trainingCourse, $prev]) }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
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
                            class="inline-flex items-center gap-2 rounded-xl bg-success-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-success-600 transition-colors">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Mark Complete
                        </button>
                    </form>
                    @endif

                    @if($next)
                    <a href="{{ route('agency.training.lesson', [$trainingCourse, $next]) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                        Next Lesson
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @else
                    <a href="{{ route('agency.training.show', $trainingCourse) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                        Back to Course
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
