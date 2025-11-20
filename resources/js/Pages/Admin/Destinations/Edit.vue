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
    destination: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.destination.name || '',
    slug: props.destination.slug || '',
    region: props.destination.region || '',
    teaser: props.destination.teaser || '',
    tag: props.destination.tag || '',
    description: props.destination.description || '',
    image_base64: props.destination.image_base64 || null,
    map_embed_url: props.destination.map_embed_url || '',
    is_featured: props.destination.is_featured ?? false,
    display_order: props.destination.display_order || 0,
    published_at: props.destination.published_at || null,
});

const imagePreview = ref(
    props.destination.image_base64 || null
);

const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const base64String = e.target.result;
            form.image_base64 = base64String;
            imagePreview.value = base64String;
        };
        reader.readAsDataURL(file);
    }
};

const generateSlug = () => {
    if (!form.name) return;
    form.slug = form.name
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
};

const submit = () => {
    form.put(route('admin.destinations.update', props.destination.id));
};
</script>

<template>
    <Head title="Edit Destination" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">
                        Edit Destination
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Update destination information
                    </p>
                </div>
                <Link
                    :href="route('admin.destinations.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Back to Destinations
                </Link>
            </div>
        </template>

        <div class="max-w-4xl">
            <form @submit.prevent="submit" class="space-y-6">
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="p-6 space-y-6">
                        <!-- Image Upload -->
                        <div>
                            <InputLabel for="image" value="Destination Image (The main picture) *" />
                            <div class="mt-2">
                                <input
                                    id="image"
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageChange"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-safari-green file:text-white hover:file:bg-safari-green/90"
                                />
                                <p class="mt-1 text-xs text-gray-500">Upload a high-quality image that represents this destination.</p>
                                <InputError class="mt-2" :message="form.errors.image_base64" />
                                <div v-if="imagePreview" class="mt-4">
                                    <p class="text-xs font-medium text-gray-700 mb-2">Preview:</p>
                                    <img :src="imagePreview" alt="Preview" class="h-48 w-full rounded-lg object-cover border border-gray-200" />
                                </div>
                            </div>
                        </div>

                        <!-- Name -->
                        <div>
                            <InputLabel for="name" value="Destination Name (Main heading) *" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                placeholder="e.g., Serengeti National Park"
                                @blur="generateSlug"
                            />
                            <p class="mt-1 text-xs text-gray-500">The main title that appears on the destination card.</p>
                            <InputError class="mt-2" :message="form.errors.name" />
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
                                placeholder="e.g., serengeti-national-park"
                            />
                            <p class="mt-1 text-xs text-gray-500">This will be used in the website URL. Use lowercase letters, numbers, and hyphens only.</p>
                            <InputError class="mt-2" :message="form.errors.slug" />
                        </div>

                        <!-- Tag -->
                        <div>
                            <InputLabel for="tag" value="Tag (Small label shown on image)" />
                            <TextInput
                                id="tag"
                                v-model="form.tag"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g., Great Migration, World Heritage"
                            />
                            <p class="mt-1 text-xs text-gray-500">A small label that appears on the destination image (e.g., "Great Migration").</p>
                            <InputError class="mt-2" :message="form.errors.tag" />
                        </div>

                        <!-- Region -->
                        <div>
                            <InputLabel for="region" value="Region" />
                            <TextInput
                                id="region"
                                v-model="form.region"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g., Northern Circuit, Southern Circuit"
                            />
                            <p class="mt-1 text-xs text-gray-500">The region where this destination is located.</p>
                            <InputError class="mt-2" :message="form.errors.region" />
                        </div>

                        <!-- Teaser -->
                        <div>
                            <InputLabel for="teaser" value="Teaser (Short description)" />
                            <TextInput
                                id="teaser"
                                v-model="form.teaser"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g., River-crossing drama, predator action, endless horizons"
                            />
                            <p class="mt-1 text-xs text-gray-500">A brief one-line description.</p>
                            <InputError class="mt-2" :message="form.errors.teaser" />
                        </div>

                        <!-- Description -->
                        <div>
                            <InputLabel for="description" value="Description (Full details)" />
                            <Textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full"
                                rows="5"
                                placeholder="e.g., River-crossing drama, predator action, endless horizons under technicolour sunsets."
                            />
                            <p class="mt-1 text-xs text-gray-500">A detailed description of the destination.</p>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <!-- Display Order -->
                        <div>
                            <InputLabel for="display_order" value="Display Order (Which destination appears first?)" />
                            <TextInput
                                id="display_order"
                                v-model="form.display_order"
                                type="number"
                                min="0"
                                class="mt-1 block w-full"
                                placeholder="0"
                            />
                            <div class="mt-2 space-y-1 rounded-lg bg-amber-50 border border-amber-200 p-3">
                                <p class="text-xs font-medium text-amber-900">How it works:</p>
                                <ul class="text-xs text-amber-700 space-y-0.5 ml-4 list-disc">
                                    <li><strong>0</strong> = First destination (appears first)</li>
                                    <li><strong>1</strong> = Second destination</li>
                                    <li><strong>2</strong> = Third destination</li>
                                </ul>
                                <p class="mt-2 text-xs text-amber-600">💡 Tip: Leave as 0 if you want this to be the first destination</p>
                            </div>
                            <InputError class="mt-2" :message="form.errors.display_order" />
                        </div>

                        <!-- Featured -->
                        <div class="flex items-center">
                            <input
                                id="is_featured"
                                v-model="form.is_featured"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-safari-green focus:ring-safari-green"
                            />
                            <InputLabel for="is_featured" value="Featured Destination" class="ml-2" />
                            <p class="ml-2 text-xs text-gray-500">Featured destinations appear prominently on the homepage.</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <Link
                        :href="route('admin.destinations.index')"
                        class="text-sm text-gray-600 hover:text-gray-900"
                    >
                        Cancel
                    </Link>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update Destination
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

