/*!
 * gulp
 * $ npm install gulp-ruby-sass gulp-autoprefixer gulp-minify-css gulp-jshint gulp-concat gulp-uglify gulp-imagemin gulp-notify gulp-rename gulp-livereload gulp-cache del --save-dev
 */

// Load plugins
var gulp = require('gulp'),
    sass = require('gulp-ruby-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    minifycss = require('gulp-minify-css'),
    jshint = require('gulp-jshint'),
    uglify = require('gulp-uglify'),
    imagemin = require('gulp-imagemin'),
    rename = require('gulp-rename'),
    concat = require('gulp-concat'),
    notify = require('gulp-notify'),
    cache = require('gulp-cache'),
    livereload = require('gulp-livereload'),
    del = require('del');

// Styles
gulp.task('styles', function() {
return sass('app/tmpl/sass/public.scss', { style: 'expanded', cacheLocation: 'cache/.sass-cache'})
    .pipe(autoprefixer('last 2 version'))
    .pipe(gulp.dest('cache/css'))
    .pipe(rename({ suffix: '.min' }))
    .pipe(minifycss())
    .pipe(gulp.dest('public/css'))
    .pipe(notify({ message: 'Styles task complete' }));
});

// Scripts
gulp.task('scripts', function() {
return gulp.src(['app/tmpl/script/vendor/jquery-1.11.3/*.js', 'app/tmpl/script/**/*.js'])
    //.pipe(jshint('.jshintrc'))
    //.pipe(jshint.reporter('default'))
    .pipe(concat('app.js'))
    .pipe(gulp.dest('cache/js'))
    .pipe(rename({ suffix: '.min' }))
    .pipe(uglify())
    .pipe(gulp.dest('public/js'))
    .pipe(notify({ message: 'Scripts task complete' }));
});

// Images
gulp.task('images', function() {
return gulp.src('src/images/**/*')
    .pipe(cache(imagemin({ optimizationLevel: 3, progressive: true, interlaced: true })))
    .pipe(gulp.dest('dist/images'))
    .pipe(notify({ message: 'Images task complete' }));
});

// Clean
gulp.task('clean', function() {
return del(['public/styles', 'public/scripts', 'public/images']);
});

// Default task
//gulp.task('default', ['clean'], function() {
//  gulp.start('styles', 'scripts', 'images');
//});

gulp.task('default', ['clean'], function() {
  gulp.start('styles', 'scripts');
});

// Watch
gulp.task('watch', function() {
// Watch .scss files
  gulp.watch('src/styles/**/*.scss', ['styles']);
// Watch .js files
  gulp.watch('src/scripts/**/*.js', ['scripts']);
// Watch image files
  gulp.watch('src/images/**/*', ['images']);
// Create LiveReload server
  livereload.listen();
// Watch any files in dist/, reload on change
  gulp.watch(['dist/**']).on('change', livereload.changed);
});
