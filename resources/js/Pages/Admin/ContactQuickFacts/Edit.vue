<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Textarea from '@/Components/Textarea.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    contactQuickFact: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    fact: props.contactQuickFact.fact || '',
    display_order: props.contactQuickFact.display_order || 0,
    is_active: props.contactQuickFact.is_active ?? true,
});

const submit = () => {
    form.put(route('admin.contact-quick-facts.update', props.contactQuickFact.id));
};
</script>

<template>
    <Head title="Edit Contact Quick Fact" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">
                        Edit Contact Quick Fact
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Update the quick fact content
                    </p>
                </div>
                <Link
                    :href="route('admin.contact-quick-facts.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Back to Quick Facts
                </Link>
            </div>
        </template>

        <div class="max-w-4xl">
            <form @submit.prevent="submit" class="space-y-6">
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="p-6 space-y-6">
                        <!-- Fact -->
                        <div>
                            <InputLabel for="fact" value="Quick Fact *" />
                            <Textarea
                                id="fact"
                                v-model="form.fact"
                                class="mt-1 block w-full"
                                rows="3"
                                required
                                placeholder="e.g., Dedicated concierge from pre-trip briefing to touchdown back home."
                            />
                            <p class="mt-1 text-xs text-gray-500">A brief statement highlighting a key benefit or feature</p>
                            <InputError class="mt-2" :message="form.errors.fact" />
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
                            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first. Use 0 for first position.</p>
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
                                <InputLabel for="is_active" value="Show this fact on the website" class="ml-2" />
                            </div>
                            <p class="mt-2 text-xs text-gray-600">☑️ Checked = Visible on website | ☐ Unchecked = Hidden from website</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <Link
                        :href="route('admin.contact-quick-facts.index')"
                        class="text-sm font-medium text-gray-700 hover:text-gray-900"
                    >
                        Cancel
                    </Link>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update Quick Fact
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

