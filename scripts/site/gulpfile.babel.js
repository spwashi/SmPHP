let gulp = require('gulp');
require("babel-polyfill");
const babel      = require("babel-core/register");
const path       = require('path');
const fs         = require('fs');
const browserify = require('browserify');
const watchify   = require('watchify');
const babelify   = require('babelify');
const source     = require('vinyl-source-stream');
const buffer     = require('vinyl-buffer');
const merge      = require('utils-merge');
const rename     = require('gulp-rename');
const uglify     = require('gulp-uglify');
const sourcemaps = require('gulp-sourcemaps');
const mocha      = require('gulp-mocha');
const gutil      = require('gulp-util');
const chalk      = require('chalk');
const sass       = require('gulp-sass');

const _js_src_dir   = path.resolve(__dirname, 'app', 'resources', 'js',);
const _css_src_dir  = path.resolve(__dirname, 'app', 'resources', 'stylesheets',);
const _css_src_file = `${_css_src_dir}/style.scss`;

const dist_dir_name_js   = path.resolve(__dirname, 'public', 'js',);
const dist_dir_name_css  = path.resolve(__dirname, 'public', 'css',);
const _test_dir_name     = path.resolve(__dirname, 'tests',);

const file_name = `${_js_src_dir}/index.js`;

///////////////////////////////////////////

// Unit testing
gulp.task('mocha', function () {
    return gulp.src([_test_dir_name + '/index.js'])
               .pipe(mocha({
                               reporter:  'spec',
                               compilers: [
                                   'js:babel-core/register',
                               ]
                           }))
});
// Unit testing
gulp.task('sass', function () {
    return gulp.src(_css_src_dir + '/scss/**/*.scss')
               .pipe(sourcemaps.init())
               .pipe(sass().on('error', sass.logError))
               .pipe(sourcemaps.write())
               .pipe(gulp.dest(dist_dir_name_css));
});

gulp.task('watch:css', () => {
    return gulp.watch(_css_src_dir + '/scss/**/*.scss', ['sass']);
});