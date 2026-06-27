@extends('layouts.app')
@section('title', 'Invoices')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Invoices</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Create and track client invoices.</p>
        </div>
        <a href="{{ route('agency.invoices.create') }}"
            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Invoice
        </a>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-success-50 border border-success-200 px-4 py-3 text-sm text-success-700 dark:bg-success-500/10 dark:border-success-500/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs text-gray-400 mb-1">Drafts</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['draft'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs text-gray-400 mb-1">Sent / Viewed</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['sent'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs text-gray-400 mb-1">Overdue</p>
            <p class="text-2xl font-bold text-error-600 dark:text-error-400">{{ $stats['overdue'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs text-gray-400 mb-1">Paid This Month</p>
            <p class="text-2xl font-bold text-success-600 dark:text-success-400">${{ number_format($stats['paid_month'], 0) }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('agency.invoices.index') }}" class="flex flex-wrap gap-3">
        <select name="status" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="">All Statuses</option>
            @foreach(['draft','sent','viewed','paid','overdue','void'] as $s)
            <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="client" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="">All Clients</option>
            @foreach($clients as $c)
            <option value="{{ $c->id }}" @selected(request('client')==$c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Filter</button>
        @if(request()->hasAny(['status','client']))
        <a href="{{ route('agency.invoices.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due</th>
                    <th class="relative px-4 py-3"><span class="sr-only">View</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                    <td class="px-4 py-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</p>
                        <p class="text-xs text-gray-400">{{ $invoice->issued_date->format('M j, Y') }}</p>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $invoice->client->name }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $invoice->status_color }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                        @if($invoice->isOverdue() && $invoice->status !== 'overdue')
                        <span class="ml-1 text-xs text-error-500">Overdue</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($invoice->total, 2) }}</td>
                    <td class="px-4 py-3 text-sm {{ $invoice->isOverdue() ? 'text-error-600 dark:text-error-400 font-medium' : 'text-gray-500 dark:text-gray-400' }}">
                        {{ $invoice->due_date->format('M j, Y') }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('agency.invoices.show', $invoice) }}"
                            class="text-sm text-brand-600 dark:text-brand-400 hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-400 italic">
                        No invoices yet. <a href="{{ route('agency.invoices.create') }}" class="text-brand-600 hover:underline">Create one</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $invoices->links() }}

</div>
@endsection
