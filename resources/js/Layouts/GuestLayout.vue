<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-gray-50 via-gray-100 to-gray-50 px-4 py-8 sm:px-6 lg:px-8">
        <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl shadow-gray-900/10">
            <!-- Logo Section -->
            <div class="flex items-center justify-center border-b border-gray-200 bg-gradient-to-br from-white to-gray-50 px-8 py-8">
                <Link href="/" class="flex items-center justify-center transition-all duration-300 hover:scale-105">
                    <img
                        src="/images/safari/mpya.png"
                        alt="Go Tanzania Safari"
                        class="h-32 w-auto object-contain sm:h-40"
                        style="filter: brightness(0.3) contrast(1.2) saturate(1.1);"
                    />
                </Link>
            </div>

            <!-- Form Section -->
            <div class="px-8 py-8">
                <slot />
            </div>
        </div>
    </div>
</template>
