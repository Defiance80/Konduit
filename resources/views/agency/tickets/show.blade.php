@extends('layouts.app')
@section('title', $ticket->subject)

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('agency.tickets.index') }}" class="text-gray-400 hover:text-gray-600"><svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-0.5">
                <span class="text-xs text-gray-400 font-mono">{{ $ticket->ticket_number }}</span>
            </div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $ticket->subject }}</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="font-semibold text-gray-900 dark:text-white mb-3">Description</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $ticket->description }}</p>
            </div>

            <!-- Comments -->
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Comments ({{ $ticket->comments->count() }})</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($ticket->comments as $comment)
                    <div class="px-6 py-4 {{ $comment->is_internal ? 'bg-warning-50/50 dark:bg-warning-900/10' : '' }}">
                        <div class="flex items-center gap-2 mb-2">
                            <img src="{{ $comment->user->avatar_url }}" class="size-7 rounded-full" alt="{{ $comment->user->name }}">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $comment->user->name }}</span>
                            @if($comment->is_internal) <span class="text-xs bg-warning-100 text-warning-700 rounded-full px-2 py-0.5">Internal</span> @endif
                            <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $comment->body }}</p>
                    </div>
                    @empty
                    <div class="px-6 py-6 text-sm text-gray-400 text-center">No comments yet</div>
                    @endforelse
                </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                    <form method="POST" action="{{ route('agency.tickets.comment', $ticket) }}" class="space-y-3">
                        @csrf
                        <textarea name="body" rows="3" placeholder="Add a comment..." required
                                  class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 cursor-pointer">
                                <input type="checkbox" name="is_internal" value="1" class="rounded border-gray-300 text-warning-500">
                                Internal note (hidden from client)
                            </label>
                            <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4">
            <!-- Status Update -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Update Ticket</h3>
                <form method="POST" action="{{ route('agency.tickets.update', $ticket) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">Status</label>
                        <select name="status" class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            @foreach(['open','in_progress','waiting','resolved','closed'] as $s)<option value="{{ $s }}" {{ $ticket->status === $s ? 'selected' : '' }}>{{ str_replace('_',' ',ucfirst($s)) }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">Priority</label>
                        <select name="priority" class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            @foreach(['low','medium','high','urgent'] as $p)<option value="{{ $p }}" {{ $ticket->priority === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">Assignee</label>
                        <select name="assignee_id" class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            <option value="">Unassigned</option>
                            @foreach($agents as $agent)<option value="{{ $agent->id }}" {{ $ticket->assignee_id == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>@endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-brand-500 py-2 text-sm font-medium text-white hover:bg-brand-600">Save</button>
                </form>
            </div>

            <!-- Info -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-400">Client</span><span class="text-gray-700 dark:text-gray-300">{{ $ticket->client->name }}</span></div>
                @if($ticket->project) <div class="flex justify-between"><span class="text-gray-400">Project</span><span class="text-gray-700 dark:text-gray-300">{{ $ticket->project->name }}</span></div> @endif
                <div class="flex justify-between"><span class="text-gray-400">Type</span><span class="text-gray-700 dark:text-gray-300">{{ str_replace('_',' ',ucfirst($ticket->type)) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Created</span><span class="text-gray-700 dark:text-gray-300">{{ $ticket->created_at->format('M j, Y') }}</span></div>
                @if($ticket->resolved_at) <div class="flex justify-between"><span class="text-gray-400">Resolved</span><span class="text-gray-700 dark:text-gray-300">{{ $ticket->resolved_at->format('M j, Y') }}</span></div> @endif
            </div>
        </div>
    </div>
</div>
@endsection
