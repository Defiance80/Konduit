@extends('layouts.app')
@section('title', 'Knowledge Base')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Knowledge Base</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Internal and client-facing articles</p>
        </div>
        <a href="{{ route('agency.knowledge-base.create') }}" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">+ New Article</a>
    </div>

    {{-- Search + filter --}}
    <form method="GET" class="flex gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search articles..."
            class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
        @if($categories->count())
        <select name="category" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            <option value="">All categories</option>
            @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
        @endif
        <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">Search</button>
    </form>

    {{-- Articles grid --}}
    @if($articles->count())
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($articles as $article)
        <a href="{{ route('agency.knowledge-base.show', $article) }}"
            class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 hover:border-brand-200 dark:hover:border-brand-800 transition-colors group">
            <div class="flex items-start justify-between mb-3">
                @if($article->category)
                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800 px-2 py-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $article->category }}</span>
                @endif
                @if($article->is_public)
                <span class="inline-flex items-center rounded-full bg-success-50 dark:bg-success-500/10 px-2 py-0.5 text-xs text-success-700 dark:text-success-400">Public</span>
                @else
                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800 px-2 py-0.5 text-xs text-gray-500 dark:text-gray-400">Internal</span>
                @endif
            </div>
            <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-brand-500 mb-2">{{ $article->title }}</h3>
            @if($article->excerpt)
            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $article->excerpt }}</p>
            @endif
            <p class="text-xs text-gray-400 mt-3">{{ $article->author->name }} · {{ $article->updated_at->format('M j, Y') }}</p>
        </a>
        @endforeach
    </div>
    @else
    <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 py-16 text-center">
        <p class="text-gray-400 text-sm">No articles yet.</p>
        <a href="{{ route('agency.knowledge-base.create') }}" class="mt-3 inline-block rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Write your first article</a>
    </div>
    @endif
</div>
@endsection
