@php
    use App\Helpers\MenuHelper;
    $isClient = auth()->check() && auth()->user()->isClientContact();
    $menuGroups = $isClient ? MenuHelper::getClientMenuGroups() : MenuHelper::getAgencyMenuGroups();
    $currentPath = '/' . request()->path();
@endphp

<aside id="sidebar"
    class="fixed flex flex-col top-0 px-5 left-0 bg-white dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-300 ease-in-out z-[99999] border-r border-gray-200"
    x-data="{
        openSubmenus: {},
        toggleSubmenu(key) {
            const open = !this.openSubmenus[key];
            this.openSubmenus = {};
            this.openSubmenus[key] = open;
        },
        isSubmenuOpen(key) { return this.openSubmenus[key] || false; },
        isActive(path) { return window.location.pathname === path || '{{ $currentPath }}' === path; }
    }"
    :class="{
        'w-[290px]': $store.sidebar.isExpanded || $store.sidebar.isMobileOpen || $store.sidebar.isHovered,
        'w-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
        'translate-x-0': $store.sidebar.isMobileOpen,
        '-translate-x-full xl:translate-x-0': !$store.sidebar.isMobileOpen
    }"
    @mouseenter="if (!$store.sidebar.isExpanded) $store.sidebar.setHovered(true)"
    @mouseleave="$store.sidebar.setHovered(false)">

    <!-- Logo -->
    <div class="pt-8 pb-7 flex"
         :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'">
        <a href="{{ $isClient ? route('client.dashboard') : route('agency.dashboard') }}" class="flex items-center gap-2">
            <div class="flex size-9 items-center justify-center rounded-lg bg-brand-500 shrink-0">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                  class="text-xl font-bold text-gray-900 dark:text-white">Konduit</span>
        </a>
    </div>

    <!-- Nav -->
    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar flex-1">
        <nav class="mb-6">
            <div class="flex flex-col gap-4">
                @foreach($menuGroups as $gi => $group)
                    <div>
                        <h2 class="mb-4 text-xs uppercase flex leading-5 text-gray-400"
                            :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'lg:justify-center' : 'justify-start'">
                            <template x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                                <span>{{ $group['title'] }}</span>
                            </template>
                            <template x-if="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">
                                <span class="block w-5 h-px bg-gray-300 dark:bg-gray-700 mx-auto"></span>
                            </template>
                        </h2>
                        <ul class="flex flex-col gap-1">
                            @foreach($group['items'] as $ii => $item)
                                <li>
                                    @if(!empty($item['subItems']))
                                        <button @click="toggleSubmenu('{{ $gi }}-{{ $ii }}')" class="menu-item group w-full"
                                            :class="isSubmenuOpen('{{ $gi }}-{{ $ii }}') ? 'menu-item-active' : 'menu-item-inactive'">
                                            <span :class="isSubmenuOpen('{{ $gi }}-{{ $ii }}') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                                {!! MenuHelper::getIconSvg($item['icon']) !!}
                                            </span>
                                            <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">
                                                {{ $item['name'] }}
                                            </span>
                                            <svg x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                                 class="ml-auto w-5 h-5 transition-transform duration-200"
                                                 :class="{ 'rotate-180': isSubmenuOpen('{{ $gi }}-{{ $ii }}') }"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <div x-show="isSubmenuOpen('{{ $gi }}-{{ $ii }}') && ($store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen)">
                                            <ul class="mt-2 space-y-1 ml-9">
                                                @foreach($item['subItems'] as $sub)
                                                    <li>
                                                        <a href="{{ $sub['path'] }}" class="menu-dropdown-item"
                                                           :class="isActive('{{ $sub['path'] }}') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                                            {{ $sub['name'] }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <a href="{{ $item['path'] }}" class="menu-item group"
                                           :class="[isActive('{{ $item['path'] }}') ? 'menu-item-active' : 'menu-item-inactive', (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start']">
                                            <span :class="isActive('{{ $item['path'] }}') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                                {!! MenuHelper::getIconSvg($item['icon']) !!}
                                            </span>
                                            <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">
                                                {{ $item['name'] }}
                                            </span>
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </nav>
    </div>
</aside>
