<header class="sticky top-0 flex w-full bg-white border-gray-200 z-[99999] dark:border-gray-800 dark:bg-gray-900 xl:border-b"
    x-data="{ menuOpen: false }">
    <div class="flex flex-col items-center justify-between grow xl:flex-row xl:px-6">
        <div class="flex items-center justify-between w-full gap-2 px-3 py-3 border-b border-gray-200 dark:border-gray-800 sm:gap-4 xl:justify-normal xl:border-b-0 xl:px-0 lg:py-4">
            <!-- Desktop sidebar toggle -->
            <button class="hidden xl:flex items-center justify-center w-10 h-10 text-gray-500 border border-gray-200 rounded-lg dark:border-gray-800 dark:text-gray-400"
                    @click="$store.sidebar.toggleExpanded()">
                <svg width="16" height="12" viewBox="0 0 16 12" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M0.583 1C0.583 0.586 0.919 0.25 1.333 0.25H14.667C15.081 0.25 15.417 0.586 15.417 1C15.417 1.414 15.081 1.75 14.667 1.75L1.333 1.75C0.919 1.75 0.583 1.414 0.583 1ZM0.583 11C0.583 10.586 0.919 10.25 1.333 10.25L14.667 10.25C15.081 10.25 15.417 10.586 15.417 11C15.417 11.414 15.081 11.75 14.667 11.75L1.333 11.75C0.919 11.75 0.583 11.414 0.583 11ZM1.333 5.25C0.919 5.25 0.583 5.586 0.583 6C0.583 6.414 0.919 6.75 1.333 6.75L8 6.75C8.414 6.75 8.75 6.414 8.75 6C8.75 5.586 8.414 5.25 8 5.25L1.333 5.25Z" fill="currentColor"/></svg>
            </button>
            <!-- Mobile toggle -->
            <button class="flex xl:hidden items-center justify-center w-10 h-10 text-gray-500 rounded-lg dark:text-gray-400"
                    @click="$store.sidebar.toggleMobileOpen()">
                <svg width="16" height="12" viewBox="0 0 16 12" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M0.583 1C0.583 0.586 0.919 0.25 1.333 0.25H14.667C15.081 0.25 15.417 0.586 15.417 1C15.417 1.414 15.081 1.75 14.667 1.75L1.333 1.75C0.919 1.75 0.583 1.414 0.583 1ZM0.583 11C0.583 10.586 0.919 10.25 1.333 10.25L14.667 10.25C15.081 10.25 15.417 10.586 15.417 11C15.417 11.414 15.081 11.75 14.667 11.75L1.333 11.75C0.919 11.75 0.583 11.414 0.583 11ZM1.333 5.25C0.919 5.25 0.583 5.586 0.583 6C0.583 6.414 0.919 6.75 1.333 6.75L8 6.75C8.414 6.75 8.75 6.414 8.75 6C8.75 5.586 8.414 5.25 8 5.25L1.333 5.25Z" fill="currentColor"/></svg>
            </button>
            <!-- Logo mobile -->
            <a href="{{ auth()->check() && auth()->user()->isClientContact() ? route('client.dashboard') : route('agency.dashboard') }}" class="xl:hidden flex items-center gap-2">
                <div class="flex size-8 items-center justify-center rounded-lg bg-brand-500">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <span class="text-lg font-bold text-gray-900 dark:text-white">Konduit</span>
            </a>
        </div>

        <div class="flex items-center justify-end w-full gap-4 px-5 py-3 xl:py-0 xl:px-0">
            <!-- Theme toggle -->
            <button class="flex items-center justify-center text-gray-500 bg-white border border-gray-200 rounded-full h-10 w-10 hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800"
                    @click="$store.theme.toggle()">
                <svg class="hidden dark:block" width="18" height="18" fill="none" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 1.5C10.4 1.5 10.75 1.88 10.75 2.29V3.54C10.75 3.96 10.4 4.29 10 4.29S9.25 3.96 9.25 3.54V2.29C9.25 1.88 9.6 1.5 10 1.5ZM10 6.79A3.21 3.21 0 1 1 10 13.21 3.21 3.21 0 0 1 10 6.79ZM15.98 5.08a.75.75 0 0 0-1.06-1.06l-.88.88a.75.75 0 0 0 1.06 1.06l.88-.88ZM18.46 10c0 .42-.34.75-.75.75h-1.25a.75.75 0 1 1 0-1.5h1.25c.41 0 .75.34.75.75ZM14.92 15.98a.75.75 0 0 0 1.06-1.06l-.88-.88a.75.75 0 0 0-1.06 1.06l.88.88ZM10 15.71c.4 0 .75.34.75.75v1.25a.75.75 0 1 1-1.5 0v-1.25c0-.41.34-.75.75-.75ZM5.96 15.09a.75.75 0 0 0-1.06 0l-.88.88a.75.75 0 1 0 1.06 1.06l.88-.88a.75.75 0 0 0 0-1.06ZM4.29 10a.75.75 0 0 0-.75-.75H2.29a.75.75 0 1 0 0 1.5h1.25c.41 0 .75-.34.75-.75ZM4.9 5.96a.75.75 0 0 0 0-1.06l-.88-.88A.75.75 0 0 0 2.96 5.08l.88.88a.75.75 0 0 0 1.06 0Z" fill="currentColor"/></svg>
                <svg class="dark:hidden" width="18" height="18" fill="none" viewBox="0 0 20 20"><path d="M17.45 11.97A8.9 8.9 0 0 1 10 18.46 8.96 8.96 0 0 1 3.04 10 8.9 8.9 0 0 1 8.22 1.82a1 1 0 0 1 .45 1.24A7.46 7.46 0 0 0 10 14.5a7.5 7.5 0 0 0 6.92-4.59 1 1 0 0 1 .53 2.06Z" fill="currentColor"/></svg>
            </button>

            <!-- User menu -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2">
                    <img src="{{ auth()->user()?->avatar_url }}" class="size-9 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700" alt="{{ auth()->user()?->name }}">
                    <div class="hidden md:block text-left">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()?->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()?->isClientContact() ? 'Client' : ucfirst(auth()->user()?->user_type ?? '') }}</div>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-900 rounded-xl shadow-theme-lg border border-gray-200 dark:border-gray-800 z-50 py-1">
                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()?->name }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ auth()->user()?->email }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-error-600 hover:bg-error-50 dark:hover:bg-error-900/20">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
