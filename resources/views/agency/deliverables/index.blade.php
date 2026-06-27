@extends('layouts.app')
@section('title', 'Deliverables')

@section('content')
<div x-data="{ showCreate: false }" class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Deliverables</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Track and manage client approvals across all projects.</p>
        </div>
        <button @click="showCreate=true"
            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Deliverable
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach([
            ['label'=>'In Progress', 'count'=>$stats['pending'],   'color'=>'warning', 'icon'=>'clock'],
            ['label'=>'Awaiting Approval', 'count'=>$stats['in_review'], 'color'=>'blue',    'icon'=>'eye'],
            ['label'=>'Approved',    'count'=>$stats['approved'],  'color'=>'success', 'icon'=>'check'],
            ['label'=>'Changes Requested', 'count'=>$stats['rejected'],  'color'=>'error',   'icon'=>'x'],
        ] as $s)
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ $s['label'] }}</p>
            <p class="text-2xl font-bold text-{{ $s['color'] }}-600 dark:text-{{ $s['color'] }}-400">{{ $s['count'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('agency.deliverables.index') }}" class="flex flex-wrap gap-3 items-center">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search deliverables…"
            class="rounded-lg border border-gray-200 bg-white px-3.5 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white w-48 focus:outline-none focus:ring-1 focus:ring-brand-500">

        <select name="status" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="">All Statuses</option>
            <option value="pending"   @selected(request('status')==='pending')>In Progress</option>
            <option value="in_review" @selected(request('status')==='in_review')>Awaiting Approval</option>
            <option value="approved"  @selected(request('status')==='approved')>Approved</option>
            <option value="rejected"  @selected(request('status')==='rejected')>Changes Requested</option>
            <option value="delivered" @selected(request('status')==='delivered')>Delivered</option>
        </select>

        <select name="project" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="">All Projects</option>
            @foreach($projects as $p)
            <option value="{{ $p->id }}" @selected(request('project')==$p->id)>{{ $p->name }}</option>
            @endforeach
        </select>

        <select name="client" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="">All Clients</option>
            @foreach($clients as $c)
            <option value="{{ $c->id }}" @selected(request('client')==$c->id)>{{ $c->name }}</option>
            @endforeach
        </select>

        <button type="submit" class="rounded-lg bg-gray-100 hover:bg-gray-200 px-3 py-2 text-sm font-medium dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-white">Filter</button>
        @if(request()->hasAny(['q','status','project','client']))
        <a href="{{ route('agency.deliverables.index') }}" class="text-sm text-gray-400 hover:text-gray-600">Clear</a>
        @endif
    </form>

    @if(session('success'))
    <div class="rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    {{-- Deliverables Table --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800 text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide">
                    <th class="px-5 py-3 text-left font-medium">Deliverable</th>
                    <th class="px-5 py-3 text-left font-medium hidden md:table-cell">Project / Client</th>
                    <th class="px-5 py-3 text-left font-medium hidden lg:table-cell">Due</th>
                    <th class="px-5 py-3 text-left font-medium">Status</th>
                    <th class="px-5 py-3 text-left font-medium hidden lg:table-cell">Version</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                @forelse($deliverables as $d)
                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 group transition-colors">
                    <td class="px-5 py-3.5">
                        <a href="{{ route('agency.deliverables.show', $d) }}" class="font-medium text-gray-900 dark:text-white hover:text-brand-500">{{ $d->name }}</a>
                        @if($d->isOverdue())
                        <span class="ml-2 text-xs text-error-500 font-medium">Overdue</span>
                        @endif
                        @if($d->hasFile())
                        <span class="ml-1 text-gray-300 dark:text-gray-600">
                            <svg class="size-3.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell">
                        <span class="text-gray-700 dark:text-gray-300">{{ $d->project->name ?? '—' }}</span>
                        <span class="text-gray-400 mx-1">·</span>
                        <span class="text-gray-400">{{ $d->client->name ?? '—' }}</span>
                    </td>
                    <td class="px-5 py-3.5 hidden lg:table-cell">
                        <span class="{{ $d->isOverdue() ? 'text-error-500 font-medium' : 'text-gray-400' }}">
                            {{ $d->due_date ? $d->due_date->format('M j, Y') : '—' }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $d->status_color }}">
                            {{ $d->status_label }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 hidden lg:table-cell text-gray-400 text-xs">v{{ $d->version }}</td>
                    <td class="px-5 py-3.5">
                        <a href="{{ route('agency.deliverables.show', $d) }}"
                            class="opacity-0 group-hover:opacity-100 transition-opacity text-gray-400 hover:text-brand-500">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <svg class="mx-auto size-10 text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">No deliverables found</p>
                        <button @click="showCreate=true" class="mt-3 text-sm text-brand-500 hover:text-brand-600 font-medium">Create your first deliverable</button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($deliverables->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800">
            {{ $deliverables->links() }}
        </div>
        @endif
    </div>

    {{-- Create Modal --}}
    <div x-show="showCreate" x-cloak class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-16" style="background: rgba(0,0,0,0.5);">
        <div @click.away="showCreate=false" class="w-full max-w-lg rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-900">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">New Deliverable</h3>
                <button @click="showCreate=false" class="text-gray-400 hover:text-gray-600">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('agency.deliverables.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Name</label>
                    <input type="text" name="name" required placeholder="Homepage redesign mockup"
                        class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Project</label>
                        <select name="project_id" required
                            class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="">Select project…</option>
                            @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Client</label>
                        <select name="client_id" required
                            class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="">Select client…</option>
                            @foreach($clients as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Description</label>
                    <textarea name="description" rows="2" placeholder="What is this deliverable?"
                        class="w-full rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Due Date</label>
                        <input type="date" name="due_date"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">External Link</label>
                        <input type="url" name="file_url" placeholder="https://figma.com/…"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Upload File <span class="text-gray-300">(optional, max 50MB)</span></label>
                    <input type="file" name="file"
                        class="w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-medium file:text-brand-600 hover:file:bg-brand-100 dark:file:bg-brand-500/10 dark:file:text-brand-400">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showCreate=false"
                        class="flex-1 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                        Create Deliverable
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
