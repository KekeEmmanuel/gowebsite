<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
            <p class="mt-1 text-sm text-gray-600">Sign in to your admin account</p>
        </div>

        <div v-if="status" class="mb-4 rounded-lg bg-green-50 p-3 text-sm font-medium text-green-800">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Email" class="text-sm font-semibold text-gray-700" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-2 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" class="text-sm font-semibold text-gray-700" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-2 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm font-medium text-safari-gold hover:text-safari-gold/80 hover:underline focus:outline-none focus:ring-2 focus:ring-safari-gold focus:ring-offset-2 rounded"
                >
                    Forgot password?
                </Link>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center bg-gradient-to-r from-safari-gold to-safari-gold/90 hover:from-safari-gold/90 hover:to-safari-gold font-semibold shadow-md hover:shadow-lg transition-all duration-200"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="!form.processing">Sign In</span>
                    <span v-else class="flex items-center">
                        <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Signing in...
                    </span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
