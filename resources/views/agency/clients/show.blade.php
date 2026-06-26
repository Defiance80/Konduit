@extends('layouts.app')
@section('title', $client->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('agency.clients.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex items-center gap-3">
            <img src="{{ $client->logo_url }}" class="size-12 rounded-xl" alt="{{ $client->name }}">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $client->name }}</h1>
                <p class="text-sm text-gray-500">{{ $client->industry ?: 'No industry set' }} &middot; {{ $client->website ?: 'No website' }}</p>
            </div>
        </div>
        <div class="ml-auto flex gap-2">
            <a href="{{ route('agency.clients.edit', $client) }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Edit</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Client Info -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 space-y-4">
            <h2 class="font-semibold text-gray-900 dark:text-white">Contact</h2>
            @if($client->email) <div><p class="text-xs text-gray-400">Email</p><p class="text-sm text-gray-700 dark:text-gray-300">{{ $client->email }}</p></div> @endif
            @if($client->phone) <div><p class="text-xs text-gray-400">Phone</p><p class="text-sm text-gray-700 dark:text-gray-300">{{ $client->phone }}</p></div> @endif
            @if($client->website) <div><p class="text-xs text-gray-400">Website</p><a href="{{ $client->website }}" target="_blank" class="text-sm text-brand-500 hover:underline">{{ $client->website }}</a></div> @endif
            @if($client->notes) <div><p class="text-xs text-gray-400">Notes</p><p class="text-sm text-gray-600 dark:text-gray-400">{{ $client->notes }}</p></div> @endif
        </div>

        <!-- Projects & Tickets -->
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Projects</h2>
                    <a href="{{ route('agency.projects.create') }}" class="text-xs text-brand-500 hover:text-brand-600">+ New</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($client->projects as $project)
                    <div class="flex items-center justify-between px-6 py-3">
                        <a href="{{ route('agency.projects.show', $project) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500">{{ $project->name }}</a>
                        @php $sc = ['active' => 'success', 'on_hold' => 'warning', 'completed' => 'gray', 'cancelled' => 'error', 'draft' => 'blue-light'][$project->status] ?? 'gray'; @endphp
                        <span class="inline-flex items-center rounded-full bg-{{ $sc }}-50 px-2 py-0.5 text-xs font-medium text-{{ $sc }}-700">{{ ucfirst($project->status) }}</span>
                    </div>
                    @empty
                    <p class="px-6 py-4 text-sm text-gray-400">No projects yet</p>
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
                        <a href="{{ route('agency.tickets.show', $ticket) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500">{{ $ticket->subject }}</a>
                        <span class="text-xs text-gray-400">{{ $ticket->ticket_number }}</span>
                    </div>
                    @empty
                    <p class="px-6 py-4 text-sm text-gray-400">No tickets</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
