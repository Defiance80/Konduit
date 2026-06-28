@extends('layouts.app')
@section('title', $trainingCourse->title)

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
        <a href="{{ route('agency.training.index') }}" class="hover:text-brand-500">Training Academy</a>
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-900 dark:text-white">{{ $trainingCourse->title }}</span>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Course info sidebar --}}
        <div class="space-y-5">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6">
                <div class="flex size-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/10 mb-4">
                    <svg class="size-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $trainingCourse->category_icon }}"/>
                    </svg>
                </div>

                <h1 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $trainingCourse->title }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">{{ $trainingCourse->description }}</p>

                <div class="space-y-3 mb-5">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Category</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $trainingCourse->category_label }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Difficulty</span>
                        @php $dc = $trainingCourse->difficulty_color; @endphp
                        <span class="inline-flex items-center rounded-full bg-{{ $dc }}-50 dark:bg-{{ $dc }}-500/10 px-2.5 py-0.5 text-xs font-medium text-{{ $dc }}-700 dark:text-{{ $dc }}-400">
                            {{ ucfirst($trainingCourse->difficulty) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Lessons</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $lessons->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Est. time</span>
                        <span class="font-medium text-gray-900 dark:text-white">~{{ $trainingCourse->estimated_minutes }} min</span>
                    </div>
                </div>

                <div class="mb-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>Your progress</span>
                    <span>{{ $progress }}%</span>
                </div>
                <div class="h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                    <div class="h-2 rounded-full {{ $progress >= 100 ? 'bg-success-500' : 'bg-brand-500' }} transition-all"
                         style="width: {{ $progress }}%"></div>
                </div>

                @if($lessons->isNotEmpty())
                @php
                    $firstIncomplete = $lessons->first(fn ($l) => !$l->is_completed);
                    $firstLesson     = $firstIncomplete ?? $lessons->first();
                @endphp
                <a href="{{ route('agency.training.lesson', [$trainingCourse, $firstLesson]) }}"
                   class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                    {{ $progress >= 100 ? 'Review Course' : ($progress > 0 ? 'Continue Learning' : 'Start Course') }}
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endif
            </div>
        </div>

        {{-- Lessons list --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Course Lessons</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($lessons as $i => $lesson)
                    <a href="{{ route('agency.training.lesson', [$trainingCourse, $lesson]) }}"
                       class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors group">
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-full
                            {{ $lesson->is_completed
                                ? 'bg-success-50 dark:bg-success-500/10 text-success-500'
                                : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 group-hover:bg-brand-50 dark:group-hover:bg-brand-500/10 group-hover:text-brand-500' }}">
                            @if($lesson->is_completed)
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @else
                            <span class="text-xs font-semibold">{{ $i + 1 }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">
                                {{ $lesson->title }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-600 mt-0.5">{{ $lesson->duration_minutes }} min read</p>
                        </div>
                        <svg class="size-4 text-gray-300 dark:text-gray-700 group-hover:text-brand-400 transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
