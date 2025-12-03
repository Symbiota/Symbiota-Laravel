import Color from "colorjs.io";
import fs from 'node:fs';

/*
 * Run from command line with `node build_theme.js`
 *
 * Note this is only to be have dependies to run in development enviroments
 */

const colorSpace = 'oklch';

const lighten = (clr, val) => new Color(clr).lighten(val).to(colorSpace).toString()
const darken = (clr, val) => new Color(clr).darken(val).to(colorSpace).toString();

const theme = 'Symbiota';
const themeJson = JSON.parse(fs.readFileSync(`themes/${theme}.json`, 'utf8'));

const symb_colors = themeJson['colors'];

// Build Derivatives
for(let color in symb_colors) {
    if(color !== "base") {
        let baseColor = symb_colors[color].DEFAULT;

        if(!symb_colors[color]['lighter']) {
            symb_colors[color]['lighter'] = lighten(baseColor, 0.1);
        }

        if(!symb_colors[color]['darker']) {
            symb_colors[color]['darker'] = darken(baseColor, 0.1);
        }

        if(!symb_colors[color]['content']) {
            let white = new Color("#FFFFFF").to(colorSpace);
            let black = new Color("#2D2D2D").to(colorSpace);

            let contrastTarget = new Color(baseColor);
            let blackContrast = contrastTarget.contrast(black, "WCAG21");
            let whiteContrast = contrastTarget.contrast(white, "WCAG21");

            symb_colors[color]['content'] = blackContrast > whiteContrast?
                black.toString():
                white.toString();
        }
    }
}

// Build defaults for banner overrides
if(!symb_colors['banner-overlay']) {
    let bannerOverlay = new Color(symb_colors['primary'].darker).to(colorSpace);
    bannerOverlay.alpha = .8;

    symb_colors['banner-overlay'] = {
        DEFAULT: bannerOverlay.toString(),
        content: symb_colors['primary'].content
    }
}

if(!symb_colors['navbar']) {
    symb_colors['navbar'] = symb_colors['primary'];
}

if(!symb_colors['footer']) {
    symb_colors['footer'] = {
        DEFAULT: symb_colors['base'][200],
        content: symb_colors['base']['content']
    }
}

let themeString = '';

// Build themeString
for(let color in symb_colors) {
    for(let subColor in symb_colors[color]) {
        let normalizeColor = new Color(symb_colors[color][subColor]).to(colorSpace);

        let varName = subColor === 'DEFAULT' ?
            `--color-${color}`:
            `--color-${color}-${subColor}`;

        themeString += `\t${varName}:${normalizeColor.toString()};\n`;
    }
}

let variableString = '';

//Build out Variables
for(let variableGroup of ['banner']) {
    for(let variable in themeJson[variableGroup]) {
        variableString += `\t--symb-${variableGroup}-${variable}: ${themeJson[variableGroup][variable]};\n`
    }
}

fs.writeFile('resources/css/theme.css', `@import "tailwindcss";\n\n:root {\n${variableString}}\n\n@theme {\n${themeString}}`, err => {
    if(err) {
        console.log(err)
    } else {
        // file written successfully
        console.log('Portal theme built successfully');
    }
})

const child_variable_aliases = `\t/* Maps variables to child portal variables */
    --symb-font: "Roboto";
    --header-bg-color: var(--color-banner-overlay);
    --header-font-color: var(--color-banner-overlay-content);
    --header-bg-image: var(--symb-banner-url);

    --brand-font-size: 1.5rem;
    --logo-width: 120px;

    --menu-top-bg-color: var(--color-navbar);
    --menu-font-size: 1rem;

    --footer-bg-color: var(--color-base-200);

    --body-bg-color: var(--color-base-100);
    --body-text-color: var(--color-base-content);
    --body-font-size: 1rem;
    --body-font-family: var(--symb-font,ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji");
    --heading-font-family: var(--symb-font,ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji");
    --link-color: var(--color-link);
    --darkest-color: var(--color-primary);
    --medium-color: var(--color-neutral);
    --light-color: var(--color-base-200);
    --bright-color: var(--color-accent);
    --max-innertext-width: 1024px;
`;

fs.writeFile('child_config/variables.css', `:root {\n${variableString}\n${themeString}\n${child_variable_aliases}}`, err => {
    if(err) {
        console.log(err)
    } else {
        // file written successfully
        console.log('Child Config built successfully');
    }
})
