@extends('layouts.app')
@section('title', 'New Retainer')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('agency.retainers.index') }}" class="text-gray-400 hover:text-gray-600"><svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">New Retainer</h1>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <form method="POST" action="{{ route('agency.retainers.store') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Retainer Name <span class="text-error-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Monthly SEO Retainer"
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
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Monthly Value ($)</label>
                    <input type="number" name="monthly_value" value="{{ old('monthly_value') }}" min="0" step="0.01"
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="2000">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Hours Included</label>
                    <input type="number" name="hours_included" value="{{ old('hours_included') }}" min="0"
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="20">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date <span class="text-error-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        @foreach(['draft','active','paused'] as $s)<option value="{{ $s }}" {{ old('status','draft') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Billing Cycle</label>
                    <select name="billing_cycle" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        @foreach(['monthly','quarterly','annually'] as $b)<option value="{{ $b }}" {{ old('billing_cycle','monthly') === $b ? 'selected' : '' }}>{{ ucfirst($b) }}</option>@endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="What's included in this retainer?">{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('agency.retainers.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Cancel</a>
                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Create Retainer</button>
            </div>
        </form>
    </div>
</div>
@endsection
