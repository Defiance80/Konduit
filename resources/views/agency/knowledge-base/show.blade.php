@extends('layouts.app')
@section('title', $article->title)

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('agency.knowledge-base.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white truncate">{{ $article->title }}</h1>
                @if($article->is_public)
                <span class="flex-shrink-0 inline-flex items-center rounded-full bg-success-50 px-2 py-0.5 text-xs text-success-700 dark:bg-success-500/10 dark:text-success-400">Public</span>
                @endif
            </div>
            <p class="text-sm text-gray-400 mt-0.5">
                @if($article->category) {{ $article->category }} · @endif
                {{ $article->author->name }} · {{ $article->updated_at->format('M j, Y') }}
            </p>
        </div>
        <a href="{{ route('agency.knowledge-base.edit', $article) }}"
            class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400">Edit</a>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-8">
        <div class="prose prose-sm dark:prose-invert max-w-none">
            {!! nl2br(e($article->content)) !!}
        </div>
    </div>

    <form action="{{ route('agency.knowledge-base.destroy', $article) }}" method="POST" class="text-right"
        onsubmit="return confirm('Delete this article?')">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm text-error-500 hover:text-error-700">Delete Article</button>
    </form>
</div>
@endsection
