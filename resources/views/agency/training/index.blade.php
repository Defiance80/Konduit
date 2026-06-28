@extends('layouts.app')
@section('title', 'Training Academy')

@section('content')
<div class="space-y-8">

    {{-- Hero --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800 p-8 text-white">
        <div class="relative z-10">
            <div class="flex items-start justify-between gap-6">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Training Academy</h1>
                    <p class="text-brand-200 text-sm max-w-xl">Master agency operations, marketing strategy, client management, and Konduit — at your own pace.</p>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-3xl font-bold">{{ $overallProgress }}%</div>
                    <div class="text-brand-300 text-xs mt-0.5">Overall progress</div>
                    <div class="text-brand-200 text-xs">{{ $completedLessons }} / {{ $totalLessons }} lessons</div>
                </div>
            </div>
            <div class="mt-5 h-2 w-full rounded-full bg-brand-700/60">
                <div class="h-2 rounded-full bg-white transition-all" style="width: {{ $overallProgress }}%"></div>
            </div>
        </div>
        <div class="absolute -right-8 -top-8 size-48 rounded-full bg-white/5"></div>
        <div class="absolute -bottom-12 -left-8 size-56 rounded-full bg-white/5"></div>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-3 gap-4">
        @php
        $statCards = [
            ['label' => 'Total Courses', 'value' => $courses->count(), 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'color' => 'brand'],
            ['label' => 'Completed',     'value' => $courses->where('user_progress', 100)->count(), 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'success'],
            ['label' => 'In Progress',   'value' => $courses->where('user_progress', '>', 0)->where('user_progress', '<', 100)->count(), 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'warning'],
        ];
        @endphp
        @foreach($statCards as $card)
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex size-9 items-center justify-center rounded-lg bg-{{ $card['color'] }}-50 dark:bg-{{ $card['color'] }}-500/10">
                    <svg class="size-4.5 text-{{ $card['color'] }}-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $card['icon'] }}"/>
                    </svg>
                </div>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $card['label'] }}</span>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $card['value'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Course groups --}}
    @foreach($grouped as $category => $categoryCourses)
    @php
        $first = $categoryCourses->first();
        $catLabel = $first->category_label;
        $catIcon  = $first->category_icon;
    @endphp
    <div>
        <div class="flex items-center gap-3 mb-4">
            <svg class="size-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $catIcon }}"/>
            </svg>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ $catLabel }}</h2>
            <div class="flex-1 h-px bg-gray-100 dark:bg-gray-800"></div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($categoryCourses as $course)
            @php
                $diffColor = $course->difficulty_color;
                $progress  = $course->user_progress;
                $status    = $progress >= 100 ? 'completed' : ($progress > 0 ? 'in_progress' : 'not_started');
            @endphp
            <a href="{{ route('agency.training.show', $course) }}"
               class="group rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6 hover:shadow-md hover:border-brand-200 dark:hover:border-brand-700 transition-all block">

                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-gray-50 dark:bg-gray-800 group-hover:bg-brand-50 dark:group-hover:bg-brand-500/10 transition-colors">
                        <svg class="size-5.5 text-gray-500 dark:text-gray-400 group-hover:text-brand-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $catIcon }}"/>
                        </svg>
                    </div>
                    <div class="flex flex-col items-end gap-1.5">
                        <span class="inline-flex items-center rounded-full bg-{{ $diffColor }}-50 dark:bg-{{ $diffColor }}-500/10 px-2.5 py-0.5 text-xs font-medium text-{{ $diffColor }}-700 dark:text-{{ $diffColor }}-400">
                            {{ ucfirst($course->difficulty) }}
                        </span>
                        @if($status === 'completed')
                        <span class="inline-flex items-center gap-1 text-xs text-success-600 dark:text-success-400 font-medium">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Done
                        </span>
                        @elseif($status === 'in_progress')
                        <span class="text-xs text-brand-500 font-medium">{{ $progress }}%</span>
                        @endif
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-gray-900 dark:text-white leading-snug mb-1.5 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">
                    {{ $course->title }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-4">{{ $course->description }}</p>

                <div class="flex items-center justify-between text-xs text-gray-400 dark:text-gray-600 mb-3">
                    <span>{{ $course->lessons_count }} lesson{{ $course->lessons_count !== 1 ? 's' : '' }}</span>
                    <span>~{{ $course->estimated_minutes }} min</span>
                </div>

                <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                    <div class="h-1.5 rounded-full transition-all
                        {{ $status === 'completed' ? 'bg-success-500' : 'bg-brand-500' }}"
                        style="width: {{ $progress }}%"></div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endforeach

</div>
@endsection
