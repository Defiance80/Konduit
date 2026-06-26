<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konduit — Agency Intelligence Platform</title>
    <meta name="description" content="Konduit gives agencies a unified intelligence layer: operational clarity, client transparency, and AI-powered insights that predict problems before they happen.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-950 text-white antialiased" x-data="{ mobileMenu: false }">

{{-- Navigation --}}
<header class="fixed top-0 inset-x-0 z-50 border-b border-white/5 bg-gray-950/80 backdrop-blur-md">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2.5">
                <svg class="h-7 w-7 text-brand-500" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="32" height="32" rx="8" fill="currentColor" fill-opacity="0.15"/>
                    <path d="M8 10C8 8.89543 8.89543 8 10 8H16C19.3137 8 22 10.6863 22 14C22 17.3137 19.3137 20 16 20H10V24H8V10Z" fill="currentColor"/>
                    <circle cx="22" cy="22" r="4" fill="currentColor" fill-opacity="0.6"/>
                </svg>
                <span class="text-lg font-bold tracking-tight text-white">Konduit</span>
            </a>

            {{-- Desktop Nav --}}
            <nav class="hidden md:flex items-center gap-8">
                <a href="#features" class="text-sm text-gray-400 hover:text-white transition-colors">Features</a>
                <a href="#how-it-works" class="text-sm text-gray-400 hover:text-white transition-colors">How It Works</a>
                <a href="#portals" class="text-sm text-gray-400 hover:text-white transition-colors">Portals</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="hidden md:block text-sm text-gray-400 hover:text-white transition-colors px-4 py-2">
                    Sign In
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600 transition-colors">
                    Get Started
                </a>
                <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-gray-400 hover:text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenu" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenu" x-cloak class="md:hidden py-4 border-t border-white/5">
            <nav class="flex flex-col gap-4">
                <a href="#features" @click="mobileMenu = false" class="text-sm text-gray-400 hover:text-white">Features</a>
                <a href="#how-it-works" @click="mobileMenu = false" class="text-sm text-gray-400 hover:text-white">How It Works</a>
                <a href="#portals" @click="mobileMenu = false" class="text-sm text-gray-400 hover:text-white">Portals</a>
                <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-white">Sign In</a>
            </nav>
        </div>
    </div>
</header>

{{-- Hero --}}
<section class="relative pt-32 pb-24 overflow-hidden">
    {{-- Background gradient --}}
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[600px] rounded-full bg-brand-500/10 blur-3xl"></div>
        <div class="absolute top-40 left-1/4 w-96 h-96 rounded-full bg-indigo-500/5 blur-3xl"></div>
    </div>

    <div class="mx-auto max-w-7xl px-6 lg:px-8 relative">
        <div class="text-center">
            <div class="inline-flex items-center gap-2 rounded-full border border-brand-500/20 bg-brand-500/10 px-4 py-1.5 mb-8">
                <span class="h-1.5 w-1.5 rounded-full bg-brand-400 animate-pulse"></span>
                <span class="text-xs font-medium text-brand-300 uppercase tracking-wider">Agency Intelligence Platform</span>
            </div>

            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight text-white leading-tight">
                Run your agency<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-indigo-400">with intelligence</span>
            </h1>

            <p class="mt-6 text-lg sm:text-xl text-gray-400 max-w-2xl mx-auto leading-relaxed">
                Konduit gives agencies a unified operational layer — real-time project visibility, AI-powered client reporting, and proactive risk detection before problems reach your inbox.
            </p>

            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl bg-brand-500 px-8 py-3.5 text-base font-semibold text-white shadow-lg hover:bg-brand-600 transition-all hover:shadow-brand-500/20 hover:shadow-xl">
                    Start Free Trial
                    <svg class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="#how-it-works" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl border border-white/10 px-8 py-3.5 text-base font-semibold text-white hover:border-white/20 hover:bg-white/5 transition-all">
                    See How It Works
                </a>
            </div>

            {{-- Stats --}}
            <div class="mt-16 grid grid-cols-3 gap-6 max-w-lg mx-auto">
                <div class="text-center">
                    <div class="text-3xl font-bold text-white">18+</div>
                    <div class="mt-1 text-sm text-gray-500">Intelligence Modules</div>
                </div>
                <div class="text-center border-x border-white/10">
                    <div class="text-3xl font-bold text-white">AI</div>
                    <div class="mt-1 text-sm text-gray-500">Powered Insights</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white">∞</div>
                    <div class="mt-1 text-sm text-gray-500">Clients Supported</div>
                </div>
            </div>
        </div>

        {{-- Dashboard Preview --}}
        <div class="mt-20 relative">
            <div class="rounded-2xl border border-white/10 bg-gray-900 shadow-2xl overflow-hidden">
                {{-- Fake browser chrome --}}
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/10 bg-gray-900/80">
                    <span class="h-3 w-3 rounded-full bg-red-500/60"></span>
                    <span class="h-3 w-3 rounded-full bg-yellow-500/60"></span>
                    <span class="h-3 w-3 rounded-full bg-green-500/60"></span>
                    <div class="ml-4 flex-1 rounded-md bg-gray-800 h-6 max-w-xs px-3 flex items-center">
                        <span class="text-xs text-gray-500">konduit.shopbluewolf.com/dashboard</span>
                    </div>
                </div>
                {{-- Mock Dashboard Content --}}
                <div class="p-6 bg-gray-900">
                    {{-- Stat cards --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        @foreach([['Clients', '12', 'text-brand-400'], ['Active Projects', '8', 'text-green-400'], ['Open Tickets', '23', 'text-yellow-400'], ['Retainers', '9', 'text-indigo-400']] as [$label, $val, $color])
                        <div class="rounded-xl bg-gray-800 p-4 border border-white/5">
                            <div class="text-2xl font-bold {{ $color }}">{{ $val }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $label }}</div>
                            <div class="mt-3 h-1 rounded-full bg-gray-700">
                                <div class="h-1 rounded-full {{ str_replace('text-', 'bg-', $color) }}" style="width: {{ rand(40, 85) }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    {{-- Project rows --}}
                    <div class="rounded-xl bg-gray-800 border border-white/5 overflow-hidden">
                        <div class="px-4 py-3 border-b border-white/5 flex items-center justify-between">
                            <span class="text-sm font-medium text-white">Active Projects</span>
                            <span class="text-xs text-brand-400">View all →</span>
                        </div>
                        @foreach([['Apex Q3 SEO Campaign', 'Apex Commerce', '42%', 'bg-brand-500'], ['Meridian Content Series', 'Meridian Health', '25%', 'bg-green-500'], ['Website Redesign', 'Apex Commerce', '15%', 'bg-yellow-500']] as [$proj, $client, $pct, $color])
                        <div class="flex items-center gap-4 px-4 py-3 border-b border-white/5 last:border-0">
                            <div class="h-8 w-8 rounded-lg {{ $color }}/20 flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold {{ str_replace('bg-', 'text-', $color) }}">{{ strtoupper(substr($proj, 0, 1)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-white truncate">{{ $proj }}</div>
                                <div class="text-xs text-gray-500">{{ $client }}</div>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <div class="w-24 h-1.5 rounded-full bg-gray-700">
                                    <div class="h-1.5 rounded-full {{ $color }}" style="width: {{ $pct }}"></div>
                                </div>
                                <span class="text-xs text-gray-400 w-8">{{ $pct }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- Glow --}}
            <div class="absolute -inset-4 bg-brand-500/5 rounded-3xl blur-xl -z-10"></div>
        </div>
    </div>
</section>

{{-- Features --}}
<section id="features" class="py-24 border-t border-white/5">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-white">Built for how agencies actually work</h2>
            <p class="mt-4 text-gray-400 max-w-xl mx-auto">Not another project manager. An intelligence layer that turns operational data into decisions.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
            $features = [
                ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'title' => 'Executive Intelligence', 'desc' => 'Real-time KPIs, revenue forecasts, and risk signals across every client and project — without opening a spreadsheet.', 'color' => 'text-brand-400 bg-brand-500/10'],
                ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Client Intelligence', 'desc' => 'Health scores, retention risk flags, communication patterns, and relationship timelines for every account.', 'color' => 'text-green-400 bg-green-500/10'],
                ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'AI Report Writer', 'desc' => 'Automatically generate client-facing reports translated from technical metrics into plain language executives understand.', 'color' => 'text-indigo-400 bg-indigo-500/10'],
                ['icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', 'title' => 'Ticketing & Approvals', 'desc' => 'Structured intake with AI classification, internal routing, and a clean client-facing approval workflow — no email chains.', 'color' => 'text-yellow-400 bg-yellow-500/10'],
                ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Financial Intelligence', 'desc' => 'Retainer tracking, profitability per client, budget burn rates, and subscription billing through Stripe — all in one view.', 'color' => 'text-orange-400 bg-orange-500/10'],
                ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => 'Capacity Engine', 'desc' => 'Workload forecasting, resource allocation, and team utilisation tracking so you never overcommit again.', 'color' => 'text-pink-400 bg-pink-500/10'],
            ];
            @endphp

            @foreach($features as $feature)
            <div class="group rounded-2xl border border-white/5 bg-gray-900 p-6 hover:border-white/10 hover:bg-gray-800/50 transition-all">
                <div class="h-10 w-10 rounded-xl {{ $feature['color'] }} flex items-center justify-center mb-4">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-white mb-2">{{ $feature['title'] }}</h3>
                <p class="text-sm text-gray-400 leading-relaxed">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- How It Works --}}
<section id="how-it-works" class="py-24 border-t border-white/5 bg-gray-900/30">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-white">What happens when a request comes in</h2>
            <p class="mt-4 text-gray-400 max-w-xl mx-auto">From client intake to resolution — every step is tracked, classified, and summarised automatically.</p>
        </div>

        <div class="relative">
            {{-- Connecting line --}}
            <div class="absolute left-1/2 top-6 bottom-6 w-px bg-gradient-to-b from-brand-500/50 via-indigo-500/30 to-transparent hidden md:block"></div>

            <div class="grid md:grid-cols-2 gap-8 md:gap-12">
                @php
                $steps = [
                    ['n' => '01', 'title' => 'Client submits via widget or portal', 'desc' => 'Clients submit requests through an embeddable widget or their dedicated portal. Screenshots, attachments, and descriptions are all captured.', 'side' => 'left'],
                    ['n' => '02', 'title' => 'AI classifies and summarises', 'desc' => 'Konduit\'s Intake AI classifies the request by type, priority, and likely resolution path — and generates an internal summary for your team.', 'side' => 'right'],
                    ['n' => '03', 'title' => 'Routed to the right person', 'desc' => 'Tickets are automatically matched to clients and assigned based on project ownership and team capacity.', 'side' => 'left'],
                    ['n' => '04', 'title' => 'Client stays informed', 'desc' => 'Clients see plain-language status updates in their portal. Internal notes never surface to clients. Resolved tickets generate AI summaries.', 'side' => 'right'],
                ];
                @endphp

                @foreach($steps as $i => $step)
                <div class="{{ $step['side'] === 'right' ? 'md:col-start-2' : '' }} flex items-start gap-4">
                    <div class="flex-shrink-0 h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center">
                        <span class="text-sm font-bold text-brand-400">{{ $step['n'] }}</span>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-white mb-1">{{ $step['title'] }}</h3>
                        <p class="text-sm text-gray-400 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Portals --}}
<section id="portals" class="py-24 border-t border-white/5">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-white">Two portals, one platform</h2>
            <p class="mt-4 text-gray-400 max-w-xl mx-auto">Your team gets full operational control. Your clients get clarity without noise.</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            {{-- Agency Portal --}}
            <div class="rounded-2xl border border-white/10 bg-gray-900 overflow-hidden">
                <div class="px-6 pt-6 pb-4 border-b border-white/5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-8 w-8 rounded-lg bg-brand-500/10 flex items-center justify-center">
                            <svg class="h-4 w-4 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-brand-400 uppercase tracking-wider">Agency Portal</span>
                    </div>
                    <h3 class="text-xl font-bold text-white">Operational Control</h3>
                    <p class="text-sm text-gray-400 mt-1">Everything your team needs to run accounts, manage projects, and stay ahead of risks.</p>
                </div>
                <div class="p-6 space-y-3">
                    @foreach(['Executive dashboard with live KPIs', 'Client health scores and retention signals', 'Project progress and budget tracking', 'Ticket management with AI summaries', 'Retainer billing and service tracking', 'Team capacity and workload view', 'AI-generated internal reports'] as $item)
                    <div class="flex items-center gap-3 text-sm text-gray-300">
                        <svg class="h-4 w-4 text-brand-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $item }}
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Client Portal --}}
            <div class="rounded-2xl border border-white/10 bg-gray-900 overflow-hidden">
                <div class="px-6 pt-6 pb-4 border-b border-white/5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-8 w-8 rounded-lg bg-green-500/10 flex items-center justify-center">
                            <svg class="h-4 w-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-green-400 uppercase tracking-wider">Client Portal</span>
                    </div>
                    <h3 class="text-xl font-bold text-white">Client Transparency</h3>
                    <p class="text-sm text-gray-400 mt-1">A clean, jargon-free window into their work — without access to your internal operations.</p>
                </div>
                <div class="p-6 space-y-3">
                    @foreach(['Project progress in plain language', 'Budget and retainer status', 'Ticket submission and status tracking', 'Deliverable review and approvals', 'AI-generated monthly reports', 'No internal notes ever visible', 'Every metric explained simply'] as $item)
                    <div class="flex items-center gap-3 text-sm text-gray-300">
                        <svg class="h-4 w-4 text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $item }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-24 border-t border-white/5">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="relative rounded-3xl bg-gradient-to-br from-brand-600 to-indigo-600 p-12 text-center overflow-hidden">
            <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
                <div class="absolute -top-24 -right-24 h-64 w-64 rounded-full bg-white/5 blur-2xl"></div>
                <div class="absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-black/10 blur-2xl"></div>
            </div>
            <div class="relative">
                <h2 class="text-3xl sm:text-4xl font-bold text-white">Ready to run a smarter agency?</h2>
                <p class="mt-4 text-lg text-white/70 max-w-xl mx-auto">Get started today — no credit card required. Your team can be up and running in under 30 minutes.</p>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl bg-white px-8 py-3.5 text-base font-semibold text-brand-700 shadow-lg hover:bg-gray-50 transition-colors">
                        Start Free Trial
                    </a>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl border border-white/20 px-8 py-3.5 text-base font-semibold text-white hover:bg-white/10 transition-colors">
                        Sign In
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="border-t border-white/5 py-10">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <svg class="h-5 w-5 text-brand-500" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="32" height="32" rx="8" fill="currentColor" fill-opacity="0.15"/>
                <path d="M8 10C8 8.89543 8.89543 8 10 8H16C19.3137 8 22 10.6863 22 14C22 17.3137 19.3137 20 16 20H10V24H8V10Z" fill="currentColor"/>
                <circle cx="22" cy="22" r="4" fill="currentColor" fill-opacity="0.6"/>
            </svg>
            <span class="text-sm font-semibold text-white">Konduit</span>
        </div>
        <p class="text-sm text-gray-500">&copy; {{ date('Y') }} Konduit. Agency Intelligence Platform.</p>
        <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Sign In →</a>
    </div>
</footer>

</body>
</html>
