<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konduit — Intelligence. Operations. Growth.</title>
    <meta name="description" content="Konduit is the Agency Intelligence Platform that brings clarity to complexity, empowers teams, and drives measurable growth.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            background-color: #0F1115;
            color: #ffffff;
            font-family: 'Inter', system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }
        :root {
            --k-blue: #0EA5FF;
            --k-deep: #1E40AF;
            --k-teal: #06B6D4;
            --k-indigo: #7C3AED;
            --k-steel: #A1A1AA;
            --k-charcoal: #0F1115;
            --k-card: #111418;
            --k-border: rgba(255,255,255,0.08);
        }

        /* Reveal animations */
        [data-reveal] {
            opacity: 0;
            transform: translateY(2rem);
            transition: opacity 0.9s cubic-bezier(0.22, 1, 0.36, 1), transform 0.9s cubic-bezier(0.22, 1, 0.36, 1);
        }
        [data-reveal].revealed { opacity: 1; transform: translateY(0); }
        [data-reveal-left] {
            opacity: 0;
            transform: translateX(-3rem);
            transition: opacity 1s, transform 1s;
        }
        [data-reveal-left].revealed { opacity: 1; transform: translateX(0); }

        /* Nav pill transition */
        #site-nav.scrolled nav {
            background: rgba(15, 17, 21, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 1rem;
            max-width: 1200px;
        }
        #site-nav.scrolled { top: 1rem; left: 1rem; right: 1rem; }

        /* Hero grid lines */
        .hero-grid {
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 80px 80px;
        }

        /* Blue glow */
        .blue-glow {
            background: radial-gradient(ellipse 60% 50% at 30% 50%, rgba(14,165,255,0.12) 0%, transparent 70%);
        }

        /* How it works progress bar */
        @keyframes k-progress {
            from { transform: scaleX(0); }
            to { transform: scaleX(1); }
        }
        .k-progress-bar {
            animation: k-progress 6s linear forwards;
            transform-origin: left;
        }

        /* Marquee */
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .marquee { animation: marquee 35s linear infinite; }

        /* Blur word */
        .blur-word-char {
            display: inline-block;
            transition: color 0.4s ease;
        }

        /* Noise overlay */
        .noise::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            opacity: 0.025;
            pointer-events: none;
            z-index: 1;
        }

        /* Feature card border glow on hover */
        .feature-card { transition: border-color 0.3s, background 0.3s; }
        .feature-card:hover { border-color: rgba(14,165,255,0.3); background: rgba(14,165,255,0.03); }

        /* Integration card */
        .int-card { transition: border-color 0.3s, transform 0.3s, background 0.3s; }
        .int-card:hover { border-color: rgba(255,255,255,0.3); background: rgba(255,255,255,0.03); transform: scale(1.02); }
        .int-card:hover .int-tag { background: white; color: #0F1115; }

        /* Step underline */
        .step-underline { transition: transform 0.5s cubic-bezier(0.22,1,0.36,1); transform-origin: left; }
        .step-active .step-underline { transform: scaleX(1); }
        .step-inactive .step-underline { transform: scaleX(0); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0F1115; }
        ::-webkit-scrollbar-thumb { background: rgba(14,165,255,0.3); border-radius: 3px; }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="{ mobileMenu: false }">

{{-- ============================================================ --}}
{{-- NAVIGATION --}}
{{-- ============================================================ --}}
<header id="site-nav" class="fixed z-50 transition-all duration-500 top-0 left-0 right-0">
    <nav class="mx-auto transition-all duration-500 max-w-[1400px]">
        <div class="flex items-center justify-between px-6 lg:px-8 h-20 transition-all duration-500" id="nav-inner">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-3 group">
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="navRing" x1="0" y1="0" x2="38" y2="38" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#0EA5FF"/>
                            <stop offset="55%" stop-color="#1E40AF"/>
                            <stop offset="100%" stop-color="#A1A1AA" stop-opacity="0.5"/>
                        </linearGradient>
                        <linearGradient id="navBlue" x1="0" y1="0" x2="1" y2="1" gradientUnits="objectBoundingBox">
                            <stop offset="0%" stop-color="#0EA5FF"/>
                            <stop offset="100%" stop-color="#1E40AF"/>
                        </linearGradient>
                        <linearGradient id="navSteel" x1="0" y1="0" x2="0" y2="1" gradientUnits="objectBoundingBox">
                            <stop offset="0%" stop-color="#E4E4E7"/>
                            <stop offset="100%" stop-color="#71717A"/>
                        </linearGradient>
                    </defs>
                    <circle cx="19" cy="19" r="17" stroke="url(#navRing)" stroke-width="2.5" fill="none"/>
                    <rect x="11" y="9.5" width="3.8" height="19" rx="0.5" fill="url(#navSteel)"/>
                    <polygon points="14.8,19 14.8,14.5 26,9.5 21.5,9.5" fill="url(#navBlue)"/>
                    <polygon points="14.8,19 14.8,23.5 21.5,28.5 26,28.5" fill="url(#navBlue)"/>
                </svg>
                <div>
                    <span class="block text-lg font-black tracking-widest text-white leading-none">KONDUIT</span>
                    <span class="block text-[9px] font-mono tracking-[0.2em] text-[#0EA5FF]/70 leading-none mt-0.5">INTELLIGENCE. OPERATIONS. GROWTH.</span>
                </div>
            </a>

            {{-- Desktop nav --}}
            <div class="hidden md:flex items-center gap-10">
                @foreach([['Platform', '#features'], ['How It Works', '#how-it-works'], ['Integrations', '#integrations'], ['Portals', '#portals']] as [$label, $href])
                <a href="{{ $href }}" class="text-sm text-white/60 hover:text-white transition-colors relative group">
                    {{ $label }}
                    <span class="absolute -bottom-1 left-0 w-0 h-px bg-[#0EA5FF] transition-all duration-300 group-hover:w-full"></span>
                </a>
                @endforeach
            </div>

            {{-- Desktop CTA --}}
            <div class="hidden md:flex items-center gap-4">
                <a href="{{ route('login') }}" class="text-sm text-white/60 hover:text-white transition-colors">Sign in</a>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-full bg-[#0EA5FF] hover:bg-[#0EA5FF]/90 text-black font-semibold text-sm px-5 h-9 transition-colors">
                    Get Started
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>

            {{-- Mobile toggle --}}
            <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-white/70 hover:text-white">
                <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileMenu" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </nav>

    {{-- Mobile menu --}}
    <div x-show="mobileMenu" x-cloak class="md:hidden fixed inset-0 bg-[#0F1115] z-40 flex flex-col px-8 pt-28 pb-8">
        <div class="flex-1 flex flex-col justify-center gap-8">
            @foreach([['Platform', '#features'], ['How It Works', '#how-it-works'], ['Integrations', '#integrations'], ['Portals', '#portals']] as [$label, $href])
            <a href="{{ $href }}" @click="mobileMenu = false" class="text-4xl font-extrabold text-white hover:text-[#0EA5FF] transition-colors">{{ $label }}</a>
            @endforeach
        </div>
        <div class="flex gap-3 pt-8 border-t border-white/10">
            <a href="{{ route('login') }}" class="flex-1 flex items-center justify-center rounded-full border border-white/20 h-14 text-base font-semibold text-white hover:border-white/40 transition-colors" @click="mobileMenu = false">Sign in</a>
            <a href="{{ route('login') }}" class="flex-1 flex items-center justify-center rounded-full bg-[#0EA5FF] h-14 text-base font-semibold text-black hover:bg-[#0EA5FF]/90 transition-colors" @click="mobileMenu = false">Get Started</a>
        </div>
    </div>
</header>

{{-- ============================================================ --}}
{{-- HERO --}}
{{-- ============================================================ --}}
<section class="relative min-h-screen flex flex-col justify-center overflow-hidden noise" id="hero" style="background-color: #0F1115;">
    {{-- Grid lines --}}
    <div class="absolute inset-0 hero-grid pointer-events-none"></div>

    {{-- Blue glow --}}
    <div class="absolute inset-0 blue-glow pointer-events-none"></div>

    {{-- Subtle vertical lines (refined) --}}
    <div class="absolute inset-0 pointer-events-none opacity-30">
        @for($i = 1; $i <= 11; $i++)
        <div class="absolute top-0 bottom-0 w-px bg-white/5" style="left: {{ 8.33 * $i }}%"></div>
        @endfor
        @for($i = 1; $i <= 7; $i++)
        <div class="absolute left-0 right-0 h-px bg-white/5" style="top: {{ 12.5 * $i }}%"></div>
        @endfor
    </div>

    {{-- Corner accent --}}
    <div class="absolute top-0 right-0 w-96 h-96 pointer-events-none" style="background: radial-gradient(circle at top right, rgba(14,165,255,0.06) 0%, transparent 70%)"></div>

    <div class="relative z-10 w-full max-w-[1400px] mx-auto px-6 lg:px-12 py-32 lg:py-40">
        <div class="lg:max-w-[58%]">
            {{-- Eyebrow --}}
            <div class="mb-8 opacity-0 translate-y-4 transition-all duration-700" id="hero-eyebrow">
                <span class="inline-flex items-center gap-3 text-sm font-mono text-white/50">
                    <span class="w-8 h-px bg-[#0EA5FF]/50"></span>
                    Agency Intelligence Platform
                </span>
            </div>

            {{-- Headline --}}
            <div class="mb-12">
                <h1 class="text-left leading-[0.92] tracking-tight text-white opacity-0 translate-y-8 transition-all duration-1000" id="hero-h1" style="font-size: clamp(2.5rem, 6.5vw, 6.5rem); font-weight: 900;">
                    <span class="block">Agency operations,</span>
                    <span class="block">
                        built to&nbsp;<span id="blur-word-container" class="inline-block"></span>
                    </span>
                </h1>
            </div>

            {{-- Sub --}}
            <p class="text-lg text-[#A1A1AA] max-w-lg leading-relaxed opacity-0 translate-y-4 transition-all duration-700 delay-300" id="hero-sub">
                Konduit gives agencies a unified intelligence layer — real-time visibility across every client, AI-powered reporting, and proactive risk detection before problems reach your inbox.
            </p>

            {{-- CTAs --}}
            <div class="mt-10 flex flex-col sm:flex-row gap-4 opacity-0 translate-y-4 transition-all duration-700 delay-500" id="hero-cta">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#0EA5FF] hover:bg-[#0EA5FF]/90 text-black font-semibold px-8 h-12 text-base transition-all hover:shadow-[0_0_30px_rgba(14,165,255,0.3)]">
                    Start Free Trial
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <a href="#how-it-works" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/15 hover:border-white/30 hover:bg-white/5 text-white font-semibold px-8 h-12 text-base transition-all">
                    See How It Works
                </a>
            </div>
        </div>
    </div>

    {{-- Bottom stats --}}
    <div class="absolute bottom-10 left-0 right-0 px-6 lg:px-12 opacity-0 transition-all duration-700 delay-700" id="hero-stats">
        <div class="max-w-[1400px] mx-auto flex items-start gap-10 lg:gap-20">
            @foreach([['18+', 'intelligence modules'], ['AI-powered', 'client reporting'], ['Multi-tenant', 'SaaS platform']] as [$val, $label])
            <div class="flex flex-col gap-1.5">
                <span class="text-3xl lg:text-4xl font-black text-white tracking-tight">{{ $val }}</span>
                <span class="text-xs font-mono text-white/40 leading-tight">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Scroll cue --}}
    <div class="absolute bottom-10 right-8 lg:right-12 flex flex-col items-center gap-2 opacity-30">
        <div class="w-px h-16 bg-gradient-to-b from-transparent to-[#0EA5FF]"></div>
        <span class="text-[10px] font-mono text-white/40 tracking-widest rotate-90 mt-2">SCROLL</span>
    </div>
</section>

{{-- ============================================================ --}}
{{-- FEATURES --}}
{{-- ============================================================ --}}
<section id="features" class="relative py-24 lg:py-32 overflow-hidden">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        {{-- Header --}}
        <div class="relative mb-20 lg:mb-28">
            <div class="grid lg:grid-cols-12 gap-8 items-end">
                <div class="lg:col-span-7">
                    <span class="inline-flex items-center gap-3 text-sm font-mono text-[#A1A1AA] mb-6" data-reveal>
                        <span class="w-12 h-px bg-[#0EA5FF]/40"></span>
                        Platform Capabilities
                    </span>
                    <h2 class="tracking-tight leading-[0.9] text-white" style="font-size: clamp(3rem, 7vw, 7.5rem); font-weight: 900;" data-reveal>
                        Intelligent<br>
                        <span class="text-white/30">operations.</span>
                    </h2>
                </div>
                <div class="lg:col-span-5 lg:pb-4" data-reveal>
                    <p class="text-xl text-[#A1A1AA] leading-relaxed">
                        Not another project manager. An intelligence layer that turns your agency data into decisions — and your agency work into client clarity.
                    </p>
                </div>
            </div>
        </div>

        {{-- Bento Grid --}}
        <div class="grid lg:grid-cols-12 gap-4 lg:gap-5">

            {{-- Card 01 — Full width, particle canvas --}}
            <div class="lg:col-span-12 relative border border-white/8 min-h-[480px] overflow-hidden flex feature-card" style="background: var(--k-card);" data-reveal>
                {{-- Left: text --}}
                <div class="relative flex-1 p-8 lg:p-12" style="z-index: 2;">
                    <canvas id="particle-canvas" class="absolute inset-0 pointer-events-auto" style="width:100%; height:100%;"></canvas>
                    <div class="relative" style="z-index: 3;">
                        <span class="font-mono text-sm text-[#A1A1AA]">01</span>
                        <h3 class="text-3xl lg:text-4xl font-black mt-4 mb-5 tracking-tight text-white">Executive Intelligence</h3>
                        <p class="text-lg text-[#A1A1AA] leading-relaxed max-w-md mb-8">
                            Real-time KPIs across every client and project. Revenue forecasts, risk signals, and portfolio health — all in one view. No spreadsheet required.
                        </p>
                        <div>
                            <span class="text-5xl lg:text-6xl font-black text-white tracking-tight">360°</span>
                            <span class="block text-sm font-mono text-[#A1A1AA] mt-2">agency-wide visibility</span>
                        </div>
                    </div>
                </div>
                {{-- Right: dashboard mockup --}}
                <div class="hidden lg:block relative w-[42%] shrink-0 overflow-hidden">
                    <div class="absolute inset-0 p-6 flex flex-col gap-3" style="z-index: 2;">
                        <div class="text-xs font-mono text-[#0EA5FF]/60 mb-2">LIVE — Agency Overview</div>
                        @foreach([['Apex Commerce', 87, '#0EA5FF'], ['Meridian Health', 72, '#06B6D4'], ['Solis Partners', 55, '#7C3AED'], ['Irongate Media', 91, '#0EA5FF']] as [$name, $score, $color])
                        <div class="rounded-lg border border-white/8 p-3" style="background: rgba(255,255,255,0.03);">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-white">{{ $name }}</span>
                                <span class="text-sm font-mono" style="color: {{ $color }}">{{ $score }}</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-white/8">
                                <div class="h-1.5 rounded-full" style="width: {{ $score }}%; background: {{ $color }};"></div>
                            </div>
                        </div>
                        @endforeach
                        <div class="mt-2 flex gap-3">
                            @foreach([['4', 'Clients'], ['8', 'Projects'], ['23', 'Tickets']] as [$n, $l])
                            <div class="flex-1 rounded-lg border border-white/8 p-3" style="background: rgba(255,255,255,0.03);">
                                <div class="text-xl font-black text-white">{{ $n }}</div>
                                <div class="text-xs text-[#A1A1AA]">{{ $l }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-r from-[#111418] via-[#111418]/60 to-transparent" style="z-index: 1;"></div>
                </div>
            </div>

            {{-- Cards 02 + 03 --}}
            <div class="lg:col-span-6 relative border border-white/8 p-8 lg:p-10 overflow-hidden feature-card" style="background: var(--k-card); min-height: 320px;" data-reveal>
                <span class="font-mono text-sm text-[#A1A1AA]">02</span>
                <h3 class="text-2xl lg:text-3xl font-black mt-4 mb-4 tracking-tight text-white">Client Intelligence</h3>
                <p class="text-[#A1A1AA] leading-relaxed mb-8">
                    Health scores, retention risk flags, and relationship timelines for every account. Know which clients need attention before they ask.
                </p>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black text-white">AI</span>
                    <span class="text-sm font-mono text-[#A1A1AA] mb-1">health monitoring</span>
                </div>
                {{-- Mini health indicators --}}
                <div class="absolute bottom-6 right-6 flex gap-2">
                    @foreach([[87,'#0EA5FF'],[72,'#06B6D4'],[55,'#7C3AED'],[91,'#0EA5FF']] as [$h,$c])
                    <div class="w-1.5 rounded-full" style="height: {{ $h/5 }}px; background: {{ $c }}; opacity: 0.7;"></div>
                    @endforeach
                </div>
                <div class="absolute top-0 right-0 bottom-0 w-32 pointer-events-none" style="background: radial-gradient(ellipse at right center, rgba(6,182,212,0.06) 0%, transparent 70%);"></div>
            </div>

            <div class="lg:col-span-6 relative border border-white/8 p-8 lg:p-10 overflow-hidden feature-card" style="background: var(--k-card); min-height: 320px;" data-reveal>
                <span class="font-mono text-sm text-[#A1A1AA]">03</span>
                <h3 class="text-2xl lg:text-3xl font-black mt-4 mb-4 tracking-tight text-white">AI Report Writer</h3>
                <p class="text-[#A1A1AA] leading-relaxed mb-8">
                    Technical metrics translated into plain language. Automated client-facing reports that make your work legible — and your agency indispensable.
                </p>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black text-white">0 min</span>
                    <span class="text-sm font-mono text-[#A1A1AA] mb-1">report generation</span>
                </div>
                <div class="absolute top-0 right-0 bottom-0 w-32 pointer-events-none" style="background: radial-gradient(ellipse at right center, rgba(14,165,255,0.06) 0%, transparent 70%);"></div>
            </div>

            {{-- Card 04 --}}
            <div class="lg:col-span-12 relative border border-white/8 p-8 lg:p-12 overflow-hidden feature-card" style="background: var(--k-card);" data-reveal>
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <span class="font-mono text-sm text-[#A1A1AA]">04</span>
                        <h3 class="text-3xl lg:text-4xl font-black mt-4 mb-5 tracking-tight text-white">Ticket & Approval Engine</h3>
                        <p class="text-lg text-[#A1A1AA] leading-relaxed">
                            Structured intake with AI classification, smart routing, and a clean client-facing approval workflow. No more reply-all chains, lost attachments, or missed deadlines.
                        </p>
                        <div class="mt-8 flex items-end gap-3">
                            <span class="text-4xl font-black text-white">100%</span>
                            <span class="text-sm font-mono text-[#A1A1AA] mb-1">audit trail</span>
                        </div>
                    </div>
                    {{-- Ticket flow visual --}}
                    <div class="flex flex-col gap-3">
                        @foreach([
                            ['Client Submission', 'Website widget / portal', '#0EA5FF', 'M3 8l7-7 7 7M5 6v10a2 2 0 002 2h10a2 2 0 002-2V6'],
                            ['AI Classification', 'Type · Priority · Route', '#06B6D4', 'M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18'],
                            ['Team Assignment', 'Routed to right person', '#7C3AED', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0'],
                            ['Client Update', 'Plain-language status', '#0EA5FF', 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z']
                        ] as [$step, $sub, $color, $icon])
                        <div class="flex items-center gap-4 rounded-lg border border-white/8 p-3" style="background: rgba(255,255,255,0.02);">
                            <div class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: {{ $color }}15;">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="{{ $color }}" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-white">{{ $step }}</div>
                                <div class="text-xs text-[#A1A1AA]">{{ $sub }}</div>
                            </div>
                            <div class="ml-auto w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $color }};"></div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- HOW IT WORKS --}}
{{-- ============================================================ --}}
<section
    id="how-it-works"
    class="relative py-24 lg:py-32 overflow-hidden noise"
    style="background-color: #090c10;"
    x-data="{ step: 0 }"
    x-init="setInterval(() => step = (step + 1) % 3, 6000)"
>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] rounded-full pointer-events-none" style="background: radial-gradient(circle, rgba(14,165,255,0.04) 0%, transparent 70%);"></div>

    <div class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12">
        {{-- Header --}}
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-end mb-0 lg:mb-0">
            <div class="pb-0 lg:pb-24">
                <span class="inline-flex items-center gap-3 text-sm font-mono text-white/40 mb-8 block" data-reveal-left>
                    <span class="w-12 h-px inline-block bg-[#0EA5FF]/30 mr-3"></span>
                    How It Works
                </span>
                <h2 class="tracking-tight leading-[0.88] text-white" style="font-size: clamp(3rem, 7vw, 7rem); font-weight: 900;" data-reveal>
                    <span class="block">Capture.</span>
                    <span class="block" style="color: rgba(255,255,255,0.3);">Classify.</span>
                    <span class="block" style="color: rgba(255,255,255,0.1);">Deliver.</span>
                </h2>
            </div>

            {{-- Animated diagram --}}
            <div class="relative h-64 lg:h-[500px] overflow-hidden" data-reveal>
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-4 p-8">
                    @foreach([[0,'Intake','Client submits request'],[1,'AI Brain','Classify & route'],[2,'Resolution','Deliver & report']] as [$i,$label,$sub])
                    <div class="w-full max-w-xs rounded-xl border p-4 transition-all duration-500"
                        :class="step === {{ $i }} ? 'border-[#0EA5FF]/40 bg-[#0EA5FF]/8 scale-105' : 'border-white/8 bg-white/2 scale-100'"
                        style="background: rgba(255,255,255,0.02);">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-black transition-colors"
                                :class="step === {{ $i }} ? 'bg-[#0EA5FF] text-black' : 'bg-white/10 text-white/40'">
                                {{ sprintf('%02d', $i + 1) }}
                            </div>
                            <div>
                                <div class="font-semibold text-sm text-white">{{ $label }}</div>
                                <div class="text-xs text-[#A1A1AA]">{{ $sub }}</div>
                            </div>
                            <div class="ml-auto w-2 h-2 rounded-full transition-colors"
                                :class="step === {{ $i }} ? 'bg-[#0EA5FF]' : 'bg-white/20'"></div>
                        </div>
                    </div>
                    @if($i < 2)
                    <div class="w-px h-4 transition-colors" :class="step > {{ $i }} ? 'bg-[#0EA5FF]/60' : 'bg-white/10'"></div>
                    @endif
                    @endforeach
                </div>
                <div class="absolute inset-x-0 bottom-0 h-20" style="background: linear-gradient(to top, #090c10, transparent);"></div>
            </div>
        </div>

        {{-- Steps --}}
        <div class="grid lg:grid-cols-3 gap-4 mt-0">
            @foreach([
                [0,'01','Capture','the request','Clients submit via your branded portal or an embeddable website widget. Screenshots, full context, and attachments are all captured automatically — no follow-up needed.'],
                [1,'02','Classify','& route','Konduit\'s Intake AI reads the submission and classifies it by type, urgency, and the most likely resolution path. The right person gets it instantly — with full context already attached.'],
                [2,'03','Deliver','& report','Work gets done. Your client sees plain-language status updates in their portal. When resolved, AI generates a summary. The loop closes automatically.']
            ] as [$idx, $num, $title, $sub, $desc])
            <button
                type="button"
                @click="step = {{ $idx }}"
                class="relative text-left p-8 lg:p-10 border transition-all duration-500 cursor-pointer"
                :class="step === {{ $idx }} ? 'border-white/50 bg-black' : 'border-white/15 bg-black hover:border-white/30'"
            >
                {{-- Number + progress line --}}
                <div class="flex items-center gap-4 mb-8">
                    <span class="text-4xl font-black transition-colors duration-300" :class="step === {{ $idx }} ? 'text-[#0EA5FF]' : 'text-white/20'">{{ $num }}</span>
                    <div class="flex-1 h-px bg-white/10 overflow-hidden">
                        <template x-if="step === {{ $idx }}">
                            <div class="h-full bg-[#0EA5FF]/60 k-progress-bar"></div>
                        </template>
                    </div>
                </div>

                <h3 class="text-3xl lg:text-4xl font-black mb-2 text-white tracking-tight">{{ $title }}</h3>
                <span class="text-xl text-white/30 font-semibold block mb-6">{{ $sub }}</span>
                <p class="text-[#A1A1AA] leading-relaxed text-sm lg:text-base" :class="step === {{ $idx }} ? 'opacity-100' : 'opacity-60'">{{ $desc }}</p>

                {{-- Bottom accent bar --}}
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#0EA5FF] step-underline" :class="step === {{ $idx }} ? 'step-active' : 'step-inactive'"></div>
            </button>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- METRICS --}}
{{-- ============================================================ --}}
<section id="metrics" class="relative py-32 lg:py-40 overflow-hidden">
    {{-- Dot grid background (canvas) --}}
    <canvas id="grid-canvas" class="absolute inset-0 pointer-events-none" style="width:100%; height:100%;"></canvas>

    <div class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12">
        {{-- Header --}}
        <div class="grid lg:grid-cols-12 gap-8 mb-20 lg:mb-28">
            <div class="lg:col-span-9">
                <div class="flex items-center gap-4 mb-6">
                    <span class="inline-flex items-center gap-2 px-3 py-1 text-xs font-mono" style="background: rgba(14,165,255,0.1); color: #0EA5FF;">
                        <span class="w-2 h-2 rounded-full animate-pulse" style="background: #0EA5FF;"></span>
                        LIVE
                    </span>
                    <span class="text-sm font-mono text-[#A1A1AA]" id="live-time"></span>
                </div>
                <h2 class="tracking-tight leading-[0.95] text-white" style="font-size: clamp(2.5rem, 6vw, 7rem); font-weight: 900;" data-reveal>
                    Platform<br>
                    <span style="color: rgba(255,255,255,0.3);">intelligence metrics.</span>
                </h2>
            </div>
        </div>

        {{-- Metrics grid --}}
        <div class="grid lg:grid-cols-3 gap-5">
            {{-- Primary metric --}}
            <div class="lg:col-span-1 border border-white/8 p-10 lg:p-12" style="background: rgba(255,255,255,0.015);" data-reveal>
                <div class="text-4xl lg:text-5xl xl:text-6xl font-black tracking-tight text-white mb-4 tabular-nums">
                    <span data-counter="4247">0</span>
                </div>
                <div class="mb-5">
                    <canvas id="dot-graph-1" style="width:100%; height: 40px; display:block;"></canvas>
                </div>
                <div class="text-base font-semibold text-white mb-1">Reports generated this month</div>
                <div class="text-sm font-mono text-[#A1A1AA]">by AI Report Writer</div>
            </div>

            {{-- Secondary metrics --}}
            <div class="border border-white/8 p-8 flex flex-col justify-between gap-6" style="background: rgba(255,255,255,0.015);" data-reveal>
                <div>
                    <div class="text-sm font-mono text-[#A1A1AA] mb-1">across all tenants</div>
                    <div class="text-base font-semibold text-white mb-3">Platform uptime</div>
                    <canvas id="dot-graph-2" style="width:100%; height: 28px; display:block;"></canvas>
                </div>
                <div class="text-3xl lg:text-4xl xl:text-5xl font-black tracking-tight text-white tabular-nums">
                    <span data-counter="99" data-suffix=".9%">0</span>
                </div>
            </div>

            <div class="border border-white/8 p-8 flex flex-col justify-between gap-6" style="background: rgba(255,255,255,0.015);" data-reveal>
                <div>
                    <div class="text-sm font-mono text-[#A1A1AA] mb-1">avg intake classification</div>
                    <div class="text-base font-semibold text-white mb-3">AI ticket classification</div>
                    <canvas id="dot-graph-3" style="width:100%; height: 28px; display:block;"></canvas>
                </div>
                <div class="text-3xl lg:text-4xl xl:text-5xl font-black tracking-tight text-white tabular-nums">
                    <span class="text-2xl text-[#A1A1AA]">&lt;</span><span data-counter="60" data-suffix=" sec">0</span>
                </div>
            </div>
        </div>

        {{-- Bottom ticker --}}
        <div class="mt-14 pt-8 border-t border-white/8 flex flex-wrap items-center gap-x-10 gap-y-3 text-sm font-mono text-[#A1A1AA]" data-reveal>
            @foreach(['GA4', 'Google Search Console', 'HubSpot', 'Shopify', 'Stripe', 'Slack', 'Figma', 'ClickUp']) as $tool)
            <span>{{ $tool }}</span>
            @endforeach
            <span class="text-[#0EA5FF]">+20 integrations</span>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- PORTALS --}}
{{-- ============================================================ --}}
<section id="portals" class="relative py-24 lg:py-32 border-t border-white/5" style="background-color: #090c10;">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="text-center mb-16" data-reveal>
            <span class="inline-flex items-center gap-4 text-sm font-mono text-[#A1A1AA] mb-8">
                <span class="w-12 h-px bg-[#0EA5FF]/30"></span>
                Dual Portal Architecture
                <span class="w-12 h-px bg-[#0EA5FF]/30"></span>
            </span>
            <h2 class="tracking-tight text-white mb-4" style="font-size: clamp(2rem, 5vw, 5rem); font-weight: 900;">Two portals.<br><span style="color: rgba(255,255,255,0.3);">One platform.</span></h2>
            <p class="text-[#A1A1AA] text-xl max-w-xl mx-auto">Your team gets full operational control. Your clients get clarity without noise. Internal notes never surface.</p>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            {{-- Agency Portal --}}
            <div class="border border-white/8 overflow-hidden" style="background: var(--k-card);" data-reveal>
                <div class="px-8 pt-8 pb-5 border-b border-white/8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-9 w-9 rounded-xl flex items-center justify-center" style="background: rgba(14,165,255,0.1);">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="#0EA5FF" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                            </svg>
                        </div>
                        <span class="text-xs font-mono font-semibold tracking-wider" style="color: #0EA5FF;">AGENCY PORTAL</span>
                    </div>
                    <h3 class="text-2xl font-black text-white tracking-tight">Operational Command</h3>
                    <p class="text-sm text-[#A1A1AA] mt-1">Everything your team needs to run accounts, close the loop, and stay ahead.</p>
                </div>
                <div class="p-8 space-y-3">
                    @foreach(['Executive dashboard with live KPIs & risk signals','Client health scores and retention intelligence','Project progress, budget burn & milestone tracking','AI-classified ticket management and routing','Retainer billing, service tracking, and capacity view','Team workload and capacity forecasting','AI-generated internal and client-facing reports']) as $item)
                    <div class="flex items-center gap-3 text-sm text-[#A1A1AA]">
                        <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="#0EA5FF" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ $item }}
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Client Portal --}}
            <div class="border border-white/8 overflow-hidden" style="background: var(--k-card);" data-reveal>
                <div class="px-8 pt-8 pb-5 border-b border-white/8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-9 w-9 rounded-xl flex items-center justify-center" style="background: rgba(6,182,212,0.1);">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="#06B6D4" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-mono font-semibold tracking-wider" style="color: #06B6D4;">CLIENT PORTAL</span>
                    </div>
                    <h3 class="text-2xl font-black text-white tracking-tight">Radical Transparency</h3>
                    <p class="text-sm text-[#A1A1AA] mt-1">A clean, jargon-free window into their work. No access to your internals.</p>
                </div>
                <div class="p-8 space-y-3">
                    @foreach(['Project progress in plain, non-technical language','Budget and retainer status at a glance','Ticket submission and real-time status tracking','Deliverable review, feedback, and approvals','AI-generated monthly performance reports','Internal notes and team discussions never visible','Every metric explained simply — no jargon']) as $item)
                    <div class="flex items-center gap-3 text-sm text-[#A1A1AA]">
                        <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="#06B6D4" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ $item }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- INTEGRATIONS --}}
{{-- ============================================================ --}}
<section id="integrations" class="relative overflow-hidden py-24 lg:py-32">
    <div class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12">
        {{-- Header --}}
        <div class="text-center mb-16">
            <span class="inline-flex items-center gap-4 text-sm font-mono text-[#A1A1AA] mb-8 justify-center" data-reveal>
                <span class="w-12 h-px bg-[#0EA5FF]/30"></span>
                Integrations
                <span class="w-12 h-px bg-[#0EA5FF]/30"></span>
            </span>
            <h2 class="tracking-tight text-white mb-6" style="font-size: clamp(2.5rem, 6vw, 6rem); font-weight: 900;" data-reveal>
                Connect<br>
                <span style="color: rgba(255,255,255,0.3);">everything.</span>
            </h2>
            <p class="text-xl text-[#A1A1AA] max-w-lg mx-auto" data-reveal>
                Konduit connects to the tools agencies already use — via official APIs and OAuth only. No browser extensions. No scrapers.
            </p>
        </div>

        {{-- Marquee row 1 --}}
        <div class="overflow-hidden mb-4 relative" style="mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);">
            <div class="marquee flex gap-4 w-max">
                @php
                $row1 = [['GA4','Analytics'],['Google Search Console','SEO'],['HubSpot','CRM'],['Shopify','eComm'],['Stripe','Payments'],['Slack','Comms'],['GitHub','Code'],['Figma','Design'],['GA4','Analytics'],['Google Search Console','SEO'],['HubSpot','CRM'],['Shopify','eComm'],['Stripe','Payments'],['Slack','Comms'],['GitHub','Code'],['Figma','Design']];
                @endphp
                @foreach($row1 as [$name, $cat])
                <div class="flex-shrink-0 flex items-center gap-3 border border-white/8 px-5 py-3 rounded-lg" style="background: var(--k-card);">
                    <span class="text-sm font-medium text-white">{{ $name }}</span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded" style="background: rgba(14,165,255,0.1); color: #0EA5FF;">{{ $cat }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Marquee row 2 --}}
        <div class="overflow-hidden mb-16 relative" style="mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);">
            <div class="flex gap-4 w-max" style="animation: marquee 28s linear infinite reverse;">
                @php
                $row2 = [['ClickUp','PM'],['Asana','PM'],['Harvest','Time'],['Clockify','Time'],['Microsoft 365','Docs'],['WordPress','CMS'],['WooCommerce','eComm'],['Jira','Dev'],['ClickUp','PM'],['Asana','PM'],['Harvest','Time'],['Clockify','Time'],['Microsoft 365','Docs'],['WordPress','CMS'],['WooCommerce','eComm'],['Jira','Dev']];
                @endphp
                @foreach($row2 as [$name, $cat])
                <div class="flex-shrink-0 flex items-center gap-3 border border-white/8 px-5 py-3 rounded-lg" style="background: var(--k-card);">
                    <span class="text-sm font-medium text-white">{{ $name }}</span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded" style="background: rgba(6,182,212,0.1); color: #06B6D4;">{{ $cat }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Stats row --}}
        <div class="flex flex-wrap items-center justify-between gap-8 pt-12 border-t border-white/8" data-reveal>
            <div class="flex flex-wrap gap-12">
                @foreach([['20+','Native integrations'],['OAuth','Auth built-in'],['Webhooks','Real-time sync']] as [$val, $label])
                <div class="flex items-baseline gap-3">
                    <span class="text-3xl font-black text-white">{{ $val }}</span>
                    <span class="text-sm text-[#A1A1AA]">{{ $label }}</span>
                </div>
                @endforeach
            </div>
            <span class="text-sm font-mono text-[#A1A1AA]">Official APIs only — no scrapers, no workarounds →</span>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- CTA --}}
{{-- ============================================================ --}}
<section class="relative py-24 lg:py-32 overflow-hidden" style="background-color: #090c10;">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div
            id="cta-box"
            class="relative border border-white/20 overflow-hidden"
            onmousemove="ctaMouseMove(event, this)"
            data-reveal
        >
            {{-- Spotlight --}}
            <div id="cta-spotlight" class="absolute inset-0 pointer-events-none transition-opacity duration-300 opacity-0"></div>

            {{-- Corner decorations --}}
            <div class="absolute top-0 right-0 w-24 h-24 border-b border-l border-white/10"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 border-t border-r border-white/10"></div>

            <div class="relative z-10 px-8 lg:px-16 py-16 lg:py-24">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-12">
                    <div class="flex-1 max-w-2xl">
                        <h2 class="tracking-tight text-white mb-6 leading-[0.95]" style="font-size: clamp(2.5rem, 5vw, 4.5rem); font-weight: 900;">
                            Ready to run<br>a smarter agency?
                        </h2>
                        <p class="text-xl text-[#A1A1AA] mb-10 leading-relaxed">
                            Get started today — no credit card required. Your team and clients can be live in under 30 minutes.
                        </p>
                        <div class="flex flex-col sm:flex-row items-start gap-4">
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#0EA5FF] hover:bg-[#0EA5FF]/90 text-black font-bold px-8 h-14 text-base transition-all hover:shadow-[0_0_40px_rgba(14,165,255,0.35)] group">
                                Start Free Trial
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full border border-white/20 hover:bg-white/5 hover:border-white/40 text-white font-semibold px-8 h-14 text-base transition-all">
                                Book a Demo
                            </a>
                        </div>
                        <p class="text-sm font-mono text-[#A1A1AA]/60 mt-6">Free 14-day trial · No credit card · Cancel anytime</p>
                    </div>

                    {{-- Right: Stats --}}
                    <div class="grid grid-cols-2 gap-4 lg:w-72 flex-shrink-0">
                        @foreach([['18+','Intelligence modules'],['AI','Powered reporting'],['∞','Client portals'],['100%','Audit trail']] as [$n, $l])
                        <div class="border border-white/8 p-5 rounded-xl" style="background: rgba(255,255,255,0.02);">
                            <div class="text-2xl font-black text-[#0EA5FF]">{{ $n }}</div>
                            <div class="text-xs text-[#A1A1AA] mt-1">{{ $l }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- FOOTER --}}
{{-- ============================================================ --}}
<footer class="relative bg-black border-t border-white/5">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12 py-16 lg:py-20">
        <div class="grid grid-cols-2 md:grid-cols-6 gap-10 lg:gap-8">
            {{-- Brand --}}
            <div class="col-span-2">
                <a href="/" class="flex items-center gap-3 mb-6">
                    <svg width="32" height="32" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="ftRing" x1="0" y1="0" x2="38" y2="38" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#0EA5FF"/>
                                <stop offset="55%" stop-color="#1E40AF"/>
                                <stop offset="100%" stop-color="#A1A1AA" stop-opacity="0.5"/>
                            </linearGradient>
                            <linearGradient id="ftBlue" x1="0" y1="0" x2="1" y2="1" gradientUnits="objectBoundingBox">
                                <stop offset="0%" stop-color="#0EA5FF"/><stop offset="100%" stop-color="#1E40AF"/>
                            </linearGradient>
                            <linearGradient id="ftSteel" x1="0" y1="0" x2="0" y2="1" gradientUnits="objectBoundingBox">
                                <stop offset="0%" stop-color="#E4E4E7"/><stop offset="100%" stop-color="#71717A"/>
                            </linearGradient>
                        </defs>
                        <circle cx="19" cy="19" r="17" stroke="url(#ftRing)" stroke-width="2.5" fill="none"/>
                        <rect x="11" y="9.5" width="3.8" height="19" rx="0.5" fill="url(#ftSteel)"/>
                        <polygon points="14.8,19 14.8,14.5 26,9.5 21.5,9.5" fill="url(#ftBlue)"/>
                        <polygon points="14.8,19 14.8,23.5 21.5,28.5 26,28.5" fill="url(#ftBlue)"/>
                    </svg>
                    <div>
                        <span class="block text-base font-black tracking-widest text-white leading-none">KONDUIT</span>
                        <span class="block text-[8px] font-mono tracking-[0.15em] text-[#0EA5FF]/60 leading-none mt-0.5">INTELLIGENCE. OPERATIONS. GROWTH.</span>
                    </div>
                </a>
                <p class="text-sm text-white/40 leading-relaxed mb-6 max-w-xs">
                    The Agency Intelligence Platform. We bring clarity to complexity, empower teams, and drive measurable growth.
                </p>
                <div class="flex gap-5">
                    @foreach(['Twitter','LinkedIn','GitHub']) as $s)
                    <a href="#" class="text-sm text-white/40 hover:text-white transition-colors">{{ $s }}</a>
                    @endforeach
                </div>
            </div>

            {{-- Links --}}
            @php
            $links = [
                'Platform' => [['Dashboard','#features'],['How It Works','#how-it-works'],['Integrations','#integrations'],['Pricing','#']],
                'Portals' => [['Agency Portal','#portals'],['Client Portal','#portals'],['AI Reports','#features'],['Ticket Engine','#features']],
                'Company' => [['About','#'],['Blog','#'],['Careers','#'],['Contact','#']],
                'Legal' => [['Privacy','#'],['Terms','#'],['Security','#']],
            ];
            @endphp
            @foreach($links as $section => $items)
            <div>
                <h3 class="text-sm font-semibold text-white mb-5">{{ $section }}</h3>
                <ul class="space-y-3">
                    @foreach($items as [$name, $href])
                    <li>
                        <a href="{{ $href }}" class="text-sm text-white/40 hover:text-white transition-colors">{{ $name }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-white/5 py-6">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-sm text-white/30">&copy; {{ date('Y') }} Konduit. All rights reserved.</p>
            <div class="flex items-center gap-2 text-sm text-white/30">
                <span class="w-2 h-2 rounded-full animate-pulse" style="background: #0EA5FF;"></span>
                All systems operational
            </div>
        </div>
    </div>
</footer>

<script>
(function() {
    // ============================================================
    // NAV SCROLL BEHAVIOR
    // ============================================================
    const nav = document.getElementById('site-nav');
    const navInner = document.getElementById('nav-inner');
    function updateNav() {
        if (window.scrollY > 20) {
            nav.classList.add('scrolled');
            navInner.style.height = '56px';
        } else {
            nav.classList.remove('scrolled');
            navInner.style.height = '';
        }
    }
    window.addEventListener('scroll', updateNav, { passive: true });

    // ============================================================
    // HERO ENTRANCE ANIMATIONS
    // ============================================================
    setTimeout(() => {
        const eyebrow = document.getElementById('hero-eyebrow');
        const h1 = document.getElementById('hero-h1');
        const sub = document.getElementById('hero-sub');
        const cta = document.getElementById('hero-cta');
        const stats = document.getElementById('hero-stats');
        if (eyebrow) { eyebrow.style.opacity = '1'; eyebrow.style.transform = 'translateY(0)'; }
        setTimeout(() => { if (h1) { h1.style.opacity = '1'; h1.style.transform = 'translateY(0)'; } }, 150);
        setTimeout(() => { if (sub) { sub.style.opacity = '1'; sub.style.transform = 'translateY(0)'; } }, 350);
        setTimeout(() => { if (cta) { cta.style.opacity = '1'; cta.style.transform = 'translateY(0)'; } }, 500);
        setTimeout(() => { if (stats) { stats.style.opacity = '1'; } }, 700);
    }, 100);

    // ============================================================
    // BLUR WORD ANIMATION
    // ============================================================
    const words = ['report', 'retain', 'scale', 'deliver'];
    const gradColors = ['#0EA5FF', '#06B6D4', '#1E40AF', '#0EA5FF'];
    let wordIdx = 0;
    const container = document.getElementById('blur-word-container');

    function hexToRgb(hex) {
        const r = parseInt(hex.slice(1,3), 16);
        const g = parseInt(hex.slice(3,5), 16);
        const b = parseInt(hex.slice(5,7), 16);
        return [r, g, b];
    }
    function lerpColor(c1, c2, t) {
        const [r1,g1,b1] = hexToRgb(c1);
        const [r2,g2,b2] = hexToRgb(c2);
        return `rgb(${Math.round(r1+(r2-r1)*t)},${Math.round(g1+(g2-g1)*t)},${Math.round(b1+(b2-b1)*t)})`;
    }

    function animateWord(word) {
        if (!container) return;
        container.innerHTML = '';
        const STAGGER = 45;
        const DURATION = 450;
        const GRADIENT_HOLD = STAGGER * word.length + DURATION + 300;

        word.split('').forEach((char, i) => {
            const span = document.createElement('span');
            span.textContent = char;
            span.className = 'blur-word-char';
            span.style.opacity = '0';
            span.style.filter = 'blur(20px)';
            // Color per letter position
            const frac = word.length > 1 ? i / (word.length - 1) : 0;
            const ci = frac * (gradColors.length - 1);
            const lo = Math.floor(ci), hi = Math.min(lo + 1, gradColors.length - 1);
            const col = lerpColor(gradColors[lo], gradColors[hi], ci - lo);
            span.style.color = col;
            container.appendChild(span);

            setTimeout(() => {
                const start = performance.now();
                const tick = (now) => {
                    const progress = Math.min((now - start) / DURATION, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    span.style.opacity = eased;
                    span.style.filter = `blur(${20 * (1 - eased)}px)`;
                    if (progress < 1) requestAnimationFrame(tick);
                };
                requestAnimationFrame(tick);
            }, i * STAGGER);
        });

        setTimeout(() => {
            Array.from(container.children).forEach(span => {
                span.style.color = 'white';
            });
        }, GRADIENT_HOLD);
    }

    if (container) {
        animateWord(words[0]);
        setInterval(() => {
            wordIdx = (wordIdx + 1) % words.length;
            animateWord(words[wordIdx]);
        }, 2800);
    }

    // ============================================================
    // INTERSECTION OBSERVER — SECTION REVEALS
    // ============================================================
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const delay = parseInt(el.dataset.delay || '0');
                setTimeout(() => el.classList.add('revealed'), delay);
                revealObserver.unobserve(el);
            }
        });
    }, { threshold: 0.08 });

    document.querySelectorAll('[data-reveal], [data-reveal-left]').forEach((el, i) => {
        el.dataset.delay = (i % 4) * 80;
        revealObserver.observe(el);
    });

    // ============================================================
    // PARTICLE CANVAS (Features)
    // ============================================================
    const pCanvas = document.getElementById('particle-canvas');
    if (pCanvas) {
        const pCtx = pCanvas.getContext('2d');
        const particles = Array.from({ length: 65 }, (_, i) => {
            const seed = i * 1.618;
            return {
                bx: (seed * 127.1) % 1,
                by: (seed * 311.7) % 1,
                phase: seed * Math.PI * 2,
                speed: 0.4 + (seed % 0.4),
                radius: 1.2 + (seed % 2.0),
            };
        });
        let pTime = 0;
        const mouseP = { x: 0.5, y: 0.5 };

        pCanvas.addEventListener('mousemove', (e) => {
            const rect = pCanvas.getBoundingClientRect();
            mouseP.x = (e.clientX - rect.left) / rect.width;
            mouseP.y = (e.clientY - rect.top) / rect.height;
        });

        function resizeP() {
            const rect = pCanvas.getBoundingClientRect();
            const dpr = Math.min(window.devicePixelRatio || 1, 2);
            pCanvas.width = rect.width * dpr;
            pCanvas.height = rect.height * dpr;
            pCtx.scale(dpr, dpr);
        }
        resizeP();
        window.addEventListener('resize', resizeP);

        function renderP() {
            const rect = pCanvas.getBoundingClientRect();
            const w = rect.width, h = rect.height;
            pCtx.clearRect(0, 0, w, h);

            particles.forEach(p => {
                const flowX = Math.sin(pTime * p.speed * 0.4 + p.phase) * 35;
                const flowY = Math.cos(pTime * p.speed * 0.3 + p.phase * 0.7) * 22;
                const bx = p.bx * w, by = p.by * h;
                const dx = p.bx - mouseP.x, dy = p.by - mouseP.y;
                const dist = Math.sqrt(dx*dx + dy*dy);
                const influence = Math.max(0, 1 - dist * 2.8);
                const x = bx + flowX + influence * Math.cos(pTime + p.phase) * 32;
                const y = by + flowY + influence * Math.sin(pTime + p.phase) * 32;
                const pulse = Math.sin(pTime * p.speed + p.phase) * 0.5 + 0.5;
                const alpha = 0.06 + pulse * 0.14 + influence * 0.25;

                pCtx.beginPath();
                pCtx.arc(x, y, p.radius + pulse * 0.8, 0, Math.PI * 2);
                // Konduit blue particles
                const r = Math.round(14 + influence * 50);
                const g = Math.round(165 + influence * 20);
                pCtx.fillStyle = `rgba(${r}, ${g}, 255, ${alpha})`;
                pCtx.fill();
            });

            pTime += 0.016;
            requestAnimationFrame(renderP);
        }
        renderP();
    }

    // ============================================================
    // GRID BACKGROUND CANVAS (Metrics)
    // ============================================================
    const gCanvas = document.getElementById('grid-canvas');
    if (gCanvas) {
        const gCtx = gCanvas.getContext('2d');
        let gTime = 0;

        function resizeG() {
            const rect = gCanvas.getBoundingClientRect();
            const dpr = Math.min(window.devicePixelRatio || 1, 2);
            gCanvas.width = rect.width * dpr;
            gCanvas.height = rect.height * dpr;
            gCtx.scale(dpr, dpr);
        }
        resizeG();
        window.addEventListener('resize', resizeG);

        function renderG() {
            const rect = gCanvas.getBoundingClientRect();
            const w = rect.width, h = rect.height;
            gCtx.clearRect(0, 0, w, h);
            const gs = 60;
            for (let x = 0; x < w; x += gs) {
                for (let y = 0; y < h; y += gs) {
                    const wave = Math.sin(x * 0.008 + y * 0.008 + gTime) * 0.5 + 0.5;
                    const sz = 1 + wave * 2;
                    gCtx.beginPath();
                    gCtx.arc(x, y, sz, 0, Math.PI * 2);
                    gCtx.fillStyle = `rgba(14, 165, 255, 0.035)`;
                    gCtx.fill();
                }
            }
            gTime += 0.015;
            requestAnimationFrame(renderG);
        }
        renderG();
    }

    // ============================================================
    // DOT GRAPH CANVASES (Metrics)
    // ============================================================
    function initDotGraph(id, color, opts = {}) {
        const canvas = document.getElementById(id);
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const {
            freq1 = 0.35, freq2 = 0.12, freqT = 0.7,
            speed = 0.025, baseline = 0.3, amplitude = 0.5,
            height = 40
        } = opts;
        let t = Math.random() * 100;

        function resize() {
            const dpr = Math.min(window.devicePixelRatio || 1, 2);
            const W = canvas.offsetWidth || 300;
            canvas.width = W * dpr;
            canvas.height = height * dpr;
            ctx.scale(dpr, dpr);
        }
        resize();
        window.addEventListener('resize', resize);

        function render() {
            const W = canvas.offsetWidth || 300;
            const H = height;
            ctx.clearRect(0, 0, W, H);
            const cols = Math.floor(W / 8);
            for (let i = 0; i < cols; i++) {
                const raw = baseline + amplitude * Math.sin(i * freq1 + t) * Math.cos(i * freq2 + t * freqT);
                const v = Math.max(0, Math.min(1, raw));
                const dotY = H - 4 - v * (H - 8);
                const x = i * 8 + 4;
                const alpha = 0.15 + v * 0.55;
                const r = 1.5 + v * 1.2;
                ctx.beginPath();
                ctx.arc(x, dotY, r, 0, Math.PI * 2);
                ctx.fillStyle = color.replace('$a', alpha);
                ctx.fill();
            }
            t += speed;
            requestAnimationFrame(render);
        }
        render();
    }

    initDotGraph('dot-graph-1', 'rgba(14,165,255,$a)', { height:40, freq1:0.28, freq2:0.09, freqT:0.5, speed:0.018, baseline:0.35, amplitude:0.55 });
    initDotGraph('dot-graph-2', 'rgba(6,182,212,$a)', { height:28, freq1:0.45, freq2:0.18, freqT:1.1, speed:0.032, baseline:0.4, amplitude:0.45 });
    initDotGraph('dot-graph-3', 'rgba(124,58,237,$a)', { height:28, freq1:0.22, freq2:0.07, freqT:0.4, speed:0.015, baseline:0.25, amplitude:0.6 });

    // ============================================================
    // ANIMATED COUNTER NUMBERS
    // ============================================================
    function animateCounter(el) {
        const target = parseInt(el.dataset.counter);
        const suffix = el.dataset.suffix || '';
        const duration = 2200;
        const start = performance.now();
        const tick = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 4);
            const val = Math.floor(eased * target);
            el.textContent = val.toLocaleString() + (progress >= 1 ? suffix : '');
            if (progress < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    }

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.dataset.animated) {
                entry.target.dataset.animated = '1';
                animateCounter(entry.target);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('[data-counter]').forEach(el => counterObserver.observe(el));

    // ============================================================
    // LIVE TIME DISPLAY
    // ============================================================
    const timeEl = document.getElementById('live-time');
    if (timeEl) {
        function tick() { timeEl.textContent = new Date().toLocaleTimeString('en-GB') + ' UTC'; }
        tick();
        setInterval(tick, 1000);
    }

    // ============================================================
    // CTA SPOTLIGHT
    // ============================================================
    const ctaBox = document.getElementById('cta-box');
    const ctaSpot = document.getElementById('cta-spotlight');
    window.ctaMouseMove = function(e, el) {
        if (!ctaSpot) return;
        const rect = el.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;
        ctaSpot.style.opacity = '1';
        ctaSpot.style.background = `radial-gradient(500px circle at ${x}% ${y}%, rgba(14,165,255,0.06), transparent 50%)`;
    };
    if (ctaBox) {
        ctaBox.addEventListener('mouseleave', () => {
            if (ctaSpot) ctaSpot.style.opacity = '0';
        });
    }

})();
</script>

</body>
</html>
