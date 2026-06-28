@extends('layouts.app')
@section('title', 'New Website Audit')

@section('content')
<div class="max-w-2xl space-y-6" x-data="{ auditType: '{{ old('type', 'website') }}' }">

    <div class="flex items-center gap-3">
        <a href="{{ route('agency.audits.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">New Audit</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Scan a website or create a manual audit.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="rounded-xl border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:bg-error-500/10 dark:border-error-500/20 dark:text-error-400">
        @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
    </div>
    @endif

    <form action="{{ route('agency.audits.store') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Audit Type Selector --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Audit Type</p>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach([
                    ['website', 'Website Scan', 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9'],
                    ['seo', 'SEO Audit', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ['content', 'Content', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['social', 'Social', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0'],
                ] as [$val, $lbl, $icon])
                <label @click="auditType = '{{ $val }}'"
                    class="relative flex cursor-pointer flex-col items-center gap-2 rounded-xl border-2 p-4 text-center transition-all"
                    :class="auditType === '{{ $val }}' ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10' : 'border-gray-200 dark:border-gray-700 hover:border-brand-300'">
                    <input type="radio" name="type" value="{{ $val }}" class="sr-only" :checked="auditType === '{{ $val }}'">
                    <svg class="size-6" :class="auditType === '{{ $val }}' ? 'text-brand-500' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"/>
                    </svg>
                    <span class="text-xs font-medium" :class="auditType === '{{ $val }}' ? 'text-brand-700 dark:text-brand-400' : 'text-gray-600 dark:text-gray-400'">{{ $lbl }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Details --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 space-y-5">
            <p class="text-sm font-semibold text-gray-900 dark:text-white">Audit Details</p>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Audit Title <span class="text-error-500">*</span></label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g. Q2 Website Audit — Acme Corp"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Client <span class="text-error-500">*</span></label>
                    <select name="client_id" required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Select Client</option>
                        @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Project (optional)</label>
                    <select name="project_id"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">No Project</option>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Website URL shown for scannable types --}}
                <div class="sm:col-span-2" x-show="['website','seo','technical','performance'].includes(auditType)">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Website URL
                        <span class="ml-1 inline-flex items-center gap-1 rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                            AI Scan
                        </span>
                    </label>
                    <input type="url" name="website_url" value="{{ old('website_url') }}" placeholder="https://yoursite.com"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Enter a URL and the AI will automatically scan and score the site across SEO, content, AEO, schema, and conversion categories.</p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Audit Date</label>
                    <input type="date" name="audited_at" value="{{ old('audited_at', now()->format('Y-m-d')) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('agency.audits.index') }}"
                class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span x-text="['website','seo','technical','performance'].includes(auditType) ? 'Create & Scan' : 'Create Audit'"></span>
            </button>
        </div>
    </form>
</div>
@endsection
