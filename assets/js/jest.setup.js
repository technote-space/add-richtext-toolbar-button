const Mousetrap = require( 'mousetrap' );
const lodash = require( 'lodash' );
global.Mousetrap = Mousetrap;
global.window.lodash = lodash;
global.window.matchMedia = () => ( {
	matches: true, addListener: () => {
	},
} );
global.artbParams = {
	translate: {},
	is_valid_contrast_checker: true,
	is_valid_remove_formatting: true,
	default_buttons: {
		'font-color': {
			name: 'font-color',
			title: 'font color',
			icon: 'dashicons-menu',
			style: 'color',
			tag_name: 'span',
			class_name: 'font-color',
			group_name: 'inspector',
			is_valid: true,
		},
		'font-size': {
			name: 'font-size',
			title: 'font size',
			icon: 'dashicons-admin-site',
			tag_name: 'span',
			class_name: 'font-size',
			group_name: 'inspector',
			is_valid: false,
		},
	},
	settings: [
		{
			name: 'test1',
			title: 'test1',
			group_name: 'group1',
			tag_name: 'span',
			class_name: 'test1',
			icon: 'dashicons-admin-customizer',
			is_valid: true,
		},
		{
			name: 'test2',
			title: 'test2',
			group_name: 'group2',
			tag_name: 'span',
			class_name: 'test2',
			icon: 'dashicons-admin-customizer',
			is_valid: false,
		},
		{
			name: 'test3-1',
			title: 'test3-1',
			group_name: 'group3',
			tag_name: 'span',
			class_name: 'test3-1',
			icon: 'dashicons-admin-customizer',
			is_valid: true,
		},
		{
			name: 'test3-2',
			title: 'test3-2',
			group_name: 'group3',
			tag_name: 'span',
			class_name: 'test3-2',
			icon: 'dashicons-admin-customizer',
			is_valid: true,
		},
	],
};

const blockEditor = require( '@wordpress/block-editor' );
const blocks = require( '@wordpress/blocks' );
const components = require( '@wordpress/components' );
const compose = require( '@wordpress/compose' );
const coreData = require( '@wordpress/core-data' );
const data = require( '@wordpress/data' );
const editor = require( '@wordpress/editor' );
const element = require( '@wordpress/element' );
const formatLibrary = require( '@wordpress/format-library' );
const hooks = require( '@wordpress/hooks' );
const i18n = require( '@wordpress/i18n' );
const keycodes = require( '@wordpress/keycodes' );
const richText = require( '@wordpress/rich-text' );
const url = require( '@wordpress/url' );

global.wp = {
	blockEditor,
	blocks,
	components,
	compose,
	coreData,
	data,
	editor,
	element,
	formatLibrary,
	hooks,
	i18n,
	keycodes,
	richText,
	url,
};
