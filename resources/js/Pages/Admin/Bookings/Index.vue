<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    bookings: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ status: 'all' }),
    },
});

const statusFilter = ref(props.filters.status || 'all');

const filteredBookings = computed(() => {
    if (statusFilter.value === 'all') {
        return props.bookings;
    }
    return props.bookings.filter(booking => booking.status === statusFilter.value);
});

const updateFilter = (status) => {
    statusFilter.value = status;
    router.get(route('admin.bookings.index'), { status }, {
        preserveState: true,
        replace: true,
    });
};

const formatDate = (dateString) => {
    if (!dateString) return '—';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const getStatusBadgeClass = (status) => {
    return status === 'completed'
        ? 'bg-green-100 text-green-800'
        : 'bg-yellow-100 text-yellow-800';
};
</script>

<template>
    <Head title="Bookings" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">
                        Tour Package Bookings
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Manage and track all tour package booking requests
                    </p>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <div v-if="$page.props.flash?.success" class="rounded-lg bg-green-50 border border-green-200 p-4">
                <p class="text-sm text-green-800">{{ $page.props.flash.success }}</p>
            </div>

            <!-- Status Filters -->
            <div class="flex gap-2">
                <button
                    @click="updateFilter('all')"
                    :class="[
                        statusFilter === 'all'
                            ? 'bg-safari-green text-white'
                            : 'bg-white text-gray-700 hover:bg-gray-50',
                        'rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium transition',
                    ]"
                >
                    All Bookings
                    <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                        {{ bookings.length }}
                    </span>
                </button>
                <button
                    @click="updateFilter('pending')"
                    :class="[
                        statusFilter === 'pending'
                            ? 'bg-yellow-500 text-white'
                            : 'bg-white text-gray-700 hover:bg-gray-50',
                        'rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium transition',
                    ]"
                >
                    Pending
                    <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                        {{ bookings.filter(b => b.status === 'pending').length }}
                    </span>
                </button>
                <button
                    @click="updateFilter('completed')"
                    :class="[
                        statusFilter === 'completed'
                            ? 'bg-green-500 text-white'
                            : 'bg-white text-gray-700 hover:bg-gray-50',
                        'rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium transition',
                    ]"
                >
                    Completed
                    <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                        {{ bookings.filter(b => b.status === 'completed').length }}
                    </span>
                </button>
            </div>

            <!-- Bookings Table -->
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Booking ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Package
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Customer
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Travel Details
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Customization
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="booking in filteredBookings" :key="booking.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                    #{{ booking.id }}
                                </td>
                                <td class="px-6 py-4">
                                    <div v-if="booking.tour_package" class="text-sm font-medium text-gray-900">
                                        {{ booking.tour_package.title }}
                                    </div>
                                    <div v-else class="text-sm text-gray-400">Package deleted</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ booking.full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ booking.email }}</div>
                                    <div class="text-xs text-gray-500">{{ booking.phone }}</div>
                                    <div v-if="booking.whatsapp" class="text-xs text-safari-green">
                                        WhatsApp: {{ booking.whatsapp }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div v-if="booking.travel_date">
                                        <div class="font-medium">{{ formatDate(booking.travel_date) }}</div>
                                    </div>
                                    <div v-else class="text-gray-400">Not specified</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ booking.number_of_travelers }} {{ booking.number_of_travelers === 1 ? 'traveler' : 'travelers' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div v-if="booking.customization_data?.locations?.length">
                                        <div class="font-medium">{{ booking.customization_data.locations.length }} location(s)</div>
                                        <div class="text-xs text-gray-500">
                                            {{ booking.customization_data.total_days || 0 }} total days
                                        </div>
                                    </div>
                                    <div v-else class="text-gray-400 text-xs">No customization</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span
                                        :class="[
                                            getStatusBadgeClass(booking.status),
                                            'inline-flex rounded-full px-2 py-1 text-xs font-semibold',
                                        ]"
                                    >
                                        {{ booking.status === 'completed' ? 'Completed' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ formatDate(booking.created_at) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <Link
                                        :href="route('admin.bookings.show', booking.id)"
                                        class="text-safari-green hover:text-safari-green/80"
                                    >
                                        View Details
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="filteredBookings.length === 0">
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="mt-4 text-sm font-medium text-gray-900">No bookings found</p>
                                    <p class="mt-2 text-sm text-gray-500">
                                        {{ statusFilter === 'all' ? 'No bookings have been submitted yet.' : `No ${statusFilter} bookings.` }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

