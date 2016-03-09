//var elixir = require('laravel-elixir');
var gulp = require('gulp');
var sass = require('gulp-sass');
var concatCss = require('gulp-concat-css');
var concat = require('gulp-concat');

gulp.task('watch-sass', function () {
    //watch sass file and compile
    gulp.watch('./resources/assets/sass/**/*.scss', ['sass']);
});

//compile sass to css
gulp.task('sass', function compileSass() {
    gulp.src('./resources/assets/sass/app.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('./public/css/'));
});

//concat vendor css files
gulp.task('vendor-css', function () {
    return gulp.src('./resources/assets/vendor-css/*.css')
        .pipe(concatCss('vendor.css'))
        .pipe(gulp.dest('./public/css/'));
});

gulp.task('vendor-js', function () {
    return gulp.src(['./resources/assets/js/vendor/jquery-2.1.4.min.js', './resources/assets/js/vendor/*.js'])
        .pipe(concat('vendor.js'))
        .pipe(gulp.dest('./public/js/'));
});


gulp.task('default', ['sass', 'vendor-css', 'vendor-js']);
