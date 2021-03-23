const { helpers, externals, plugins, presets } = require( '@humanmade/webpack-helpers' );
const { filePath } = helpers;
const { copy, clean, manifest, miniCssExtract } = plugins;
const WebpackRTLPlugin = require( 'webpack-rtl-plugin' );
const CopyPlugin = require( 'copy-webpack-plugin' );

module.exports = presets.production( {
	externals,
	entry: {
		editor: filePath( 'assets/src/editor/index.js' ),
	},
	output: {
		path: filePath( 'assets/dist' ),
		filename: './[name]-[chunkhash].js',
	},
	plugins: [
		clean( {
			cleanOnceBeforeBuildPatterns: [ '*', '!editor-styles.css' ],
		} ),
		miniCssExtract( {
			filename: './[name]-[chunkhash].css',
		} ),
		new WebpackRTLPlugin(),
		copy( {
			patterns: [
				{ from: filePath( 'assets/src/fonts' ), to: filePath( 'assets/dist/fonts' ) },
				{ from: filePath( 'assets/src/admin-copy' ), to: filePath( 'assets/dist/admin' ) },
				{ from: filePath( 'assets/src/images' ), to: filePath( 'assets/dist/images' ) },
				{ from: filePath( 'assets/src/libs' ), to: filePath( 'assets/dist' ) }
			]
		} ),
	]
} );
