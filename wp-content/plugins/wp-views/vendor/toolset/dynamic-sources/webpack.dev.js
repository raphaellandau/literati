const { merge } = require( 'webpack-merge' );
const build = require( './webpack.config.js' );

module.exports = merge( build, {
	devtool: 'source-map',
} );
