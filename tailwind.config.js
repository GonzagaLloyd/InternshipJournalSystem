import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                cinzel: ['Cinzel', 'serif'],
                cormorant: ['Cormorant Garamond', 'serif'],
            },
            colors: {
                // Scriptorium Custom Palette
                brass: 'var(--color-accent-brass)',
                umber: 'var(--color-accent-umber)',
                parchment: 'var(--color-text-parchment)',
                cream: 'var(--color-text-cream)',
                void: 'var(--color-background-void)',
                obsidian: 'var(--color-background-obsidian)',
                coal: 'var(--color-background-coal)',
            }
        },
    },

    plugins: [forms],
};
