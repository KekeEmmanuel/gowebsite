<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    bookings: {
        type: Array,
        default: () => [],
    },
});

const deleteBooking = (bookingId) => {
    if (confirm('Are you sure you want to delete this booking?')) {
        router.delete(route('admin.bookings.destroy', bookingId));
    }
};

const updateStatus = (bookingId, status) => {
    router.put(route('admin.bookings.update', bookingId), {
        status: status,
    });
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
        completed: 'bg-blue-100 text-blue-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Bookings" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">Bookings</h2>
                    <p class="mt-1 text-sm text-gray-500">Manage all safari booking requests</p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div v-if="bookings.length === 0" class="text-center py-12">
                            <p class="text-gray-500">No bookings yet.</p>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Booking ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Safari</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Guest</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Contact</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Travellers</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Travel Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="booking in bookings" :key="booking.id" class="hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">#{{ booking.id }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div>
                                                <p class="font-medium">{{ booking.itinerary.title }}</p>
                                                <p class="text-xs text-gray-500">{{ booking.itinerary.slug }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div>
                                                <p class="font-medium">{{ booking.full_name }}</p>
                                                <p v-if="booking.country" class="text-xs text-gray-500">{{ booking.country }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div>
                                                <p>{{ booking.email }}</p>
                                                <p class="text-xs text-gray-500">{{ booking.phone }}</p>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ booking.number_of_travellers }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            <span v-if="booking.preferred_travel_date">{{ booking.preferred_travel_date }}</span>
                                            <span v-else class="text-gray-400">Not specified</span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            <select
                                                :value="booking.status"
                                                @change="updateStatus(booking.id, $event.target.value)"
                                                :class="['rounded-full px-3 py-1 text-xs font-semibold border-0', getStatusColor(booking.status)]"
                                            >
                                                <option value="pending">Pending</option>
                                                <option value="confirmed">Confirmed</option>
                                                <option value="cancelled">Cancelled</option>
                                                <option value="completed">Completed</option>
                                            </select>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ booking.created_at }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                            <button
                                                @click="deleteBooking(booking.id)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

