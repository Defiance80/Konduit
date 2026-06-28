@extends('layouts.app')
@section('title', $audit->title)

@php
$categories = $audit->category_scores ?? [];
$cats = [
    ['key' => 'technical_seo',    'default_label' => 'Technical SEO',           'color' => '#3B82F6'],
    ['key' => 'content',          'default_label' => 'Content Quality',          'color' => '#8B5CF6'],
    ['key' => 'aeo',              'default_label' => 'Answer Engine Opt.',        'color' => '#06B6D4'],
    ['key' => 'schema',           'default_label' => 'Schema & Structured Data', 'color' => '#F59E0B'],
    ['key' => 'performance',      'default_label' => 'Page Experience',           'color' => '#10B981'],
    ['key' => 'social_conversion','default_label' => 'Social & Conversion',       'color' => '#EF4444'],
];
$n = count($cats);
$cx = 150; $cy = 150; $maxR = 100;
$dataPoints = collect($cats)->map(function ($cat, $i) use ($categories, $cx, $cy, $maxR, $n) {
    $score = ($categories[$cat['key']]['score'] ?? 0) / 100;
    $angle = deg2rad(-90 + ($i * 360 / $n));
    return round($cx + $maxR * $score * cos($angle), 2) . ',' . round($cy + $maxR * $score * sin($angle), 2);
})->join(' ');
$gridPolygons = collect([0.2, 0.4, 0.6, 0.8, 1.0])->map(function ($pct) use ($cx, $cy, $maxR, $n) {
    return collect(range(0, $n - 1))->map(function ($i) use ($cx, $cy, $maxR, $pct, $n) {
        $angle = deg2rad(-90 + ($i * 360 / $n));
        return round($cx + $maxR * $pct * cos($angle), 2) . ',' . round($cy + $maxR * $pct * sin($angle), 2);
    })->join(' ');
});
$axisData = collect($cats)->map(function ($cat, $i) use ($cx, $cy, $maxR, $n, $categories) {
    $angle = deg2rad(-90 + ($i * 360 / $n));
    $lr = $maxR + 32;
    return [
        'label' => $categories[$cat['key']]['label'] ?? $cat['default_label'],
        'score' => $categories[$cat['key']]['score'] ?? null,
        'color' => $cat['color'],
        'x2'    => round($cx + $maxR * cos($angle), 2),
        'y2'    => round($cy + $maxR * sin($angle), 2),
        'lx'    => round($cx + $lr * cos($angle), 2),
        'ly'    => round($cy + $lr * sin($angle), 2),
    ];
});
$score = $audit->score ?? 0;
$scoreColor = $score >= 80 ? '#10B981' : ($score >= 60 ? '#F59E0B' : '#EF4444');
$scoreDash = round(2 * 3.14159 * 34);
$scoreOffset = round($scoreDash * (1 - $score / 100));
@endphp

@section('content')
<div x-data="{ showEdit: false, openFinding: null }" class="space-y-6">

    {{-- Top header bar --}}
    <div class="h-1.5 rounded-full w-full" style="background: linear-gradient(90deg, #3B82F6, #8B5CF6, #06B6D4, #10B981)"></div>

    {{-- Header row --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div class="flex items-start gap-3">
            <a href="{{ route('agency.audits.index') }}" class="mt-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 flex-shrink-0">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $audit->title }}</h1>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        @if($audit->status==='complete') bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400
                        @elseif($audit->status==='shared') bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400
                        @elseif($audit->status==='in_progress') bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400
                        @else bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 @endif">
                        {{ ucfirst(str_replace('_', ' ', $audit->status)) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $audit->client->name }} · {{ $audit->type_label }}
                    @if($audit->audited_at) · {{ $audit->audited_at->format('M j, Y') }} @endif
                    @if($audit->website_url)
                        · <a href="{{ $audit->website_url }}" target="_blank" class="text-brand-600 dark:text-brand-400 hover:underline">{{ parse_url($audit->website_url, PHP_URL_HOST) }}</a>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($audit->website_url)
            <a href="{{ route('agency.audits.scan', $audit) }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Re-scan
            </a>
            @endif
            @if(!$audit->visible_to_client && $audit->status === 'complete')
            <form action="{{ route('agency.audits.share', $audit) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3 py-2 text-sm font-medium text-white hover:bg-brand-600">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                    Share with Client
                </button>
            </form>
            @elseif($audit->visible_to_client)
            <span class="inline-flex items-center gap-1 rounded-full bg-success-50 px-3 py-1.5 text-xs font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
                <svg class="size-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Client can see this
            </span>
            @endif
            <button @click="showEdit=!showEdit"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </button>
            <form action="{{ route('agency.audits.destroy', $audit) }}" method="POST" onsubmit="return confirm('Delete this audit?')">
                @csrf @method('DELETE')
                <button type="submit" class="rounded-lg border border-error-200 px-3 py-2 text-sm text-error-600 hover:bg-error-50 dark:border-error-800 dark:text-error-400">Delete</button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:bg-success-500/10 dark:border-success-500/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    @if($audit->status === 'in_progress')
    <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-4 dark:bg-blue-500/10 dark:border-blue-500/20">
        <div class="flex items-center gap-3">
            <div class="size-5 rounded-full border-2 border-blue-500 border-t-transparent animate-spin flex-shrink-0"></div>
            <div>
                <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Scanning in progress...</p>
                <p class="text-xs text-blue-600 dark:text-blue-400">Refreshing this page will show the completed report.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- MAIN REPORT: shown when scan is complete --}}
    @if(!empty($categories))

    {{-- Overall Score + Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Score ring --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6 flex flex-col items-center justify-center gap-4">
            <div class="relative">
                <svg class="size-36 -rotate-90" viewBox="0 0 80 80">
                    <circle cx="40" cy="40" r="34" fill="none" stroke="#E5E7EB" stroke-width="7"/>
                    <circle cx="40" cy="40" r="34" fill="none"
                        stroke="{{ $scoreColor }}" stroke-width="7"
                        stroke-dasharray="{{ $scoreDash }}"
                        stroke-dashoffset="{{ $scoreOffset }}"
                        stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $score }}</span>
                    <span class="text-xs text-gray-400">/ 100</span>
                </div>
            </div>
            <div class="text-center">
                <p class="text-base font-semibold" style="color: {{ $scoreColor }}">
                    {{ $score >= 80 ? 'Great' : ($score >= 60 ? 'Needs Work' : 'Critical Issues') }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Overall Digital Health Score</p>
            </div>

            {{-- Mini score bars --}}
            <div class="w-full space-y-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                @foreach($cats as $cat)
                @php $cs = $categories[$cat['key']]['score'] ?? 0; @endphp
                <div class="flex items-center gap-2">
                    <span class="w-24 text-[10px] text-gray-500 dark:text-gray-400 truncate">{{ $categories[$cat['key']]['label'] ?? $cat['default_label'] }}</span>
                    <div class="flex-1 h-1.5 rounded-full bg-gray-100 dark:bg-gray-800">
                        <div class="h-1.5 rounded-full transition-all" style="width:{{ $cs }}%; background-color:{{ $cat['color'] }}"></div>
                    </div>
                    <span class="w-6 text-right text-[10px] font-medium text-gray-600 dark:text-gray-400">{{ $cs }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Executive Summary + Radar chart --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Summary --}}
            @if($audit->executive_summary)
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="size-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">AI Summary</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{{ $audit->executive_summary }}</p>
            </div>
            @endif

            {{-- Radar chart --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Performance Radar</h3>
                <div class="flex justify-center">
                    <svg viewBox="0 0 300 300" class="w-full max-w-xs">
                        {{-- Grid polygons --}}
                        @foreach($gridPolygons as $i => $pts)
                        <polygon points="{{ $pts }}" fill="none" stroke="#E5E7EB" stroke-width="0.8"
                            class="dark:stroke-gray-700" opacity="{{ 0.4 + $i * 0.15 }}"/>
                        @endforeach

                        {{-- Axis lines --}}
                        @foreach($axisData as $axis)
                        <line x1="{{ $cx }}" y1="{{ $cy }}" x2="{{ $axis['x2'] }}" y2="{{ $axis['y2'] }}" stroke="#E5E7EB" stroke-width="0.8" class="dark:stroke-gray-700"/>
                        @endforeach

                        {{-- Data polygon --}}
                        <polygon points="{{ $dataPoints }}" fill="#465FFF" fill-opacity="0.15" stroke="#465FFF" stroke-width="2"/>

                        {{-- Data points --}}
                        @foreach($cats as $i => $cat)
                        @php
                            $s = ($categories[$cat['key']]['score'] ?? 0) / 100;
                            $ang = deg2rad(-90 + ($i * 360 / $n));
                            $px = round($cx + $maxR * $s * cos($ang), 2);
                            $py = round($cy + $maxR * $s * sin($ang), 2);
                        @endphp
                        <circle cx="{{ $px }}" cy="{{ $py }}" r="4" fill="{{ $cat['color'] }}" stroke="white" stroke-width="1.5"/>
                        @endforeach

                        {{-- Labels --}}
                        @foreach($axisData as $axis)
                        @php
                            $ax = $axis['lx'];
                            $ay = $axis['ly'];
                            $anchor = $ax < $cx - 5 ? 'end' : ($ax > $cx + 5 ? 'start' : 'middle');
                        @endphp
                        <text x="{{ $ax }}" y="{{ $ay }}" text-anchor="{{ $anchor }}"
                            font-size="8.5" font-weight="500" fill="#6B7280"
                            class="dark:fill-gray-400">{{ $axis['label'] }}</text>
                        @if($axis['score'] !== null)
                        <text x="{{ $ax }}" y="{{ $ay + 10 }}" text-anchor="{{ $anchor }}"
                            font-size="9" font-weight="700" fill="{{ $axis['color'] }}">{{ $axis['score'] }}</text>
                        @endif
                        @endforeach

                        {{-- Center label --}}
                        <text x="{{ $cx }}" y="{{ $cy + 4 }}" text-anchor="middle" font-size="11" font-weight="700" fill="#1F2937" class="dark:fill-white">{{ $score }}</text>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Category Breakdown --}}
    <div>
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Category Breakdown</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($cats as $cat)
            @php
                $cd = $categories[$cat['key']] ?? [];
                $cs = $cd['score'] ?? null;
                $grade = $cd['grade'] ?? '—';
                $issues = $cd['issues'] ?? [];
                $wins = $cd['wins'] ?? [];
                $recs = $cd['recommendations'] ?? [];
                $gradeColor = $cs !== null ? ($cs >= 80 ? 'text-success-600' : ($cs >= 60 ? 'text-warning-600' : 'text-error-600')) : 'text-gray-400';
            @endphp
            <div x-data="{ open: false }" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                {{-- Card header --}}
                <div class="px-5 pt-5 pb-4">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $cd['label'] ?? $cat['default_label'] }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-bold {{ $gradeColor }} dark:{{ str_replace('600', '400', $gradeColor) }}">{{ $grade }}</span>
                        </div>
                    </div>

                    {{-- Score bar --}}
                    @if($cs !== null)
                    <div class="mb-3">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-gray-500 dark:text-gray-400">Score</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $cs }}/100</span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-2 rounded-full transition-all" style="width:{{ $cs }}%; background-color:{{ $cat['color'] }}"></div>
                        </div>
                    </div>
                    @endif

                    {{-- Quick wins --}}
                    @if(!empty($wins))
                    @foreach(array_slice($wins, 0, 1) as $win)
                    <div class="flex items-start gap-1.5 mb-2">
                        <svg class="size-3.5 text-success-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <p class="text-xs text-success-700 dark:text-success-400">{{ $win }}</p>
                    </div>
                    @endforeach
                    @endif

                    {{-- Top issue --}}
                    @if(!empty($issues))
                    @foreach(array_slice($issues, 0, 1) as $issue)
                    <div class="flex items-start gap-1.5">
                        <svg class="size-3.5 text-error-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $issue }}</p>
                    </div>
                    @endforeach
                    @endif
                </div>

                {{-- Expand toggle --}}
                @if(count($issues) > 1 || count($recs) > 0)
                <button @click="open=!open"
                    class="w-full flex items-center justify-between px-5 py-2.5 text-xs text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 transition-colors">
                    <span x-text="open ? 'Hide details' : 'View all findings'"></span>
                    <svg class="size-3.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-cloak class="px-5 pb-5 space-y-3 border-t border-gray-100 dark:border-gray-800 pt-3">
                    @if(count($issues) > 1)
                    <div>
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">All Issues</p>
                        <ul class="space-y-1.5">
                            @foreach($issues as $issue)
                            <li class="flex items-start gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                <span class="mt-1.5 size-1.5 rounded-full flex-shrink-0" style="background:{{ $cat['color'] }}"></span>
                                {{ $issue }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @if(!empty($recs))
                    <div>
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Recommendations</p>
                        <ul class="space-y-2">
                            @foreach($recs as $rec)
                            <li class="rounded-lg p-2.5" style="background:{{ $cat['color'] }}15; border-left: 3px solid {{ $cat['color'] }};">
                                <p class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $rec['title'] ?? '' }}</p>
                                @if(!empty($rec['detail']))
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $rec['detail'] }}</p>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Top Recommendations --}}
    @if(!empty($audit->recommendations))
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Priority Action Plan</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Top recommendations sorted by impact.</p>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-800">
            @foreach($audit->recommendations as $i => $rec)
            @php
                $pri = $rec['priority'] ?? 'medium';
                $priColors = [
                    'critical' => ['bg'=>'bg-error-100 dark:bg-error-500/20', 'text'=>'text-error-700 dark:text-error-400', 'dot'=>'#EF4444'],
                    'high'     => ['bg'=>'bg-orange-100 dark:bg-orange-500/20', 'text'=>'text-orange-700 dark:text-orange-400', 'dot'=>'#F97316'],
                    'medium'   => ['bg'=>'bg-blue-100 dark:bg-blue-500/20', 'text'=>'text-blue-700 dark:text-blue-400', 'dot'=>'#3B82F6'],
                    'low'      => ['bg'=>'bg-gray-100 dark:bg-gray-800', 'text'=>'text-gray-600 dark:text-gray-400', 'dot'=>'#9CA3AF'],
                ];
                $pc = $priColors[$pri] ?? $priColors['medium'];
            @endphp
            <div class="flex items-start gap-4 px-6 py-4">
                <div class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background:{{ $pc['dot'] }}">{{ $i + 1 }}</div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $rec['title'] }}</p>
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-medium {{ $pc['bg'] }} {{ $pc['text'] }}">{{ ucfirst($pri) }}</span>
                    </div>
                    @if(!empty($rec['detail']))
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $rec['detail'] }}</p>
                    @endif
                    @if(!empty($rec['category']))
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 capitalize">{{ str_replace('_', ' ', $rec['category']) }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Scan data summary --}}
    @if(!empty($audit->scan_data) && !isset($audit->scan_data['error']))
    @php $sd = $audit->scan_data; @endphp
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Technical Data Snapshot</h2>
        </div>
        <div class="p-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach([
                ['label'=>'Title Length', 'value'=> strlen($sd['title'] ?? '') . ' chars', 'good'=> strlen($sd['title'] ?? '') >= 50 && strlen($sd['title'] ?? '') <= 70],
                ['label'=>'Meta Desc', 'value'=> strlen($sd['metaDesc'] ?? '') > 0 ? strlen($sd['metaDesc'] ?? '') . ' chars' : 'Missing', 'good'=> strlen($sd['metaDesc'] ?? '') > 0],
                ['label'=>'H1 Tags', 'value'=> count($sd['h1'] ?? []), 'good'=> count($sd['h1'] ?? []) === 1],
                ['label'=>'Schema Types', 'value'=> count($sd['schemaTypes'] ?? []) > 0 ? implode(', ', $sd['schemaTypes']) : 'None', 'good'=> count($sd['schemaTypes'] ?? []) > 0],
                ['label'=>'Images with Alt', 'value'=> ($sd['imagesWithAlt'] ?? 0) . '/' . ($sd['totalImages'] ?? 0), 'good'=> ($sd['totalImages'] ?? 0) === 0 || (($sd['imagesWithAlt'] ?? 0) / max(1, $sd['totalImages'] ?? 1)) >= 0.8],
                ['label'=>'Sitemap', 'value'=> ($sd['hasSitemap'] ?? false) ? 'Found' : 'Missing', 'good'=> $sd['hasSitemap'] ?? false],
                ['label'=>'Robots.txt', 'value'=> ($sd['hasRobotsTxt'] ?? false) ? 'Found' : 'Missing', 'good'=> $sd['hasRobotsTxt'] ?? false],
                ['label'=>'Mobile Ready', 'value'=> ($sd['hasViewport'] ?? false) ? 'Yes' : 'No', 'good'=> $sd['hasViewport'] ?? false],
                ['label'=>'Social Links', 'value'=> count($sd['socialLinks'] ?? []) > 0 ? implode(', ', $sd['socialLinks']) : 'None', 'good'=> count($sd['socialLinks'] ?? []) >= 2],
                ['label'=>'Email Capture', 'value'=> ($sd['hasEmailForm'] ?? false) ? 'Yes' : 'No', 'good'=> $sd['hasEmailForm'] ?? false],
                ['label'=>'CTAs Found', 'value'=> ($sd['ctaCount'] ?? 0), 'good'=> ($sd['ctaCount'] ?? 0) >= 2],
                ['label'=>'Word Count', 'value'=> number_format($sd['wordCount'] ?? 0), 'good'=> ($sd['wordCount'] ?? 0) >= 300],
            ] as $item)
            <div class="text-center">
                <div class="size-8 rounded-full mx-auto mb-2 flex items-center justify-center {{ $item['good'] ? 'bg-success-100 dark:bg-success-500/20' : 'bg-error-100 dark:bg-error-500/20' }}">
                    @if($item['good'])
                    <svg class="size-4 text-success-600 dark:text-success-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @else
                    <svg class="size-4 text-error-600 dark:text-error-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    @endif
                </div>
                <p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $item['label'] }}</p>
                <p class="text-xs font-semibold text-gray-900 dark:text-white mt-0.5 truncate">{{ $item['value'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @elseif($audit->status !== 'in_progress')

    {{-- No scan yet --}}
    <div class="rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 py-16 text-center">
        <svg class="size-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">No scan data yet</h3>
        @if($audit->website_url)
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Ready to scan <strong>{{ parse_url($audit->website_url, PHP_URL_HOST) }}</strong></p>
        <a href="{{ route('agency.audits.scan', $audit) }}"
            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Run AI Website Scan
        </a>
        @else
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Add a website URL to run an AI-powered scan.</p>
        <button @click="showEdit=true" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
            Add Website URL
        </button>
        @endif
    </div>

    @endif

    {{-- Edit form --}}
    <div x-show="showEdit" x-cloak class="rounded-2xl border border-brand-200 bg-brand-50/30 dark:border-brand-800/50 dark:bg-brand-500/5 p-6">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Edit Audit</h3>
        <form action="{{ route('agency.audits.update', $audit) }}" method="POST" class="space-y-4">
            @csrf @method('PATCH')
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Website URL</label>
                    <input type="url" name="website_url" value="{{ $audit->website_url }}" placeholder="https://example.com"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3.5 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3.5 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="draft" @selected($audit->status==='draft')>Draft</option>
                        <option value="in_progress" @selected($audit->status==='in_progress')>In Progress</option>
                        <option value="complete" @selected($audit->status==='complete')>Complete</option>
                        <option value="shared" @selected($audit->status==='shared')>Shared</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Overall Score (manual override)</label>
                    <input type="number" name="score" min="0" max="100" value="{{ $audit->score }}"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3.5 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                <button type="button" @click="showEdit=false" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">Cancel</button>
                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Save</button>
            </div>
        </form>
    </div>

</div>
@endsection
