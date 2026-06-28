@extends('layouts.app')
@section('title', 'Marketing Intelligence Feed')

@section('content')
<div class="space-y-6" x-data="newsFeed()">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Marketing Intelligence</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Curated daily brief — generated {{ $brief['generated_at'] ?? now()->format('F j, Y') }}
            </p>
        </div>
        <button @click="refreshFeed()"
            :disabled="loading"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-50">
            <svg class="size-4" :class="loading ? 'animate-spin' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span x-text="loading ? 'Refreshing...' : 'Refresh Brief'"></span>
        </button>
    </div>

    {{-- Type filters --}}
    <div class="flex flex-wrap gap-2">
        @php
            $types = [
                'all'      => ['label' => 'All', 'color' => 'gray'],
                'trend'    => ['label' => 'Trends', 'color' => 'brand'],
                'platform' => ['label' => 'Platform Updates', 'color' => 'blue-light'],
                'industry' => ['label' => 'Industry News', 'color' => 'success'],
                'tip'      => ['label' => 'Agency Tips', 'color' => 'warning'],
            ];
        @endphp
        @foreach($types as $key => $meta)
        <button @click="activeFilter = '{{ $key }}'"
            :class="activeFilter === '{{ $key }}'
                ? 'bg-brand-500 text-white border-brand-500'
                : 'bg-white dark:bg-gray-900 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-brand-300'"
            class="rounded-full border px-4 py-1.5 text-sm font-medium transition-colors">
            {{ $meta['label'] }}
        </button>
        @endforeach
    </div>

    {{-- News Grid --}}
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2" x-ref="grid">
        @php
            $typeConfig = [
                'trend'    => ['label' => 'Trend',           'color' => 'brand',      'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
                'platform' => ['label' => 'Platform Update', 'color' => 'blue-light', 'icon' => 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z'],
                'industry' => ['label' => 'Industry News',   'color' => 'success',    'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
                'tip'      => ['label' => 'Agency Tip',      'color' => 'warning',    'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
            ];
        @endphp

        @forelse($brief['items'] ?? [] as $item)
        @php
            $cfg = $typeConfig[$item['type']] ?? $typeConfig['industry'];
            $color = $cfg['color'];
        @endphp
        <div x-show="activeFilter === 'all' || activeFilter === '{{ $item['type'] }}'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6 hover:shadow-md dark:hover:shadow-gray-900 transition-shadow">
            <div class="flex items-start gap-4">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-{{ $color }}-50 dark:bg-{{ $color }}-500/10">
                    <svg class="size-5 text-{{ $color }}-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $cfg['icon'] }}"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center rounded-full bg-{{ $color }}-50 dark:bg-{{ $color }}-500/10 px-2.5 py-0.5 text-xs font-medium text-{{ $color }}-700 dark:text-{{ $color }}-400">
                            {{ $cfg['label'] }}
                        </span>
                        <span class="text-xs text-gray-400 dark:text-gray-600">{{ $item['category'] ?? '' }}</span>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white leading-snug mb-2">
                        {{ $item['headline'] }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed mb-3">
                        {{ $item['summary'] }}
                    </p>
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-800/60 px-3 py-2.5 border-l-2 border-{{ $color }}-400">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Action</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $item['relevance'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="lg:col-span-2 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 py-16 text-center">
            <svg class="mx-auto size-10 text-gray-300 dark:text-gray-700 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 text-sm">No items yet.</p>
            <button @click="refreshFeed()" class="mt-3 text-sm text-brand-500 hover:text-brand-600 font-medium">Generate your first brief</button>
        </div>
        @endforelse
    </div>

    {{-- About this feature --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6">
        <div class="flex items-start gap-4">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/10">
                <svg class="size-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">About Marketing Intelligence</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                    This brief is AI-curated daily based on your clients' industries and the current marketing landscape.
                    It refreshes automatically every 24 hours. Use the Refresh button to generate an updated brief at any time.
                    Items are selected to be actionable for your agency and relevant to your client portfolio.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function newsFeed() {
    return {
        activeFilter: 'all',
        loading: false,
        async refreshFeed() {
            this.loading = true;
            try {
                const resp = await fetch('{{ route('agency.news.refresh') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    }
                });
                if (resp.ok) {
                    window.location.reload();
                }
            } catch(e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
