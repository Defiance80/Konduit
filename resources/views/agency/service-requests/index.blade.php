@extends('layouts.app')
@section('title', 'Service Requests')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Service Requests</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Client requests from the Marketplace.</p>
        </div>
        @if($pendingCount)
        <div class="flex items-center gap-2 rounded-lg bg-warning-50 dark:bg-warning-500/10 border border-warning-200 dark:border-warning-500/20 px-3 py-1.5">
            <span class="size-2 rounded-full bg-warning-500 animate-pulse"></span>
            <span class="text-xs font-medium text-warning-700 dark:text-warning-400">{{ $pendingCount }} pending review</span>
        </div>
        @endif
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-success-50 border border-success-200 px-4 py-3 text-sm text-success-700 dark:bg-success-500/10 dark:border-success-500/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quote</th>
                    <th class="relative px-4 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800" x-data="{ openId: null }">
                @forelse($requests as $req)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                    <td class="px-4 py-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $req->title }}</p>
                        @if($req->message)
                        <p class="text-xs text-gray-400 truncate max-w-xs">{{ Str::limit($req->message, 60) }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-0.5">{{ $req->created_at->format('M j, Y') }}</p>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $req->client->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $req->service?->name ?? 'Custom' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            @if($req->status==='accepted') bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400
                            @elseif($req->status==='quoted') bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400
                            @elseif($req->status==='declined') bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400
                            @elseif($req->status==='reviewing') bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400
                            @else bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400 @endif">
                            {{ ucfirst($req->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $req->price_quoted ? '$'.number_format($req->price_quoted,0) : '—' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button @click="openId === {{ $req->id }} ? openId=null : openId={{ $req->id }}"
                            class="text-sm text-brand-600 dark:text-brand-400 hover:underline">Respond</button>
                    </td>
                </tr>
                <tr x-show="openId === {{ $req->id }}" class="bg-gray-50 dark:bg-gray-800/30">
                    <td colspan="6" class="px-4 pb-4 pt-1">
                        <form action="{{ route('agency.service-requests.update', $req) }}" method="POST" class="flex flex-wrap gap-3 items-end">
                            @csrf @method('PATCH')
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <select name="status" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                                    @foreach(['pending','reviewing','quoted','accepted','declined'] as $s)
                                    <option value="{{ $s }}" @selected($req->status===$s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Price Quote</label>
                                <input type="number" name="price_quoted" value="{{ $req->price_quoted }}" min="0" step="0.01" placeholder="0.00"
                                    class="w-32 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Response</label>
                                <input type="text" name="agency_response" value="{{ $req->agency_response }}" placeholder="Response to send to client…"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                            </div>
                            <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Save</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-400 italic">No service requests yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $requests->links() }}

</div>
@endsection
