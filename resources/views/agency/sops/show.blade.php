@extends('layouts.app')
@section('title', $sop->title)

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('agency.sops.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $sop->title }}</h1>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                    bg-{{ $sop->status_color }}-50 text-{{ $sop->status_color }}-700
                    dark:bg-{{ $sop->status_color }}-500/10 dark:text-{{ $sop->status_color }}-400">
                    {{ ucfirst($sop->status) }}
                </span>
            </div>
            <p class="text-sm text-gray-400 mt-0.5">
                @if($sop->category) {{ $sop->category->name }} · @endif
                v{{ $sop->version }} · Written by {{ $sop->author->name }}
            </p>
        </div>
        <a href="{{ route('agency.sops.edit', $sop) }}"
            class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400">Edit</a>
    </div>

    @if($sop->description)
    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $sop->description }}</p>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-8">
        <div class="prose prose-sm dark:prose-invert max-w-none">
            {!! nl2br(e($sop->content)) !!}
        </div>
    </div>

    <form action="{{ route('agency.sops.destroy', $sop) }}" method="POST" class="text-right"
        onsubmit="return confirm('Delete this SOP?')">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm text-error-500 hover:text-error-700">Delete SOP</button>
    </form>
</div>
@endsection
