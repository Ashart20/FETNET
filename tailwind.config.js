// tailwind.config.js

// Gunakan 'require' untuk mengimpor plugin, sesuai dengan sintaks CommonJS
const forms = require('@tailwindcss/forms');
const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class', // Sudah benar

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            // Menambahkan kembali font family default dari Laravel (Figtree)
            // Ini adalah praktik yang baik agar font kustom tidak menimpa font dasar.
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'brand-purple': '#6D28D9',
                'dark-primary': '#111827',
                'dark-secondary': '#1F2937',
                'dark-tertiary': '#374151',
                'text-main': '#F9FAFB',
                'text-secondary': '#9CA3AF',
            },
        },
    },

    plugins: [
        forms, // Gunakan variabel yang sudah di-require di atas
    ],
};
