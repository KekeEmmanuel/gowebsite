<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    aboutHighlight: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    title: props.aboutHighlight.title || '',
    copy: props.aboutHighlight.copy || '',
    display_order: props.aboutHighlight.display_order || 0,
    is_active: props.aboutHighlight.is_active ?? true,
});

const submit = () => {
    form.put(route('admin.about-highlights.update', props.aboutHighlight.id));
};
</script>

<template>
    <Head title="Edit About Highlight" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">
                        Edit About Highlight
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Update the highlight content
                    </p>
                </div>
                <Link
                    :href="route('admin.about-highlights.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Back to About Highlights
                </Link>
            </div>
        </template>

        <div class="max-w-4xl">
            <form @submit.prevent="submit" class="space-y-6">
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="p-6 space-y-6">
                        <!-- Title -->
                        <div>
                            <InputLabel for="title" value="Highlight Title (Main heading) *" />
                            <TextInput
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                placeholder="e.g., Dedicated Journey Architect"
                            />
                            <p class="mt-1 text-xs text-gray-500">This is the main heading that appears on the highlight card.</p>
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <!-- Copy -->
                        <div>
                            <InputLabel for="copy" value="Description Text (What the highlight says) *" />
                            <Textarea
                                id="copy"
                                v-model="form.copy"
                                class="mt-1 block w-full"
                                rows="4"
                                required
                                placeholder="e.g., A single expert consultant curates, books, and monitors every detail from the first call to your return home."
                            />
                            <p class="mt-1 text-xs text-gray-500">A short description that explains what this highlight offers to visitors.</p>
                            <InputError class="mt-2" :message="form.errors.copy" />
                        </div>

                        <!-- Display Order -->
                        <div>
                            <InputLabel for="display_order" value="Display Order (Which highlight appears first?)" />
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
                                    <li><strong>0</strong> = First highlight (appears on the left)</li>
                                    <li><strong>1</strong> = Second highlight (appears in the middle)</li>
                                    <li><strong>2</strong> = Third highlight (appears on the right)</li>
                                </ul>
                                <p class="mt-2 text-xs text-amber-600">💡 Tip: Leave as 0 if you want this to be the first highlight</p>
                            </div>
                            <InputError class="mt-2" :message="form.errors.display_order" />
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
                                <InputLabel for="is_active" value="Show this highlight on the website" class="ml-2" />
                            </div>
                            <p class="mt-2 text-xs text-gray-600">☑️ Checked = Visible on website | ☐ Unchecked = Hidden from website</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <Link
                        :href="route('admin.about-highlights.index')"
                        class="text-sm font-medium text-gray-700 hover:text-gray-900"
                    >
                        Cancel
                    </Link>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update About Highlight
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

