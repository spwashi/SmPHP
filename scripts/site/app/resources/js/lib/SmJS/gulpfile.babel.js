"use strict";

let gulp = require('gulp');

require("babel-polyfill");
const babel      = require("babel-core/register");
const path       = require('path');
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
/* nicer browserify errors */
const gutil = require('gulp-util');
const chalk = require('chalk');

function map_error(err) {
    if (err.fileName) {
        // regular error
        gutil.log(chalk.red(err.name) + ': '
            + chalk.yellow(err.fileName.replace(__dirname + '/src/js/', '')) + ': ' + 'Line ' + chalk.magenta(err.lineNumber)
            + ' & ' + 'Column ' + chalk.magenta(err.columnNumber || err.column)
            + ': '
            + chalk.blue(err.description))
    } else {
        // browserify error..
        gutil.log(chalk.red(err.name)
            + ': '
            + chalk.yellow(err.message))
    }
    
    this.emit('end');
}

const _src_dir  = path.resolve(__dirname, 'src');
const _dist_dir = path.resolve(__dirname, 'dist');
const _test_dir = path.resolve(__dirname, 'tests');
const file_name = `${_src_dir}/index.js`;

gulp.task('mocha', i => {
    return gulp.src([_test_dir + '/index.js'])
               .pipe(mocha({reporter: 'spec', compilers: ['js:babel-core/register',]}))
});

/**
 * Put everything together
 * @param bundler
 * @return {*}
 */
const bundle_js_dev = bundler => bundler.bundle()
                                        .on('error', map_error)
                                        .pipe(source('app.js'))
                                        .pipe(buffer())
                                        .pipe(gulp.dest(_dist_dir))
                                        .pipe(rename('app.min.js'))
                                        .pipe(sourcemaps.init({loadMaps: true}))
                                        .pipe(uglify()) // capture sourcemaps from transforms
                                        .pipe(sourcemaps.write('.'))
                                        .pipe(gulp.dest(_dist_dir));

// Update the rendered files whenever one of the files under the index is updated
gulp.task('watchify', () => {
    const browserify = merge(watchify.args, {debug: true, verbose: true});
    const bundler    = watchify(browserify(file_name, browserify)).transform(babelify);
    bundle_js_dev(bundler);
    bundler.on('update', _updated_filename => {
        bundle_js_dev(bundler);
        console.log(_updated_filename);
    })
});

// Without watchify
gulp.task('browserify', () => {
    return bundle_js_dev(browserify(file_name, {debug: true}).transform(babelify));
});

// Without sourcemaps
gulp.task('browserify-production', () => {
    return browserify(file_name).transform(babelify)
                                .bundle()
                                .on('error', map_error)
                                .pipe(source('app.js'))
                                .pipe(buffer())
                                .pipe(rename('app.min.js'))
                                .pipe(uglify())
                                .pipe(gulp.dest(`${_src_dir}`))
});