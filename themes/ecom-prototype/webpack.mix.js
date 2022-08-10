const mix = require('laravel-mix');
const webpackConfig = require('./webpack.config');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your theme assets. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.webpackConfig(webpackConfig)
    .options({ processCssUrls: false })
    .js('./assets/js/app.js', 'dist/js')
    .sass('./assets/sass/style.scss', 'dist/css')
    .copy('node_modules/bootstrap-icons/font/fonts/', 'assets/vendor/bootstrap-icons/fonts/')
;
