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
    label: '',
    subtitle: '',
    description: '',
    cta_label: '',
    cta_url: '#safaris',
    position: 0,
    is_active: true,
    image_base64: null,
});

const imagePreview = ref(null);

const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            // e.target.result is already a data URL (base64 with prefix)
            const base64String = e.target.result;
            form.image_base64 = base64String;
            imagePreview.value = base64String;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post(route('admin.hero-slides.store'));
};
</script>

<template>
    <Head title="Create Hero Slide" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">
                        Create Hero Slide
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Add a new slide to your homepage carousel
                    </p>
                </div>
                <Link
                    :href="route('admin.hero-slides.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Back to Hero Slides
                </Link>
            </div>
        </template>

        <div class="max-w-4xl">
            <form @submit.prevent="submit" class="space-y-6">
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="p-6 space-y-6">
                        <!-- Image Upload -->
                        <div>
                            <InputLabel for="image" value="Slide Image (The background picture) *" />
                            <div class="mt-2">
                                <input
                                    id="image"
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageChange"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-safari-green file:text-white hover:file:bg-safari-green/90"
                                />
                                <div class="mt-2 rounded-lg bg-purple-50 border border-purple-200 p-3">
                                    <p class="text-xs font-medium text-purple-900">📸 Image Tips:</p>
                                    <ul class="mt-1 text-xs text-purple-700 space-y-0.5 ml-4 list-disc">
                                        <li>Use high-quality images (1920x1080px or larger recommended)</li>
                                        <li>Choose images that represent Tanzania safaris, wildlife, or landscapes</li>
                                        <li>Make sure text will be readable over the image (darker images work better)</li>
                                    </ul>
                                </div>
                                <InputError class="mt-2" :message="form.errors.image_base64" />
                                <div v-if="imagePreview" class="mt-4">
                                    <p class="text-xs font-medium text-gray-700 mb-2">Preview:</p>
                                    <img :src="imagePreview" alt="Preview" class="h-48 w-full rounded-lg object-cover border border-gray-200" />
                                </div>
                            </div>
                        </div>

                        <!-- Label (Small text above title) -->
                        <div>
                            <InputLabel for="label" value="Small Text Above Title (Optional)" />
                            <TextInput
                                id="label"
                                v-model="form.label"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g., Tailored Luxury Adventures"
                            />
                            <p class="mt-1 text-xs text-gray-500">This appears as small text above the main title. Leave empty if not needed.</p>
                            <InputError class="mt-2" :message="form.errors.label" />
                        </div>

                        <!-- Title -->
                        <div>
                            <InputLabel for="title" value="Main Title (The Big Heading) *" />
                            <TextInput
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                placeholder="e.g., Encounter Tanzania's wild soul – from savannah to spice isles"
                            />
                            <p class="mt-1 text-xs text-gray-500">This is the main headline that visitors will see first. Make it compelling!</p>
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <!-- Description -->
                        <div>
                            <InputLabel for="description" value="Description Text (Optional)" />
                            <Textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full"
                                rows="4"
                                placeholder="e.g., Join expert guides for once-in-a-lifetime safaris blending iconic wildlife moments, Kilimanjaro summits, and barefoot beach retreats."
                            />
                            <p class="mt-1 text-xs text-gray-500">A short paragraph that describes what this slide is about. This appears below the title.</p>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <!-- CTA Label -->
                        <div>
                            <InputLabel for="cta_label" value="Button Text (What the button says) *" />
                            <TextInput
                                id="cta_label"
                                v-model="form.cta_label"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                placeholder="e.g., Explore Signature Safaris"
                            />
                            <p class="mt-1 text-xs text-gray-500">The text that appears on the main call-to-action button.</p>
                            <InputError class="mt-2" :message="form.errors.cta_label" />
                        </div>

                        <!-- CTA URL -->
                        <div>
                            <InputLabel for="cta_url" value="Where should the button link to? *" />
                            <TextInput
                                id="cta_url"
                                v-model="form.cta_url"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                placeholder="#safaris"
                            />
                            <div class="mt-2 space-y-1 rounded-lg bg-blue-50 border border-blue-200 p-3">
                                <p class="text-xs font-medium text-blue-900">Examples:</p>
                                <ul class="text-xs text-blue-700 space-y-0.5 ml-4 list-disc">
                                    <li><code class="bg-blue-100 px-1 rounded">#safaris</code> - Links to Safaris section on the same page</li>
                                    <li><code class="bg-blue-100 px-1 rounded">#about</code> - Links to About section</li>
                                    <li><code class="bg-blue-100 px-1 rounded">#contact</code> - Links to Contact section</li>
                                    <li><code class="bg-blue-100 px-1 rounded">#destinations</code> - Links to Destinations section</li>
                                </ul>
                                <p class="mt-2 text-xs text-blue-600">💡 Tip: Use <code class="bg-blue-100 px-1 rounded">#</code> followed by the section name to link within the page</p>
                            </div>
                            <InputError class="mt-2" :message="form.errors.cta_url" />
                        </div>

                        <!-- Position -->
                        <div>
                            <InputLabel for="position" value="Display Order (Which slide appears first?)" />
                            <TextInput
                                id="position"
                                v-model="form.position"
                                type="number"
                                min="0"
                                class="mt-1 block w-full"
                                placeholder="0"
                            />
                            <div class="mt-2 space-y-1 rounded-lg bg-amber-50 border border-amber-200 p-3">
                                <p class="text-xs font-medium text-amber-900">How it works:</p>
                                <ul class="text-xs text-amber-700 space-y-0.5 ml-4 list-disc">
                                    <li><strong>0</strong> = First slide (appears first)</li>
                                    <li><strong>1</strong> = Second slide</li>
                                    <li><strong>2</strong> = Third slide</li>
                                    <li>And so on...</li>
                                </ul>
                                <p class="mt-2 text-xs text-amber-600">💡 Tip: Leave as 0 if you want this to be the first slide</p>
                            </div>
                            <InputError class="mt-2" :message="form.errors.position" />
                        </div>

                        <!-- Active Status -->
                        <div class="rounded-lg bg-gray-50 border border-gray-200 p-4">
                            <div class="flex items-center">
                                <input
                                    id="is_active"
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 text-safari-green focus:ring-safari-green"
                                />
                                <InputLabel for="is_active" value="Show this slide on the website" class="ml-2" />
                            </div>
                            <p class="mt-2 text-xs text-gray-600">☑️ Checked = Visible on website | ☐ Unchecked = Hidden from website</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <Link
                        :href="route('admin.hero-slides.index')"
                        class="text-sm font-medium text-gray-700 hover:text-gray-900"
                    >
                        Cancel
                    </Link>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Create Hero Slide
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

