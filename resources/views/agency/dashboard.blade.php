@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
                {{ explode(' ', auth()->user()->name)[0] }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ now()->format('l, F j, Y') }} — here's what's happening across your agency.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('agency.tickets.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Ticket
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         Marketing Intelligence Carousel — TOP OF DASHBOARD
    ═══════════════════════════════════════════════════════════════ --}}
    @php
        $newsItems   = array_values($newsBrief['items'] ?? []);
        $newsCount   = count($newsItems);
        $newsPerPage = 3;
        $newsMaxCur  = max(0, $newsCount - $newsPerPage);
        // Track is (count/3 * 100)% wide. Each card = (100/count)% of track = (100/3)% of container.
        $newsTrackW  = $newsCount > 0 ? round($newsCount / $newsPerPage * 100, 2) : 100;
        $newsStepPct = $newsCount > 0 ? round(100 / $newsCount, 4) : 0;

        $newsGradients = [
            'trend'    => ['g' => 'from-brand-500 to-indigo-700',  'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            'platform' => ['g' => 'from-sky-500 to-blue-700',      'icon' => 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z'],
            'industry' => ['g' => 'from-emerald-500 to-teal-700',  'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
            'tip'      => ['g' => 'from-amber-400 to-orange-600',  'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
        ];
    @endphp

    @if($newsCount > 0)
    <div x-data="{
            cur: 0,
            max: {{ $newsMaxCur }},
            timer: null,
            init()  { this.start(); },
            start() { this.timer = setInterval(() => { this.cur = this.cur >= this.max ? 0 : this.cur + 1; }, 6000); },
            stop()  { clearInterval(this.timer); },
            prev()  { this.stop(); this.cur = Math.max(0, this.cur - 1); },
            next()  { this.stop(); this.cur = Math.min(this.max, this.cur + 1); }
         }"
         @mouseenter="stop()"
         @mouseleave="start()"
         class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-hidden">

        {{-- Panel header --}}
        <div class="flex items-center justify-between px-6 py-3.5 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2.5">
                <div class="flex size-7 items-center justify-center rounded-lg bg-brand-50 dark:bg-brand-500/10">
                    <svg class="size-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Marketing Intelligence</h2>
                    <p class="text-xs text-gray-400">{{ $newsBrief['generated_at'] ?? now()->format('F j, Y') }} · AI-curated briefing</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                {{-- Pagination dots --}}
                @if($newsMaxCur > 0)
                <div class="hidden sm:flex items-center gap-1.5">
                    @for($i = 0; $i <= $newsMaxCur; $i++)
                    <button @click="cur = {{ $i }}"
                            :class="cur === {{ $i }} ? 'bg-brand-500 w-5' : 'bg-gray-200 dark:bg-gray-700 w-1.5'"
                            class="h-1.5 rounded-full transition-all duration-300"></button>
                    @endfor
                </div>
                {{-- Prev / Next --}}
                <div class="flex gap-1">
                    <button @click="prev()" :disabled="cur === 0"
                            class="flex size-7 items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button @click="next()" :disabled="cur >= max"
                            class="flex size-7 items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                @endif
                <a href="{{ route('agency.news.index') }}" class="text-xs font-medium text-brand-500 hover:text-brand-600 transition-colors whitespace-nowrap">View all →</a>
            </div>
        </div>

        {{-- Sliding card track --}}
        <div class="overflow-hidden">
            <div class="flex transition-transform duration-500 ease-in-out"
                 :style="`transform: translateX(-${cur * {{ $newsStepPct }}}%)`"
                 style="width: {{ $newsTrackW }}%">
                @foreach($newsItems as $idx => $item)
                @php
                    $cfg    = $newsGradients[$item['type'] ?? 'industry'] ?? $newsGradients['industry'];
                    $cardW  = $newsCount > 0 ? round(100 / $newsCount, 4) : 100;
                @endphp
                <div style="width: {{ $cardW }}%; flex-shrink: 0;"
                     class="p-3 {{ $idx === 0 ? 'pl-4' : '' }} {{ $idx === $newsCount - 1 ? 'pr-4' : '' }}">
                    <article class="rounded-xl overflow-hidden border border-gray-100 dark:border-gray-800 hover:shadow-lg hover:shadow-gray-100 dark:hover:shadow-gray-900/50 transition-shadow h-full flex flex-col">

                        {{-- Gradient image area --}}
                        <div class="relative h-44 bg-gradient-to-br {{ $cfg['g'] }} overflow-hidden flex-shrink-0">
                            <div class="absolute -right-10 -top-10 size-36 rounded-full bg-white/10"></div>
                            <div class="absolute -left-8 -bottom-8 size-44 rounded-full bg-black/10"></div>
                            <div class="absolute right-8 bottom-6 size-16 rounded-full bg-white/10"></div>
                            <div class="absolute right-3 bottom-0 opacity-15 pointer-events-none">
                                <svg class="size-28 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.8" d="{{ $cfg['icon'] }}"/>
                                </svg>
                            </div>
                            <div class="absolute inset-0 p-4 flex flex-col justify-between">
                                <span class="inline-flex self-start items-center rounded-full bg-white/20 backdrop-blur-sm px-2.5 py-0.5 text-xs font-semibold text-white uppercase tracking-wide">
                                    {{ $item['category'] ?? ucfirst($item['type'] ?? 'News') }}
                                </span>
                                <h3 class="text-base font-bold text-white leading-snug line-clamp-2 drop-shadow-sm">
                                    {{ $item['headline'] }}
                                </h3>
                            </div>
                        </div>

                        {{-- Card body --}}
                        <div class="bg-white dark:bg-gray-900 p-4 flex flex-col flex-1">
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed line-clamp-3 flex-1">
                                {{ $item['summary'] }}
                            </p>
                            @if(!empty($item['relevance']))
                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 flex items-start gap-1.5">
                                <svg class="size-3 text-brand-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <p class="text-xs text-gray-400 leading-relaxed line-clamp-2">{{ $item['relevance'] }}</p>
                            </div>
                            @endif
                        </div>

                    </article>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         Stats Grid
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @php
        $statCards = [
            ['label' => 'Total Clients',   'value' => $stats['clients'],         'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'brand'],
            ['label' => 'Active Projects', 'value' => $stats['active_projects'], 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color' => 'success'],
            ['label' => 'Open Tickets',    'value' => $stats['open_tickets'],    'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', 'color' => 'warning'],
            ['label' => 'Tasks Due Today', 'value' => $stats['tasks_due_today'], 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'color' => 'error'],
        ];
        @endphp
        @foreach($statCards as $card)
        <div class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 px-5 py-4">
            <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-{{ $card['color'] }}-50 dark:bg-{{ $card['color'] }}-500/10">
                <svg class="size-5 text-{{ $card['color'] }}-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $card['value'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $card['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Active Projects + Open Tickets --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="font-semibold text-gray-900 dark:text-white">Active Projects</h2>
                <a href="{{ route('agency.projects.index') }}" class="text-xs text-brand-500 hover:text-brand-600 font-medium">View all →</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($recentProjects ?? [] as $project)
                <div class="flex items-center gap-4 px-6 py-3.5">
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('agency.projects.show', $project) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500 transition-colors truncate block">{{ $project->name }}</a>
                        <p class="text-xs text-gray-400 truncate">{{ $project->client->company_name ?? '—' }}</p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <div class="w-20 h-1.5 rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-1.5 rounded-full bg-brand-400" style="width: {{ $project->progress ?? 0 }}%"></div>
                        </div>
                        <span class="text-xs text-gray-500 w-8 text-right">{{ $project->progress ?? 0 }}%</span>
                        @php $sc = ['active' => 'success', 'on_hold' => 'warning', 'completed' => 'brand', 'cancelled' => 'error'][$project->status] ?? 'gray'; @endphp
                        <span class="inline-flex rounded-full bg-{{ $sc }}-50 dark:bg-{{ $sc }}-500/10 px-2 py-0.5 text-xs font-medium text-{{ $sc }}-700 dark:text-{{ $sc }}-400">{{ ucfirst(str_replace('_',' ',$project->status)) }}</span>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-400">No active projects yet.</p>
                    <a href="{{ route('agency.projects.create') }}" class="mt-2 inline-block text-sm text-brand-500 hover:text-brand-600">Create your first project →</a>
                </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="font-semibold text-gray-900 dark:text-white">Open Tickets</h2>
                <a href="{{ route('agency.tickets.index') }}" class="text-xs text-brand-500 hover:text-brand-600 font-medium">View all →</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($recentTickets ?? [] as $ticket)
                @php $pc = ['critical' => 'error', 'high' => 'warning', 'medium' => 'brand', 'low' => 'gray'][$ticket->priority] ?? 'gray'; @endphp
                <div class="px-5 py-3">
                    <a href="{{ route('agency.tickets.show', $ticket) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-brand-500 transition-colors line-clamp-1">{{ $ticket->title }}</a>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex rounded-full bg-{{ $pc }}-50 dark:bg-{{ $pc }}-500/10 px-1.5 py-0.5 text-xs font-medium text-{{ $pc }}-600 dark:text-{{ $pc }}-400">{{ ucfirst($ticket->priority) }}</span>
                        <span class="text-xs text-gray-400 truncate">{{ $ticket->client->company_name ?? '—' }}</span>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center"><p class="text-sm text-gray-400">No open tickets.</p></div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Training Academy --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2.5">
                <div class="flex size-7 items-center justify-center rounded-lg bg-brand-50 dark:bg-brand-500/10">
                    <svg class="size-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Training Academy</h2>
                    <p class="text-xs text-gray-400">Courses and skill-building for your team — at your own pace.</p>
                </div>
            </div>
            <a href="{{ route('agency.training.index') }}" class="text-xs font-medium text-brand-500 hover:text-brand-600">Browse courses →</a>
        </div>
        <div class="grid grid-cols-1 divide-y divide-gray-100 dark:divide-gray-800 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
            @forelse($featuredCourses ?? [] as $course)
            <a href="{{ route('agency.training.show', $course) }}" class="px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                <div class="flex items-center justify-between mb-2">
                    <span class="inline-flex rounded-full bg-{{ $course->difficultyColor }}-50 dark:bg-{{ $course->difficultyColor }}-500/10 px-2 py-0.5 text-xs font-medium text-{{ $course->difficultyColor }}-600 dark:text-{{ $course->difficultyColor }}-400">{{ ucfirst($course->difficulty) }}</span>
                    <span class="text-xs text-gray-400">{{ $course->lessons_count ?? 0 }} lessons</span>
                </div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-brand-500 transition-colors line-clamp-2">{{ $course->title }}</h3>
                @if(($trainingProgress[$course->id] ?? 0) > 0)
                <div class="mt-2 h-1 rounded-full bg-gray-100 dark:bg-gray-800">
                    <div class="h-1 rounded-full bg-brand-400" style="width: {{ $trainingProgress[$course->id] }}%"></div>
                </div>
                @endif
            </a>
            @empty
            <div class="col-span-3 px-6 py-6 text-center">
                <p class="text-sm text-gray-400">No courses published yet.</p>
                <a href="{{ route('agency.training.index') }}" class="mt-1 inline-block text-sm text-brand-500 hover:text-brand-600">Go to Training Academy →</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Clients --}}
    @if(($recentClients ?? collect())->count() > 0)
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-white">Recent Clients</h2>
            <a href="{{ route('agency.clients.index') }}" class="text-xs text-brand-500 hover:text-brand-600 font-medium">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Retainer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Health</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($recentClients as $client)
                    @php
                        $health = $client->health_score ?? 0;
                        $hc     = $health >= 70 ? 'success' : ($health >= 40 ? 'warning' : 'error');
                        $sc2    = ['active' => 'success', 'at_risk' => 'warning', 'churned' => 'error', 'prospect' => 'brand'][$client->status] ?? 'gray';
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-3">
                            <a href="{{ route('agency.clients.show', $client) }}" class="font-medium text-gray-900 dark:text-white hover:text-brand-500 transition-colors">{{ $client->company_name }}</a>
                            <p class="text-xs text-gray-400">{{ $client->industry }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex rounded-full bg-{{ $sc2 }}-50 dark:bg-{{ $sc2 }}-500/10 px-2 py-0.5 text-xs font-medium text-{{ $sc2 }}-700 dark:text-{{ $sc2 }}-400">{{ ucfirst($client->status) }}</span>
                        </td>
                        <td class="px-6 py-3 text-gray-600 dark:text-gray-400">
                            {{ $client->retainers->count() > 0 ? '$'.number_format($client->retainers->sum('monthly_value'),0).'/mo' : '—' }}
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-1.5 rounded-full bg-gray-100 dark:bg-gray-800">
                                    <div class="h-1.5 rounded-full bg-{{ $hc }}-400" style="width: {{ $health }}%"></div>
                                </div>
                                <span class="text-xs text-{{ $hc }}-600 dark:text-{{ $hc }}-400">{{ $health }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
