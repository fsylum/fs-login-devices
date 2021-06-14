let mix = require('laravel-mix');

mix.sass('assets/src/scss/admin.scss', 'css')
    .js('assets/src/js/admin.js', 'js')
    .setPublicPath('assets/dist')
    .disableSuccessNotifications();
