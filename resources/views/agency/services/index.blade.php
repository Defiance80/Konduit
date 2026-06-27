@extends('layouts.app')
@section('title', 'Service Library')

@section('content')
<div x-data="{ showAddService: false, showAddCategory: false, editService: null }" class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Service Library</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Define what your agency offers. Clients browse this in the Marketplace.</p>
        </div>
        <div class="flex gap-2">
            <button @click="showAddCategory=true"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                + Category
            </button>
            <button @click="showAddService=true"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Service
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-success-50 border border-success-200 px-4 py-3 text-sm text-success-700 dark:bg-success-500/10 dark:border-success-500/20 dark:text-success-400">
        {{ session('success') }}
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3">
        @foreach([
            ['label'=>'Total Services', 'count'=>$stats['total'],  'color'=>'gray'],
            ['label'=>'Active',         'count'=>$stats['active'], 'color'=>'success'],
            ['label'=>'Draft',          'count'=>$stats['draft'],  'color'=>'warning'],
        ] as $s)
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ $s['label'] }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $s['count'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Service Categories --}}
    @forelse($categories as $category)
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center gap-3 px-5 py-3 border-b border-gray-100 dark:border-gray-800">
            <span class="inline-block size-3 rounded-full" style="background:{{ $category->color }}"></span>
            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $category->name }}</h3>
            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $category->services->count() }} services</span>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-800">
            @forelse($category->services as $service)
            <div class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->name }}</p>
                        @if($service->status !== 'active')
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $service->status === 'draft' ? 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400' : 'bg-gray-100 text-gray-500' }}">
                            {{ ucfirst($service->status) }}
                        </span>
                        @endif
                    </div>
                    @if($service->description)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $service->description }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-4 ml-4">
                    <span class="text-sm font-semibold text-brand-600 dark:text-brand-400">{{ $service->price_formatted }}</span>
                    @if($service->estimated_hours)
                    <span class="text-xs text-gray-400">~{{ $service->estimated_hours }}h</span>
                    @endif
                    <button @click="editService = {{ $service->toJson() }}"
                        class="text-xs text-gray-400 hover:text-brand-600 dark:hover:text-brand-400">Edit</button>
                    <form action="{{ route('agency.services.destroy', $service) }}" method="POST"
                        onsubmit="return confirm('Remove this service?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-error-500 hover:text-error-700">Remove</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="px-5 py-4 text-sm text-gray-400 italic">No services in this category yet.</p>
            @endforelse
        </div>
    </div>
    @empty
    {{-- no categories --}}
    @endforelse

    {{-- Uncategorised --}}
    @if($uncategorised->isNotEmpty())
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center gap-3 px-5 py-3 border-b border-gray-100 dark:border-gray-800">
            <h3 class="font-semibold text-gray-500 dark:text-gray-400">Uncategorised</h3>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-800">
            @foreach($uncategorised as $service)
            <div class="flex items-center justify-between px-5 py-4">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->name }}</p>
                    @if($service->description)
                    <p class="text-xs text-gray-500 mt-0.5">{{ $service->description }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm font-semibold text-brand-600 dark:text-brand-400">{{ $service->price_formatted }}</span>
                    <form action="{{ route('agency.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Remove?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-error-500">Remove</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($categories->isEmpty() && $uncategorised->isEmpty())
    <div class="rounded-xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-gray-900">
        <div class="mx-auto size-12 rounded-full bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center mb-4">
            <svg class="size-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">No services yet</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Add services to your library so clients can request them from the Marketplace.</p>
        <button @click="showAddService=true" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
            Add First Service
        </button>
    </div>
    @endif
</div>

{{-- Add Service Modal --}}
<div x-show="showAddService" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
    <div @click.outside="showAddService=false" class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add Service</h3>
            <button @click="showAddService=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('agency.services.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Service Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Monthly SEO Retainer"
                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Short Description</label>
                    <textarea name="description" rows="2" placeholder="What this service includes at a high level…"
                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                    <select name="category_id" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="">No Category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Price Type</label>
                    <select name="price_type" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="fixed">Fixed Price</option>
                        <option value="hourly">Per Hour</option>
                        <option value="monthly">Per Month</option>
                        <option value="custom">Custom Quote</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Price</label>
                    <input type="number" name="price" step="0.01" min="0" placeholder="0.00"
                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Est. Hours</label>
                    <input type="number" name="estimated_hours" step="0.5" min="0" placeholder="0"
                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">What's Included (one per line)</label>
                    <textarea name="features" rows="3" placeholder="Keyword research&#10;On-page optimisation&#10;Monthly reporting"
                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="active">Active (visible in marketplace)</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="showAddService=false"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                    Add Service
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Add Category Modal --}}
<div x-show="showAddCategory" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
    <div @click.outside="showAddCategory=false" class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-900 shadow-xl p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add Category</h3>
        <form action="{{ route('agency.service-categories.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Category Name *</label>
                <input type="text" name="name" required placeholder="e.g. SEO, Design, Social Media"
                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                <input type="color" name="color" value="#6366f1" class="h-9 w-full rounded-lg border border-gray-200 p-0.5 cursor-pointer">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="showAddCategory=false"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
