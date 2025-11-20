<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    itineraries: {
        type: Array,
        required: true,
    },
});

const deleteItinerary = (id) => {
    if (confirm('Are you sure you want to delete this safari/itinerary?')) {
        router.delete(route('admin.itineraries.destroy', id));
    }
};
</script>

<template>
    <Head title="Safaris (Itineraries)" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">
                        Safaris (Itineraries)
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Manage the safari packages displayed on your website
                    </p>
                </div>
                <Link
                    :href="route('admin.itineraries.create')"
                    class="inline-flex items-center gap-2 rounded-lg bg-safari-green px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-safari-green/90"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add New Safari
                </Link>
            </div>
        </template>

        <div class="space-y-6">
            <div v-if="$page.props.flash?.success" class="rounded-lg bg-green-50 border border-green-200 p-4">
                <p class="text-sm text-green-800">{{ $page.props.flash.success }}</p>
            </div>

            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Image
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Title
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Badge
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Duration
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Price
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="itinerary in itineraries" :key="itinerary.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="h-16 w-24 overflow-hidden rounded-lg bg-gray-100">
                                        <img
                                            v-if="itinerary.image_base64"
                                            :src="itinerary.image_base64"
                                            :alt="itinerary.title"
                                            class="h-full w-full object-cover"
                                        />
                                        <div v-else class="flex h-full items-center justify-center text-xs text-gray-400">
                                            No Image
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ itinerary.title }}</div>
                                    <div class="text-xs text-gray-500 line-clamp-2">{{ itinerary.summary }}</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                    <span v-if="itinerary.badge" class="rounded-full bg-safari-gold/20 px-3 py-1 text-xs font-medium text-safari-green">
                                        {{ itinerary.badge }}
                                    </span>
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                    {{ itinerary.duration_days }} days
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                    <span v-if="itinerary.price_from">${{ itinerary.price_from }}</span>
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span
                                        :class="[
                                            itinerary.published_at
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-yellow-100 text-yellow-800',
                                            'inline-flex rounded-full px-2 py-1 text-xs font-semibold',
                                        ]"
                                    >
                                        {{ itinerary.published_at ? 'Published' : 'Draft' }}
                                    </span>
                                    <span
                                        v-if="itinerary.is_featured"
                                        class="ml-2 inline-flex rounded-full bg-purple-100 px-2 py-1 text-xs font-semibold text-purple-800"
                                    >
                                        Featured
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <Link
                                        :href="route('admin.itineraries.edit', itinerary.id)"
                                        class="text-safari-green hover:text-safari-green/80"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="deleteItinerary(itinerary.id)"
                                        class="ml-4 text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="itineraries.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                    <p class="mt-4 text-sm font-medium text-gray-900">No safaris</p>
                                    <p class="mt-2 text-sm text-gray-500">Get started by creating a new safari package.</p>
                                    <div class="mt-6">
                                        <Link
                                            :href="route('admin.itineraries.create')"
                                            class="inline-flex items-center gap-2 rounded-lg bg-safari-green px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-safari-green/90"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Add Your First Safari
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

