<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    featureCard: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    icon: props.featureCard.icon,
    title: props.featureCard.title,
    headline: props.featureCard.headline || '',
    copy: props.featureCard.copy,
    count_value: props.featureCard.count_value,
    display_order: props.featureCard.display_order,
    is_active: props.featureCard.is_active,
});

const submit = () => {
    form.put(route('admin.feature-cards.update', props.featureCard.id));
};
</script>

<template>
    <Head title="Edit Feature Card" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Feature Card
                </h2>
                <Link
                    :href="route('admin.feature-cards.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Back to Feature Cards
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="space-y-6">
                            <div>
                                <InputLabel for="icon" value="Card Icon (The picture shown) *" />
                                <select
                                    id="icon"
                                    v-model="form.icon"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-safari-green focus:ring-safari-green"
                                >
                                    <option value="travellers">👥 Travellers (Shows animated counter)</option>
                                    <option value="pricing">💰 Pricing (Shows price tag icon)</option>
                                    <option value="support">🛟 Support (Shows support icon)</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Choose the icon that best represents this feature card.</p>
                                <InputError class="mt-2" :message="form.errors.icon" />
                            </div>

                            <div>
                                <InputLabel for="title" value="Card Title (Main heading) *" />
                                <TextInput
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="e.g., Happy Travellers Yearly"
                                />
                                <p class="mt-1 text-xs text-gray-500">This is the main heading that appears on the card.</p>
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div>
                                <InputLabel for="headline" value="Headline (Optional - Only for Travellers card)" />
                                <TextInput
                                    id="headline"
                                    v-model="form.headline"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="e.g., 500+ Happy Travellers Yearly"
                                />
                                <p class="mt-1 text-xs text-gray-500">Only used for the Travellers card. Leave empty for other card types.</p>
                                <InputError class="mt-2" :message="form.errors.headline" />
                            </div>

                            <div>
                                <InputLabel for="copy" value="Description Text (What the card says) *" />
                                <Textarea
                                    id="copy"
                                    v-model="form.copy"
                                    class="mt-1 block w-full"
                                    rows="4"
                                    required
                                    placeholder="e.g., Expert travel designers crafting hand-picked itineraries and seamless logistics for every guest."
                                />
                                <p class="mt-1 text-xs text-gray-500">A short description that explains what this feature offers to visitors.</p>
                                <InputError class="mt-2" :message="form.errors.copy" />
                            </div>

                            <div>
                                <InputLabel for="count_value" value="Counter Number (For animated counter)" />
                                <TextInput
                                    id="count_value"
                                    v-model="form.count_value"
                                    type="number"
                                    min="0"
                                    class="mt-1 block w-full"
                                    placeholder="e.g., 500"
                                />
                                <div class="mt-2 rounded-lg bg-blue-50 border border-blue-200 p-3">
                                    <p class="text-xs font-medium text-blue-900">💡 When to use:</p>
                                    <ul class="mt-1 text-xs text-blue-700 space-y-0.5 ml-4 list-disc">
                                        <li>Only needed for the "Travellers" card type</li>
                                        <li>This number will animate when visitors hover over the card</li>
                                        <li>Leave empty for other card types (Pricing, Support)</li>
                                    </ul>
                                </div>
                                <InputError class="mt-2" :message="form.errors.count_value" />
                            </div>

                            <div>
                                <InputLabel for="display_order" value="Display Order (Which card appears first?)" />
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
                                        <li><strong>0</strong> = First card (appears on the left)</li>
                                        <li><strong>1</strong> = Second card (appears in the middle)</li>
                                        <li><strong>2</strong> = Third card (appears on the right)</li>
                                    </ul>
                                    <p class="mt-2 text-xs text-amber-600">💡 Tip: Leave as 0 if you want this to be the first card</p>
                                </div>
                                <InputError class="mt-2" :message="form.errors.display_order" />
                            </div>

                            <div class="flex items-center">
                                <input
                                    id="is_active"
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 text-safari-green focus:ring-safari-green"
                                />
                                <InputLabel for="is_active" value="Active" class="ml-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-4">
                            <Link
                                :href="route('admin.feature-cards.index')"
                                class="text-sm text-gray-600 hover:text-gray-900"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Update Feature Card
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

