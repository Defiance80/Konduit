@extends('layouts.app')
@section('title', $invoice->invoice_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Back + Actions --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('agency.invoices.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</h1>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $invoice->status_color }}">{{ ucfirst($invoice->status) }}</span>
            </div>
        </div>
        <div class="flex gap-2">
            @if($invoice->status === 'draft')
            <form action="{{ route('agency.invoices.sent', $invoice) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Mark Sent
                </button>
            </form>
            @endif
            @if(in_array($invoice->status, ['sent','viewed','overdue']))
            <form action="{{ route('agency.invoices.paid', $invoice) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="rounded-lg bg-success-600 px-4 py-2 text-sm font-medium text-white hover:bg-success-700">
                    Mark Paid
                </button>
            </form>
            @endif
            @if(!in_array($invoice->status, ['paid','void']))
            <form action="{{ route('agency.invoices.void', $invoice) }}" method="POST" onsubmit="return confirm('Void this invoice?')">
                @csrf @method('PATCH')
                <button type="submit" class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400">
                    Void
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Invoice Card --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex justify-between">
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Client</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $invoice->client->name }}</p>
                @if($invoice->project)
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $invoice->project->name }}</p>
                @endif
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400 mb-0.5">Invoice</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Issued: {{ $invoice->issued_date->format('M j, Y') }}</p>
                <p class="text-sm {{ $invoice->isOverdue() ? 'text-error-600 dark:text-error-400 font-semibold' : 'text-gray-500 dark:text-gray-400' }}">
                    Due: {{ $invoice->due_date->format('M j, Y') }}
                </p>
            </div>
        </div>

        {{-- Items --}}
        <div class="px-6 py-4">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                        <th class="pb-3">Description</th>
                        <th class="pb-3 text-right w-16">Qty</th>
                        <th class="pb-3 text-right w-24">Unit Price</th>
                        <th class="pb-3 text-right w-24">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="py-3 text-sm text-gray-700 dark:text-gray-300">{{ $item->description }}</td>
                        <td class="py-3 text-sm text-gray-600 dark:text-gray-400 text-right">{{ $item->quantity }}</td>
                        <td class="py-3 text-sm text-gray-600 dark:text-gray-400 text-right">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-3 text-sm font-medium text-gray-900 dark:text-white text-right">${{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Totals --}}
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 space-y-2 max-w-xs ml-auto">
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>Subtotal</span><span>${{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                @if($invoice->tax_rate > 0)
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>Tax ({{ $invoice->tax_rate }}%)</span><span>${{ number_format($invoice->tax_amount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-base font-bold text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span>Total</span><span>${{ number_format($invoice->total, 2) }}</span>
                </div>
                @if($invoice->status === 'paid' && $invoice->paid_at)
                <div class="flex justify-between text-sm text-success-600 dark:text-success-400 font-medium pt-1">
                    <span>Paid</span><span>{{ $invoice->paid_at->format('M j, Y') }}</span>
                </div>
                @endif
            </div>
        </div>

        @if($invoice->notes)
        <div class="px-6 pb-5 pt-0 border-t border-gray-100 dark:border-gray-800 mt-4">
            <p class="text-xs font-medium text-gray-500 mb-1">Notes</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $invoice->notes }}</p>
        </div>
        @endif
    </div>

    {{-- Delete --}}
    @if(in_array($invoice->status, ['draft','void']))
    <div class="flex justify-end">
        <form action="{{ route('agency.invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Delete this invoice?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-sm text-error-600 hover:text-error-700 dark:text-error-400">Delete Invoice</button>
        </form>
    </div>
    @endif

</div>
@endsection
