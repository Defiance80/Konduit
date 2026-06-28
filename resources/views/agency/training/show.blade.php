@extends('layouts.app')
@section('title', $trainingCourse->title)

@section('content')
<div class="space-y-6">

    {{-- Back + breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('agency.training.index') }}" class="hover:text-brand-500 transition-colors">Training Academy</a>
        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        @if($trainingCourse->curriculum)
        <span>{{ $trainingCourse->curriculum->title }}</span>
        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        @endif
        <span class="text-gray-600 dark:text-gray-300">{{ $trainingCourse->title }}</span>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Main --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Course hero card --}}
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-hidden">
                <div class="h-2 bg-gradient-to-r from-brand-400 to-brand-600"></div>
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex rounded-full bg-{{ $trainingCourse->difficultyColor }}-50 dark:bg-{{ $trainingCourse->difficultyColor }}-500/10 px-2.5 py-0.5 text-xs font-medium text-{{ $trainingCourse->difficultyColor }}-600 dark:text-{{ $trainingCourse->difficultyColor }}-400">
                                    {{ ucfirst($trainingCourse->difficulty) }}
                                </span>
                                @if($trainingCourse->curriculum)
                                <span class="text-xs text-gray-400">{{ $trainingCourse->curriculum->title }}</span>
                                @endif
                            </div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $trainingCourse->title }}</h1>
                            @if($trainingCourse->description)
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $trainingCourse->description }}</p>
                            @endif
                        </div>
                        <div class="text-center shrink-0">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $progress }}%</div>
                            <div class="text-xs text-gray-400">complete</div>
                        </div>
                    </div>

                    <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                        <div class="h-2 rounded-full {{ $progress === 100 ? 'bg-success-400' : 'bg-brand-400' }} transition-all" style="width: {{ $progress }}%"></div>
                    </div>

                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <span>{{ $lessons->count() }} {{ Str::plural('lesson', $lessons->count()) }}</span>
                        <span>·</span>
                        <span>{{ $trainingCourse->estimated_minutes ?? 0 }} min estimated</span>
                        @if($trainingCourse->estimated_minutes)
                        <span>·</span>
                        <span>{{ floor(($trainingCourse->estimated_minutes ?? 0) / 60) > 0 ? floor($trainingCourse->estimated_minutes/60).'h ' : '' }}{{ ($trainingCourse->estimated_minutes ?? 0) % 60 }}min</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Lessons list --}}
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Lessons</h2>
                    @if($isAdmin)
                    <a href="{{ route('agency.training.lessons.create', $trainingCourse) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600 transition-colors">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Lesson
                    </a>
                    @endif
                </div>

                @forelse($lessons as $index => $lesson)
                <div class="flex items-center gap-4 px-6 py-4 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-800' : '' }} hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                    {{-- Completion indicator --}}
                    <div class="shrink-0">
                        @if($lesson->is_completed)
                        <div class="flex size-8 items-center justify-center rounded-full bg-success-50 dark:bg-success-500/10">
                            <svg class="size-4 text-success-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        @else
                        <div class="flex size-8 items-center justify-center rounded-full border-2 border-gray-200 dark:border-gray-700 text-gray-400 text-xs font-semibold">
                            {{ $index + 1 }}
                        </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            {{-- Type icon --}}
                            @if($lesson->type === 'video')
                            <svg class="size-3.5 text-sky-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                            <a href="{{ route('agency.training.lesson', [$trainingCourse, $lesson]) }}"
                               class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500 transition-colors truncate">
                                {{ $lesson->title }}
                            </a>
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs text-gray-400 capitalize">{{ $lesson->type ?? 'written' }}</span>
                            @if($lesson->duration_minutes)
                            <span class="text-xs text-gray-300 dark:text-gray-600">·</span>
                            <span class="text-xs text-gray-400">{{ $lesson->duration_minutes }} min</span>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('agency.training.lesson', [$trainingCourse, $lesson]) }}"
                       class="shrink-0 inline-flex items-center gap-1 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-brand-300 hover:text-brand-500 transition-colors opacity-0 group-hover:opacity-100">
                        {{ $lesson->is_completed ? 'Review' : 'Start' }}
                        <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                @empty
                <div class="px-6 py-10 text-center">
                    <p class="text-sm text-gray-400">No lessons added yet.</p>
                    @if($isAdmin)
                    <a href="{{ route('agency.training.lessons.create', $trainingCourse) }}" class="mt-2 inline-block text-sm text-brand-500 hover:text-brand-600">Add the first lesson →</a>
                    @endif
                </div>
                @endforelse
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Start / Continue CTA --}}
            @php
                $firstIncomplete = $lessons->first(fn ($l) => !$l->is_completed);
                $firstLesson     = $lessons->first();
            @endphp
            @if($firstLesson)
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
                @if($progress === 100)
                <div class="text-center">
                    <div class="inline-flex size-12 items-center justify-center rounded-full bg-success-50 dark:bg-success-500/10 mb-3">
                        <svg class="size-6 text-success-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Course Complete!</h3>
                    <p class="text-sm text-gray-400">You've finished all {{ $lessons->count() }} lessons.</p>
                    <a href="{{ route('agency.training.lesson', [$trainingCourse, $firstLesson]) }}"
                       class="mt-4 inline-flex w-full items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Review Course
                    </a>
                </div>
                @elseif($progress > 0)
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Continue Learning</h3>
                <p class="text-sm text-gray-400 mb-4">Pick up where you left off.</p>
                <a href="{{ route('agency.training.lesson', [$trainingCourse, $firstIncomplete ?? $firstLesson]) }}"
                   class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Continue
                </a>
                @else
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Ready to start?</h3>
                <p class="text-sm text-gray-400 mb-4">{{ $lessons->count() }} lessons · {{ $trainingCourse->estimated_minutes ?? 0 }}min</p>
                <a href="{{ route('agency.training.lesson', [$trainingCourse, $firstLesson]) }}"
                   class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/></svg>
                    Start Course
                </a>
                @endif
            </div>
            @endif

            {{-- Admin: Assignments panel --}}
            @if($isAdmin)
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Team Assignments</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Assign this course to team members so it appears in their training.</p>
                </div>

                {{-- Existing assignments --}}
                @if($assignments->count() > 0)
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($assignments as $assignment)
                    <div class="flex items-center gap-3 px-5 py-2.5">
                        <div class="flex size-7 shrink-0 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 text-xs font-bold">
                            {{ strtoupper(substr($assignment->user->name, 0, 1)) }}
                        </div>
                        <span class="text-sm text-gray-700 dark:text-gray-300 flex-1 truncate">{{ $assignment->user->name }}</span>
                        <form method="POST" action="{{ route('agency.training.assign.remove', [$trainingCourse, $assignment->user]) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-gray-400 hover:text-error-500 transition-colors">Remove</button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Add assignments --}}
                @php $unassigned = $teamMembers->whereNotIn('id', $assignedUserIds); @endphp
                @if($unassigned->count() > 0)
                <form method="POST" action="{{ route('agency.training.assign', $trainingCourse) }}" class="p-5 {{ $assignments->count() > 0 ? 'border-t border-gray-100 dark:border-gray-800' : '' }}">
                    @csrf
                    <p class="text-xs text-gray-400 mb-3">Add team members:</p>
                    <div class="space-y-2 mb-4 max-h-40 overflow-y-auto">
                        @foreach($unassigned as $member)
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="checkbox" name="user_ids[]" value="{{ $member->id }}"
                                   class="rounded border-gray-300 dark:border-gray-600 text-brand-500 focus:ring-brand-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-brand-500 transition-colors">{{ $member->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Assign Selected
                    </button>
                </form>
                @elseif($teamMembers->count() === 0)
                <p class="px-5 py-4 text-xs text-gray-400">No team members in this tenant yet.</p>
                @else
                <p class="px-5 py-4 text-xs text-gray-400">All team members are already assigned.</p>
                @endif
            </div>

            {{-- Admin: Delete course --}}
            <form method="POST" action="{{ route('agency.training.destroy', $trainingCourse) }}"
                  onsubmit="return confirm('Delete this course and all its lessons? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full rounded-xl border border-error-200 dark:border-error-800 px-4 py-2.5 text-sm font-medium text-error-600 dark:text-error-400 hover:bg-error-50 dark:hover:bg-error-500/10 transition-colors">
                    Delete Course
                </button>
            </form>
            @endif

        </div>
    </div>
</div>
@endsection
