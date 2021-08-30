const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

mix.setPublicPath('../../public').mergeManifest();

mix.js(__dirname + '/Resources/assets/js/app.js', 'js/documentaryprocedure.js')
    .sass( __dirname + '/Resources/assets/sass/app.scss', 'css/documentaryprocedure.css');

if (mix.inProduction()) {
    mix.version();
}