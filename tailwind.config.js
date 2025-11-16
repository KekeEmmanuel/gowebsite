import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/marketing/**/*.{vue,ts,js,jsx,tsx}',
    ],

    theme: {
        extend: {
            colors: {
                safari: {
                    green: '#1F3B2B',
                    gold: '#D99A38',
                    sand: '#F4E9D7',
                },
                charcoal: '#2E2E2E',
            },
            fontFamily: {
                sans: ['"Manrope"', ...defaultTheme.fontFamily.sans],
                heading: ['"Outfit"', ...defaultTheme.fontFamily.sans],
                body: ['"Manrope"', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
