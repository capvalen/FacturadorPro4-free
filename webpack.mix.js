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

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/style.scss', 'public/css/app.css')
   .sass('resources/sass/auth.scss', 'public/css/auth.css')
   .extract(['vue'])
   .version();

mix.webpackConfig({
    resolve: {
        alias: {
            '@components': path.resolve(__dirname, 'resources/js/components'),
            '@views': path.resolve(__dirname, 'resources/js/views/tenant'),
            '@helpers': path.resolve(__dirname, 'resources/js/helpers'),
            '@mixins': path.resolve(__dirname, 'resources/js/mixins'),

            '@viewsModuleSale': path.resolve(__dirname, 'modules/Sale/Resources/assets/js/views'),
            '@viewsModuleFinance': path.resolve(__dirname, 'modules/Finance/Resources/assets/js/views'),
            '@viewsModulePurchase': path.resolve(__dirname, 'modules/Purchase/Resources/assets/js/views'),
            '@viewsModuleExpense': path.resolve(__dirname, 'modules/Expense/Resources/assets/js/views'),
            '@viewsModuleOrder': path.resolve(__dirname, 'modules/Order/Resources/assets/js/views'),
            '@viewsModuleAccount': path.resolve(__dirname, 'modules/Account/Resources/assets/js/views'),
            '@viewsModuleItem': path.resolve(__dirname, 'modules/Item/Resources/assets/js/views'),
            '@viewsModuleHotel': path.resolve(__dirname, 'modules/Hotel/Resources/assets/js/views'),
            '@viewsModuleDocumentary': path.resolve(__dirname, 'modules/DocumentaryProcedure/Resources/assets/js/views'),
        }
    }
}).sourceMaps()

mix.disableSuccessNotifications();
