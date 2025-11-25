/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')

export default {
    content: [
        "./resources/**/*.blade.php",
    ],
    safelist: [
        'bg-primary',
        'bg-primary-content',
        'bg-secondary',
        'bg-secondary-content',
        'bg-neutral',
        'bg-neutral-content',
        'text-neutral-content',
    ],
    theme: {
        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            black: colors.black,
            red: colors.red,
            white: colors.white,
            gray: colors.gray,
            emerald: colors.emerald,
            indigo: colors.indigo,
            yellow: colors.yellow,
        },
        fontFamily: {
            sans: ['Roboto', 'sans-serif'],
            serif: ['Merriweather', 'serif'],
        },
        extend: {
            borderColor: (theme) => ({
                DEFAULT: theme('colors.base.300')
            })
        },
    },
    plugins: [],
}

