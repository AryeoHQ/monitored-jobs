const mix = require('laravel-mix');
const webpack = require('webpack');
const path = require('path');

mix.options({
    terser: {
        terserOptions: {
            compress: {
                drop_console: true,
            },
        },
    },
})
    .setPublicPath('public')
    .js('resources/js/monitored-jobs.js', 'public/js')
    .vue()
    .postCss("resources/css/monitored-jobs.css", "public/css", [
        require("tailwindcss"),
    ])
    .version()
    .webpackConfig({
        resolve: {
            symlinks: false,
            alias: {
                '@': path.resolve(__dirname, 'resources/js/'),
            },
        },
        plugins: [
            new webpack.IgnorePlugin({
                resourceRegExp: /^\.\/locale$/,
                contextRegExp: /moment$/,
            }),
        ],
    });