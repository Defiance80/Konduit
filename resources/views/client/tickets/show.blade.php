@extends('layouts.app')
@section('title', $ticket->subject)

@section('content')
<div class="max-w-2xl space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('client.tickets.index') }}" class="hover:text-gray-600 dark:hover:text-gray-300">Support</a>
        <span>/</span>
        <span class="text-gray-600 dark:text-gray-300">{{ $ticket->ticket_number }}</span>
    </div>

    @if(session('success'))
    <div class="rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    {{-- Header --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="p-6">
            @php
                $statusColors = ['open'=>'blue','in_progress'=>'warning','waiting'=>'orange','resolved'=>'success','closed'=>'gray'];
                $sc = $statusColors[$ticket->status] ?? 'gray';
                $statusLabels = ['open'=>'Open','in_progress'=>'In Progress','waiting'=>'Waiting on You','resolved'=>'Resolved','closed'=>'Closed'];
            @endphp
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xs text-gray-400">{{ $ticket->ticket_number }}</span>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-{{ $sc }}-50 text-{{ $sc }}-700 dark:bg-{{ $sc }}-500/10 dark:text-{{ $sc }}-400">
                    {{ $statusLabels[$ticket->status] ?? ucfirst($ticket->status) }}
                </span>
            </div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">{{ $ticket->subject }}</h1>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $ticket->description }}</p>
            </div>
        </div>
        <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-800 flex flex-wrap gap-4 text-xs text-gray-400">
            <span>Submitted {{ $ticket->created_at->format('M j, Y') }}</span>
            @if($ticket->project)<span>Project: {{ $ticket->project->name }}</span>@endif
            @if($ticket->assignee)<span>Assigned to: {{ $ticket->assignee->name }}</span>@endif
        </div>
    </div>

    {{-- Conversation --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Conversation</h2>
        </div>

        <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
            @forelse($publicComments as $comment)
            @php $isMe = $comment->user_id === auth()->id(); @endphp
            <div class="px-6 py-4 {{ $isMe ? 'bg-brand-50/30 dark:bg-brand-500/5' : '' }}">
                <div class="flex items-center gap-2 mb-2">
                    <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}" class="size-6 rounded-full">
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $isMe ? 'You' : $comment->user->name }}</span>
                    <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $comment->body }}</p>
            </div>
            @empty
            <p class="px-6 py-8 text-center text-sm text-gray-400">No replies yet. We'll respond here shortly.</p>
            @endforelse
        </div>

        {{-- Reply form -- only when not closed/resolved --}}
        @if(!in_array($ticket->status, ['resolved', 'closed']))
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            <form method="POST" action="{{ route('client.tickets.comment', $ticket) }}">
                @csrf
                <textarea name="body" rows="3" required
                    placeholder="Add a reply or provide additional information…"
                    class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white resize-none mb-3"></textarea>
                <button type="submit"
                    class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                    Send Reply
                </button>
            </form>
        </div>
        @else
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            <p class="text-sm text-gray-400 text-center">This ticket is {{ $ticket->status }}. <a href="{{ route('client.tickets.create') }}" class="text-brand-500 hover:text-brand-600 font-medium">Open a new request</a> if you need further help.</p>
        </div>
        @endif
    </div>
</div>
@endsection
