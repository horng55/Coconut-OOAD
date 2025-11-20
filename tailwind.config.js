import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import colors from 'tailwindcss/colors';
import aspectRatio from '@tailwindcss/aspect-ratio';
import flowbitePlugin from 'flowbite/plugin';

export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/js/**/*.vue',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif'],
            },
            colors: {
                school: {
                    primary: '#2563eb',      // Blue - Trust, Knowledge
                    secondary: '#059669',    // Green - Growth, Success
                    accent: '#f59e0b',       // Amber - Achievement
                    dark: '#1e40af',        // Dark Blue
                    light: '#dbeafe',       // Light Blue
                },
                customColor: colors.teal,
            },
        },
    },
    plugins: [
        forms,
        typography,
        aspectRatio,
        flowbitePlugin,
    ],
};
