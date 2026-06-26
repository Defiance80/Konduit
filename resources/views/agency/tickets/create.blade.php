@extends('layouts.app')
@section('title', 'New Ticket')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('agency.tickets.index') }}" class="text-gray-400 hover:text-gray-600"><svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">New Ticket</h1>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <form method="POST" action="{{ route('agency.tickets.store') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Subject <span class="text-error-500">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required placeholder="Briefly describe the issue"
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Client <span class="text-error-500">*</span></label>
                    <select name="client_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Select client...</option>
                        @foreach($clients as $c)<option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Project (optional)</label>
                    <select name="project_id" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">No project</option>
                        @foreach($projects as $p)<option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                    <select name="type" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        @foreach(['task','bug','feature','question','change_request'] as $t)<option value="{{ $t }}" {{ old('type','task') === $t ? 'selected' : '' }}>{{ str_replace('_',' ',ucfirst($t)) }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                    <select name="priority" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        @foreach(['low','medium','high','urgent'] as $p)<option value="{{ $p }}" {{ old('priority','medium') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Assign To</label>
                    <select name="assignee_id" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Unassigned</option>
                        @foreach($agents as $agent)<option value="{{ $agent->id }}" {{ old('assignee_id') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>@endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Description <span class="text-error-500">*</span></label>
                    <textarea name="description" rows="5" required
                              class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Describe the issue in detail...">{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('agency.tickets.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Cancel</a>
                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Create Ticket</button>
            </div>
        </form>
    </div>
</div>
@endsection
