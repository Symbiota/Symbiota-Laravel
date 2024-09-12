export default {
    from: 'resources/css/app.css',
    to: 'resources/css/output.css',
    plugins: {
        'postcss-import': {},
        'tailwindcss/nesting': 'postcss-nesting',
        tailwindcss: {},
        autoprefixer: {},
    },
}
