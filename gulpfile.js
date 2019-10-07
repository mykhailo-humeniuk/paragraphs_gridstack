'use strict';

const gulp = require('gulp');
const rename = require('gulp-rename');

const less = require('gulp-less');
const browserify = require('browserify');
const source = require('vinyl-source-stream');
const buffer = require('vinyl-buffer');
const sourcemaps = require('gulp-sourcemaps');
const uglify = require('gulp-uglify');
const es = require('event-stream');

const LessPluginCleanCSS = require('less-plugin-clean-css');
const LessPluginAutoPrefix = require('less-plugin-autoprefix');
const cleanCSS = new LessPluginCleanCSS({advanced: true});
const autoPrefix = new LessPluginAutoPrefix({
  browsers: ['> 5%', 'ie >= 9'],
  remove: false,
  grid: true,
});

const pathsgridstack = {
  less: ['./less/*.less'],
  es6: ['./es6/*.es6'],
  js: './js'
};
const jsFiles = [
  './es6/widget.es6',
  './es6/formatter.es6'
];

// Process LESS files from main theme and custom modules.
const processLessgridstack = function (pipe, env) {
  var options = {};
  if (env != 'dev') {
    options = {plugins: [autoPrefix, cleanCSS]};
  }
  return pipe
      .pipe(less(options))
      .pipe(rename({suffix: '.min'}))
      .pipe(gulp.dest('./css'));
};

// One-time processing of files.
gulp.task('less', function () {
  return processLessgridstack(gulp.src(pathsgridstack.less));
});

gulp.task('es6', function () {
  // map them to our stream function
  let tasks = jsFiles.map(function (entry) {
    return browserify({entries: [entry]})
        .transform("babelify", {presets: ["es2015"]})
        .bundle()
        .pipe(source(entry))
        .pipe(buffer())
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .on('error', function (err) {
          gutil.log(gutil.colors.red('[Error]'), err.toString());
        })
        // rename them to have "bundle as postfix"
        .pipe(rename({
          dirname: './',
          extname: '.min.js'
        }))
        .pipe(sourcemaps.write('./map'))
        .pipe(gulp.dest(pathsgridstack.js));
  });
  // create a merged stream
  return es.merge.apply(null, tasks);
});

gulp.task('build', ['less', 'es6']);

gulp.task('default', ['build']);
