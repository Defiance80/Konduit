@extends('layouts.app')
@section('title', 'SOP Library')

@section('content')
<div x-data="{ showCatModal: false }" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">SOP Library</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Standard operating procedures for your agency</p>
        </div>
        <div class="flex gap-2">
            <button @click="showCatModal=true" class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400">+ Category</button>
            <a href="{{ route('agency.sops.create') }}" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">+ New SOP</a>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    {{-- Categories --}}
    @forelse($categories as $category)
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <span class="size-3 rounded-full" style="background-color: {{ $category->color }}"></span>
            <h2 class="font-semibold text-gray-900 dark:text-white">{{ $category->name }}</h2>
            <span class="text-xs text-gray-400">{{ $category->sops->count() }} SOPs</span>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-800">
            @forelse($category->sops as $sop)
            <div class="flex items-center justify-between px-6 py-3">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="{{ route('agency.sops.show', $sop) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500 truncate">{{ $sop->title }}</a>
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                        bg-{{ $sop->status_color }}-50 text-{{ $sop->status_color }}-700
                        dark:bg-{{ $sop->status_color }}-500/10 dark:text-{{ $sop->status_color }}-400">
                        {{ ucfirst($sop->status) }}
                    </span>
                </div>
                <span class="text-xs text-gray-400 ml-4 flex-shrink-0">v{{ $sop->version }}</span>
            </div>
            @empty
            <p class="px-6 py-3 text-sm text-gray-400">No SOPs in this category yet.</p>
            @endforelse
        </div>
    </div>
    @empty
    @endforelse

    {{-- Uncategorized --}}
    @if($uncategorized->count() > 0)
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-white">Uncategorized</h2>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-800">
            @foreach($uncategorized as $sop)
            <div class="flex items-center justify-between px-6 py-3">
                <a href="{{ route('agency.sops.show', $sop) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500">{{ $sop->title }}</a>
                <span class="text-xs text-gray-400">v{{ $sop->version }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($categories->isEmpty() && $uncategorized->isEmpty())
    <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 py-16 text-center">
        <p class="text-gray-400 text-sm">No SOPs yet.</p>
        <a href="{{ route('agency.sops.create') }}" class="mt-3 inline-block rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Create your first SOP</a>
    </div>
    @endif

    {{-- Add Category Modal --}}
    <div x-show="showCatModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-xl" @click.stop>
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">New Category</h3>
            <form action="{{ route('agency.sops.storeCategory') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="name" placeholder="Category name" required
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <input type="color" name="color" value="#6366f1" class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700">
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="showCatModal=false" class="flex-1 rounded-lg border border-gray-200 py-2 text-sm text-gray-600 dark:border-gray-700 dark:text-gray-400">Cancel</button>
                    <button type="submit" class="flex-1 rounded-lg bg-brand-500 py-2 text-sm font-medium text-white hover:bg-brand-600">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
