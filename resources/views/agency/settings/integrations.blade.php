@extends('layouts.app')
@section('title', 'Integrations')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Integrations</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Connect your agency tools to centralise data and workflows.</p>
        </div>
        <a href="{{ route('agency.settings.index') }}"
            class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            ← Back to Settings
        </a>
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:bg-success-500/10 dark:border-success-500/20 dark:text-success-400">{{ session('success') }}</div>
    @endif

    @php
    $services = [
        'google_analytics' => [
            'name' => 'Google Analytics',
            'group' => 'Google',
            'desc' => 'Pull website traffic, user behaviour, and goal conversion data.',
            'fields' => [['key' => 'api_key', 'label' => 'Measurement ID', 'placeholder' => 'G-XXXXXXXXXX']],
            'icon_color' => '#EA4335',
            'icon' => '<path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>',
        ],
        'google_search_console' => [
            'name' => 'Search Console',
            'group' => 'Google',
            'desc' => 'Import keyword rankings, impressions, clicks, and page positions.',
            'fields' => [['key' => 'api_key', 'label' => 'Site URL / API Key', 'placeholder' => 'https://yoursite.com']],
            'icon_color' => '#34A853',
            'icon' => '<rect width="20" height="20" rx="3" fill="#34A853"/><path d="M10 4l6 12H4L10 4z" fill="white"/>',
        ],
        'asana' => [
            'name' => 'Asana',
            'group' => 'Project Management',
            'desc' => 'Sync tasks and projects from your Asana workspace.',
            'fields' => [
                ['key' => 'api_token', 'label' => 'Personal Access Token', 'placeholder' => '0/xxxxx'],
                ['key' => 'workspace_id', 'label' => 'Workspace ID (optional)', 'placeholder' => '123456789'],
            ],
            'icon_color' => '#F06A6A',
            'icon' => '<circle cx="12" cy="5" r="4" fill="#F06A6A"/><circle cx="5.5" cy="17" r="4" fill="#F06A6A"/><circle cx="18.5" cy="17" r="4" fill="#F06A6A"/>',
        ],
        'monday' => [
            'name' => 'Monday.com',
            'group' => 'Project Management',
            'desc' => 'Import boards, items and status from Monday.com.',
            'fields' => [
                ['key' => 'api_token', 'label' => 'API Token', 'placeholder' => 'eyJhbGci...'],
                ['key' => 'workspace_id', 'label' => 'Board ID (optional)', 'placeholder' => '123456'],
            ],
            'icon_color' => '#FF3D57',
            'icon' => '<rect width="24" height="24" rx="6" fill="#FF3D57"/><circle cx="7" cy="12" r="3" fill="white"/><circle cx="12" cy="12" r="3" fill="white"/><circle cx="17" cy="12" r="3" fill="white"/>',
        ],
        'motion' => [
            'name' => 'Motion',
            'group' => 'Project Management',
            'desc' => 'Sync AI-scheduled tasks and planned work from Motion.',
            'fields' => [['key' => 'api_token', 'label' => 'API Key', 'placeholder' => 'motion_sk_...']],
            'icon_color' => '#7C3AED',
            'icon' => '<rect width="24" height="24" rx="6" fill="#7C3AED"/><path d="M5 12l5 5L19 7" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>',
        ],
        'harvest' => [
            'name' => 'Harvest',
            'group' => 'Time & Finance',
            'desc' => 'Pull time entries, invoices and expenses from Harvest.',
            'fields' => [
                ['key' => 'account_id', 'label' => 'Account ID', 'placeholder' => '123456'],
                ['key' => 'api_token', 'label' => 'Personal Access Token', 'placeholder' => 'harvest_sk_...'],
            ],
            'icon_color' => '#FA5D00',
            'icon' => '<circle cx="12" cy="12" r="10" fill="#FA5D00"/><path d="M12 6v6l4 2" stroke="white" stroke-width="2" stroke-linecap="round"/>',
        ],
        'slack' => [
            'name' => 'Slack',
            'group' => 'Communication',
            'desc' => 'Send Konduit notifications (new tickets, approvals) to Slack.',
            'fields' => [['key' => 'webhook_url', 'label' => 'Incoming Webhook URL', 'placeholder' => 'https://hooks.slack.com/services/...']],
            'icon_color' => '#611F69',
            'icon' => '<path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313zM8.834 5.042a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zM8.834 6.313a2.528 2.528 0 0 1 2.521 2.521 2.528 2.528 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312zM18.956 8.834a2.528 2.528 0 0 1 2.522-2.521A2.528 2.528 0 0 1 24 8.834a2.528 2.528 0 0 1-2.522 2.521h-2.522V8.834zM17.688 8.834a2.528 2.528 0 0 1-2.523 2.521 2.527 2.527 0 0 1-2.52-2.521V2.522A2.527 2.527 0 0 1 15.165 0a2.528 2.528 0 0 1 2.523 2.522v6.312zM15.165 18.956a2.528 2.528 0 0 1 2.523 2.522A2.528 2.528 0 0 1 15.165 24a2.527 2.527 0 0 1-2.52-2.522v-2.522h2.52zM15.165 17.688a2.527 2.527 0 0 1-2.52-2.523 2.526 2.526 0 0 1 2.52-2.52h6.313A2.527 2.527 0 0 1 24 15.165a2.528 2.528 0 0 1-2.522 2.523h-6.313z" fill="#611F69"/>',
        ],
        'zapier' => [
            'name' => 'Zapier',
            'group' => 'Automation',
            'desc' => 'Trigger Zapier zaps from Konduit events via webhook.',
            'fields' => [['key' => 'webhook_url', 'label' => 'Zap Webhook URL', 'placeholder' => 'https://hooks.zapier.com/hooks/...']],
            'icon_color' => '#FF4A00',
            'icon' => '<rect width="24" height="24" rx="5" fill="#FF4A00"/><path d="M12 4l2 6h6l-5 3.5 2 6L12 16l-5 3.5 2-6L4 10h6L12 4z" fill="white"/>',
        ],
        'mailchimp' => [
            'name' => 'Mailchimp',
            'group' => 'Marketing',
            'desc' => 'Sync contacts and email campaign performance.',
            'fields' => [
                ['key' => 'api_key', 'label' => 'API Key', 'placeholder' => 'xxxxx-us1'],
                ['key' => 'workspace_id', 'label' => 'List ID (optional)', 'placeholder' => 'abc123'],
            ],
            'icon_color' => '#FFE01B',
            'icon' => '<rect width="24" height="24" rx="12" fill="#FFE01B"/><path d="M12 8c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4z" fill="#000"/>',
        ],
        'hubspot' => [
            'name' => 'HubSpot',
            'group' => 'CRM',
            'desc' => 'Import deals, contacts and lead activity from HubSpot CRM.',
            'fields' => [['key' => 'api_token', 'label' => 'Private App Token', 'placeholder' => 'pat-na1-...']],
            'icon_color' => '#FF7A59',
            'icon' => '<rect width="24" height="24" rx="6" fill="#FF7A59"/><path d="M15 8.5a2 2 0 100-4 2 2 0 000 4zM9 12a3 3 0 100 6 3 3 0 000-6z" fill="white"/><path d="M14 10.5l-3 2.5" stroke="white" stroke-width="1.5"/>',
        ],
    ];

    $groups = collect($services)->groupBy('group');
    @endphp

    @foreach($groups as $groupName => $groupServices)
    <div>
        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">{{ $groupName }}</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($groupServices as $serviceKey => $service)
            @php
                $connected = isset($integrations[$serviceKey]);
                $connectedAt = $connected ? ($integrations[$serviceKey]['connected_at'] ?? null) : null;
            @endphp
            <div x-data="{ open: {{ $connected ? 'true' : 'false' }} }"
                class="rounded-2xl border bg-white dark:bg-gray-900 overflow-hidden transition-all {{ $connected ? 'border-brand-200 dark:border-brand-800' : 'border-gray-200 dark:border-gray-800' }}">

                {{-- Header --}}
                <div class="flex items-center gap-4 px-5 py-4">
                    <div class="size-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:{{ $service['icon_color'] }}15">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none">
                            {!! $service['icon'] !!}
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $service['name'] }}</p>
                            @if($connected)
                            <span class="inline-flex items-center gap-1 rounded-full bg-success-50 px-1.5 py-0.5 text-[10px] font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
                                <span class="size-1.5 rounded-full bg-success-500"></span> Connected
                            </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-1">{{ $service['desc'] }}</p>
                    </div>
                    <button @click="open=!open" class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="size-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>

                {{-- Expandable form --}}
                <div x-show="open" x-cloak class="px-5 pb-5 border-t border-gray-100 dark:border-gray-800 pt-4">
                    @if($connected && $connectedAt)
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">
                        Connected {{ \Carbon\Carbon::parse($connectedAt)->diffForHumans() }}
                    </p>
                    @endif

                    <form action="{{ route('agency.settings.integrations.save', $serviceKey) }}" method="POST" class="space-y-3">
                        @csrf
                        @foreach($service['fields'] as $field)
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">{{ $field['label'] }}</label>
                            <input type="{{ str_contains(strtolower($field['key']), 'token') || str_contains(strtolower($field['key']), 'key') ? 'password' : 'text' }}"
                                name="{{ $field['key'] }}"
                                placeholder="{{ $field['placeholder'] }}"
                                value="{{ $connected ? str_repeat('•', min(16, strlen($integrations[$serviceKey][$field['key']] ?? ''))) : '' }}"
                                autocomplete="off"
                                class="h-9 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-brand-300 focus:outline-none focus:ring-1 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                        @endforeach

                        <div class="flex items-center gap-2 pt-1">
                            <button type="submit"
                                class="flex-1 rounded-lg bg-brand-500 px-3 py-2 text-xs font-medium text-white hover:bg-brand-600 text-center">
                                {{ $connected ? 'Update' : 'Connect' }}
                            </button>
                            @if($connected)
                            <form action="{{ route('agency.settings.integrations.remove', $serviceKey) }}" method="POST" class="flex-shrink-0">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Disconnect {{ $service['name'] }}?')"
                                    class="rounded-lg border border-error-200 px-3 py-2 text-xs text-error-600 hover:bg-error-50 dark:border-error-800 dark:text-error-400">
                                    Disconnect
                                </button>
                            </form>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

</div>
@endsection
