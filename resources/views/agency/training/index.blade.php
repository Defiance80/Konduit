@extends('layouts.app')
@section('title', 'Training Academy')

@section('content')
<div class="space-y-8">

    {{-- Hero --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800 p-8 text-white">
        <div class="absolute -right-16 -top-16 size-64 rounded-full bg-white/5"></div>
        <div class="absolute -left-10 -bottom-10 size-48 rounded-full bg-white/5"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between gap-6">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Training Academy</h1>
                    <p class="text-brand-200 text-sm max-w-xl">Structured learning paths organised by curriculum — from platform basics to advanced marketing strategy, all in one place.</p>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-3xl font-bold">{{ $overallProgress }}%</div>
                    <div class="text-brand-300 text-xs mt-0.5">Overall progress</div>
                    <div class="text-brand-200 text-xs">{{ $completedLessons }} / {{ $totalLessons }} lessons</div>
                </div>
            </div>
            <div class="mt-5 h-2 rounded-full bg-white/20">
                <div class="h-2 rounded-full bg-white transition-all duration-700" style="width: {{ $overallProgress }}%"></div>
            </div>
        </div>
    </div>

    {{-- Admin toolbar --}}
    @if($isAdmin)
    <div class="flex items-center gap-3 p-4 rounded-xl border border-brand-200 dark:border-brand-800 bg-brand-50 dark:bg-brand-500/10">
        <svg class="size-4 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <span class="text-sm font-medium text-brand-700 dark:text-brand-300 flex-1">Admin mode — you can create and manage courses and curricula.</span>
        <a href="{{ route('agency.training.create-curriculum') }}"
           class="inline-flex items-center gap-1.5 rounded-lg border border-brand-300 dark:border-brand-700 bg-white dark:bg-gray-900 px-3 py-1.5 text-xs font-medium text-brand-600 dark:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-500/10 transition-colors">
            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Curriculum
        </a>
        <a href="{{ route('agency.training.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600 transition-colors">
            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Course
        </a>
    </div>
    @endif

    {{-- Curricula --}}
    @forelse($curricula as $curriculum)
    @php
        $clrMap = [
            'brand'      => 'brand',
            'blue-light' => 'sky',
            'success'    => 'emerald',
            'warning'    => 'amber',
            'error'      => 'red',
        ];
        $c = $clrMap[$curriculum->color] ?? 'brand';
    @endphp
    <div>
        <div class="flex items-center gap-3 mb-4">
            <div class="inline-flex items-center gap-2 rounded-xl border border-{{ $c }}-200 dark:border-{{ $c }}-800 bg-{{ $c }}-50 dark:bg-{{ $c }}-500/10 px-4 py-2 text-{{ $c }}-700 dark:text-{{ $c }}-400">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="text-sm font-semibold">{{ $curriculum->title }}</span>
                <span class="text-xs opacity-60">· {{ $curriculum->courses->count() }} {{ Str::plural('course', $curriculum->courses->count()) }}</span>
            </div>
            @if($curriculum->description)
            <p class="text-sm text-gray-400 hidden sm:block">{{ $curriculum->description }}</p>
            @endif
        </div>

        @if($curriculum->courses->count() > 0)
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($curriculum->courses as $course)
            <a href="{{ route('agency.training.show', $course) }}"
               class="group rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-hidden hover:shadow-lg hover:shadow-gray-100 dark:hover:shadow-gray-900/50 transition-all hover:-translate-y-0.5 flex flex-col">

                <div class="h-1 bg-{{ $c }}-400 dark:bg-{{ $c }}-500 opacity-70"></div>

                <div class="p-5 flex flex-col flex-1">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex gap-2 flex-wrap">
                            <span class="inline-flex items-center rounded-full bg-{{ $course->difficultyColor }}-50 dark:bg-{{ $course->difficultyColor }}-500/10 px-2 py-0.5 text-xs font-medium text-{{ $course->difficultyColor }}-600 dark:text-{{ $course->difficultyColor }}-400">
                                {{ ucfirst($course->difficulty) }}
                            </span>
                            @if($course->is_assigned)
                            <span class="inline-flex items-center gap-1 rounded-full bg-brand-50 dark:bg-brand-500/10 px-2 py-0.5 text-xs font-medium text-brand-600 dark:text-brand-400">
                                <svg class="size-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Assigned
                            </span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-400 shrink-0">{{ $course->estimated_minutes ?? 0 }}min</span>
                    </div>

                    <h3 class="text-base font-semibold text-gray-900 dark:text-white group-hover:text-brand-500 transition-colors flex-1 mb-2 leading-snug">{{ $course->title }}</h3>

                    @if($course->description)
                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-4">{{ $course->description }}</p>
                    @endif

                    <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs text-gray-400">{{ $course->lessons_count ?? 0 }} {{ Str::plural('lesson', $course->lessons_count ?? 0) }}</span>
                            <span class="text-xs font-semibold {{ ($course->user_progress ?? 0) === 100 ? 'text-success-600 dark:text-success-400' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $course->user_progress ?? 0 }}%
                            </span>
                        </div>
                        <div class="h-1.5 rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-1.5 rounded-full transition-all duration-500 {{ ($course->user_progress ?? 0) === 100 ? 'bg-success-400' : 'bg-brand-400' }}"
                                 style="width: {{ $course->user_progress ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach

            @if($isAdmin)
            <a href="{{ route('agency.training.create') }}?curriculum_id={{ $curriculum->id }}"
               class="rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 hover:border-brand-300 dark:hover:border-brand-700 flex items-center justify-center min-h-36 transition-colors group">
                <div class="text-center">
                    <svg class="size-8 text-gray-300 dark:text-gray-600 group-hover:text-brand-400 transition-colors mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                    <p class="text-sm text-gray-400 group-hover:text-brand-500 transition-colors">Add course</p>
                </div>
            </a>
            @endif
        </div>
        @else
        <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 py-8 text-center">
            <p class="text-sm text-gray-400">No courses in this curriculum yet.</p>
            @if($isAdmin)
            <a href="{{ route('agency.training.create') }}?curriculum_id={{ $curriculum->id }}" class="mt-2 inline-block text-sm text-brand-500 hover:text-brand-600">Add the first course →</a>
            @endif
        </div>
        @endif
    </div>
    @empty
    <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 py-16 text-center">
        <svg class="size-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        <p class="text-gray-400">No curricula yet.</p>
        @if($isAdmin)
        <a href="{{ route('agency.training.create-curriculum') }}" class="mt-2 inline-block text-sm text-brand-500 hover:text-brand-600">Create your first curriculum →</a>
        @endif
    </div>
    @endforelse

</div>
@endsection
