@extends('layouts.app')
@section('title', 'Clients')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Clients</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $clients->total() }} total clients</p>
        </div>
        <a href="{{ route('agency.clients.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Client
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse($clients as $client)
        <div class="rounded-2xl border border-gray-200 bg-white p-5 hover:shadow-theme-md transition-shadow dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-3 mb-4">
                <img src="{{ $client->logo_url }}" class="size-12 rounded-xl object-cover" alt="{{ $client->name }}">
                <div class="min-w-0">
                    <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $client->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $client->industry ?: 'No industry' }}</p>
                </div>
            </div>
            <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                <span>{{ $client->projects_count }} projects</span>
                <span>{{ $client->tickets_count }} tickets</span>
                @php $c = ['active' => 'success', 'inactive' => 'gray', 'prospect' => 'blue-light'][$client->status] ?? 'gray'; @endphp
                <span class="inline-flex items-center rounded-full bg-{{ $c }}-50 px-2 py-0.5 text-xs font-medium text-{{ $c }}-700 dark:bg-{{ $c }}-500/10 dark:text-{{ $c }}-400">
                    {{ ucfirst($client->status) }}
                </span>
            </div>
            <a href="{{ route('agency.clients.show', $client) }}"
               class="flex w-full items-center justify-center rounded-lg border border-gray-200 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                View Client
            </a>
        </div>
        @empty
        <div class="col-span-full rounded-2xl border border-dashed border-gray-200 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-900">
            <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800">
                <svg class="size-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">No clients yet</h3>
            <p class="text-sm text-gray-500 mb-4">Add your first client to get started</p>
            <a href="{{ route('agency.clients.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                Add Client
            </a>
        </div>
        @endforelse
    </div>

    {{ $clients->links() }}
</div>
@endsection
