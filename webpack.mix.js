let mix = require('laravel-mix');

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

mix.js('resources/js/cp.js', 'resources/dist/js/cp.js')
    .setPublicPath('resources/dist');

if (! mix.inProduction()) {
    mix.copy('resources/dist/js/cp.js', '../../../public/vendor/simple-commerce/js/cp.js');
}
