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
    name: '',
    slug: '',
    location: '',
    type: 'lodge',
    mood: '',
    short_description: '',
    description: '',
    amenities: [],
    price_from: null,
    capacity: null,
    display_order: 0,
    is_active: true,
    is_featured: false,
    published_at: null,
    hero_image: null,
    gallery_images: [],
});

const heroImagePreview = ref(null);
const galleryPreviews = ref([]);
const newAmenity = ref('');

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

const addAmenity = () => {
    if (newAmenity.value.trim()) {
        form.amenities.push(newAmenity.value.trim());
        newAmenity.value = '';
    }
};

const removeAmenity = (index) => {
    form.amenities.splice(index, 1);
};

const generateSlug = () => {
    if (!form.name) return;
    form.slug = form.name
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
};

const submit = () => {
    form.post(route('admin.lodges.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Create Lodge/Camp" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">
                        Create Lodge/Camp
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Add a new lodge or camp to your website
                    </p>
                </div>
                <Link
                    :href="route('admin.lodges.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Back to Lodges
                </Link>
            </div>
        </template>

        <div class="max-w-4xl">
            <form @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data" novalidate>
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="p-6 space-y-6">
                        <!-- Hero Image Upload -->
                        <div>
                            <InputLabel for="hero_image" value="Hero Image (Main picture)" />
                            <div class="mt-2">
                                <input
                                    id="hero_image"
                                    type="file"
                                    accept="image/*"
                                    @change="handleHeroImageChange"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-safari-green file:text-white hover:file:bg-safari-green/90"
                                />
                                <p class="mt-1 text-xs text-gray-500">Upload a high-quality image that represents this lodge/camp.</p>
                                <InputError class="mt-2" :message="form.errors.hero_image" />
                                <div v-if="heroImagePreview" class="mt-4">
                                    <p class="text-xs font-medium text-gray-700 mb-2">Preview:</p>
                                    <img :src="heroImagePreview" alt="Preview" class="h-48 w-full rounded-lg object-cover border border-gray-200" @error="(e) => { e.target.src = '/images/safari/beach-1.jpg'; }" />
                                </div>
                            </div>
                        </div>

                        <!-- Name -->
                        <div>
                            <InputLabel for="name" value="Lodge/Camp Name *" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g., Serengeti Luxury Camp"
                                @blur="generateSlug"
                            />
                            <InputError v-if="form.errors.name" class="mt-2" :message="form.errors.name" />
                        </div>

                        <!-- Slug -->
                        <div>
                            <InputLabel for="slug" value="URL Slug" />
                            <TextInput
                                id="slug"
                                v-model="form.slug"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g., serengeti-luxury-camp"
                            />
                            <InputError v-if="form.errors.slug" class="mt-2" :message="form.errors.slug" />
                        </div>

                        <!-- Location & Type -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="location" value="Location *" />
                                <TextInput
                                    id="location"
                                    v-model="form.location"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="e.g., Serengeti National Park"
                                />
                                <InputError v-if="form.errors.location" class="mt-2" :message="form.errors.location" />
                            </div>
                            <div>
                                <InputLabel for="type" value="Type *" />
                                <select
                                    id="type"
                                    v-model="form.type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-safari-green focus:ring-safari-green"
                                >
                                    <option value="lodge">Lodge</option>
                                    <option value="camp">Camp</option>
                                </select>
                                <InputError v-if="form.errors.type" class="mt-2" :message="form.errors.type" />
                            </div>
                        </div>

                        <!-- Mood -->
                        <div>
                            <InputLabel for="mood" value="Mood/Atmosphere" />
                            <Textarea
                                id="mood"
                                v-model="form.mood"
                                class="mt-1 block w-full"
                                rows="2"
                                placeholder="Brief description of the atmosphere (e.g., Luxury, Authentic, Relaxing)"
                            />
                            <InputError v-if="form.errors.mood" class="mt-2" :message="form.errors.mood" />
                        </div>

                        <!-- Short Description -->
                        <div>
                            <InputLabel for="short_description" value="Short Description" />
                            <Textarea
                                id="short_description"
                                v-model="form.short_description"
                                class="mt-1 block w-full"
                                rows="3"
                                placeholder="Brief description that appears on lodge cards"
                            />
                            <InputError v-if="form.errors.short_description" class="mt-2" :message="form.errors.short_description" />
                        </div>

                        <!-- Description -->
                        <div>
                            <InputLabel for="description" value="Full Description" />
                            <Textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full"
                                rows="8"
                                placeholder="Detailed description of the lodge/camp"
                            />
                            <InputError v-if="form.errors.description" class="mt-2" :message="form.errors.description" />
                        </div>

                        <!-- Amenities -->
                        <div>
                            <InputLabel value="Amenities" />
                            <div class="mt-2 flex gap-2">
                                <TextInput
                                    v-model="newAmenity"
                                    type="text"
                                    class="block w-full"
                                    placeholder="Add an amenity (e.g., WiFi, Pool, Restaurant)"
                                    @keyup.enter.prevent="addAmenity"
                                />
                                <button
                                    type="button"
                                    @click="addAmenity"
                                    class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
                                >
                                    Add
                                </button>
                            </div>
                            <div v-if="form.amenities.length > 0" class="mt-3 flex flex-wrap gap-2">
                                <span
                                    v-for="(amenity, index) in form.amenities"
                                    :key="index"
                                    class="inline-flex items-center gap-1 rounded-full bg-safari-green/10 px-3 py-1 text-sm text-safari-green"
                                >
                                    {{ amenity }}
                                    <button
                                        type="button"
                                        @click="removeAmenity(index)"
                                        class="text-safari-green hover:text-safari-green/80"
                                    >
                                        ×
                                    </button>
                                </span>
                            </div>
                            <InputError v-if="form.errors.amenities" class="mt-2" :message="form.errors.amenities" />
                        </div>

                        <!-- Price & Capacity -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="price_from" value="Starting Price" />
                                <TextInput
                                    id="price_from"
                                    v-model="form.price_from"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                    placeholder="e.g., 500.00"
                                />
                                <InputError v-if="form.errors.price_from" class="mt-2" :message="form.errors.price_from" />
                            </div>
                            <div>
                                <InputLabel for="capacity" value="Capacity (Guests)" />
                                <TextInput
                                    id="capacity"
                                    v-model="form.capacity"
                                    type="number"
                                    min="1"
                                    class="mt-1 block w-full"
                                    placeholder="e.g., 20"
                                />
                                <InputError v-if="form.errors.capacity" class="mt-2" :message="form.errors.capacity" />
                            </div>
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
                                <InputError v-if="form.errors.gallery_images" class="mt-2" :message="form.errors.gallery_images" />
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
                            <InputError v-if="form.errors.display_order" class="mt-2" :message="form.errors.display_order" />
                        </div>

                        <!-- Status & Featured -->
                        <div class="space-y-4 rounded-lg bg-gray-50 border border-gray-200 p-4">
                            <div class="flex items-center">
                                <input
                                    id="is_active"
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 text-safari-green focus:ring-safari-green"
                                />
                                <InputLabel for="is_active" value="Active" class="ml-2" />
                            </div>
                            <div class="flex items-center">
                                <input
                                    id="is_featured"
                                    v-model="form.is_featured"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 text-safari-green focus:ring-safari-green"
                                />
                                <InputLabel for="is_featured" value="Feature this lodge/camp on the homepage" class="ml-2" />
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
                        :href="route('admin.lodges.index')"
                        class="text-sm font-medium text-gray-700 hover:text-gray-900"
                    >
                        Cancel
                    </Link>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Create Lodge/Camp
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

