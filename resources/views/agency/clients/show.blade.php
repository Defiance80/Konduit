@extends('layouts.app')
@section('title', $client->name)

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <a href="{{ route('agency.clients.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 flex-shrink-0">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex items-center gap-4 flex-1 min-w-0">
            <img src="{{ $client->logo_url }}" class="size-14 rounded-2xl flex-shrink-0 border border-gray-100 dark:border-gray-700" alt="{{ $client->name }}">
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $client->name }}</h1>
                    @php $statusColors = ['active'=>'success','inactive'=>'gray','prospect'=>'warning']; @endphp
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                        bg-{{ $statusColors[$client->status] ?? 'gray' }}-50 text-{{ $statusColors[$client->status] ?? 'gray' }}-700
                        dark:bg-{{ $statusColors[$client->status] ?? 'gray' }}-500/10 dark:text-{{ $statusColors[$client->status] ?? 'gray' }}-400">
                        {{ ucfirst($client->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $client->industry ?: 'No industry set' }}
                    @if($client->website)
                    · <a href="{{ $client->website }}" target="_blank" class="text-brand-500 hover:underline">{{ parse_url($client->website, PHP_URL_HOST) }}</a>
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('agency.tickets.create') }}?client={{ $client->id }}" class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800">+ Ticket</a>
            <a href="{{ route('agency.projects.create') }}?client={{ $client->id }}" class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800">+ Project</a>
            <a href="{{ route('agency.clients.edit', $client) }}" class="rounded-lg bg-brand-500 px-3 py-2 text-xs font-medium text-white hover:bg-brand-600">Edit</a>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:border-error-800 dark:bg-error-900/20 dark:text-error-400">{{ session('error') }}</div>
    @endif

    {{-- KPI Row --}}
    @php
        $activeProjects = $client->projects->where('status', 'active')->count();
        $openTickets    = $client->tickets->where('status', 'open')->count();
        $monthlyValue   = $retainer ? (float)$retainer->monthly_value : 0;
        $healthScore    = $client->healthScore;
    @endphp
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 px-5 py-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Active Projects</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $activeProjects }}</p>
            <p class="text-xs text-brand-500 mt-0.5">{{ $client->projects->count() }} total</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 px-5 py-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Open Tickets</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $openTickets }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $client->tickets->count() }} total</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 px-5 py-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Monthly Value</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">${{ number_format($monthlyValue, 0) }}</p>
            <p class="text-xs text-success-500 mt-0.5">{{ $retainer ? $retainer->name : 'No active retainer' }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 px-5 py-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Health Score</p>
            @if($healthScore)
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $healthScore->engagement_score }}<span class="text-sm font-normal text-gray-400">/100</span></p>
            <div class="mt-1.5 h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                <div class="h-1.5 rounded-full bg-{{ $healthScore->engagement_color }}-400" style="width:{{ $healthScore->engagement_score }}%"></div>
            </div>
            @else
            <p class="text-2xl font-bold text-gray-400 mt-1">—</p>
            <p class="text-xs text-gray-400 mt-0.5">Not calculated</p>
            @endif
        </div>
    </div>

    {{-- AI Summary --}}
    @include('partials.ai-summary-card', [
        'summary'       => $aiSummary,
        'generateRoute' => 'agency.clients.ai-summary',
        'generateParam' => $client,
        'label'         => 'Client',
    ])

    {{-- Main content --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- LEFT: Contact sidebar --}}
        <div class="space-y-4">

            {{-- Contact info --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 space-y-3">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Contact Information</h3>
                @if($client->email)
                <div class="flex items-start gap-3">
                    <svg class="size-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-400">Company Email</p>
                        <a href="mailto:{{ $client->email }}" class="text-sm text-brand-500 hover:underline break-all">{{ $client->email }}</a>
                    </div>
                </div>
                @endif
                @if($client->phone)
                <div class="flex items-start gap-3">
                    <svg class="size-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <div>
                        <p class="text-xs text-gray-400">Phone</p>
                        <a href="tel:{{ $client->phone }}" class="text-sm text-gray-700 dark:text-gray-300 hover:text-brand-500">{{ $client->phone }}</a>
                    </div>
                </div>
                @endif
                @if($client->website)
                <div class="flex items-start gap-3">
                    <svg class="size-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-400">Website</p>
                        <a href="{{ $client->website }}" target="_blank" class="text-sm text-brand-500 hover:underline break-all">{{ $client->website }}</a>
                    </div>
                </div>
                @endif
                @if($client->address)
                <div class="flex items-start gap-3">
                    <svg class="size-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <div>
                        <p class="text-xs text-gray-400">Address</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $client->address }}</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Contact person --}}
            @if($client->contact_person)
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 space-y-3">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Primary Contact</h3>
                <div class="flex items-center gap-3">
                    <div class="size-9 rounded-full bg-brand-100 dark:bg-brand-500/10 flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-semibold text-brand-600 dark:text-brand-400">{{ substr($client->contact_person, 0, 1) }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->contact_person }}</p>
                        @if($client->contact_person_email)
                        <a href="mailto:{{ $client->contact_person_email }}" class="text-xs text-brand-500 hover:underline break-all">{{ $client->contact_person_email }}</a>
                        @endif
                        @if($client->contact_person_phone)
                        <p class="text-xs text-gray-400">{{ $client->contact_person_phone }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Active Retainer --}}
            @if($retainer)
            <div class="rounded-2xl border border-brand-200/60 bg-brand-50/40 dark:border-brand-800/40 dark:bg-brand-500/5 p-5">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-brand-700 dark:text-brand-300">Active Retainer</h3>
                    <span class="text-xs text-brand-500 font-medium">${{ number_format($retainer->monthly_value, 0) }}/mo</span>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">{{ $retainer->name }}</p>
                @if($retainer->start_date)
                <p class="text-xs text-gray-400 mt-0.5">Since {{ $retainer->start_date->format('M Y') }}</p>
                @endif
                @if($retainer->description)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">{{ Str::limit($retainer->description, 100) }}</p>
                @endif
            </div>
            @endif

            {{-- Services Interested --}}
            @if($client->services_interested && count($client->services_interested) > 0)
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Services</h3>
                @php
                $serviceLabels = [
                    'web_design'=>'Web Design & Development','seo'=>'SEO & Content',
                    'social_media'=>'Social Media','branding'=>'Branding & Identity',
                    'email_marketing'=>'Email Marketing','ppc'=>'PPC & Paid Ads',
                    'analytics'=>'Analytics & Reporting','ecommerce'=>'E-commerce',
                    'video'=>'Video & Animation','app_dev'=>'App Development',
                    'copywriting'=>'Copywriting','photography'=>'Photography & Creative',
                ];
                @endphp
                <div class="flex flex-wrap gap-1.5">
                    @foreach($client->services_interested as $svc)
                    <span class="inline-flex items-center rounded-full bg-brand-50 dark:bg-brand-500/10 px-2.5 py-1 text-xs font-medium text-brand-700 dark:text-brand-300">
                        {{ $serviceLabels[$svc] ?? $svc }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Internal Notes --}}
            @if($client->notes)
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Internal Notes</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $client->notes }}</p>
            </div>
            @endif

        </div>

        {{-- RIGHT: Activity columns --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Projects --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Projects</h3>
                    <a href="{{ route('agency.projects.create') }}" class="text-xs text-brand-500 hover:text-brand-600 font-medium">+ New Project</a>
                </div>
                @forelse($client->projects as $project)
                @php $sc = ['active'=>'success','on_hold'=>'warning','completed'=>'gray','cancelled'=>'error','draft'=>'blue-light'][$project->status] ?? 'gray'; @endphp
                <a href="{{ route('agency.projects.show', $project) }}" class="flex items-center gap-4 px-5 py-3.5 border-b border-gray-50 dark:border-gray-800/50 last:border-0 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors group">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-brand-500 truncate">{{ $project->name }}</p>
                        @if($project->due_date)
                        <p class="text-xs text-gray-400 mt-0.5">Due {{ $project->due_date->format('M j, Y') }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <div class="hidden sm:block w-20">
                            <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="h-1.5 rounded-full bg-{{ $sc }}-400" style="width:{{ $project->progress }}%"></div>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5 text-right">{{ $project->progress }}%</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-{{ $sc }}-50 text-{{ $sc }}-700 dark:bg-{{ $sc }}-500/10 dark:text-{{ $sc }}-400">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                        <svg class="size-4 text-gray-300 group-hover:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
                @empty
                <div class="px-5 py-8 text-center">
                    <p class="text-sm text-gray-400">No projects yet.</p>
                    <a href="{{ route('agency.projects.create') }}" class="mt-1 inline-block text-xs text-brand-500 hover:underline">Create a project →</a>
                </div>
                @endforelse
            </div>

            {{-- Tickets --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Support Tickets</h3>
                    <a href="{{ route('agency.tickets.create') }}" class="text-xs text-brand-500 hover:text-brand-600 font-medium">+ New Ticket</a>
                </div>
                @forelse($client->tickets as $ticket)
                @php
                    $pc = ['urgent'=>'error','high'=>'warning','medium'=>'blue-light','low'=>'gray'][$ticket->priority] ?? 'gray';
                    $sc2 = ['open'=>'brand','in_progress'=>'warning','resolved'=>'success','closed'=>'gray','on_hold'=>'warning'][$ticket->status] ?? 'gray';
                @endphp
                <a href="{{ route('agency.tickets.show', $ticket) }}" class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-50 dark:border-gray-800/50 last:border-0 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors group">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-brand-500 truncate">{{ $ticket->subject }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $ticket->ticket_number }} · {{ $ticket->created_at->format('M j') }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-{{ $pc }}-50 text-{{ $pc }}-700 dark:bg-{{ $pc }}-500/10 dark:text-{{ $pc }}-400">{{ ucfirst($ticket->priority) }}</span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-{{ $sc2 }}-50 text-{{ $sc2 }}-700 dark:bg-{{ $sc2 }}-500/10 dark:text-{{ $sc2 }}-400">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                        <svg class="size-4 text-gray-300 group-hover:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
                @empty
                <div class="px-5 py-8 text-center"><p class="text-sm text-gray-400">No tickets.</p></div>
                @endforelse
            </div>

            {{-- Invoices --}}
            @if($client->invoices->count() > 0)
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Invoices</h3>
                    <a href="{{ route('agency.invoices.index') }}" class="text-xs text-brand-500 hover:text-brand-600 font-medium">View all →</a>
                </div>
                @foreach($client->invoices->take(5) as $invoice)
                <a href="{{ route('agency.invoices.show', $invoice) }}" class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-50 dark:border-gray-800/50 last:border-0 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors group">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-brand-500">{{ $invoice->invoice_number }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $invoice->issued_date ? $invoice->issued_date->format('M j, Y') : '—' }}</p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($invoice->total, 0) }}</p>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $invoice->status_color }}">{{ ucfirst($invoice->status) }}</span>
                        <svg class="size-4 text-gray-300 group-hover:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
                @endforeach
            </div>
            @endif

            {{-- Deliverables pending approval --}}
            @if($deliverables->count() > 0)
            <div class="rounded-2xl border border-warning-200/60 bg-warning-50/30 dark:border-warning-800/40 dark:bg-warning-500/5 overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-4 border-b border-warning-100 dark:border-warning-800/30">
                    <div class="size-2 rounded-full bg-warning-400 animate-pulse"></div>
                    <h3 class="font-semibold text-warning-700 dark:text-warning-300">Awaiting Approval</h3>
                    <span class="ml-auto inline-flex items-center rounded-full bg-warning-100 px-2 py-0.5 text-xs font-medium text-warning-700">{{ $deliverables->count() }}</span>
                </div>
                @foreach($deliverables as $deliverable)
                <a href="{{ route('agency.deliverables.show', $deliverable) }}" class="flex items-center gap-3 px-5 py-3 border-b border-warning-100/50 dark:border-warning-800/20 last:border-0 hover:bg-warning-50/50 transition-colors group">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-brand-500 truncate">{{ $deliverable->name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $deliverable->project->name }}</p>
                    </div>
                    <svg class="size-4 text-warning-300 group-hover:text-brand-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endforeach
            </div>
            @endif

            {{-- Documents --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Documents & Contracts</h3>
                </div>

                @forelse($client->documents as $doc)
                <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-50 dark:border-gray-800/50 last:border-0">
                    <div class="size-8 rounded-lg bg-{{ $doc->type_color }}-50 dark:bg-{{ $doc->type_color }}-500/10 flex items-center justify-center flex-shrink-0">
                        <svg class="size-4 text-{{ $doc->type_color }}-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $doc->name }}</p>
                        <p class="text-xs text-gray-400">{{ ucfirst($doc->document_type) }}{{ $doc->file_size_formatted ? ' · ' . $doc->file_size_formatted : '' }} · {{ $doc->created_at->format('M j, Y') }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('agency.clients.documents.download', [$client, $doc]) }}" class="rounded-lg border border-gray-200 dark:border-gray-700 px-2.5 py-1.5 text-xs text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800">Download</a>
                        <form method="POST" action="{{ route('agency.clients.documents.destroy', [$client, $doc]) }}" onsubmit="return confirm('Delete this document?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="rounded-lg border border-error-200 px-2.5 py-1.5 text-xs text-error-600 hover:bg-error-50 dark:border-error-800 dark:text-error-400">Delete</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="px-5 py-6 text-center"><p class="text-sm text-gray-400">No documents uploaded yet.</p></div>
                @endforelse

                {{-- Upload form --}}
                <div class="border-t border-gray-100 dark:border-gray-800 px-5 py-4" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="flex items-center gap-2 text-xs font-medium text-brand-500 hover:text-brand-600">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Upload Document
                    </button>
                    <form x-show="open" x-transition method="POST" action="{{ route('agency.clients.documents.store', $client) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2">
                                <input type="file" name="file" required class="block w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-brand-700 hover:file:bg-brand-100">
                            </div>
                            <div>
                                <select name="document_type" class="h-9 w-full rounded-lg border border-gray-300 bg-white dark:bg-gray-900 dark:border-gray-700 px-3 text-sm text-gray-700 dark:text-gray-300 focus:border-brand-400 focus:outline-none">
                                    <option value="contract">Contract</option>
                                    <option value="legal">Legal</option>
                                    <option value="policy">Policy</option>
                                    <option value="proposal">Proposal</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <input type="text" name="notes" placeholder="Notes (optional)" class="h-9 w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-900 px-3 text-sm focus:border-brand-400 focus:outline-none">
                            </div>
                        </div>
                        <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-xs font-medium text-white hover:bg-brand-600">Upload</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
