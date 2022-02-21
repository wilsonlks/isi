const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js', 'js/jquery-1.10.2.min.js', 'js/jquery-ui.js', 'js/bootstrap.min.js')
    .sass('resources/sass/app.scss', 'public/css')
    .sourceMaps();
