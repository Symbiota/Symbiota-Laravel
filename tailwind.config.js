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
            indigo: colors.indigo,
            yellow: colors.yellow,
            base: {
                100: "#FFFFFF",
                200: "#F2F2F2",
                300: "#F2F0E9",
                content: "#2D2D2D"
            },
            primary: {
                DEFAULT: "#1B3D2F",
                content: "#FFF"
            },
            secondary: {
                DEFAULT: "#BFD245",
                content: "#FFF"
            },
            neutral: {
                DEFAULT: "#494440",
                content: "#FFF"
            },
            accent: {
                DEFAULT: "#F9F871",
                content: "#2D2D2D"
            },
            success: {
                DEFAULT: colors.green[100],
                content: "#FFF"
            },
            warning: {
                DEFAULT: colors.yellow[100],
                content: "#FFF"
            },
            info: {
                DEFAULT: colors.blue[100],
                content: "#FFF"
            },
            error: {
                DEFAULT: colors.red[100],
                content: "#FFF"
            },
        },
        fontFamily: {
            sans: ['Roboto', 'sans-serif'],
            serif: ['Merriweather', 'serif'],
        },
        extend: {},
    },
    plugins: [],
}

