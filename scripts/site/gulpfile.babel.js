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
const sass       = require('node-sass');

const _js_src_dir   = path.resolve(__dirname, 'app', 'resources', 'js',);
const _css_src_dir  = path.resolve(__dirname, 'app', 'resources', 'stylesheets',);
const _css_src_file = `${_css_src_dir}/style.scss`;

const dist_dir_name_js   = path.resolve(__dirname, 'public', 'js',);
const dist_dir_name_css  = path.resolve(__dirname, 'public', 'css',);
const dist_file_name_css = `${dist_dir_name_css}/style.css`;
const _test_dir_name     = path.resolve(__dirname, 'tests',);

const file_name = `${_js_src_dir}/index.js`;

/**
 * Put everything together
 * @param bundler
 * @return {*}
 */
function bundle_js_dev(bundler) {
    return bundler.bundle()
                  .on('error', map_error)
                  .pipe(source('app.js'))
                  .pipe(buffer())
                  .pipe(gulp.dest(dist_dir_name_js))
                  .pipe(rename('app.min.js'))
                  .pipe(sourcemaps.init({loadMaps: true}))
                  // // capture sourcemaps from transforms
                  .pipe(uglify())
                  .pipe(sourcemaps.write('.'))
                  .pipe(gulp.dest(dist_dir_name_js))
}

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
    sass.render({file: _css_src_file},
                (error, result) => {
                    if (!error) {
                        // No errors during the compilation, write this result on the disk
                        fs.writeFile(dist_file_name_css,
                                     result.css,
                                     err => {
                                         if (err) {
                                             console.log('could not write to disk --', err);
                                         }
                                     });
                    }
                });
    
});

///////////////////////////////////////////

// Update the rendered files whenever one of the files under the index is updated
gulp.task('watchify', function () {
    let args    = merge(watchify.args, {debug: true, verbose: true});
    let bundler =
            watchify(browserify(file_name, args))
                .transform(babelify, {presets: ["es2015"]});
    bundle_js_dev(bundler);
    bundler.on('update', _updated_filename => {
        bundle_js_dev(bundler);
        console.log(_updated_filename);
    })
});

// Without watchify
gulp.task('browserify', function () {
    try {
        let bundler =
                browserify(file_name,
                           {
                               debug: true
                           })
                    .transform(babelify,
                               {
                                   presets: ["es2015", "react"]
                               });
        return bundle_js_dev(bundler)
    } catch (e) {
        console.log(e.stack);
    }
});

// Without sourcemaps
gulp.task('browserify-production', function () {
    let bundler = browserify(file_name).transform(babelify, {/* options */});
    return bundler.bundle()
                  .on('error', map_error)
                  .pipe(source('app.js'))
                  .pipe(buffer())
                  .pipe(rename('app.min.js'))
                  .pipe(uglify())
                  .pipe(gulp.dest(`${_src_dir}`))
});