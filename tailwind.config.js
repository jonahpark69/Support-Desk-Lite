import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    safelist: [
        'bg-amber-50',
        'text-amber-700',
        'ring-amber-200',
        'bg-blue-50',
        'text-blue-700',
        'ring-blue-200',
        'bg-emerald-50',
        'text-emerald-700',
        'ring-emerald-200',
        'bg-slate-100',
        'text-slate-700',
        'ring-slate-200',
        'bg-sky-50',
        'text-sky-700',
        'ring-sky-200',
        'bg-orange-50',
        'text-orange-700',
        'ring-orange-200',
        'bg-rose-50',
        'text-rose-700',
        'ring-rose-200',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
