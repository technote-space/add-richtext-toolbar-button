const webpack = require( 'webpack' );
const pkg = require( './package' );

const banner = `${ pkg.name }-gutenberg ${ pkg.version }\nCopyright (c) ${ new Date().getFullYear() } ${ pkg.author }\nLicense: ${ pkg.license }`;

const webpackConfig = {
	context: __dirname + '/src/gutenberg',
	entry: './index.js',
	output: {
		path: __dirname,
		filename: `${ pkg.name }-gutenberg.min.js`,
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader',
			},
		],
	},
	externals: {
		lodash: 'lodash',
	},
	plugins: [
		new webpack.BannerPlugin( banner ),
		new webpack.DefinePlugin( {
			'PLUGIN_NAME': JSON.stringify( `${ pkg.name }` ),
			'PARAMETER_NAME': JSON.stringify( 'artbParams' ),
		} ),
	],
};

module.exports = webpackConfig;