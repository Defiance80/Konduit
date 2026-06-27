@extends('layouts.app')
@section('title', 'New SOP')

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('agency.sops.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">New SOP</h1>
    </div>

    <form action="{{ route('agency.sops.store') }}" method="POST" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6 space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Title</label>
            <input type="text" name="title" required value="{{ old('title') }}"
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Category</label>
                <select name="sop_category_id" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">Uncategorized</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('sop_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Version</label>
                <input type="text" name="version" value="{{ old('version', '1.0') }}" placeholder="1.0"
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
            <input type="text" name="description" value="{{ old('description') }}" placeholder="One-line description"
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Content</label>
            <textarea name="content" rows="16" required
                placeholder="Write the SOP content here. Markdown is supported."
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-mono dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-brand-500">{{ old('content') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
            <select name="status" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>
        </div>
        <div class="flex gap-3 pt-2">
            <a href="{{ route('agency.sops.index') }}" class="rounded-lg border border-gray-200 px-6 py-2.5 text-sm text-gray-600 dark:border-gray-700 dark:text-gray-400">Cancel</a>
            <button type="submit" class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Create SOP</button>
        </div>
    </form>
</div>
@endsection
