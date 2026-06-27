@extends('layouts.app')
@section('title', 'New Audit')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('agency.audits.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">New Audit</h1>
    </div>

    <form action="{{ route('agency.audits.store') }}" method="POST"
        class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 space-y-4">
        @csrf

        @if($errors->any())
        <div class="rounded-lg bg-error-50 border border-error-200 px-4 py-3 text-sm text-error-700 dark:bg-error-500/10 dark:border-error-500/20 dark:text-error-400">
            @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
        </div>
        @endif

        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Audit Title *</label>
                <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g. Q2 SEO Audit – Acme Corp"
                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Client *</label>
                <select name="client_id" required class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">Select Client</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->id }}" @selected(old('client_id')==$c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Audit Type *</label>
                <select name="type" required class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="seo">SEO Audit</option>
                    <option value="website">Website Audit</option>
                    <option value="social">Social Media Audit</option>
                    <option value="content">Content Audit</option>
                    <option value="technical">Technical Audit</option>
                    <option value="performance">Performance Audit</option>
                    <option value="general">General Audit</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Project (optional)</label>
                <select name="project_id" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">No Project</option>
                    @foreach($projects as $p)
                    <option value="{{ $p->id }}" @selected(old('project_id')==$p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Score (0–100)</label>
                <input type="number" name="score" min="0" max="100" value="{{ old('score') }}" placeholder="Leave blank if not scored yet"
                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Audit Date</label>
                <input type="date" name="audited_at" value="{{ old('audited_at', now()->format('Y-m-d')) }}"
                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Executive Summary</label>
                <textarea name="executive_summary" rows="4" placeholder="Brief overview of what was audited and the key headline finding…"
                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old('executive_summary') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('agency.audits.index') }}"
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                Cancel
            </a>
            <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2 text-sm font-medium text-white hover:bg-brand-600">
                Create Audit
            </button>
        </div>
    </form>

</div>
@endsection
