@extends('layouts.app')
@section('title', $retainer->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('agency.retainers.index') }}" class="text-gray-400 hover:text-gray-600"><svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
        <div class="flex-1">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $retainer->name }}</h1>
            <p class="text-sm text-gray-500">{{ $retainer->client->name }}</p>
        </div>
        <a href="{{ route('agency.retainers.edit', $retainer) }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Edit</a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 space-y-3">
            <h2 class="font-semibold text-gray-900 dark:text-white">Details</h2>
            @php $sc = ['active' => 'success', 'paused' => 'warning', 'cancelled' => 'error', 'completed' => 'gray', 'draft' => 'blue-light'][$retainer->status] ?? 'gray'; @endphp
            <div class="flex justify-between text-sm"><span class="text-gray-400">Status</span><span class="inline-flex items-center rounded-full bg-{{ $sc }}-50 px-2 py-0.5 text-xs font-medium text-{{ $sc }}-700">{{ ucfirst($retainer->status) }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-400">Monthly Value</span><span class="font-medium text-gray-900 dark:text-white">${{ number_format($retainer->monthly_value, 0) }}</span></div>
            @if($retainer->hours_included) <div class="flex justify-between text-sm"><span class="text-gray-400">Hours</span><span class="text-gray-700 dark:text-gray-300">{{ $retainer->hours_included }}h/mo</span></div> @endif
            <div class="flex justify-between text-sm"><span class="text-gray-400">Billing</span><span class="text-gray-700 dark:text-gray-300">{{ ucfirst($retainer->billing_cycle) }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-400">Start Date</span><span class="text-gray-700 dark:text-gray-300">{{ $retainer->start_date->format('M j, Y') }}</span></div>
            @if($retainer->end_date) <div class="flex justify-between text-sm"><span class="text-gray-400">End Date</span><span class="text-gray-700 dark:text-gray-300">{{ $retainer->end_date->format('M j, Y') }}</span></div> @endif
        </div>
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800"><h2 class="font-semibold text-gray-900 dark:text-white">Projects under this retainer</h2></div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($retainer->projects as $project)
                    <div class="flex items-center justify-between px-6 py-3">
                        <a href="{{ route('agency.projects.show', $project) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500">{{ $project->name }}</a>
                        <span class="text-xs text-gray-400">{{ $project->progress }}%</span>
                    </div>
                    @empty
                    <p class="px-6 py-6 text-sm text-gray-400 text-center">No projects linked to this retainer</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
