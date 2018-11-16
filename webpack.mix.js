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

mix.js('resources/assets/js/index.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css/app.css')
   .options({
      processCssUrls: false
   })
   .styles([
      'public/css/app.css',
      'public/css/style.css',
      'public/css/header.css',
      'public/css/main.css',
      'public/css/spin.css'
   ], 'public/css/app.css');