@extends('layouts.app')
@section('title', 'New Invoice')

@section('content')
<div x-data="{
    items: [{ description: '', quantity: 1, unit_price: '' }],
    taxRate: 0,
    get subtotal() { return this.items.reduce((s,i) => s + (parseFloat(i.quantity)||0)*(parseFloat(i.unit_price)||0), 0) },
    get taxAmount() { return this.subtotal * (parseFloat(this.taxRate)||0) / 100 },
    get total() { return this.subtotal + this.taxAmount },
    addItem() { this.items.push({ description: '', quantity: 1, unit_price: '' }) },
    removeItem(i) { if(this.items.length > 1) this.items.splice(i,1) }
}" class="max-w-3xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('agency.invoices.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">New Invoice</h1>
    </div>

    <form action="{{ route('agency.invoices.store') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Meta --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 space-y-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Invoice Details</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Client *</label>
                    <select name="client_id" required class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="">Select Client</option>
                        @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ old('client_id')==$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Project (optional)</label>
                    <select name="project_id" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="">No Project</option>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Issue Date *</label>
                    <input type="date" name="issued_date" required value="{{ old('issued_date', now()->format('Y-m-d')) }}"
                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date *</label>
                    <input type="date" name="due_date" required value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}"
                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tax Rate (%)</label>
                    <input type="number" name="tax_rate" x-model="taxRate" min="0" max="100" step="0.01" value="0"
                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (optional)</label>
                <textarea name="notes" rows="2" placeholder="Payment terms, bank details, thank-you note…"
                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500"></textarea>
            </div>
        </div>

        {{-- Line Items --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 space-y-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Line Items</h3>

            <template x-for="(item, index) in items" :key="index">
                <div class="flex gap-3 items-start">
                    <div class="flex-1">
                        <input type="text" :name="`items[${index}][description]`" x-model="item.description"
                            placeholder="Description" required
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                    </div>
                    <div class="w-20">
                        <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity"
                            placeholder="Qty" min="0.01" step="0.01" required
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                    </div>
                    <div class="w-28">
                        <input type="number" :name="`items[${index}][unit_price]`" x-model="item.unit_price"
                            placeholder="Unit $" min="0" step="0.01" required
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                    </div>
                    <div class="w-24 py-2 text-right text-sm font-medium text-gray-700 dark:text-gray-300">
                        $<span x-text="((parseFloat(item.quantity)||0)*(parseFloat(item.unit_price)||0)).toFixed(2)"></span>
                    </div>
                    <button type="button" @click="removeItem(index)" class="py-2 text-gray-400 hover:text-error-500">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>

            <button type="button" @click="addItem()"
                class="text-sm text-brand-600 dark:text-brand-400 hover:underline flex items-center gap-1">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Line Item
            </button>

            {{-- Totals --}}
            <div class="border-t border-gray-100 dark:border-gray-800 pt-3 space-y-1.5">
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>Subtotal</span>
                    <span>$<span x-text="subtotal.toFixed(2)"></span></span>
                </div>
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>Tax (<span x-text="taxRate"></span>%)</span>
                    <span>$<span x-text="taxAmount.toFixed(2)"></span></span>
                </div>
                <div class="flex justify-between text-base font-bold text-gray-900 dark:text-white pt-1 border-t border-gray-100 dark:border-gray-800">
                    <span>Total</span>
                    <span>$<span x-text="total.toFixed(2)"></span></span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('agency.invoices.index') }}"
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                Cancel
            </a>
            <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2 text-sm font-medium text-white hover:bg-brand-600">
                Create Invoice
            </button>
        </div>
    </form>

</div>
@endsection
