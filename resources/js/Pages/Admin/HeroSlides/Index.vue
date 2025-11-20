<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    heroSlides: {
        type: Array,
        required: true,
    },
});

const deleteSlide = (id) => {
    if (confirm('Are you sure you want to delete this hero slide?')) {
        router.delete(route('admin.hero-slides.destroy', id));
    }
};
</script>

<template>
    <Head title="Hero Slides" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">
                        Hero Slides
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Manage the carousel slides on your homepage
                    </p>
                </div>
                <Link
                    :href="route('admin.hero-slides.create')"
                    class="inline-flex items-center gap-2 rounded-lg bg-safari-green px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-safari-green/90"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add New Slide
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
                                    Label
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Title
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Position
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
                            <tr v-for="slide in heroSlides" :key="slide.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="h-16 w-24 overflow-hidden rounded-lg bg-gray-100">
                                        <img
                                            v-if="slide.image_base64"
                                            :src="slide.image_base64"
                                            :alt="slide.title"
                                            class="h-full w-full object-cover"
                                        />
                                        <img
                                            v-else-if="slide.media && slide.media[0]"
                                            :src="slide.media[0].original_url"
                                            :alt="slide.title"
                                            class="h-full w-full object-cover"
                                        />
                                        <div v-else class="flex h-full items-center justify-center text-xs text-gray-400">
                                            No Image
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ slide.label || slide.subtitle || '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ slide.title }}</div>
                                    <div class="text-xs text-gray-500 line-clamp-2">{{ slide.description || slide.subtitle }}</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                    {{ slide.position }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span
                                        :class="[
                                            slide.is_active
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800',
                                            'inline-flex rounded-full px-2 py-1 text-xs font-semibold',
                                        ]"
                                    >
                                        {{ slide.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <Link
                                        :href="route('admin.hero-slides.edit', slide.id)"
                                        class="text-safari-green hover:text-safari-green/80"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="deleteSlide(slide.id)"
                                        class="ml-4 text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="heroSlides.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-4 text-sm font-medium text-gray-900">No hero slides</p>
                                    <p class="mt-2 text-sm text-gray-500">Get started by creating a new hero slide.</p>
                                    <div class="mt-6">
                                        <Link
                                            :href="route('admin.hero-slides.create')"
                                            class="inline-flex items-center gap-2 rounded-lg bg-safari-green px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-safari-green/90"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Add Your First Slide
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

