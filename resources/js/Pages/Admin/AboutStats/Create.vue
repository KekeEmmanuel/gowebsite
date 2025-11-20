<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    value: '',
    label: '',
    display_order: 0,
    is_active: true,
});

const submit = () => {
    form.post(route('admin.about-stats.store'));
};
</script>

<template>
    <Head title="Create About Stat" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">
                        Create About Stat
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Add a new statistic to the About section
                    </p>
                </div>
                <Link
                    :href="route('admin.about-stats.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Back to About Stats
                </Link>
            </div>
        </template>

        <div class="max-w-4xl">
            <form @submit.prevent="submit" class="space-y-6">
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="p-6 space-y-6">
                        <!-- Value -->
                        <div>
                            <InputLabel for="value" value="Stat Value (The number or percentage) *" />
                            <TextInput
                                id="value"
                                v-model="form.value"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                placeholder="e.g., 18+, 32, 96%"
                            />
                            <p class="mt-1 text-xs text-gray-500">This is the big number that appears prominently. Can include symbols like + or %.</p>
                            <InputError class="mt-2" :message="form.errors.value" />
                        </div>

                        <!-- Label -->
                        <div>
                            <InputLabel for="label" value="Stat Label (What it represents) *" />
                            <TextInput
                                id="label"
                                v-model="form.label"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                placeholder="e.g., Years curating luxury safaris"
                            />
                            <p class="mt-1 text-xs text-gray-500">A short description of what this statistic represents.</p>
                            <InputError class="mt-2" :message="form.errors.label" />
                        </div>

                        <!-- Display Order -->
                        <div>
                            <InputLabel for="display_order" value="Display Order (Which stat appears first?)" />
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
                                    <li><strong>0</strong> = First stat (appears on top)</li>
                                    <li><strong>1</strong> = Second stat</li>
                                    <li><strong>2</strong> = Third stat</li>
                                </ul>
                                <p class="mt-2 text-xs text-amber-600">💡 Tip: Leave as 0 if you want this to be the first stat</p>
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
                                <InputLabel for="is_active" value="Show this stat on the website" class="ml-2" />
                            </div>
                            <p class="mt-2 text-xs text-gray-600">☑️ Checked = Visible on website | ☐ Unchecked = Hidden from website</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <Link
                        :href="route('admin.about-stats.index')"
                        class="text-sm font-medium text-gray-700 hover:text-gray-900"
                    >
                        Cancel
                    </Link>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Create About Stat
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

