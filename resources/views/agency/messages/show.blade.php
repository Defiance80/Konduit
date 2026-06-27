@extends('layouts.app')
@section('title', $thread->subject)

@section('content')
<div class="space-y-5 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('agency.messages.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $thread->subject }}</h1>
            <div class="flex items-center gap-2 mt-0.5">
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs
                    {{ $thread->type === 'internal' ? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' : 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400' }}">
                    {{ $thread->type === 'internal' ? 'Internal' : 'Client Thread' }}
                </span>
                @if($thread->client)
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $thread->client->name }}</span>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-success-50 border border-success-200 px-4 py-3 text-sm text-success-700 dark:bg-success-500/10 dark:border-success-500/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    {{-- Messages --}}
    <div class="space-y-3">
        @foreach($messages as $message)
        @php $isMe = $message->user_id === auth()->id(); @endphp
        <div class="flex gap-3 {{ $isMe ? 'flex-row-reverse' : '' }}">
            <img src="{{ $message->user->avatar_url }}" alt="{{ $message->user->name }}"
                class="size-8 rounded-full object-cover flex-shrink-0 mt-0.5">
            <div class="max-w-[75%]">
                <div class="flex items-center gap-2 mb-1 {{ $isMe ? 'flex-row-reverse' : '' }}">
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $message->user->name }}</span>
                    <span class="text-xs text-gray-400">{{ $message->created_at->diffForHumans() }}</span>
                    @if($message->is_internal && $thread->type !== 'internal')
                    <span class="text-xs bg-gray-100 text-gray-500 rounded px-1.5 py-0.5 dark:bg-gray-800 dark:text-gray-400">Internal</span>
                    @endif
                </div>
                <div class="rounded-2xl px-4 py-3 text-sm
                    {{ $isMe
                        ? 'bg-brand-500 text-white rounded-tr-sm'
                        : 'bg-white border border-gray-200 text-gray-700 dark:bg-gray-900 dark:border-gray-800 dark:text-gray-300 rounded-tl-sm' }}">
                    {{ $message->body }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Reply box --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-4">
        <form action="{{ route('agency.messages.reply', $thread) }}" method="POST" class="space-y-3">
            @csrf
            <textarea name="body" required rows="3" placeholder="Type a reply…"
                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500 resize-none"></textarea>
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Send Reply
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
