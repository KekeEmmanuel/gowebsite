<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    title: '',
    slug: '',
    short_description: '',
    description: '',
    price_from: null,
    duration_days: null,
    max_participants: null,
    display_order: 0,
    is_featured: false,
    published_at: null,
    hero_image: null,
    gallery_images: [],
});

const heroImagePreview = ref(null);
const galleryPreviews = ref([]);

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
    galleryPreviews.value = [];
    files.forEach((file) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            galleryPreviews.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    });
};

const removeGalleryImage = (index) => {
    form.gallery_images.splice(index, 1);
    galleryPreviews.value.splice(index, 1);
};

const generateSlug = () => {
    if (!form.title) return;
    form.slug = form.title
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
};

const submit = () => {
    form.post(route('admin.tour-packages.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Create Tour Package" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">
                        Create Tour Package
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Add a new tour package to your website
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
            <!-- General Error Display -->
            <div v-if="$page.props.errors?.error" class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
                <p class="text-sm text-red-800">{{ $page.props.errors.error }}</p>
            </div>
            
            <form @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data">
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="p-6 space-y-6">
                        <!-- Hero Image Upload -->
                        <div>
                            <InputLabel for="hero_image" value="Hero Image (Main picture) *" />
                            <div class="mt-2">
                                <input
                                    id="hero_image"
                                    type="file"
                                    accept="image/*"
                                    @change="handleHeroImageChange"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-safari-green file:text-white hover:file:bg-safari-green/90"
                                />
                                <p class="mt-1 text-xs text-gray-500">Upload a high-quality image that represents this tour package.</p>
                                <InputError class="mt-2" :message="form.errors.hero_image" />
                                <div v-if="heroImagePreview" class="mt-4">
                                    <p class="text-xs font-medium text-gray-700 mb-2">Preview:</p>
                                    <img :src="heroImagePreview" alt="Preview" class="h-48 w-full rounded-lg object-cover border border-gray-200" />
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
                                required
                                placeholder="e.g., Ultimate Serengeti Adventure"
                                @blur="generateSlug"
                            />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <!-- Slug -->
                        <div>
                            <InputLabel for="slug" value="URL Slug *" />
                            <TextInput
                                id="slug"
                                v-model="form.slug"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                placeholder="e.g., ultimate-serengeti-adventure"
                            />
                            <InputError class="mt-2" :message="form.errors.slug" />
                        </div>

                        <!-- Short Description -->
                        <div>
                            <InputLabel for="short_description" value="Short Description" />
                            <Textarea
                                id="short_description"
                                v-model="form.short_description"
                                class="mt-1 block w-full"
                                rows="3"
                                placeholder="Brief description that appears on package cards"
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
                                required
                                placeholder="Detailed description of the tour package"
                            />
                            <InputError class="mt-2" :message="form.errors.description" />
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
                                placeholder="e.g., 3200.00"
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
                            <InputLabel for="gallery_images" value="Gallery Images" />
                            <div class="mt-2">
                                <input
                                    id="gallery_images"
                                    type="file"
                                    accept="image/*"
                                    multiple
                                    @change="handleGalleryImagesChange"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-safari-green file:text-white hover:file:bg-safari-green/90"
                                />
                                <p class="mt-1 text-xs text-gray-500">Upload multiple images for the gallery (optional).</p>
                                <InputError class="mt-2" :message="form.errors.gallery_images" />
                                <div v-if="galleryPreviews.length > 0" class="mt-4 grid grid-cols-4 gap-4">
                                    <div v-for="(preview, index) in galleryPreviews" :key="index" class="relative">
                                        <img :src="preview" alt="Gallery preview" class="h-24 w-full rounded-lg object-cover border border-gray-200" />
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
                        Create Package
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

