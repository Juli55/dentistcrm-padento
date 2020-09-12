// var gulp       = require('gulp');
var elixir = require('laravel-elixir');
// var livereload = require('gulp-livereload');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function (mix) {
    var optionsApp = {
        includePaths: [
            'bower_components/bootstrap-sass/assets/stylesheets',
            'bower_components/components-font-awesome/scss',
        ]
    };
    var optionsStyle = {
        includePaths: [
            'bower_components/foundation-sites/scss',
        ]
    };

//    mix.sass([
//        'app/*.scss',
//       'app/*/*.scss',
//    ], 'public/assets/css/app.css', optionsApp);
//    mix.sass(['padento/*.scss', 'padento/*/*.scss'], 'public/assets/css/style.css', optionsStyle);

    mix.scripts([
        'vendor/jquery.js',
        'vendor/moment.js',
        'vendor/bootstrap.js',
        '../../../bower_components/bootstrap-fileinput/js/plugins/canvas-to-blob.min.js',
        '../../../bower_components/bootstrap-fileinput/js/fileinput.min.js',
        '../../../bower_components/bootstrap-fileinput/js/locales/de.js',
        'vendor/fullcalendar2.js',
        'vendor/bootbox.min.js',
        'vendor/daterangepicker.js',
        'vendor/de.js',
        'vendor/vue.js',
        'vendor/vue-resource.js',
        'vendor/vue-router.js',
        'vendor/vue-simple-store.min.js',
        '../../../bower_components/vue-validator/dist/vue-validator.min.js',
        '../../../bower_components/summernote/dist/summernote.min.js',
        '../../../bower_components/messenger/build/js/messenger.min.js',
        '../../../bower_components/messenger/build/js/messenger-theme-future.js',
        '../../../bower_components/clipboard/dist/clipboard.js',
        '../../../bower_components/js-cookie/src/js.cookie.js',

        '../../../bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
        '../../../bower_components/bootstrap-select/dist/js/bootstrap-select.min.js',
    ], 'public/js/all.js', 'resources/assets/js');

    mix.scripts([
        '../../../bower_components/jquery/dist/jquery.min.js',
        '../../../bower_components/what-input/dist/what-input.min.js',
        '../../../bower_components/foundation-sites/dist/js/foundation.js',
        '../../../node_modules/jquery-smooth-scroll/src/jquery.smooth-scroll.js',
        'resources/assets/js/scripts.js'
    ], 'public/assets/js/scripts.js');

    mix.browserify([
        'admindashboard.js',
    ]);
    // mix.task('livereload', 'public/js/all.js');
    // mix.task('livereload', 'public/assets/js/scripts.js');
    // mix.task('livereload', 'public/js/all.js');
    // mix.task('livereload', 'public/assets/css/style.css');
    // mix.task('livereload', 'public/assets/css/app.css');

});

// /**
//  * Logic for LiveReload to work properly on watch task.
//  */
// gulp.on('task_start', function (e) {
//     // only start LiveReload server if task is 'watch'
//     if (e.task === 'watch') {
//         livereload.listen();
//     }
// });
// gulp.task('watch-lr-css', function () {
//     // notify a CSS change, so that livereload can update it without a page refresh
//     livereload.changed(['style.css']);
// });
// gulp.task('watch-lr', function () {
//     // notify any other changes, so that livereload can refresh the page
//     livereload.changed(['all.js', 'scripts.js', 'admindashboard.js']);
// });