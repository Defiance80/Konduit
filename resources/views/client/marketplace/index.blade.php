@extends('layouts.app')
@section('title', 'Service Marketplace')

@section('content')
<div x-data="{ requestService: null, showCustom: false }" class="space-y-5">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Service Marketplace</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Browse services and request what you need. We'll follow up with a tailored quote.</p>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-success-50 border border-success-200 px-4 py-3 text-sm text-success-700 dark:bg-success-500/10 dark:border-success-500/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    {{-- Service categories --}}
    @forelse($categories as $category)
    @if($category->services->isNotEmpty())
    <div>
        <div class="flex items-center gap-2 mb-3">
            <span class="inline-block size-2.5 rounded-full" style="background:{{ $category->color }}"></span>
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $category->name }}</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($category->services as $service)
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 flex flex-col">
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ $service->name }}</h3>
                    @if($service->description)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 leading-relaxed">{{ $service->description }}</p>
                    @endif
                    @if($service->features && count($service->features))
                    <ul class="space-y-1 mb-4">
                        @foreach(array_slice($service->features, 0, 4) as $feature)
                        <li class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                            <svg class="size-3.5 text-success-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-800 mt-auto">
                    <span class="text-sm font-bold text-brand-600 dark:text-brand-400">{{ $service->price_formatted }}</span>
                    <button @click="requestService = {{ $service->toJson() }}"
                        class="rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600">
                        Request
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @empty
    <div class="rounded-xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-gray-900">
        <p class="text-gray-500 dark:text-gray-400">No services are available yet. Check back soon.</p>
    </div>
    @endforelse

    {{-- Custom request --}}
    <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/30 p-6 text-center">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Looking for something else?</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Send us a custom request and we'll put together a proposal.</p>
        <button @click="showCustom=true"
            class="rounded-lg border border-brand-300 bg-white px-4 py-2 text-sm font-medium text-brand-600 hover:bg-brand-50 dark:bg-gray-900 dark:border-brand-700 dark:text-brand-400 dark:hover:bg-brand-500/10">
            Submit Custom Request
        </button>
    </div>

    {{-- My Requests --}}
    @if($myRequests->isNotEmpty())
    <div>
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Your Requests</h2>
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 divide-y divide-gray-50 dark:divide-gray-800">
            @foreach($myRequests as $req)
            <div class="flex items-start justify-between px-5 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $req->title }}</p>
                    @if($req->service)
                    <p class="text-xs text-gray-400">Service: {{ $req->service->name }}</p>
                    @endif
                    @if($req->agency_response)
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 italic">"{{ Str::limit($req->agency_response, 80) }}"</p>
                    @endif
                </div>
                <div class="ml-4 flex-shrink-0">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        @if($req->status==='accepted') bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400
                        @elseif($req->status==='quoted') bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400
                        @elseif($req->status==='declined') bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400
                        @elseif($req->status==='reviewing') bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400
                        @else bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400 @endif">
                        {{ ucfirst($req->status) }}
                    </span>
                    @if($req->price_quoted)
                    <p class="text-xs font-semibold text-gray-900 dark:text-white mt-1 text-right">${{ number_format($req->price_quoted, 0) }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- Request Service Modal --}}
<div x-show="requestService !== null" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
    <div @click.outside="requestService=null" class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="requestService?.name ?? 'Request Service'"></h3>
            <button @click="requestService=null" class="text-gray-400 hover:text-gray-600">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('client.marketplace.request') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="service_id" :value="requestService?.id">
            <input type="hidden" name="title" :value="'Request: ' + (requestService?.name ?? '')">
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Message (optional)</label>
                <textarea name="message" rows="4" placeholder="Tell us more about what you need, timelines, or any specific requirements…"
                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" @click="requestService=null"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Custom Request Modal --}}
<div x-show="showCustom" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
    <div @click.outside="showCustom=false" class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Custom Service Request</h3>
            <button @click="showCustom=false" class="text-gray-400 hover:text-gray-600">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('client.marketplace.request') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">What do you need? *</label>
                <input type="text" name="title" required placeholder="e.g. Email campaign for product launch"
                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Details</label>
                <textarea name="message" rows="4" placeholder="Timeline, goals, budget range, references…"
                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showCustom=false"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
