const SpeedMeasurePlugin = require( 'speed-measure-webpack-plugin' );
const DuplicatePackageCheckerPlugin = require( 'duplicate-package-checker-webpack-plugin' );
const HardSource = require( 'hard-source-webpack-plugin' );
const smp = new SpeedMeasurePlugin();
const webpack = require( 'webpack' );
const pkg = require( './package' );
const path = require( 'path' );

const banner = `${ pkg.name } ${ pkg.version }\nCopyright (c) ${ new Date().getFullYear() } ${ pkg.author }\nLicense: ${ pkg.license }`;

const webpackConfig = {
	context: path.resolve( __dirname, 'src' ),
	entry: './index.js',
	output: {
		path: __dirname,
		filename: 'index.min.js',
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader?cacheDirectory',
			},
		],
	},
	plugins: [
		new webpack.BannerPlugin( banner ),
		new DuplicatePackageCheckerPlugin(),
		new HardSource(),
	],
};

module.exports = smp.wrap( webpackConfig );
