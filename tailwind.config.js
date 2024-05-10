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
        {
            pattern: /bg-(violet|red|green|gray)+/,
            variants: ['before', 'after'],
        },
        { pattern: /text-(violet|red|green|gray)+/ },
        { pattern: /border-(violet|red|green|gray)+/ },
    ],
};
