@extends('layouts.app')
@section('title', 'Retainers')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Retainers</h1>
            <p class="text-sm text-gray-500 mt-1">Monthly recurring contracts</p>
        </div>
        <a href="{{ route('agency.retainers.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Retainer
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-medium uppercase text-gray-500 mb-1">Active</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['active'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-medium uppercase text-gray-500 mb-1">Monthly Revenue</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['monthly'], 0) }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-medium uppercase text-gray-500 mb-1">Paused</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['paused'] }}</p>
        </div>
    </div>

    <!-- List -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-6 py-3.5 text-left text-xs font-medium uppercase text-gray-400">Retainer</th>
                    <th class="px-6 py-3.5 text-left text-xs font-medium uppercase text-gray-400">Client</th>
                    <th class="px-6 py-3.5 text-left text-xs font-medium uppercase text-gray-400">Monthly Value</th>
                    <th class="px-6 py-3.5 text-left text-xs font-medium uppercase text-gray-400">Status</th>
                    <th class="px-6 py-3.5 text-left text-xs font-medium uppercase text-gray-400">Starts</th>
                    <th class="px-6 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($retainers as $retainer)
                @php $sc = ['active' => 'success', 'paused' => 'warning', 'cancelled' => 'error', 'completed' => 'gray', 'draft' => 'blue-light'][$retainer->status] ?? 'gray'; @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $retainer->name }}</td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $retainer->client->name }}</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300">${{ number_format($retainer->monthly_value, 0) }}</td>
                    <td class="px-6 py-4"><span class="inline-flex items-center rounded-full bg-{{ $sc }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $sc }}-700 dark:bg-{{ $sc }}-500/10 dark:text-{{ $sc }}-400">{{ ucfirst($retainer->status) }}</span></td>
                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $retainer->start_date->format('M j, Y') }}</td>
                    <td class="px-6 py-4 text-right"><a href="{{ route('agency.retainers.show', $retainer) }}" class="text-brand-500 hover:text-brand-600 text-xs font-medium">View</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">No retainers yet. <a href="{{ route('agency.retainers.create') }}" class="text-brand-500">Create one</a></td></tr>
                @endforelse
            </tbody>
        </table>
        @if($retainers->hasPages()) <div class="border-t px-6 py-4">{{ $retainers->links() }}</div> @endif
    </div>
</div>
@endsection
