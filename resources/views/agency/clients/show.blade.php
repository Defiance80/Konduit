@extends('layouts.app')
@section('title', $client->name)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('agency.clients.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <img src="{{ $client->logo_url }}" class="size-12 rounded-xl flex-shrink-0" alt="{{ $client->name }}">
            <div class="min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white truncate">{{ $client->name }}</h1>
                <p class="text-sm text-gray-500">{{ $client->industry ?: 'No industry set' }}@if($client->website) &middot; <a href="{{ $client->website }}" target="_blank" class="text-brand-500 hover:underline">{{ $client->website }}</a>@endif</p>
            </div>
        </div>
        <a href="{{ route('agency.clients.edit', $client) }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Edit</a>
    </div>

    @if(session('success'))
    <div class="rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:border-error-800 dark:bg-error-900/20 dark:text-error-400">{{ session('error') }}</div>
    @endif

    {{-- AI Summary --}}
    @include('partials.ai-summary-card', [
        'summary'        => $aiSummary,
        'generateRoute'  => 'agency.clients.ai-summary',
        'generateParam'  => $client,
        'label'          => 'Client',
    ])

    {{-- Main content --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Client Info --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 space-y-4">
            <h2 class="font-semibold text-gray-900 dark:text-white">Contact</h2>
            @if($client->email)
            <div>
                <p class="text-xs text-gray-400">Email</p>
                <a href="mailto:{{ $client->email }}" class="text-sm text-brand-600 dark:text-brand-400 hover:underline">{{ $client->email }}</a>
            </div>
            @endif
            @if($client->phone)
            <div>
                <p class="text-xs text-gray-400">Phone</p>
                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $client->phone }}</p>
            </div>
            @endif
            @if($client->website)
            <div>
                <p class="text-xs text-gray-400">Website</p>
                <a href="{{ $client->website }}" target="_blank" class="text-sm text-brand-500 hover:underline">{{ $client->website }}</a>
            </div>
            @endif

            {{-- Active Retainer --}}
            @php $retainer = $client->retainers->where('status', 'active')->first(); @endphp
            @if($retainer)
            <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
                <p class="text-xs text-gray-400 mb-1">Active Retainer</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($retainer->monthly_value, 0) }}/mo</p>
                <p class="text-xs text-gray-400">{{ $retainer->name }}</p>
            </div>
            @endif

            @if($client->notes)
            <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
                <p class="text-xs text-gray-400 mb-1">Notes</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $client->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Projects & Tickets --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Projects</h2>
                    <a href="{{ route('agency.projects.create') }}" class="text-xs text-brand-500 hover:text-brand-600">+ New</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($client->projects as $project)
                    <div class="flex items-center justify-between px-6 py-3">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <a href="{{ route('agency.projects.show', $project) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500 truncate">{{ $project->name }}</a>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            @php $sc = ['active' => 'success', 'on_hold' => 'warning', 'completed' => 'gray', 'cancelled' => 'error', 'draft' => 'blue-light'][$project->status] ?? 'gray'; @endphp
                            <span class="inline-flex items-center rounded-full bg-{{ $sc }}-50 px-2 py-0.5 text-xs font-medium text-{{ $sc }}-700 dark:bg-{{ $sc }}-500/10 dark:text-{{ $sc }}-400">{{ ucfirst($project->status) }}</span>
                            <span class="text-xs text-gray-400 w-8 text-right">{{ $project->progress }}%</span>
                        </div>
                    </div>
                    @empty
                    <p class="px-6 py-4 text-sm text-gray-400">No projects yet. <a href="{{ route('agency.projects.create') }}" class="text-brand-500 hover:underline">Create one</a>.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Recent Tickets</h2>
                    <a href="{{ route('agency.tickets.create') }}" class="text-xs text-brand-500 hover:text-brand-600">+ New</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($client->tickets as $ticket)
                    <div class="flex items-center justify-between px-6 py-3">
                        <a href="{{ route('agency.tickets.show', $ticket) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500 flex-1 truncate">{{ $ticket->subject }}</a>
                        <span class="text-xs text-gray-400 flex-shrink-0 ml-3">{{ $ticket->ticket_number }}</span>
                    </div>
                    @empty
                    <p class="px-6 py-4 text-sm text-gray-400">No tickets.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
