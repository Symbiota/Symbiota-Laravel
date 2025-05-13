/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')
const Color = require('color')

const lighten = (clr, val) => Color(clr).lighten(val).rgb().string()
const darken = (clr, val) => Color(clr).darken(val).rgb().string()
const whatText = (clr) => Color(clr).isDark()? '#FFFFFF': '#2D2D2D';

const symb_colors = {
    primary: {
        DEFAULT:'#1B3D2F',
    },
    secondary: {
        DEFAULT:'#664147',
    },
    neutral: {
        DEFAULT:'#494440',
    },
    accent: {
        DEFAULT:'#BFD246',
        //content:'#FFFFFF',
    },
    success: {
        DEFAULT:colors.green[500],
    },
    warning: {
        DEFAULT:colors.yellow[500],
    },
    info: {
        DEFAULT:'#664147',
    },
    error: {
        DEFAULT:colors.red[500],
    },
    link: {
        DEFAULT: '#126c00',
        // Used for on hover
        lighter: darken('#BFD246', 0.2)
    },
    base: {
        100: "#FFFFFF",
        200: "#F2F2F2",
        //300: "#F2F0E9",
        300: "#E0E0E0",
        content: "#2D2D2D",
        //content: "#494440"
    },
};

for(let color in symb_colors) {
    if(color === "base") continue;

    if(!symb_colors[color]['lighter']) {
        symb_colors[color]['lighter'] = lighten(symb_colors[color].DEFAULT, 0.2);
    }

    if(!symb_colors[color]['darker']) {
        symb_colors[color]['darker'] = darken(symb_colors[color].DEFAULT, 0.2);
    }

    if(!symb_colors[color]['content']) {
        symb_colors[color]['content'] = whatText(symb_colors[color].DEFAULT);
    }
}

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
            ...symb_colors
        },
        fontFamily: {
            sans: ['Roboto', 'sans-serif'],
            serif: ['Merriweather', 'serif'],
        },
        extend: {},
    },
    plugins: [],
}

