require('dotenv').config();

const env = process.env.NODE_ENV || 'development';
const { series, parallel, src, dest, watch } = require('gulp');
const { rollup } = require('rollup');
const { terser } = require('rollup-plugin-terser');
const sass = require('gulp-dart-sass');
const postcss = require('gulp-postcss');
const concat = require('gulp-concat');
const autoprefixer = require('autoprefixer');
const babel = require('rollup-plugin-babel');
const bs = require('browser-sync').create();
const { nodeResolve } = require('@rollup/plugin-node-resolve');
const commonjs = require('@rollup/plugin-commonjs');
const multi = require('@rollup/plugin-multi-entry');
const replace = require('@rollup/plugin-replace');

function css() {
	return src(['./src/app.scss', './blocks/**/*.scss'])
		.pipe(concat('app.min.scss'))
		.pipe(
			sass({
				outputStyle: 'compressed',
				includePaths: ['./src'],
			}).on('error', sass.logError)
		)
		.pipe(postcss([autoprefixer()]))
		.pipe(dest('./dist'))
		.pipe(bs.stream());
}

function login_page_css() {
	return src(['./src/scss/_login-page.scss'])
		.pipe(concat('login-page.min.scss'))
		.pipe(
			sass({
				outputStyle: 'compressed',
			}).on('error', sass.logError)
		)
		.pipe(postcss([autoprefixer()]))
		.pipe(dest('./dist'))
		.pipe(bs.stream());
}

async function blocks_js() {
	const bundle = await rollup({
		external: ['jquery', 'vue', 'axios'],
		input: ['./blocks/**/*.js'],
		plugins: [
			multi(),
			nodeResolve({ browser: true }),
			commonjs({ include: 'node_modules/**' }),
			replace({
				'process.env.NODE_ENV': JSON.stringify(env),
			}),
			babel({ exclude: 'node_modules/**' }),
			terser(),
		],
	});

	return bundle.write({
		globals: {
			jquery: '$',
			vue: 'Vue',
			axios: 'axios',
		},
		file: './dist/rpmblocks.min.js',
		name: 'RPMBlocks',
		format: 'iife',
		sourcemap: true,
	});
}

async function modules_js() {
	const bundle = await rollup({
		external: ['jquery', 'vue', 'axios'],
		input: ['./src/js/**/*.js'],
		plugins: [
			multi(),
			nodeResolve({ browser: true }),
			commonjs({ include: 'node_modules/**' }),
			replace({
				'process.env.NODE_ENV': JSON.stringify(env),
			}),
			babel({ exclude: 'node_modules/**' }),
			terser(),
		],
	});

	return bundle.write({
		globals: {
			jquery: '$',
			vue: 'Vue',
			axios: 'axios',
		},
		file: './dist/rpmmodules.min.js',
		name: 'RPMModules',
		format: 'iife',
		sourcemap: true,
	});
}

async function js_entrypoint() {
	const bundle = await rollup({
		external: ['jquery', 'vue', 'axios'],
		input: ['./src/app.js'],
		plugins: [
			nodeResolve(),
			commonjs({ include: 'node_modules/**' }),
			replace({
				'process.env.NODE_ENV': JSON.stringify(env),
			}),
			babel({ exclude: 'node_modules/**' }),
		],
	});

	return bundle.write({
		globals: {
			jquery: '$',
			vue: 'Vue',
			axios: 'axios',
		},
		file: './dist/app.js',
		format: 'iife',
		sourcemap: true,
	});
}

function serve(cb) {
	bs.init({
		proxy: process.env.GULP_PROXY,
	});

	watch(['src/**/*.scss', 'blocks/**/*.scss'], parallel(css, login_page_css));
	watch(['blocks/**/*.js', 'src/**/*.js'], parallel(blocks_js, modules_js, js_entrypoint));
	watch(['*.html', 'dist/**/*.js', '**/*.php']).on('change', bs.reload);

	cb();
}

exports.default = series(parallel(parallel(css, login_page_css), parallel(blocks_js, modules_js, js_entrypoint)), serve);
exports.javascript = series(parallel(blocks_js, js_entrypoint));
exports.css = series(parallel(css, login_page_css));
