// gulpfile.js

const gulp = require('gulp');
const babel = require('gulp-babel');
const uglify = require('gulp-uglify');
const sass = require('gulp-sass')(require('sass'));
const cleanCSS = require('gulp-clean-css');
const autoprefixer = require('gulp-autoprefixer');
const rename = require('gulp-rename');
const sourcemaps = require('gulp-sourcemaps');

// JavaScript task - transpile and minify
gulp.task('scripts', () => {
	return gulp.src('js/**/*.js')  // your source folder
		.pipe(sourcemaps.init())          // initialize sourcemaps
		.pipe(babel({
			presets: ['@babel/preset-env']  // use Babel to transpile ES6+
		}))
		.pipe(uglify())                  // minify JS
		.pipe(rename({ extname: '.min.js' }))  // rename to .min.js
		.pipe(sourcemaps.write('.'))      // write sourcemaps to the current folder
		.pipe(gulp.dest('dist/js'));      // output folder
});

// Sass task - compile and minify
gulp.task('styles', () => {
	return gulp.src('scss/**/*.scss')  // your source folder
		.pipe(sourcemaps.init())              // initialize sourcemaps
		.pipe(sass().on('error', sass.logError))  // compile Sass
		.pipe(autoprefixer())                 // add vendor prefixes
		.pipe(cleanCSS())                    // minify CSS
		.pipe(rename({ extname: '.min.css' })) // rename to .min.css
		.pipe(sourcemaps.write('.'))          // write sourcemaps to the current folder
		.pipe(gulp.dest('css/'));         // output folder
});

// Watch task - watch for file changes
gulp.task('watch', () => {
	gulp.watch('js/**/*.js', gulp.series('scripts'));  // watch JS files
	gulp.watch('scss/**/*.scss', gulp.series('styles'));  // watch Sass files
});

// Default task - run the tasks
gulp.task('default', gulp.series('scripts', 'styles', 'watch'));
