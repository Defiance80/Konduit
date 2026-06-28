@extends('layouts.app')
@section('title', 'Settings')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Settings</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your agency profile and account preferences.</p>
    </div>

    @if(session('success'))
    <div class="rounded-lg border border-success-200 bg-success-50 p-4 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="rounded-lg border border-error-200 bg-error-50 p-4 text-sm text-error-700 dark:border-error-800 dark:bg-error-900/20 dark:text-error-400">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Sidebar nav --}}
        <div class="lg:col-span-1">
            <nav class="space-y-1 rounded-2xl border border-gray-200 bg-white p-2 dark:border-gray-800 dark:bg-gray-900">
                @foreach([
                    ['agency',    'Agency Profile', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16'],
                    ['profile',   'Your Profile',   'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    ['password',  'Password',       'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                    ['dashboard', 'Dashboard',      'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'],
                ] as [$tab, $label, $icon])
                <a href="#{{ $tab }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white transition-colors">
                    <svg class="size-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"/>
                    </svg>
                    {{ $label }}
                </a>
                @endforeach
                <a href="{{ route('agency.settings.integrations') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white transition-colors">
                    <svg class="size-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    Integrations
                </a>
            </nav>

            {{-- Plan info --}}
            <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Current Plan</p>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white capitalize">{{ $tenant->plan ?? 'Professional' }}</span>
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400">Active</span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Agency: {{ $tenant->name }}</p>
                <a href="#" class="block w-full text-center rounded-lg border border-brand-200 px-3 py-2 text-xs font-medium text-brand-600 hover:bg-brand-50 dark:border-brand-800 dark:text-brand-400 dark:hover:bg-brand-500/10 transition-colors">
                    Manage Billing
                </a>
            </div>
        </div>

        {{-- Forms --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Agency Profile --}}
            <div id="agency" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Agency Profile</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">This information appears in client-facing reports and portals.</p>
                </div>
                <form method="POST" action="{{ route('agency.settings.agency') }}" class="p-6 space-y-5">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Agency Name</label>
                            <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required
                                class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contact Email</label>
                            <input type="email" name="email" value="{{ old('email', $tenant->email) }}"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Website</label>
                            <input type="url" name="website" value="{{ old('website', $tenant->website) }}" placeholder="https://"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Timezone</label>
                            <select name="timezone"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                @foreach(\DateTimeZone::listIdentifiers() as $tz)
                                <option value="{{ $tz }}" @selected(old('timezone', $tenant->timezone) === $tz)>{{ $tz }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                            Save Agency Profile
                        </button>
                    </div>
                </form>
            </div>

            {{-- Your Profile --}}
            <div id="profile" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Your Profile</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Update your personal account details.</p>
                </div>
                <form method="POST" action="{{ route('agency.settings.profile') }}" class="p-6 space-y-5">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-center gap-4 mb-2">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="size-14 rounded-full object-cover">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Str::title(str_replace('_', ' ', $user->roles->first()?->name ?? 'Agency User')) }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Job Title</label>
                            <input type="text" name="job_title" value="{{ old('job_title', $user->job_title) }}"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                    </div>
                    <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                            Save Profile
                        </button>
                    </div>
                </form>
            </div>

            {{-- Password --}}
            <div id="password" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Change Password</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Use a strong, unique password.</p>
                </div>
                <form method="POST" action="{{ route('agency.settings.password') }}" class="p-6 space-y-5">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Current Password</label>
                        <input type="password" name="current_password" required
                            class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        @error('current_password')
                        <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">New Password</label>
                            <input type="password" name="password" required minlength="8"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Confirm Password</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                    </div>
                    <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

            {{-- Dashboard Preferences --}}
            @php
                $dashSettings = $tenant->data['dashboard'] ?? [];
                $newsKeywords = $dashSettings['news_focus_keywords'] ?? '';
                $newsCategories = $dashSettings['news_categories'] ?? ['trend', 'platform', 'industry', 'tip'];
                $showCarousel = $dashSettings['show_news_carousel'] ?? true;
            @endphp
            <div id="dashboard" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Dashboard Preferences</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Configure the Marketing Intelligence news feed and dashboard display options.</p>
                </div>
                <form method="POST" action="{{ route('agency.settings.dashboard') }}" class="p-6 space-y-6">
                    @csrf
                    @method('PATCH')

                    {{-- News carousel toggle --}}
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Show news carousel</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Display the Marketing Intelligence news carousel at the top of the dashboard.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" name="show_news_carousel" value="1" class="sr-only peer" {{ $showCarousel ? 'checked' : '' }}>
                            <div class="w-11 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-500 dark:bg-gray-700 peer-focus:ring-2 peer-focus:ring-brand-300 dark:peer-focus:ring-brand-800 transition-colors"></div>
                            <div class="absolute left-0.5 top-0.5 size-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></div>
                        </label>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-800 pt-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">News Focus Keywords</label>
                        <input type="text" name="news_focus_keywords" value="{{ old('news_focus_keywords', $newsKeywords) }}"
                               placeholder="e.g. paid social, SEO, e-commerce, B2B SaaS"
                               class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <p class="mt-1.5 text-xs text-gray-400">Comma-separated keywords that guide what topics the AI emphasises in your news brief. Leave blank for general marketing intelligence.</p>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-800 pt-5">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">News Categories to Show</p>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach(['trend' => 'Industry Trends', 'platform' => 'Platform Updates', 'industry' => 'Industry News', 'tip' => 'Actionable Tips'] as $cat => $catLabel)
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="news_categories[]" value="{{ $cat }}"
                                       class="rounded border-gray-300 dark:border-gray-600 text-brand-500 focus:ring-brand-500"
                                       {{ in_array($cat, $newsCategories) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-brand-500 transition-colors">{{ $catLabel }}</span>
                            </label>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs text-gray-400">Select which types of intel appear in your dashboard news feed.</p>
                    </div>

                    <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                            Save Dashboard Preferences
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
