<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Received — {{ $tenant->name }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-full bg-gray-50 flex items-center justify-center p-4">

<div class="w-full max-w-md text-center">
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-8">
        {{-- Success icon --}}
        <div class="inline-flex size-16 items-center justify-center rounded-full bg-success-50 mb-5">
            <svg class="size-8 text-success-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h1 class="text-xl font-semibold text-gray-900 mb-2">We've got it</h1>
        <p class="text-sm text-gray-500 mb-6">
            Your request has been received and our team has been notified.
        </p>

        {{-- Ticket reference --}}
        <div class="rounded-xl bg-gray-50 border border-gray-200 px-5 py-4 mb-6 text-left">
            <p class="text-xs text-gray-400 mb-2">Your reference number</p>
            <p class="text-2xl font-bold text-brand-600">{{ $ticket->ticket_number }}</p>
            <p class="text-xs text-gray-400 mt-1">Keep this for your records.</p>
        </div>

        {{-- AI client message --}}
        @if(!empty($classification['client_message']))
        <div class="rounded-xl bg-brand-50/60 border border-brand-100 px-5 py-4 mb-6 text-left">
            <p class="text-xs font-semibold text-brand-700 uppercase tracking-wide mb-1.5">From the team</p>
            <p class="text-sm text-gray-700 leading-relaxed">{{ $classification['client_message'] }}</p>
        </div>
        @endif

        {{-- Priority badge --}}
        @php
            $priorityColor = match($ticket->priority) {
                'urgent' => 'error', 'high' => 'warning', default => 'blue-light'
            };
        @endphp
        <div class="flex items-center justify-center gap-2 mb-6">
            <span class="inline-flex items-center rounded-full bg-{{ $priorityColor }}-50 border border-{{ $priorityColor }}-100 px-3 py-1 text-xs font-medium text-{{ $priorityColor }}-700">
                {{ ucfirst($ticket->priority) }} priority
            </span>
            <span class="inline-flex items-center rounded-full bg-gray-100 border border-gray-200 px-3 py-1 text-xs font-medium text-gray-600">
                {{ ucfirst(str_replace('_', ' ', $ticket->type)) }}
            </span>
        </div>

        <p class="text-xs text-gray-400">
            Submitted to {{ $tenant->name }}@if($tenant->email) · <a href="mailto:{{ $tenant->email }}" class="text-brand-500 hover:underline">{{ $tenant->email }}</a>@endif
        </p>
    </div>

    <p class="text-center text-xs text-gray-400 mt-6">Powered by <span class="font-medium text-gray-500">Konduit</span></p>
</div>
</body>
</html>
