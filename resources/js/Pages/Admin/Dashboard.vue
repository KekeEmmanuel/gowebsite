<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    recentMessages: {
        type: Array,
        default: () => [],
    },
});

const statCards = computed(() => [
    {
        title: 'Hero Slides',
        value: props.stats.heroSlides,
        active: props.stats.activeHeroSlides,
        icon: 'image',
        color: 'from-blue-500 to-blue-600',
        route: 'admin.hero-slides.index',
    },
    {
        title: 'Safaris',
        value: props.stats.itineraries,
        active: props.stats.activeItineraries,
        icon: 'map',
        color: 'from-safari-green to-green-700',
        route: 'admin.itineraries.index',
    },
    {
        title: 'Destinations',
        value: props.stats.destinations,
        active: props.stats.featuredDestinations,
        icon: 'globe',
        color: 'from-safari-gold to-yellow-600',
        route: 'admin.destinations.index',
    },
    {
        title: 'Lodges',
        value: props.stats.lodges,
        active: props.stats.lodges,
        icon: 'home',
        color: 'from-purple-500 to-purple-600',
        route: 'admin.lodges.index',
    },
    {
        title: 'Feature Cards',
        value: props.stats.featureCards,
        active: props.stats.featureCards,
        icon: 'star',
        color: 'from-pink-500 to-pink-600',
        route: 'admin.feature-cards.index',
    },
    {
        title: 'Messages',
        value: props.stats.contactMessages,
        active: props.stats.contactMessages,
        icon: 'mail',
        color: 'from-indigo-500 to-indigo-600',
        route: '#',
    },
]);

const getIcon = (iconName) => {
    const icons = {
        image: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
        map: 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
        globe: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        home: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        star: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
        mail: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    };
    return icons[iconName] || icons.star;
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Welcome back! Here's what's happening with your website content.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a
                        href="/"
                        target="_blank"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        View Website
                    </a>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Statistics Grid -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                <Link
                    v-for="card in statCards"
                    :key="card.title"
                    :href="card.route !== '#' ? route(card.route) : '#'"
                    class="group relative overflow-hidden rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 transition-all hover:shadow-md hover:ring-safari-green/20"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ card.title }}</p>
                            <p class="mt-2 text-2xl font-bold text-gray-900">{{ card.value }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                <span class="font-semibold text-green-600">{{ card.active }}</span>
                                <span class="text-gray-400"> active</span>
                            </p>
                        </div>
                        <div :class="['flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br', card.color, 'shadow-lg']">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIcon(card.icon)" />
                            </svg>
                        </div>
                    </div>
                    <div class="absolute inset-x-0 bottom-0 h-1 bg-gray-100">
                        <div :class="['h-full w-0 bg-gradient-to-r transition-all duration-300 group-hover:w-full', card.color]"></div>
                    </div>
                </Link>
            </div>

            <!-- Main Content Grid -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Quick Actions - Takes 2 columns on large screens -->
                <div class="lg:col-span-2">
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
                            <p class="mt-1 text-sm text-gray-500">Create new content quickly</p>
                        </div>
                        <div class="p-6">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <Link
                                    :href="route('admin.hero-slides.create')"
                                    class="group flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 transition hover:border-safari-green hover:bg-safari-green/5 hover:shadow-sm"
                                >
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-blue-100 group-hover:bg-blue-200">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-gray-900">New Hero Slide</p>
                                        <p class="text-xs text-gray-500">Add a carousel slide</p>
                                    </div>
                                    <svg class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-safari-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>

                                <Link
                                    :href="route('admin.itineraries.create')"
                                    class="group flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 transition hover:border-safari-green hover:bg-safari-green/5 hover:shadow-sm"
                                >
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-green-100 group-hover:bg-green-200">
                                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-gray-900">New Safari</p>
                                        <p class="text-xs text-gray-500">Create itinerary</p>
                                    </div>
                                    <svg class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-safari-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>

                                <Link
                                    :href="route('admin.destinations.create')"
                                    class="group flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 transition hover:border-safari-green hover:bg-safari-green/5 hover:shadow-sm"
                                >
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-yellow-100 group-hover:bg-yellow-200">
                                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-gray-900">New Destination</p>
                                        <p class="text-xs text-gray-500">Add destination</p>
                                    </div>
                                    <svg class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-safari-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>

                                <Link
                                    :href="route('admin.lodges.create')"
                                    class="group flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 transition hover:border-safari-green hover:bg-safari-green/5 hover:shadow-sm"
                                >
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-purple-100 group-hover:bg-purple-200">
                                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-gray-900">New Lodge</p>
                                        <p class="text-xs text-gray-500">Add accommodation</p>
                                    </div>
                                    <svg class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-safari-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Messages Widget -->
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Messages</h2>
                        <p class="mt-1 text-sm text-gray-500">Latest inquiries</p>
                    </div>
                    <div class="p-6">
                        <div v-if="recentMessages.length > 0" class="space-y-4">
                            <div
                                v-for="message in recentMessages"
                                :key="message.id"
                                class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50 p-3 transition hover:border-gray-200 hover:bg-white"
                            >
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 text-xs font-semibold text-white shadow-sm">
                                    {{ (message.name || 'G').charAt(0).toUpperCase() }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ message.name || 'Guest' }}</p>
                                    <p class="mt-1 line-clamp-2 text-xs text-gray-600">{{ message.message || message.email }}</p>
                                    <p class="mt-1.5 text-[10px] text-gray-400">{{ new Date(message.created_at).toLocaleDateString() }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="mt-3 text-sm font-medium text-gray-500">No messages yet</p>
                            <p class="mt-1 text-xs text-gray-400">New inquiries will appear here</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Management Links -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Link
                    :href="route('admin.feature-cards.index')"
                    class="group flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-safari-green hover:shadow-md"
                >
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-pink-100 group-hover:bg-pink-200">
                        <svg class="h-6 w-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-gray-900">Feature Cards</p>
                        <p class="text-xs text-gray-500">{{ stats.featureCards }} items</p>
                    </div>
                </Link>

                <Link
                    :href="route('admin.about-stats.index')"
                    class="group flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-safari-green hover:shadow-md"
                >
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-blue-100 group-hover:bg-blue-200">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-gray-900">About Stats</p>
                        <p class="text-xs text-gray-500">{{ stats.aboutStats }} items</p>
                    </div>
                </Link>

                <Link
                    :href="route('admin.about-highlights.index')"
                    class="group flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-safari-green hover:shadow-md"
                >
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-green-100 group-hover:bg-green-200">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-gray-900">Highlights</p>
                        <p class="text-xs text-gray-500">{{ stats.aboutHighlights }} items</p>
                    </div>
                </Link>

                <Link
                    :href="route('admin.contact-channels.index')"
                    class="group flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-safari-green hover:shadow-md"
                >
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-indigo-100 group-hover:bg-indigo-200">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-gray-900">Contact Info</p>
                        <p class="text-xs text-gray-500">{{ stats.contactChannels }} channels</p>
                    </div>
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
