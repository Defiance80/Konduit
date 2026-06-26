<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} | Konduit</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const saved = localStorage.getItem('theme');
                    const system = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    this.theme = saved || system;
                    this.updateTheme();
                },
                theme: 'light',
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
                updateTheme() {
                    const html = document.documentElement;
                    if (this.theme === 'dark') {
                        html.classList.add('dark');
                        document.body.classList.add('dark', 'bg-gray-900');
                    } else {
                        html.classList.remove('dark');
                        document.body.classList.remove('dark', 'bg-gray-900');
                    }
                }
            });
            Alpine.store('sidebar', {
                isExpanded: window.innerWidth >= 1280,
                isMobileOpen: false,
                isHovered: false,
                toggleExpanded() { this.isExpanded = !this.isExpanded; this.isMobileOpen = false; },
                toggleMobileOpen() { this.isMobileOpen = !this.isMobileOpen; },
                setMobileOpen(val) { this.isMobileOpen = val; },
                setHovered(val) { if (window.innerWidth >= 1280 && !this.isExpanded) this.isHovered = val; }
            });
        });
    </script>
    <script>
        (function() {
            const t = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (t === 'dark') { document.documentElement.classList.add('dark'); document.body.classList.add('dark','bg-gray-900'); }
        })();
    </script>
</head>
<body x-data="{ loaded: true }"
      x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280; window.addEventListener('resize', () => { if(window.innerWidth < 1280){ $store.sidebar.setMobileOpen(false); $store.sidebar.isExpanded = false; } else { $store.sidebar.isMobileOpen = false; $store.sidebar.isExpanded = true; }});">

    <div class="min-h-screen xl:flex">
        <!-- Backdrop -->
        <div x-show="$store.sidebar.isMobileOpen" @click="$store.sidebar.setMobileOpen(false)"
             class="fixed inset-0 z-50 bg-gray-900/50 xl:hidden"></div>

        @include('layouts.sidebar')

        <div class="flex-1 transition-all duration-300 ease-in-out"
             :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
                'ml-0': $store.sidebar.isMobileOpen
             }">
            @include('layouts.header')
            <div class="p-4 mx-auto max-w-[1536px] md:p-6">
                @if(session('success'))
                    <div class="mb-4 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:border-error-800 dark:bg-error-900/20 dark:text-error-400">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
