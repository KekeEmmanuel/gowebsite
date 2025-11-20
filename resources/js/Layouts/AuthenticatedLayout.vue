<script setup>
import { ref, computed } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';

const showingSidebar = ref(false);
const sidebarCollapsed = ref(false);

const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
    // Persist to localStorage
    if (typeof window !== 'undefined') {
        localStorage.setItem('sidebarCollapsed', sidebarCollapsed.value);
    }
};

// Load sidebar state from localStorage
if (typeof window !== 'undefined') {
    sidebarCollapsed.value = localStorage.getItem('sidebarCollapsed') === 'true';
}

const menuItems = [
    {
        title: 'Dashboard',
        route: 'dashboard',
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        badge: null,
    },
    {
        group: 'Content',
        items: [
            { title: 'Hero Slides', route: 'admin.hero-slides.index', icon: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z' },
            { title: 'Feature Cards', route: 'admin.feature-cards.index', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
        ],
    },
    {
        group: 'About',
        items: [
            { title: 'Stats', route: 'admin.about-stats.index', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
            { title: 'Highlights', route: 'admin.about-highlights.index', icon: 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z' },
        ],
    },
    {
        group: 'Travel',
        items: [
            { title: 'Safaris', route: 'admin.itineraries.index', icon: 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7' },
            { title: 'Destinations', route: 'admin.destinations.index', icon: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
            { title: 'Lodges', route: 'admin.lodges.index', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
        ],
    },
    {
        group: 'Contact',
        items: [
            { title: 'Channels', route: 'admin.contact-channels.index', icon: 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z' },
            { title: 'Quick Facts', route: 'admin.contact-quick-facts.index', icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        ],
    },
];

const isActiveRoute = (routeName) => {
    try {
        const currentUrl = window.location.pathname;
        const routeUrl = new URL(route(routeName), window.location.origin).pathname;
        return currentUrl.startsWith(routeUrl) && routeUrl !== '/admin';
    } catch {
        return false;
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Top Navigation Bar -->
        <nav class="fixed top-0 left-0 right-0 z-40 h-16 border-b border-gray-200 bg-white shadow-sm">
            <div class="flex h-full items-center">
                <!-- Sidebar Toggle & Logo -->
                <div class="flex h-full items-center border-r border-gray-200">
                    <button
                        @click="toggleSidebar"
                        class="flex h-full items-center justify-center px-4 text-gray-500 transition hover:bg-gray-50 hover:text-gray-700 lg:px-5"
                        title="Toggle Sidebar"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <Link
                        :href="route('dashboard')"
                        class="hidden items-center border-r border-gray-200 px-5 lg:flex"
                        :class="sidebarCollapsed ? 'lg:hidden' : ''"
                    >
                        <img
                            src="/images/safari/mpya.png"
                            alt="Go Tanzania Safari"
                            class="h-20 w-auto max-w-[280px] object-contain"
                            style="filter: brightness(0.3) contrast(1.2) saturate(1.1);"
                        />
                    </Link>
                </div>

                <!-- Search Bar (Desktop) -->
                <div class="ml-4 hidden flex-1 items-center lg:flex" :class="sidebarCollapsed ? 'max-w-md' : 'max-w-lg'">
                    <div class="relative w-full">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            placeholder="Search content..."
                            class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-3 text-sm placeholder-gray-500 focus:border-safari-green focus:outline-none focus:ring-1 focus:ring-safari-green"
                        />
                    </div>
                </div>

                <!-- Right Side Actions -->
                <div class="ml-auto flex items-center gap-2 px-4">
                    <!-- View Website -->
                    <a
                        href="/"
                        target="_blank"
                        class="hidden items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 md:flex"
                        title="View Website"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span class="hidden lg:inline">View Site</span>
                    </a>

                    <!-- User Menu -->
                    <Dropdown align="right" width="56">
                        <template #trigger>
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-medium text-gray-700 transition hover:bg-gray-100 focus:outline-none"
                            >
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-safari-green to-green-700 text-xs font-semibold text-white shadow-sm">
                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </div>
                                <span class="hidden text-left lg:block">
                                    <span class="block text-xs font-medium text-gray-900">{{ $page.props.auth.user.name }}</span>
                                    <span class="text-xs text-gray-500">{{ $page.props.auth.user.email }}</span>
                                </span>
                                <svg class="hidden h-4 w-4 text-gray-400 lg:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </template>

                        <template #content>
                            <div class="px-4 py-3 border-b border-gray-200">
                                <p class="text-sm font-semibold text-gray-900">{{ $page.props.auth.user.name }}</p>
                                <p class="mt-0.5 text-xs text-gray-500 truncate">{{ $page.props.auth.user.email }}</p>
                            </div>
                            <DropdownLink :href="route('profile.edit')" class="flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profile Settings
                            </DropdownLink>
                            <DropdownLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="flex items-center gap-2 text-red-600 hover:bg-red-50"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </nav>

        <div class="flex">
            <!-- Professional Sidebar -->
            <aside
                :class="{
                    'translate-x-0': showingSidebar,
                    '-translate-x-full': !showingSidebar,
                    'lg:translate-x-0': true,
                    'lg:w-16': sidebarCollapsed,
                    'lg:w-72': !sidebarCollapsed,
                }"
                class="fixed top-16 left-0 z-30 flex h-[calc(100vh-4rem)] transform flex-col overflow-hidden border-r border-gray-200 bg-white transition-all duration-300 ease-in-out"
            >
                <!-- Sidebar Header (Collapsed) -->
                <div v-if="sidebarCollapsed" class="flex h-12 items-center justify-center border-b border-gray-200 lg:flex">
                    <img
                        src="/images/safari/mpya.png"
                        alt="Go Tanzania Safari"
                        class="h-12 w-auto object-contain"
                        style="filter: brightness(0.3) contrast(1.2) saturate(1.1);"
                    />
                </div>

                <!-- Sidebar Content -->
                <nav class="flex-1 overflow-y-auto px-2 py-4 lg:px-3">
                    <div class="space-y-1">
                        <!-- Dashboard -->
                        <Link
                            :href="route('dashboard')"
                            :class="[
                                route().current('dashboard')
                                    ? 'bg-safari-green text-white shadow-md'
                                    : 'text-gray-700 hover:bg-gray-100 hover:text-safari-green',
                                sidebarCollapsed
                                    ? 'justify-center px-2'
                                    : 'justify-start px-3',
                                'group relative flex items-center gap-3 rounded-lg py-2.5 text-sm font-medium transition-all',
                            ]"
                            :title="sidebarCollapsed ? 'Dashboard' : ''"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="menuItems[0].icon" />
                            </svg>
                            <span :class="sidebarCollapsed ? 'hidden' : ''">Dashboard</span>
                            <span
                                v-if="sidebarCollapsed && route().current('dashboard')"
                                class="absolute right-2 h-2 w-2 rounded-full bg-white"
                            ></span>
                        </Link>

                        <!-- Menu Groups -->
                        <template v-for="(item, index) in menuItems.slice(1)" :key="index">
                            <div class="mt-6">
                                <p
                                    v-if="!sidebarCollapsed"
                                    class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400"
                                >
                                    {{ item.group }}
                                </p>
                                <div class="mt-2 space-y-0.5">
                                    <Link
                                        v-for="(subItem, subIndex) in item.items"
                                        :key="subIndex"
                                        :href="route(subItem.route)"
                                        :class="[
                                            isActiveRoute(subItem.route)
                                                ? 'bg-safari-green text-white shadow-md'
                                                : 'text-gray-700 hover:bg-gray-100 hover:text-safari-green',
                                            sidebarCollapsed
                                                ? 'justify-center px-2'
                                                : 'justify-start px-3',
                                            'group relative flex items-center gap-3 rounded-lg py-2.5 text-sm font-medium transition-all',
                                        ]"
                                        :title="sidebarCollapsed ? subItem.title : ''"
                                    >
                                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="subItem.icon" />
                                        </svg>
                                        <span :class="sidebarCollapsed ? 'hidden' : ''" class="truncate">{{ subItem.title }}</span>
                                        <span
                                            v-if="sidebarCollapsed && isActiveRoute(subItem.route)"
                                            class="absolute right-2 h-2 w-2 rounded-full bg-white"
                                        ></span>
                                    </Link>
                                </div>
                            </div>
                        </template>
                    </div>
                </nav>

                <!-- Sidebar Footer -->
                <div class="border-t border-gray-200 p-3">
                    <div v-if="!sidebarCollapsed" class="rounded-lg bg-gradient-to-br from-safari-green/10 to-green-50 p-3">
                        <p class="text-xs font-semibold text-safari-green">Need Help?</p>
                        <p class="mt-1 text-[10px] text-gray-600">Check documentation or contact support</p>
                    </div>
                    <div v-else class="flex justify-center">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-safari-green/10 to-green-50"></div>
                    </div>
                </div>
            </aside>

            <!-- Mobile Overlay -->
            <div
                v-if="showingSidebar"
                @click="showingSidebar = false"
                class="fixed inset-0 z-20 bg-gray-900 bg-opacity-50 lg:hidden"
            ></div>

            <!-- Main Content Area -->
            <div
                class="w-full flex-1 transition-all duration-300 pt-16"
                :class="sidebarCollapsed ? 'lg:ml-16' : 'lg:ml-72'"
            >
                <!-- Page Header -->
                <header class="sticky top-16 z-10 border-b border-gray-200 bg-white/95 backdrop-blur-sm" v-if="$slots.header">
                    <div class="px-4 py-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Content -->
                <main class="min-h-[calc(100vh-4rem)]">
                    <div class="px-4 py-6 sm:px-6 lg:px-8">
                        <slot />
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>
