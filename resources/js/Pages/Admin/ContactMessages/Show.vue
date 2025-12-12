<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    message: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    status: props.message.status,
});

const submit = () => {
    form.patch(route('admin.contact-messages.update', props.message.id), {
        preserveScroll: true,
    });
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

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'new':
            return 'bg-blue-100 text-blue-800';
        case 'in_progress':
            return 'bg-yellow-100 text-yellow-800';
        case 'closed':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const getStatusLabel = (status) => {
    switch (status) {
        case 'new':
            return 'New';
        case 'in_progress':
            return 'In Progress';
        case 'closed':
            return 'Closed';
        default:
            return status;
    }
};
</script>

<template>
    <Head :title="`Contact Message #${message.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('admin.contact-messages.index')"
                            class="text-gray-400 hover:text-gray-600"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </Link>
                        <h2 class="text-2xl font-bold leading-tight text-gray-900">
                            Contact Message #{{ message.id }}
                        </h2>
                        <span
                            :class="[
                                getStatusBadgeClass(message.status),
                                'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                            ]"
                        >
                            {{ getStatusLabel(message.status) }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        View and manage contact message details
                    </p>
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
                    <!-- Contact Information -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Contact Information</h3>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ message.name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Email</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    <a :href="`mailto:${message.email}`" class="text-safari-green hover:text-safari-green/80">
                                        {{ message.email }}
                                    </a>
                                </p>
                            </div>
                            <div v-if="message.phone">
                                <label class="block text-sm font-medium text-gray-500">Phone</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    <a :href="`tel:${message.phone}`" class="text-safari-green hover:text-safari-green/80">
                                        {{ message.phone }}
                                    </a>
                                </p>
                            </div>
                            <div v-if="message.service_type">
                                <label class="block text-sm font-medium text-gray-500">Service Type</label>
                                <p class="mt-1 text-sm text-gray-900">{{ message.service_type.name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Message Content -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Message</h3>
                        </div>
                        <div class="px-6 py-4">
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ message.message }}</p>
                        </div>
                    </div>

                    <!-- UTM Tracking (if available) -->
                    <div v-if="message.utm_source || message.utm_medium || message.utm_campaign" class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">UTM Tracking</h3>
                        </div>
                        <div class="px-6 py-4 space-y-2 text-sm">
                            <div v-if="message.utm_source">
                                <span class="font-medium text-gray-500">Source:</span>
                                <span class="ml-2 text-gray-900">{{ message.utm_source }}</span>
                            </div>
                            <div v-if="message.utm_medium">
                                <span class="font-medium text-gray-500">Medium:</span>
                                <span class="ml-2 text-gray-900">{{ message.utm_medium }}</span>
                            </div>
                            <div v-if="message.utm_campaign">
                                <span class="font-medium text-gray-500">Campaign:</span>
                                <span class="ml-2 text-gray-900">{{ message.utm_campaign }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Meta Information -->
                    <div v-if="message.meta" class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Technical Details</h3>
                        </div>
                        <div class="px-6 py-4 space-y-2 text-sm">
                            <div v-if="message.meta.ip">
                                <span class="font-medium text-gray-500">IP Address:</span>
                                <span class="ml-2 text-gray-900">{{ message.meta.ip }}</span>
                            </div>
                            <div v-if="message.meta.user_agent">
                                <span class="font-medium text-gray-500">User Agent:</span>
                                <span class="ml-2 text-gray-900 text-xs">{{ message.meta.user_agent }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status Update -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Update Status</h3>
                        </div>
                        <form @submit.prevent="submit" class="px-6 py-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select
                                    v-model="form.status"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-safari-green focus:ring-safari-green"
                                >
                                    <option value="new">New</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="closed">Closed</option>
                                </select>
                                <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.status }}
                                </div>
                            </div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full rounded-lg bg-safari-green px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-safari-green/90 focus:outline-none focus:ring-2 focus:ring-safari-green focus:ring-offset-2 disabled:opacity-50"
                            >
                                <span v-if="form.processing">Updating...</span>
                                <span v-else>Update Status</span>
                            </button>
                        </form>
                    </div>

                    <!-- Message Details -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Message Details</h3>
                        </div>
                        <div class="px-6 py-4 space-y-3 text-sm">
                            <div>
                                <span class="font-medium text-gray-500">Submitted:</span>
                                <p class="mt-1 text-gray-900">{{ formatDateTime(message.created_at) }}</p>
                            </div>
                            <div v-if="message.resolved_at">
                                <span class="font-medium text-gray-500">Resolved:</span>
                                <p class="mt-1 text-gray-900">{{ formatDateTime(message.resolved_at) }}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500">Last Updated:</span>
                                <p class="mt-1 text-gray-900">{{ formatDateTime(message.updated_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

