module.exports = {
	root: true,
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	globals: {
		wp: 'off',
	},
	plugins: [ 'indent-empty-lines' ],
};
