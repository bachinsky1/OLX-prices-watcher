const colors = require('tailwindcss/colors');

module.exports = {
    purge: {
        content: [
            './resources/**/*.vue',
            './resources/**/*.js',
            './resources/**/*.ts',
            './resources/**/*.php',
            './resources/**/*.html',
        ],
    },
    darkMode: 'media', 
    theme: {
        extend: {
            colors: {
                theme: {
                    DEFAULT: colors.blue,
                    dark: colors.blueDark, 
                },
                danger: colors.red,
            },
        },
    },
    variants: {
        extend: {},
    },
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
        require("@tailwindcss/aspect-ratio"),
    ],
};
