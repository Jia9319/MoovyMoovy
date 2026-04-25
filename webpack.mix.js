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

<<<<<<< HEAD
mix.js('resources/js/app.js', 'public/js')
    .react()
    .sass('resources/sass/app.scss', 'public/css');
=======
mix.js('resources/js/app.js', 'public/js').react()
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);
>>>>>>> 232664b70b1a11c398655d2d56352bb17d1806b0
