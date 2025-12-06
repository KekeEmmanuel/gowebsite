<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    itinerary: {
        type: Object,
        required: true,
    },
    serviceTypes: {
        type: Array,
        default: () => [],
    },
    destinations: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    _method: 'put',
    title: props.itinerary.title || '',
    summary: props.itinerary.summary || '',
    badge: props.itinerary.badge || '',
    hero_image: null,
    delete_hero_image: false,
    gallery_images: [],
    delete_gallery_images: [],
    slug: props.itinerary.slug || '',
    service_type_id: props.itinerary.service_type_id || null,
    destination_id: props.itinerary.destination_id || null,
    duration_days: props.itinerary.duration_days || 7,
    price_from: props.itinerary.price_from || null,
    difficulty: props.itinerary.difficulty || '',
    highlights: props.itinerary.highlights || [],
    inclusions: props.itinerary.inclusions || [],
    exclusions: props.itinerary.exclusions || [],
    tags: props.itinerary.tags || [],
    display_order: props.itinerary.display_order || 0,
    is_featured: props.itinerary.is_featured ?? false,
    published_at: props.itinerary.published_at ? props.itinerary.published_at.split(' ')[0] : null,
});

const heroImagePreview = ref(props.itinerary.hero_image_url || null);
const existingGalleryImages = ref(props.itinerary.gallery_images || []);
const newGalleryPreviews = ref([]);
const originalHeroImageUrl = ref(props.itinerary.hero_image_url || null);

const handleHeroImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        heroImagePreview.value = URL.createObjectURL(file);
        form.hero_image = file;
        form.delete_hero_image = false; // Reset delete flag if new image is selected
    } else {
        heroImagePreview.value = originalHeroImageUrl.value;
        form.hero_image = null;
    }
};

const deleteHeroImage = () => {
    if (confirm('Are you sure you want to delete the hero image?')) {
        form.delete_hero_image = true;
        heroImagePreview.value = null;
        form.hero_image = null;
        // Clear the file input
        const input = document.getElementById('hero_image');
        if (input) {
            input.value = '';
        }
    }
};

const handleNewGalleryImagesChange = (event) => {
    const files = Array.from(event.target.files);
    files.forEach((file) => {
        if (file) {
            newGalleryPreviews.value.push({
                file: file,
                preview: URL.createObjectURL(file),
            });
            form.gallery_images.push(file);
        }
    });
    event.target.value = '';
};

const removeNewGalleryImage = (index) => {
    URL.revokeObjectURL(newGalleryPreviews.value[index].preview);
    newGalleryPreviews.value.splice(index, 1);
    form.gallery_images.splice(index, 1);
};

const removeExistingGalleryImage = (mediaId, index) => {
    if (confirm('Are you sure you want to delete this image?')) {
        form.delete_gallery_images.push(mediaId);
        existingGalleryImages.value.splice(index, 1);
    }
};

const triggerFileInput = (inputId) => {
    const input = document.getElementById(inputId);
    if (input) {
        input.click();
    }
};

const addHighlight = () => {
    if (!form.highlights) {
        form.highlights = [];
    }
    form.highlights.push('');
};

const removeHighlight = (index) => {
    form.highlights.splice(index, 1);
};

const submit = () => {
    // Create FormData manually to ensure files are sent correctly
    const formData = new FormData();
    
    // Add all form fields except files
    Object.keys(form.data()).forEach((key) => {
        if (key === 'hero_image' || key === 'gallery_images' || key === '_method') {
            return; // Handle separately
        }
        
        const value = form[key];
        if (Array.isArray(value)) {
            value.forEach((item, index) => {
                formData.append(`${key}[${index}]`, item);
            });
        } else if (value !== null && value !== undefined) {
            formData.append(key, value);
        }
    });
    
    // Add _method for PUT
    formData.append('_method', 'PUT');
    
    // Add hero image if new one is selected
    if (form.hero_image) {
        formData.append('hero_image', form.hero_image);
    }
    
    // Add flag to delete hero image
    if (form.delete_hero_image) {
        formData.append('delete_hero_image', '1');
    }
    
    // Add new gallery images as array
    if (form.gallery_images && form.gallery_images.length > 0) {
        form.gallery_images.forEach((file) => {
            formData.append('gallery_images[]', file);
        });
    }
    
    // Add images to delete
    if (form.delete_gallery_images && form.delete_gallery_images.length > 0) {
        form.delete_gallery_images.forEach((mediaId) => {
            formData.append('delete_gallery_images[]', mediaId);
        });
    }
    
    // Submit using router.post with FormData
    router.post(route('admin.itineraries.update', props.itinerary.id), formData, {
        forceFormData: true,
        onSuccess: () => {
            form.gallery_images = [];
            newGalleryPreviews.value = [];
            form.delete_gallery_images = [];
            router.reload({ only: ['itinerary'] });
        },
        onError: (errors) => {
            console.error('Form submission errors:', errors);
        },
    });
};
</script>

<template>
    <Head title="Edit Safari (Itinerary)" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">
                        Edit Safari (Itinerary)
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Update the safari package content
                    </p>
                </div>
                <Link
                    :href="route('admin.itineraries.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Back to Safaris
                </Link>
            </div>
        </template>

        <div class="max-w-4xl">
            <form @submit.prevent="submit" class="space-y-6">
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="p-6 space-y-6">
                        <!-- Hero Image Upload -->
                        <div>
                            <InputLabel for="hero_image" value="Hero Image (Main picture)" />
                            <div class="mt-2">
                                <label
                                    for="hero_image"
                                    @click="triggerFileInput('hero_image')"
                                    class="flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-sm font-medium text-gray-700 transition-colors hover:border-safari-green hover:bg-gray-100"
                                >
                                    <svg class="mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span v-if="!heroImagePreview">Choose Hero Image</span>
                                    <span v-else>Change Hero Image</span>
                                </label>
                                <input
                                    id="hero_image"
                                    type="file"
                                    accept="image/*"
                                    @change="handleHeroImageChange"
                                    class="hidden"
                                />
                                <p class="mt-1 text-xs text-gray-500">Upload a new image or leave empty to keep the current one.</p>
                                <InputError class="mt-2" :message="form.errors.hero_image" />
                                <div v-if="heroImagePreview || originalHeroImageUrl" class="mt-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-xs font-medium text-gray-700">Current Hero Image:</p>
                                        <button
                                            v-if="originalHeroImageUrl && !form.delete_hero_image"
                                            type="button"
                                            @click="deleteHeroImage"
                                            class="text-xs text-red-600 hover:text-red-800 font-medium"
                                        >
                                            Delete Image
                                        </button>
                                    </div>
                                    <div v-if="heroImagePreview" class="relative group">
                                        <img :src="heroImagePreview" alt="Preview" class="h-48 w-full rounded-lg object-cover border border-gray-200" />
                                        <div v-if="form.delete_hero_image" class="absolute inset-0 bg-red-500/20 border-2 border-red-500 rounded-lg flex items-center justify-center">
                                            <span class="text-red-600 font-semibold">Will be deleted</span>
                                        </div>
                                    </div>
                                    <div v-else-if="originalHeroImageUrl && !form.delete_hero_image" class="relative group">
                                        <img :src="originalHeroImageUrl" alt="Current Hero Image" class="h-48 w-full rounded-lg object-cover border border-gray-200" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Existing Gallery Images -->
                        <div v-if="existingGalleryImages.length > 0 || form.delete_gallery_images.length > 0">
                            <InputLabel value="Existing Gallery Images" />
                            <div class="mt-2">
                                <p class="text-xs text-gray-500 mb-3">Current gallery images. Click the X to remove them.</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    <div
                                        v-for="(image, index) in existingGalleryImages"
                                        :key="image.id"
                                        class="relative group"
                                    >
                                        <img
                                            :src="image.url"
                                            :alt="image.name || 'Gallery image'"
                                            class="h-32 w-full rounded-lg object-cover border border-gray-200"
                                            :class="{'opacity-50': form.delete_gallery_images.includes(image.id)}"
                                        />
                                        <div v-if="form.delete_gallery_images.includes(image.id)" class="absolute inset-0 bg-red-500/20 border-2 border-red-500 rounded-lg flex items-center justify-center">
                                            <span class="text-red-600 font-semibold text-xs">Will be deleted</span>
                                        </div>
                                        <button
                                            type="button"
                                            @click="removeExistingGalleryImage(image.id, index)"
                                            class="absolute top-2 right-2 rounded-full bg-red-500 p-1.5 text-white opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600 z-10"
                                            aria-label="Remove image"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- New Gallery Images Upload -->
                        <div>
                            <InputLabel for="new_gallery_images" value="Add New Gallery Images" />
                            <div class="mt-2">
                                <label
                                    for="new_gallery_images"
                                    @click="triggerFileInput('new_gallery_images')"
                                    class="flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-sm font-medium text-gray-700 transition-colors hover:border-safari-green hover:bg-gray-100"
                                >
                                    <svg class="mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span v-if="newGalleryPreviews.length === 0">Choose Gallery Images (Multiple)</span>
                                    <span v-else>Add More Gallery Images</span>
                                </label>
                                <input
                                    id="new_gallery_images"
                                    type="file"
                                    accept="image/*"
                                    multiple
                                    @change="handleNewGalleryImagesChange"
                                    class="hidden"
                                />
                                <p class="mt-1 text-xs text-gray-500">Select additional images for the safari gallery.</p>
                                <InputError class="mt-2" :message="form.errors.gallery_images" />
                                
                                <!-- New Gallery Previews -->
                                <div v-if="newGalleryPreviews.length > 0" class="mt-4">
                                    <p class="text-xs font-medium text-gray-700 mb-2">New Gallery Images ({{ newGalleryPreviews.length }}):</p>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                        <div
                                            v-for="(preview, index) in newGalleryPreviews"
                                            :key="index"
                                            class="relative group"
                                        >
                                            <img
                                                :src="preview.preview"
                                                :alt="`New Gallery Image ${index + 1}`"
                                                class="h-32 w-full rounded-lg object-cover border border-gray-200"
                                            />
                                            <button
                                                type="button"
                                                @click="removeNewGalleryImage(index)"
                                                class="absolute top-2 right-2 rounded-full bg-red-500 p-1.5 text-white opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600"
                                                aria-label="Remove image"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <div>
                            <InputLabel for="title" value="Safari Title (Main heading) *" />
                            <TextInput
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <!-- Slug -->
                        <div>
                            <InputLabel for="slug" value="URL Slug (For the website address) *" />
                            <TextInput
                                id="slug"
                                v-model="form.slug"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.slug" />
                        </div>

                        <!-- Summary -->
                        <div>
                            <InputLabel for="summary" value="Summary (Short description)" />
                            <Textarea
                                id="summary"
                                v-model="form.summary"
                                class="mt-1 block w-full"
                                rows="3"
                            />
                            <InputError class="mt-2" :message="form.errors.summary" />
                        </div>

                        <!-- Badge -->
                        <div>
                            <InputLabel for="badge" value="Badge Text (Optional)" />
                            <TextInput
                                id="badge"
                                v-model="form.badge"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.badge" />
                        </div>

                        <!-- Service Type -->
                        <div>
                            <InputLabel for="service_type_id" value="Service Type *" />
                            <select
                                id="service_type_id"
                                v-model="form.service_type_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-safari-green focus:ring-safari-green"
                                required
                            >
                                <option value="">Select a service type</option>
                                <option v-for="type in serviceTypes" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.service_type_id" />
                        </div>

                        <!-- Destination -->
                        <div>
                            <InputLabel for="destination_id" value="Destination (Optional)" />
                            <select
                                id="destination_id"
                                v-model="form.destination_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-safari-green focus:ring-safari-green"
                            >
                                <option :value="null">No destination</option>
                                <option v-for="destination in destinations" :key="destination.id" :value="destination.id">
                                    {{ destination.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.destination_id" />
                        </div>

                        <!-- Duration -->
                        <div>
                            <InputLabel for="duration_days" value="Duration (Number of days) *" />
                            <TextInput
                                id="duration_days"
                                v-model="form.duration_days"
                                type="number"
                                min="1"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.duration_days" />
                        </div>

                        <!-- Price -->
                        <div>
                            <InputLabel for="price_from" value="Starting Price (Optional)" />
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

                        <!-- Highlights -->
                        <div>
                            <div class="flex items-center justify-between">
                                <InputLabel for="highlights" value="Highlights (Key features)" />
                                <button
                                    type="button"
                                    @click="addHighlight"
                                    class="text-xs text-safari-green hover:text-safari-green/80"
                                >
                                    + Add Highlight
                                </button>
                            </div>
                            <div v-if="form.highlights && form.highlights.length > 0" class="mt-2 space-y-2">
                                <div v-for="(highlight, index) in form.highlights" :key="index" class="flex gap-2">
                                    <TextInput
                                        v-model="form.highlights[index]"
                                        type="text"
                                        class="flex-1"
                                        :placeholder="`Highlight ${index + 1}`"
                                    />
                                    <button
                                        type="button"
                                        @click="removeHighlight(index)"
                                        class="rounded-md bg-red-100 px-3 py-2 text-sm text-red-600 hover:bg-red-200"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.highlights" />
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
                                <InputLabel for="is_featured" value="Feature this safari on the homepage" class="ml-2" />
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
                        :href="route('admin.itineraries.index')"
                        class="text-sm font-medium text-gray-700 hover:text-gray-900"
                    >
                        Cancel
                    </Link>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update Safari
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

