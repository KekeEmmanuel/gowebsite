<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    package: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    title: props.package.title || '',
    slug: props.package.slug || '',
    short_description: props.package.short_description || '',
    description: props.package.description || '',
    price_from: props.package.price_from || null,
    duration_days: props.package.duration_days || null,
    max_participants: props.package.max_participants || null,
    display_order: props.package.display_order || 0,
    is_featured: props.package.is_featured ?? false,
    published_at: props.package.published_at ? props.package.published_at.split('T')[0] : null,
    hero_image: null,
    gallery_images: [],
}, {
    resetOnSuccess: false,
    preserveScroll: true,
});

// Track if form has been submitted to only show errors after submission
const hasSubmitted = ref(false);

const submit = () => {
    hasSubmitted.value = true;
    form.put(route('admin.tour-packages.update', props.package.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            hasSubmitted.value = false;
        },
    });
};

const heroImagePreview = ref(props.package.hero_image || null);
const galleryPreviews = ref(props.package.gallery?.map(g => g.url) || []);

const handleHeroImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.hero_image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            heroImagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const handleGalleryImagesChange = (event) => {
    const files = Array.from(event.target.files);
    form.gallery_images = files;
    files.forEach((file) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            galleryPreviews.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    });
};

const removeGalleryImage = (index) => {
    if (index < props.package.gallery?.length) {
        // Existing image - would need to handle deletion via API
        galleryPreviews.value.splice(index, 1);
    } else {
        // New image
        const newImageIndex = index - (props.package.gallery?.length || 0);
        form.gallery_images.splice(newImageIndex, 1);
        galleryPreviews.value.splice(index, 1);
    }
};
</script>

<template>
    <Head title="Edit Tour Package" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">
                        Edit Tour Package
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Update the tour package content
                    </p>
                </div>
                <Link
                    :href="route('admin.tour-packages.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Back to Packages
                </Link>
            </div>
        </template>

        <div class="max-w-4xl">
            <form @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data" novalidate>
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="p-6 space-y-6">
                        <!-- Hero Image Upload -->
                        <div>
                            <InputLabel for="hero_image" value="Hero Image" />
                            <div class="mt-2">
                                <input
                                    id="hero_image"
                                    type="file"
                                    accept="image/*"
                                    @change="handleHeroImageChange"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-safari-green file:text-white hover:file:bg-safari-green/90"
                                />
                                <p class="mt-1 text-xs text-gray-500">Upload a new image or leave empty to keep the current one.</p>
                                <InputError class="mt-2" :message="form.errors.hero_image" />
                                <div v-if="heroImagePreview" class="mt-4">
                                    <p class="text-xs font-medium text-gray-700 mb-2">Preview:</p>
                                    <img :src="heroImagePreview" alt="Preview" class="h-48 w-full rounded-lg object-cover border border-gray-200" @error="(e) => { e.target.src = '/images/safari/beach-1.jpg'; }" />
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <div>
                            <InputLabel for="title" value="Package Title *" />
                            <TextInput
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError v-if="hasSubmitted" class="mt-2" :message="form.errors.title" />
                        </div>

                        <!-- Slug -->
                        <div>
                            <InputLabel for="slug" value="URL Slug *" />
                            <TextInput
                                id="slug"
                                v-model="form.slug"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError v-if="hasSubmitted" class="mt-2" :message="form.errors.slug" />
                        </div>

                        <!-- Short Description -->
                        <div>
                            <InputLabel for="short_description" value="Short Description" />
                            <Textarea
                                id="short_description"
                                v-model="form.short_description"
                                class="mt-1 block w-full"
                                rows="3"
                            />
                            <InputError class="mt-2" :message="form.errors.short_description" />
                        </div>

                        <!-- Description -->
                        <div>
                            <InputLabel for="description" value="Full Description *" />
                            <Textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full"
                                rows="8"
                            />
                            <InputError v-if="hasSubmitted" class="mt-2" :message="form.errors.description" />
                        </div>

                        <!-- Duration -->
                        <div>
                            <InputLabel for="duration_days" value="Duration (Days)" />
                            <TextInput
                                id="duration_days"
                                v-model="form.duration_days"
                                type="number"
                                min="1"
                                class="mt-1 block w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.duration_days" />
                        </div>

                        <!-- Price -->
                        <div>
                            <InputLabel for="price_from" value="Starting Price" />
                            <TextInput
                                id="price_from"
                                v-model="form.price_from"
                                type="number"
                                min="0"
                                step="0.01"
                                class="mt-1 block w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.price_from" />
                        </div>

                        <!-- Max Participants -->
                        <div>
                            <InputLabel for="max_participants" value="Max Participants" />
                            <TextInput
                                id="max_participants"
                                v-model="form.max_participants"
                                type="number"
                                min="1"
                                class="mt-1 block w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.max_participants" />
                        </div>

                        <!-- Gallery Images -->
                        <div>
                            <InputLabel for="gallery_images" value="Add Gallery Images" />
                            <div class="mt-2">
                                <input
                                    id="gallery_images"
                                    type="file"
                                    accept="image/*"
                                    multiple
                                    @change="handleGalleryImagesChange"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-safari-green file:text-white hover:file:bg-safari-green/90"
                                />
                                <p class="mt-1 text-xs text-gray-500">Add more images to the gallery (optional).</p>
                                <InputError class="mt-2" :message="form.errors.gallery_images" />
                                <div v-if="galleryPreviews.length > 0" class="mt-4 grid grid-cols-4 gap-4">
                                    <div v-for="(preview, index) in galleryPreviews" :key="index" class="relative">
                                        <img :src="preview" alt="Gallery preview" class="h-24 w-full rounded-lg object-cover border border-gray-200" @error="(e) => { e.target.src = '/images/safari/beach-1.jpg'; }" />
                                        <button
                                            type="button"
                                            @click="removeGalleryImage(index)"
                                            class="absolute top-1 right-1 rounded-full bg-red-500 p-1 text-white hover:bg-red-600"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Display Order -->
                        <div>
                            <InputLabel for="display_order" value="Display Order" />
                            <TextInput
                                id="display_order"
                                v-model="form.display_order"
                                type="number"
                                min="0"
                                class="mt-1 block w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.display_order" />
                        </div>

                        <!-- Featured & Published -->
                        <div class="space-y-4 rounded-lg bg-gray-50 border border-gray-200 p-4">
                            <div class="flex items-center">
                                <input
                                    id="is_featured"
                                    v-model="form.is_featured"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 text-safari-green focus:ring-safari-green"
                                />
                                <InputLabel for="is_featured" value="Feature this package on the homepage" class="ml-2" />
                            </div>
                            <div>
                                <InputLabel for="published_at" value="Publish Date (Optional)" />
                                <TextInput
                                    id="published_at"
                                    v-model="form.published_at"
                                    type="date"
                                    class="mt-1 block w-full"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <Link
                        :href="route('admin.tour-packages.index')"
                        class="text-sm font-medium text-gray-700 hover:text-gray-900"
                    >
                        Cancel
                    </Link>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update Package
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

