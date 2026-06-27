@extends('layouts.app')
@section('title', 'Capacity Engine')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Capacity Engine</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
            Team workload for the week of {{ $weekStart->format('M j') }} – {{ $weekEnd->format('M j, Y') }}
        </p>
    </div>

    {{-- Agency-wide stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach([
            ['label'=>'Open Tasks',       'value'=>$agencyStats['total_open_tasks'],  'color'=>'brand'],
            ['label'=>'Overdue',          'value'=>$agencyStats['overdue_tasks'],      'color'=>'error'],
            ['label'=>'Unassigned',       'value'=>$agencyStats['unassigned_tasks'],   'color'=>'warning'],
            ['label'=>'Est. Hours Open',  'value'=>$agencyStats['total_est_hours'].'h','color'=>'gray'],
        ] as $s)
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ $s['label'] }}</p>
            <p class="text-2xl font-bold text-{{ $s['color'] }}-600 dark:text-{{ $s['color'] }}-400">{{ $s['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Team capacity cards --}}
    @if($capacityData->isEmpty())
    <div class="rounded-xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-gray-900">
        <p class="text-gray-500 dark:text-gray-400">No team members found. Invite team members under Settings > Team.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($capacityData as $data)
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            {{-- Member header --}}
            <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100 dark:border-gray-800">
                <img src="{{ $data['member']->avatar_url }}" alt="{{ $data['member']->name }}"
                    class="size-9 rounded-full object-cover">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $data['member']->name }}</p>
                    <p class="text-xs text-gray-400">{{ $data['member']->email }}</p>
                </div>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                    @if($data['load_color']==='error')   bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400
                    @elseif($data['load_color']==='warning') bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400
                    @elseif($data['load_color']==='success') bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400
                    @else bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 @endif">
                    {{ $data['load_label'] }}
                </span>
            </div>

            {{-- Workload bar --}}
            <div class="px-4 py-3">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Load</span>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ round($data['load_score']) }}%</span>
                </div>
                <div class="h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                    <div class="h-2 rounded-full transition-all
                        @if($data['load_color']==='error')   bg-error-500
                        @elseif($data['load_color']==='warning') bg-warning-500
                        @elseif($data['load_color']==='success') bg-success-500
                        @else bg-gray-300 @endif"
                        style="width: {{ min(100, $data['load_score']) }}%"></div>
                </div>
            </div>

            {{-- Quick stats --}}
            <div class="grid grid-cols-3 divide-x divide-gray-100 dark:divide-gray-800 border-t border-gray-100 dark:border-gray-800">
                <div class="px-3 py-2 text-center">
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $data['total_tasks'] }}</p>
                    <p class="text-xs text-gray-400">Tasks</p>
                </div>
                <div class="px-3 py-2 text-center">
                    <p class="text-lg font-bold {{ $data['overdue_tasks'] > 0 ? 'text-error-600 dark:text-error-400' : 'text-gray-900 dark:text-white' }}">{{ $data['overdue_tasks'] }}</p>
                    <p class="text-xs text-gray-400">Overdue</p>
                </div>
                <div class="px-3 py-2 text-center">
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $data['estimated_hours'] }}h</p>
                    <p class="text-xs text-gray-400">Est.</p>
                </div>
            </div>

            {{-- Top tasks --}}
            @if($data['tasks']->isNotEmpty())
            <div class="px-4 pb-4 space-y-1.5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 pt-2">Active tasks</p>
                @foreach($data['tasks'] as $task)
                <div class="flex items-center gap-2">
                    <div class="size-1.5 rounded-full flex-shrink-0
                        {{ $task->priority === 'urgent' ? 'bg-error-500' : ($task->priority === 'high' ? 'bg-warning-500' : 'bg-gray-300 dark:bg-gray-600') }}">
                    </div>
                    <a href="{{ route('agency.tasks.show', $task) }}"
                        class="text-xs text-gray-600 dark:text-gray-400 hover:text-brand-600 dark:hover:text-brand-400 truncate">
                        {{ $task->title }}
                    </a>
                    @if($task->due_date)
                    <span class="text-xs text-gray-400 ml-auto flex-shrink-0 {{ $task->isOverdue() ? 'text-error-500' : '' }}">
                        {{ $task->due_date->format('M j') }}
                    </span>
                    @endif
                </div>
                @endforeach
                @if($data['total_tasks'] > 5)
                <a href="{{ route('agency.tasks.index', ['assignee' => $data['member']->id]) }}"
                    class="text-xs text-brand-600 dark:text-brand-400 hover:underline">
                    +{{ $data['total_tasks'] - 5 }} more tasks
                </a>
                @endif
            </div>
            @else
            <div class="px-4 pb-4">
                <p class="text-xs text-gray-400 dark:text-gray-500 italic mt-2">No open tasks assigned.</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
