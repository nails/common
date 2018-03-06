/* globals require, console */

/**
 * The Nails [opinionated] Gulpfile
 * ================================
 *
 * This gulp file will compile SASS and JS into the assets/build folder.
 *
 * CSS: It is expected that there will be one or more "top level" SASS files
 *      which import various sub files.
 *
 *      Example folder structure:
 *      assets/sass/app/component1.scss
 *      assets/sass/app/component2.scss
 *      assets/sass/app.scss
 *
 *      Using the default gulp file, all of the above files will be watched for
 *      changes, and when detected the `css:app` task will be run - this will
 *      compile `asset/sass/app.scss` to `assets/css/app.css`
 *
 * JS:  It is expected that there will be one or more "top level" JS files
 *      and, optionally, some sub files
 *
 *      Example folder structure:
 *      assets/js/app/component/component1.js
 *      assets/js/app/component/component2.js
 *      assets/js/app.js
 *
 *      Using the default gulp file, all of the above files will be watched for
 *      changes, and when detected the `js:app` task will be run - this will
 *      concatenates all of the above files (in the above order, i.e components
 *      first) into a single, compressed file at `assets/js/app.min.js`
 *
 * --------------------------------------------------------------------------
 *
 * This object defines the various tasks and specified what the input files will
 * be as well as the output file. It also defines which files to watch for changes
 * for that particular task.
 *
 * Gulp tasks will be named `css:property` or `js:property` depending on the type
 * of asset being loaded; `property` is the value of the top-most keys. e.g:
 *
 *  css:app
 *  js:app
 */

var css = {
    'nails': {
        'input': [
            'assets/sass/nails.scss'
        ],
        'output': 'nails.css',
        'watch': [
            'assets/sass/nails.scss',
            'assets/sass/base/**/*.scss',
            'assets/sass/components/**/*.scss'
        ]
    }
};

var js = {};


// --------------------------------------------------------------------------
//  End of configurable options
// --------------------------------------------------------------------------


//  Load all the things we'll need
var gulp         = require('gulp');
var sass         = require('gulp-sass');
var concat       = require('gulp-concat');
var autoPrefixer = require('gulp-autoprefixer');
var cleanCSS     = require('gulp-clean-css');
var rename       = require('gulp-rename');
var uglify       = require('gulp-uglify');
var sourceMap    = require('gulp-sourcemaps');
var runSequence  = require('run-sequence');
var notify       = require('gulp-notify');

// ES2015 reqs
var browserify   = require('browserify');
var gulp         = require('gulp');
var source       = require('vinyl-source-stream');
var transform    = require('vinyl-transform');
var buffer       = require('vinyl-buffer');
var babelify     = require('babelify');




// --------------------------------------------------------------------------

/**
 * Creates a new Gulp task
 * @param type   string The type of task to create [css|js]
 * @param task   string The name to give the task
 * @param input  array  The files to compile
 * @param output string The name to give the output file
 */
function createTask(type, task, input, output) {
    gulp.task(
        task,
        function() {
            if (type === 'css') {
                compileCss(task, input, output);
            } else {
                compileJs(task, input, output);
            }
        }
    );
}

/**
 * Creates a new Gulp watcher
 * @param input  array  The files to watch
 * @param task string The task to execute
 */
function createWatch(input, task) {
    gulp.watch(input, [task]);
}
/**
 * Executes a compilation of CSS files
 * @param task   string The task name
 * @param input  array  The files to compile
 * @param output string The name to give the output file
 * @returns {*}
 */
function compileCss(task, input, output) {
    return gulp
        .src(input)
        .pipe(sass().on('error', sass.logError))
        .pipe(concat(output))
        .pipe(autoPrefixer({
            browsers: ['last 2 versions', 'ie 8', 'ie 9'],
            cascade:  false
        }))
        .pipe(cleanCSS())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('./assets/css/'))
        .on('error', notify.onError({
            message: '[' + task + '] Error compiling CSS',
            title:   '<%= error.message %>',
            sound:   'Funk'
        }))
        .on('error', function (err) {
            console.log('Error compiling CSS: ', err);
        })
        .pipe(notify({
            title:   'Successfully compiled CSS',
            message: '[' + task + '] All .scss files were successfully compiled into CSS',
            sound:   false,
            icon:    false,
            onLast:  true
        }));
}

/**
 * Executes a compilation of JS files
 * @param task   string The task name
 * @param input  array  The files to compile
 * @param output string The name to give the output file
 * @returns {*}
 */
function compileJs(task, input, output) {

    var b = browserify({
        entries: './assets/js/app.js',
        debug: true,
        transform: [babelify.configure({
          presets: ['es2015']
        })]
    });

    return b.bundle()
        .pipe(source('app.js'))
        .pipe(buffer())
        .pipe(sourceMap.init({loadMaps: true}))
            // Add transformation tasks to the pipeline here.
            .pipe(uglify())
            .pipe(rename({
                suffix: '.min'
            }))
            .on('error', notify.onError({
                message: '[' + task + '] Error compiling JS',
                title:   '<%= error.message %>',
                sound:   'Funk',
                icon:    false,
                onLast:  true
            }))
            .on('error', function (err) {
                console.log('Error compiling JS: ', err);
            })
        .pipe(sourceMap.write('./'))
        .pipe(gulp.dest('./assets/js/'))
        .pipe(notify({
            title:   'Successfully compiled JS',
            message: '[' + task + '] All .js files were successfully minified and sourceMap generated',
            sound:   false,
            icon:    false,
            onLast:  true
        }))
        ;
}

// --------------------------------------------------------------------------

//  Compile Gulp tasks
var key;

//  CSS
for (key in css) {
    if (css.hasOwnProperty(key)) {
        createTask('css', 'css:' + key, css[key].input, css[key].output);
    }
}

//  JS
for (key in js) {
    if (js.hasOwnProperty(key)) {
        createTask('js', 'js:' + key, js[key].input, js[key].output);
    }
}

// --------------------------------------------------------------------------

//  Additional tasks
//  Loads watchers for all configured tasks
gulp.task('default', function() {

    for (key in css) {
        if (css.hasOwnProperty(key)) {
            createWatch(css[key].watch, 'css:' + key);
        }
    }

    //  JS
    for (key in js) {
        if (js.hasOwnProperty(key)) {
            createWatch(js[key].watch, 'js:' + key);
        }
    }

});

//  Execute all configured tasks
gulp.task('build', function() {

    var tasks = [];

    for (key in css) {
        if (css.hasOwnProperty(key)) {
            tasks.push('css:' + key);
        }
    }

    //  JS
    for (key in js) {
        if (js.hasOwnProperty(key)) {
            tasks.push('js:' + key);
        }
    }
    runSequence(tasks);
});
