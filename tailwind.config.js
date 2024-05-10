import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors');

delete colors['lightBlue'];
delete colors['warmGray'];
delete colors['trueGray'];
delete colors['coolGray'];
delete colors['blueGray'];

export default {
    important: true,
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
        colors: {
            ...colors
        },
    },
    plugins: [forms],
    safelist: [
        { pattern: /bg-violet+/ },
        { pattern: /bg-red+/ },
        { pattern: /bg-green+/ },
        { pattern: /bg-gray+/ },
        { pattern: /text-violet+/ },
        { pattern: /text-red+/ },
        { pattern: /text-green+/ },
        { pattern: /text-gray+/ },
        { pattern: /border-violet+/ },
        { pattern: /border-red+/ },
        { pattern: /border-green+/ },
        { pattern: /border-gray+/ },
    ],
};
