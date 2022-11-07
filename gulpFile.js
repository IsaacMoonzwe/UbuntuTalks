const { src, dest, watch, series, parallel } = require('gulp');
const sass = require('gulp-sass');
      sass.compiler = require('node-sass');
const babel = require('gulp-babel');
const sourcemaps = require('gulp-sourcemaps');

const autoprefixer = require('gulp-autoprefixer');
const svgSprite = require('gulp-svg-sprite');
const concat = require('gulp-concat');
const merge = require('merge-stream');
//
// SVG Sprite Config
//
const config = {
  shape: {
    dimension: { // Set maximum dimensions
      maxWidth: 32,
      maxHeight: 32,
      precision: 2,
      attributes: false, 
    },
    // spacing: { // Add padding
    //   padding: 10,
    //   box: 'padding'
    // },
  },
  mode: {
    symbol: {
      dest: './',
      sprite: 'sprite.yo-coach.svg'
    }
  },
  dest: './'
};

//
// Tasks
//
//
function css(){
    var common = src('scss/common*.scss')
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(sass())
        .pipe(autoprefixer())
        .pipe(sourcemaps.write('.'))
        .pipe(dest('application/views/css'))
        .pipe(dest('dashboard-application/views/css'));
    var frontend = src('scss/frontend*.scss')
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(sass())
        .pipe(autoprefixer())
        .pipe(sourcemaps.write('.'))
        .pipe(dest('application/views/css'));
    var dashboard = src('scss/dashboard*.scss')
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(sass())
        .pipe(autoprefixer())
        .pipe(sourcemaps.write('.'))
        .pipe(dest('dashboard-application/views/css'));
    return merge(common, frontend, dashboard);
}

/* function js(){
  return src('./js/*.js')
      .pipe(sourcemaps.init({loadMaps: true}))
			.pipe(babel({
        presets: ['@babel/env']
      }))
      .pipe(concat('main.js'))
      .pipe(sourcemaps.write('.'))
			.pipe(dest('./js'));
} */

function svg(){
  return src('application/views/images/sprite/*.svg')
      .pipe(svgSprite(config))
      .pipe(dest('application/views/images'));
}

// Watch files
function watchFiles() {
	// Watch SCSS changes
  watch(['scss'], parallel(css));

}

exports.default = parallel(css, svg);
exports.watch = watchFiles;
