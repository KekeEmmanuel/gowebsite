<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    contactChannel: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    label: props.contactChannel.label || '',
    value: props.contactChannel.value || '',
    detail: props.contactChannel.detail || '',
    display_order: props.contactChannel.display_order || 0,
    is_active: props.contactChannel.is_active ?? true,
});

const submit = () => {
    form.put(route('admin.contact-channels.update', props.contactChannel.id));
};
</script>

<template>
    <Head title="Edit Contact Channel" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">
                        Edit Contact Channel
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Update the contact channel information
                    </p>
                </div>
                <Link
                    :href="route('admin.contact-channels.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Back to Contact Channels
                </Link>
            </div>
        </template>

        <div class="max-w-4xl">
            <form @submit.prevent="submit" class="space-y-6">
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="p-6 space-y-6">
                        <!-- Label -->
                        <div>
                            <InputLabel for="label" value="Label (e.g., Call, Email, WhatsApp) *" />
                            <TextInput
                                id="label"
                                v-model="form.label"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                placeholder="e.g., Call, Email, WhatsApp"
                            />
                            <p class="mt-1 text-xs text-gray-500">The type of contact channel (Call, Email, WhatsApp, etc.)</p>
                            <InputError class="mt-2" :message="form.errors.label" />
                        </div>

                        <!-- Value -->
                        <div>
                            <InputLabel for="value" value="Contact Value (Phone number, email address, etc.) *" />
                            <TextInput
                                id="value"
                                v-model="form.value"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                placeholder="e.g., +255 (0) 742 123 456 or bookings@gotanzaniasafari.com"
                            />
                            <p class="mt-1 text-xs text-gray-500">The actual contact information (phone number, email, etc.)</p>
                            <InputError class="mt-2" :message="form.errors.value" />
                        </div>

                        <!-- Detail -->
                        <div>
                            <InputLabel for="detail" value="Detail (Optional description)" />
                            <Textarea
                                id="detail"
                                v-model="form.detail"
                                class="mt-1 block w-full"
                                rows="2"
                                placeholder="e.g., Daily 08:00 – 20:00 East Africa Time"
                            />
                            <p class="mt-1 text-xs text-gray-500">Additional information about this contact channel (hours, response time, etc.)</p>
                            <InputError class="mt-2" :message="form.errors.detail" />
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
                                <InputLabel for="is_active" value="Show this channel on the website" class="ml-2" />
                            </div>
                            <p class="mt-2 text-xs text-gray-600">☑️ Checked = Visible on website | ☐ Unchecked = Hidden from website</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <Link
                        :href="route('admin.contact-channels.index')"
                        class="text-sm font-medium text-gray-700 hover:text-gray-900"
                    >
                        Cancel
                    </Link>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update Contact Channel
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

