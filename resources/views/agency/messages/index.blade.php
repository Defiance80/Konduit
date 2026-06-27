@extends('layouts.app')
@section('title', 'Messages')

@section('content')
<div x-data="{ showNew: false }" class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Messages</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Internal team threads and client communications.</p>
        </div>
        <button @click="showNew=true"
            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Thread
        </button>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-success-50 border border-success-200 px-4 py-3 text-sm text-success-700 dark:bg-success-500/10 dark:border-success-500/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    {{-- Thread list --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 divide-y divide-gray-50 dark:divide-gray-800">
        @forelse($threads as $thread)
        <a href="{{ route('agency.messages.show', $thread) }}"
            class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors group">
            <div class="flex-shrink-0 size-9 rounded-full flex items-center justify-center mt-0.5
                {{ $thread->type === 'internal' ? 'bg-gray-100 dark:bg-gray-800' : 'bg-brand-50 dark:bg-brand-500/10' }}">
                @if($thread->type === 'internal')
                <svg class="size-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                @else
                <svg class="size-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-brand-600 dark:group-hover:text-brand-400 truncate">{{ $thread->subject }}</p>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs
                            {{ $thread->type === 'internal' ? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' : 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400' }}">
                            {{ $thread->type === 'internal' ? 'Internal' : 'Client' }}
                        </span>
                        @if($thread->last_message_at)
                        <span class="text-xs text-gray-400">{{ $thread->last_message_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
                @if($thread->client)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $thread->client->name }}</p>
                @endif
                @php $latest = $thread->messages->first(); @endphp
                @if($latest)
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 truncate">{{ $latest->user->name }}: {{ Str::limit($latest->body, 80) }}</p>
                @endif
            </div>
        </a>
        @empty
        <div class="px-5 py-12 text-center">
            <div class="mx-auto size-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                <svg class="size-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">No messages yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Start a thread with your team or a client.</p>
            <button @click="showNew=true" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                Start First Thread
            </button>
        </div>
        @endforelse
    </div>

</div>

{{-- New Thread Modal --}}
<div x-show="showNew" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
    <div @click.outside="showNew=false" class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">New Thread</h3>
            <button @click="showNew=false" class="text-gray-400 hover:text-gray-600">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('agency.messages.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Subject *</label>
                <input type="text" name="subject" required placeholder="What's this thread about?"
                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div x-data="{ type: 'internal' }">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="type" value="internal" x-model="type" class="accent-brand-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Internal (team only)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="type" value="client" x-model="type" class="accent-brand-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Client thread</span>
                    </label>
                </div>
                <div x-show="type==='client'" class="mt-3">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Client</label>
                    <select name="client_id" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="">Select Client</option>
                        @foreach($clients as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">First Message *</label>
                <textarea name="body" required rows="4" placeholder="Type your message…"
                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showNew=false"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                    Start Thread
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
