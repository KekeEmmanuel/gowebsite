<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    messages: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ status: 'all' }),
    },
    stats: {
        type: Object,
        default: () => ({}),
    },
});

const statusFilter = ref(props.filters.status || 'all');

const filteredMessages = computed(() => {
    if (statusFilter.value === 'all') {
        return props.messages;
    }
    return props.messages.filter(message => message.status === statusFilter.value);
});

const updateFilter = (status) => {
    statusFilter.value = status;
    router.get(route('admin.contact-messages.index'), { status }, {
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
    <Head title="Contact Messages" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">
                        Contact Messages
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        View and manage all messages from the "Get in Touch" form
                    </p>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <div v-if="$page.props.flash?.success" class="rounded-lg bg-green-50 border border-green-200 p-4">
                <p class="text-sm text-green-800">{{ $page.props.flash.success }}</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
                    <div class="text-sm font-medium text-gray-500">Total Messages</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ stats.total || 0 }}</div>
                </div>
                <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
                    <div class="text-sm font-medium text-gray-500">New</div>
                    <div class="mt-2 text-2xl font-bold text-blue-600">{{ stats.new || 0 }}</div>
                </div>
                <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
                    <div class="text-sm font-medium text-gray-500">In Progress</div>
                    <div class="mt-2 text-2xl font-bold text-yellow-600">{{ stats.in_progress || 0 }}</div>
                </div>
                <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
                    <div class="text-sm font-medium text-gray-500">Closed</div>
                    <div class="mt-2 text-2xl font-bold text-green-600">{{ stats.closed || 0 }}</div>
                </div>
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
                    All Messages
                    <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                        {{ messages.length }}
                    </span>
                </button>
                <button
                    @click="updateFilter('new')"
                    :class="[
                        statusFilter === 'new'
                            ? 'bg-blue-500 text-white'
                            : 'bg-white text-gray-700 hover:bg-gray-50',
                        'rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium transition',
                    ]"
                >
                    New
                    <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                        {{ messages.filter(m => m.status === 'new').length }}
                    </span>
                </button>
                <button
                    @click="updateFilter('in_progress')"
                    :class="[
                        statusFilter === 'in_progress'
                            ? 'bg-yellow-500 text-white'
                            : 'bg-white text-gray-700 hover:bg-gray-50',
                        'rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium transition',
                    ]"
                >
                    In Progress
                    <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                        {{ messages.filter(m => m.status === 'in_progress').length }}
                    </span>
                </button>
                <button
                    @click="updateFilter('closed')"
                    :class="[
                        statusFilter === 'closed'
                            ? 'bg-green-500 text-white'
                            : 'bg-white text-gray-700 hover:bg-gray-50',
                        'rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium transition',
                    ]"
                >
                    Closed
                    <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                        {{ messages.filter(m => m.status === 'closed').length }}
                    </span>
                </button>
            </div>

            <!-- Messages Table -->
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Contact Info
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Message Preview
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Service Type
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
                            <tr v-for="message in filteredMessages" :key="message.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                    #{{ message.id }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ message.name }}</div>
                                    <div class="text-xs text-gray-500">{{ message.email }}</div>
                                    <div v-if="message.phone" class="text-xs text-gray-500">{{ message.phone }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 line-clamp-2 max-w-md">
                                        {{ message.message }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div v-if="message.service_type" class="font-medium">
                                        {{ message.service_type.name }}
                                    </div>
                                    <div v-else class="text-gray-400">—</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span
                                        :class="[
                                            getStatusBadgeClass(message.status),
                                            'inline-flex rounded-full px-2 py-1 text-xs font-semibold',
                                        ]"
                                    >
                                        {{ getStatusLabel(message.status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ formatDate(message.created_at) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <Link
                                        :href="route('admin.contact-messages.show', message.id)"
                                        class="text-safari-green hover:text-safari-green/80"
                                    >
                                        View Details
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="filteredMessages.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-4 text-sm font-medium text-gray-900">No messages found</p>
                                    <p class="mt-2 text-sm text-gray-500">
                                        {{ statusFilter === 'all' ? 'No messages have been submitted yet.' : `No ${statusFilter} messages.` }}
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

