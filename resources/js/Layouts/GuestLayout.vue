<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0">
        <div>
            <Link href="/" class="flex items-center justify-center">
                <img
                    src="/images/safari/mpya.png"
                    alt="Go Tanzania Safari"
                    class="h-24 w-auto object-contain transition-all duration-300 hover:scale-105"
                    style="filter: brightness(0.3) contrast(1.2) saturate(1.1);"
                />
            </Link>
        </div>

        <div class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>
