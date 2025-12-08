<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    booking: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    status: props.booking.status,
    admin_notes: props.booking.admin_notes || '',
});

const submit = () => {
    form.put(route('admin.bookings.update', props.booking.id), {
        preserveScroll: true,
    });
};

const markCompleted = () => {
    if (confirm('Mark this booking as completed?')) {
        router.post(route('admin.bookings.complete', props.booking.id), {}, {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateString) => {
    if (!dateString) return 'Not specified';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatDateTime = (dateString) => {
    if (!dateString) return 'Not specified';
    return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head :title="`Booking #${booking.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('admin.bookings.index')"
                            class="text-gray-400 hover:text-gray-600"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </Link>
                        <h2 class="text-2xl font-bold leading-tight text-gray-900">
                            Booking #{{ booking.id }}
                        </h2>
                        <span
                            :class="[
                                booking.status === 'completed'
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-yellow-100 text-yellow-800',
                                'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                            ]"
                        >
                            {{ booking.status === 'completed' ? 'Completed' : 'Pending' }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        View and manage booking details
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        v-if="booking.status !== 'completed'"
                        @click="markCompleted"
                        class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-green-700"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Mark as Completed
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <div v-if="$page.props.flash?.success" class="rounded-lg bg-green-50 border border-green-200 p-4">
                <p class="text-sm text-green-800">{{ $page.props.flash.success }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Package Information -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Package Information</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div v-if="booking.tour_package">
                                <Link
                                    :href="route('admin.tour-packages.edit', booking.tour_package.id)"
                                    class="text-lg font-semibold text-safari-green hover:text-safari-green/80"
                                >
                                    {{ booking.tour_package.title }}
                                </Link>
                                <div v-if="booking.tour_package.description" class="mt-2 text-sm text-gray-600 line-clamp-3">
                                    {{ booking.tour_package.description }}
                                </div>
                                <div class="mt-4 flex flex-wrap gap-4 text-sm">
                                    <div v-if="booking.tour_package.price_from">
                                        <span class="font-medium text-gray-700">Price:</span>
                                        <span class="ml-2 text-safari-gold font-semibold">
                                            ${{ Number(booking.tour_package.price_from).toLocaleString() }}
                                        </span>
                                    </div>
                                    <div v-if="booking.tour_package.duration_days">
                                        <span class="font-medium text-gray-700">Duration:</span>
                                        <span class="ml-2 text-gray-600">{{ booking.tour_package.duration_days }} days</span>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-gray-400 italic">
                                Package has been deleted
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Customer Information</h3>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Full Name</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ booking.full_name }}</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <a :href="`mailto:${booking.email}`" class="text-safari-green hover:underline">
                                            {{ booking.email }}
                                        </a>
                                    </p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Phone</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <a :href="`tel:${booking.phone}`" class="text-safari-green hover:underline">
                                            {{ booking.phone }}
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <div v-if="booking.whatsapp">
                                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">WhatsApp</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    <a :href="`https://wa.me/${booking.whatsapp.replace(/[^0-9]/g, '')}`" target="_blank" class="text-safari-green hover:underline">
                                        {{ booking.whatsapp }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Travel Details -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Travel Details</h3>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Travel Date</label>
                                    <p class="mt-1 text-sm font-medium text-gray-900">
                                        {{ formatDate(booking.travel_date) }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Number of Travelers</label>
                                    <p class="mt-1 text-sm font-medium text-gray-900">
                                        {{ booking.number_of_travelers }} {{ booking.number_of_travelers === 1 ? 'traveler' : 'travelers' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Package Customization -->
                    <div v-if="booking.customization_data?.locations?.length" class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Package Customization</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="space-y-3">
                                <div
                                    v-for="(location, index) in booking.customization_data.locations"
                                    :key="index"
                                    class="flex items-center justify-between p-3 rounded-lg bg-safari-sand/5 border border-safari-sand/20"
                                >
                                    <div>
                                        <p class="font-medium text-gray-900">{{ location.location }}</p>
                                        <p class="text-sm text-gray-600">{{ location.days }} {{ location.days === 1 ? 'day' : 'days' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-sm">
                                    <span class="font-medium text-gray-700">Total Days:</span>
                                    <span class="ml-2 text-safari-gold font-semibold">
                                        {{ booking.customization_data.total_days || 0 }} days
                                    </span>
                                </p>
                            </div>
                            <div v-if="booking.customization_data.special_preferences" class="mt-4">
                                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Special Preferences</label>
                                <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ booking.customization_data.special_preferences }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Special Requests -->
                    <div v-if="booking.special_requests" class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Special Requests</h3>
                        </div>
                        <div class="px-6 py-4">
                            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ booking.special_requests }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Booking Status & Actions -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Booking Management</h3>
                        </div>
                        <form @submit.prevent="submit" class="px-6 py-4 space-y-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status
                                </label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-safari-green focus:outline-none focus:ring-2 focus:ring-safari-green/20"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>

                            <div>
                                <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Admin Notes
                                </label>
                                <textarea
                                    id="admin_notes"
                                    v-model="form.admin_notes"
                                    rows="6"
                                    placeholder="Add internal notes about this booking..."
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-safari-green focus:outline-none focus:ring-2 focus:ring-safari-green/20"
                                ></textarea>
                                <p class="mt-1 text-xs text-gray-500">These notes are only visible to admins</p>
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full rounded-lg bg-safari-green px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-safari-green/90 disabled:opacity-50"
                            >
                                <span v-if="form.processing">Saving...</span>
                                <span v-else>Save Changes</span>
                            </button>
                        </form>
                    </div>

                    <!-- Booking Metadata -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Booking Information</h3>
                        </div>
                        <div class="px-6 py-4 space-y-3 text-sm">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Booking ID</label>
                                <p class="mt-1 font-medium text-gray-900">#{{ booking.id }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Submitted</label>
                                <p class="mt-1 text-gray-900">{{ formatDateTime(booking.created_at) }}</p>
                            </div>
                            <div v-if="booking.completed_at">
                                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Completed</label>
                                <p class="mt-1 text-gray-900">{{ formatDateTime(booking.completed_at) }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Last Updated</label>
                                <p class="mt-1 text-gray-900">{{ formatDateTime(booking.updated_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

