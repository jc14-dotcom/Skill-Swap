const { pupThemeTokens } = require('./TAILWIND_THEME_TOKENS');

/** @type {import('tailwindcss').Config} */
module.exports = {
        content: [
                "./app/Views/**/*.php",
                "./resources/views/**/*.php",
                "./public/**/*.php",
                "./public/assets/js/**/*.js",
                "./resources/js/**/*.js",
        ],
        theme: {
                extend: {
                        ...pupThemeTokens,
                        fontFamily: {
                                sans: ['Manrope', 'Sora', 'Segoe UI', 'sans-serif'],
                                heading: ['Sora', 'Manrope', 'Segoe UI', 'sans-serif'],
                        },
                },
        },
        plugins: [],
};
