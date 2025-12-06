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
    title: '',
    summary: '',
    badge: '',
    hero_image: null,
    gallery_images: [],
    slug: '',
    service_type_id: null,
    destination_id: null,
    duration_days: 7,
    price_from: null,
    difficulty: '',
    highlights: [],
    inclusions: [],
    exclusions: [],
    tags: [],
    display_order: 0,
    is_featured: false,
    published_at: null,
});

const heroImagePreview = ref(null);
const galleryPreviews = ref([]);

const handleHeroImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        heroImagePreview.value = URL.createObjectURL(file);
        form.hero_image = file;
    } else {
        heroImagePreview.value = null;
        form.hero_image = null;
    }
};

const handleGalleryImagesChange = (event) => {
    const files = Array.from(event.target.files);
    files.forEach((file) => {
        if (file) {
            galleryPreviews.value.push({
                file: file,
                preview: URL.createObjectURL(file),
            });
            form.gallery_images.push(file);
        }
    });
    // Reset the input so the same file can be selected again
    event.target.value = '';
};

const removeGalleryImage = (index) => {
    URL.revokeObjectURL(galleryPreviews.value[index].preview); // Clean up URL
    galleryPreviews.value.splice(index, 1);
    form.gallery_images.splice(index, 1);
};

const triggerFileInput = (inputId) => {
    const input = document.getElementById(inputId);
    if (input) {
        // Small delay to ensure the input is ready
        setTimeout(() => {
            input.click();
        }, 10);
    }
};

const submit = () => {
    // Validate required fields
    if (!form.title || !form.slug || !form.service_type_id || !form.duration_days) {
        alert('Please fill in all required fields (Title, Slug, Service Type, Duration)');
        return;
    }
    
    // Create FormData manually to ensure files are sent correctly
    const formData = new FormData();
    
    // Add all form fields except files
    Object.keys(form.data()).forEach((key) => {
        if (key === 'hero_image' || key === 'gallery_images') {
            return; // Handle files separately
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
    
    // Add hero image
    if (form.hero_image) {
        formData.append('hero_image', form.hero_image);
        console.log('Adding hero image:', form.hero_image.name, form.hero_image.size);
    }
    
    // Add gallery images as array
    if (form.gallery_images && form.gallery_images.length > 0) {
        form.gallery_images.forEach((file, index) => {
            formData.append('gallery_images[]', file);
            console.log(`Adding gallery image ${index + 1}:`, file.name, file.size);
        });
    }
    
    console.log('Submitting form with:', {
        title: form.title,
        hero_image: form.hero_image ? form.hero_image.name : 'none',
        gallery_count: form.gallery_images.length
    });
    
    // Submit using router.post with FormData
    router.post(route('admin.itineraries.store'), formData, {
        forceFormData: true,
        onSuccess: (page) => {
            console.log('Success!', page);
            form.reset();
            heroImagePreview.value = null;
            galleryPreviews.value = [];
            // Redirect will happen automatically
        },
        onError: (errors) => {
            console.error('Form submission errors:', errors);
            alert('Error saving safari: ' + JSON.stringify(errors));
        },
        onFinish: () => {
            console.log('Form submission finished');
        },
    });
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

const generateSlug = () => {
    if (!form.title) return;
    form.slug = form.title
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
};
</script>

<template>
    <Head title="Create Safari (Itinerary)" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">
                        Create Safari (Itinerary)
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Add a new safari package to your website
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
                            <InputLabel for="hero_image" value="Hero Image (Main picture) *" />
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
                                <p class="mt-1 text-xs text-gray-500">Upload the main image that will appear on the safari card.</p>
                                <InputError class="mt-2" :message="form.errors.hero_image" />
                                <div v-if="heroImagePreview" class="mt-4">
                                    <p class="text-xs font-medium text-gray-700 mb-2">Preview:</p>
                                    <img :src="heroImagePreview" alt="Preview" class="h-48 w-full rounded-lg object-cover border border-gray-200" />
                                </div>
                            </div>
                        </div>

                        <!-- Gallery Images Upload -->
                        <div>
                            <InputLabel for="gallery_images" value="Gallery Images (Additional photos)" />
                            <div class="mt-2">
                                <label
                                    for="gallery_images"
                                    @click="triggerFileInput('gallery_images')"
                                    class="flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-sm font-medium text-gray-700 transition-colors hover:border-safari-green hover:bg-gray-100"
                                >
                                    <svg class="mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span v-if="galleryPreviews.length === 0">Choose Gallery Images (Multiple)</span>
                                    <span v-else>Add More Gallery Images</span>
                                </label>
                                <input
                                    id="gallery_images"
                                    type="file"
                                    accept="image/*"
                                    multiple
                                    @change="handleGalleryImagesChange"
                                    class="hidden"
                                />
                                <p class="mt-1 text-xs text-gray-500">Upload multiple images to create a gallery. These will appear in the safari detail page and can be slided through on the safari cards.</p>
                                <InputError class="mt-2" :message="form.errors.gallery_images" />
                                
                                <!-- Gallery Previews -->
                                <div v-if="galleryPreviews.length > 0" class="mt-4">
                                    <p class="text-xs font-medium text-gray-700 mb-2">Gallery Images ({{ galleryPreviews.length }}):</p>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                        <div
                                            v-for="(preview, index) in galleryPreviews"
                                            :key="index"
                                            class="relative group"
                                        >
                                            <img
                                                :src="preview.preview"
                                                :alt="`Gallery image ${index + 1}`"
                                                class="h-32 w-full rounded-lg object-cover border border-gray-200"
                                            />
                                            <button
                                                type="button"
                                                @click="removeGalleryImage(index)"
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
                                placeholder="e.g., Great Migration Serengeti Safari"
                                @blur="generateSlug"
                            />
                            <p class="mt-1 text-xs text-gray-500">The main title that appears on the safari card.</p>
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
                                placeholder="e.g., great-migration-serengeti-safari"
                            />
                            <p class="mt-1 text-xs text-gray-500">This will be used in the website URL. Use lowercase letters, numbers, and hyphens only.</p>
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
                                placeholder="e.g., Follow the famed wildebeest march with private 4x4 game drives..."
                            />
                            <p class="mt-1 text-xs text-gray-500">A brief description that appears on the safari card.</p>
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
                                placeholder="e.g., Signature Collection, Expedition Grade"
                            />
                            <p class="mt-1 text-xs text-gray-500">A small label that appears on the image (e.g., "Signature Collection").</p>
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
                            <p v-if="serviceTypes.length === 0" class="mt-1 text-xs text-amber-600">
                                ⚠️ No service types found. Please create a service type first or contact an administrator.
                            </p>
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
                            <p class="mt-1 text-xs text-gray-500">How many days does this safari last?</p>
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
                                placeholder="e.g., 2500.00"
                            />
                            <p class="mt-1 text-xs text-gray-500">Starting price in USD (leave empty if not applicable).</p>
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
                            <p v-else class="mt-2 text-xs text-gray-500">Click "Add Highlight" to add key features of this safari.</p>
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
                                placeholder="0"
                            />
                            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first. Leave as 0 for first position.</p>
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
                                <p class="mt-1 text-xs text-gray-500">Leave empty to save as draft. Set a date to publish automatically.</p>
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
                        Create Safari
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

