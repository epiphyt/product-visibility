const defaultConfig = require( './node_modules/@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );
const IgnoreEmitPlugin = require( 'ignore-emit-webpack-plugin' );
const MiniCSSExtractPlugin = require( 'mini-css-extract-plugin' );

const isProduction = process.env.NODE_ENV === 'production';
const mode = isProduction ? 'production' : 'development';

const jsFiles = {
	'admin-meta-box-tabs': path.resolve(
		process.cwd(),
		'assets/js',
		'admin-meta-box-tabs.js'
	),
};
const scssFiles = {
	admin: path.resolve( process.cwd(), 'assets/style/scss', 'admin.scss' ),
};

module.exports = [
	// JavaScript minification
	{
		mode: mode,
		devtool: ! isProduction ? 'source-map' : 'hidden-source-map',
		entry: jsFiles,
		output: {
			filename: '[name].min.js',
			path: path.resolve( process.cwd(), 'assets/js/build' ),
		},
		optimization: {
			minimize: true,
			minimizer: defaultConfig.optimization.minimizer,
		},
	},
	// compiled + minified CSS file
	{
		mode: mode,
		devtool: ! isProduction ? 'source-map' : 'hidden-source-map',
		entry: scssFiles,
		output: {
			path: path.resolve( process.cwd(), 'assets/style/build' ),
		},
		module: {
			rules: [
				{
					test: /\.(sc|sa)ss$/,
					use: [
						MiniCSSExtractPlugin.loader,
						{
							loader: 'css-loader',
							options: {
								sourceMap: ! isProduction,
								url: false,
							},
						},
						{
							loader: 'sass-loader',
							options: {
								sourceMap: ! isProduction,
								sassOptions: {
									minimize: true,
									outputStyle: 'compressed',
								},
							},
						},
					],
				},
			],
		},
		plugins: [
			new MiniCSSExtractPlugin( { filename: '[name].min.css' } ),
			new IgnoreEmitPlugin( [ '.js' ] ),
		],
	},
	// compiled CSS
	{
		mode: mode,
		devtool: ! isProduction ? 'source-map' : 'hidden-source-map',
		entry: scssFiles,
		output: {
			path: path.resolve( process.cwd(), 'assets/style/build' ),
		},
		module: {
			rules: [
				{
					test: /\.(sc|sa)ss$/,
					use: [
						MiniCSSExtractPlugin.loader,
						{
							loader: 'css-loader',
							options: {
								sourceMap: ! isProduction,
								url: false,
							},
						},
						{
							loader: 'sass-loader',
							options: {
								sourceMap: ! isProduction,
								sassOptions: {
									minimize: false,
									outputStyle: 'expanded',
								},
							},
						},
					],
				},
			],
		},
		plugins: [
			new MiniCSSExtractPlugin( { filename: '[name].css' } ),
			new IgnoreEmitPlugin( [ '.js' ] ),
		],
	},
];
