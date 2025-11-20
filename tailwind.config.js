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
                    'green-light': '#2D4A3A',
                    'green-dark': '#15241A',
                    gold: '#D99A38',
                    'gold-light': '#E8B563',
                    'gold-dark': '#B8822A',
                    sand: '#F4E9D7',
                    'sand-light': '#FAF5ED',
                    'sand-dark': '#E8DCC4',
                },
                charcoal: {
                    DEFAULT: '#1A1A1A',
                    light: '#2E2E2E',
                    lighter: '#4A4A4A',
                    dark: '#0F0F0F',
                },
            },
            fontFamily: {
                sans: ['"Manrope"', ...defaultTheme.fontFamily.sans],
                heading: ['"Outfit"', ...defaultTheme.fontFamily.sans],
                body: ['"Manrope"', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                'display': ['4.5rem', { lineHeight: '1.1', letterSpacing: '-0.02em', fontWeight: '700' }],
                'display-sm': ['3.5rem', { lineHeight: '1.1', letterSpacing: '-0.02em', fontWeight: '700' }],
            },
            boxShadow: {
                'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                'medium': '0 4px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 30px -5px rgba(0, 0, 0, 0.05)',
                'large': '0 10px 40px -10px rgba(0, 0, 0, 0.15), 0 20px 50px -10px rgba(0, 0, 0, 0.1)',
                'glow-gold': '0 0 20px rgba(217, 154, 56, 0.3), 0 0 40px rgba(217, 154, 56, 0.1)',
                'glow-green': '0 0 20px rgba(31, 59, 43, 0.2), 0 0 40px rgba(31, 59, 43, 0.1)',
            },
            backdropBlur: {
                xs: '2px',
            },
            animation: {
                'fade-in': 'fadeIn 0.6s ease-out',
                'fade-in-up': 'fadeInUp 0.6s ease-out',
                'slide-in': 'slideIn 0.5s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideIn: {
                    '0%': { transform: 'translateX(-20px)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
            },
        },
    },

    plugins: [forms],
};
