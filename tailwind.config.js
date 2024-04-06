/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')
export default {
    content: [
        "./resources/**/*.blade.php",
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
            primary: "#1B3D2F",
            secondary: "#BFD245",
            indigo: colors.indigo,
            yellow: colors.yellow,
        },
        fontFamily: {
            sans: ['Roboto', 'sans-serif'],
            serif: ['Merriweather', 'serif'],
        },
        extend: {},
    },
    plugins: [],
}

