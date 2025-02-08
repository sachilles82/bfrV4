import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import colors from 'tailwindcss/colors';

function withOpacity(variableName) {
    return ({ opacityValue }) => {
        if (opacityValue !== undefined) {
            return `rgb(var(${variableName}), ${opacityValue})`;
        }
        return `rgb(var(${variableName}))`;
    };
}
/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'selector',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './vendor/livewire/flux-pro/stubs/**/*.blade.php',
        './vendor/livewire/flux/stubs/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },


            colors: {
                // Re-assign Flux's gray of choice...

                zinc: colors.gray,
                // gray: colors.gray,


                gray: {
                    50:  withOpacity('--colors-gray-50'),
                    100: withOpacity('--colors-gray-100'),
                    200: withOpacity('--colors-gray-200'),
                    300: withOpacity('--colors-gray-300'),
                    400: withOpacity('--colors-gray-400'),
                    500: withOpacity('--colors-gray-500'),
                    600: withOpacity('--colors-gray-600'),
                    700: withOpacity('--colors-gray-700'),
                    800: withOpacity('--colors-gray-800'),
                    900: withOpacity('--colors-gray-900'),
                    950: withOpacity('--colors-gray-950'),
                },
                // Hier "verbiegen" wir indigo → orange (oder was auch immer du willst):
                indigo: {
                    50:  withOpacity('--colors-indigo-50'),
                    100: withOpacity('--colors-indigo-100'),
                    200: withOpacity('--colors-indigo-200'),
                    300: withOpacity('--colors-indigo-300'),
                    400: withOpacity('--colors-indigo-400'),
                    500: withOpacity('--colors-indigo-500'),
                    600: withOpacity('--colors-indigo-600'),
                    700: withOpacity('--colors-indigo-700'),
                    800: withOpacity('--colors-indigo-800'),
                    900: withOpacity('--colors-indigo-900'),
                    950: withOpacity('--colors-indigo-950'),
                },
                accent: {

                    navigation: 'var(--color-navigation)',
                    'navigation-button': 'var(--color-navigation-button)',
                    'navigation-icon': 'var(--color-navigation-icon)',
                    'navigation-label': 'var(--color-navigation-label)',

                    'navigation-text': 'var(--color-navigation-text)',
                    focus: 'var(--color-focus)',
                    // background: 'var(--color-background)',

                    DEFAULT: 'var(--color-accent)',
                    content: 'var(--color-accent-content)',
                    foreground: 'var(--color-accent-foreground)',
                },

            },
        },
    },

    plugins: [
        forms, typography,
    ],

};
