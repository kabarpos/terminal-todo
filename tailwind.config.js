const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Light mode colors
                'light-bg': '#ffffff',
                'light-card': '#f8fafc',
                'light-text': '#0f172a',
                'light-border': '#e2e8f0',
                
                // Dark mode colors
                'dark-bg': '#0f1729',
                'dark-card': '#1e293b',
                'dark-text': '#f8fafc',
                'dark-border': '#334155',
            },
        },
    },
    plugins: [require("@tailwindcss/forms")],
};
