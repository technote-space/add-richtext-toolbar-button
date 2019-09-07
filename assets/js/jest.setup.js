const Mousetrap = require( 'mousetrap' );
const lodash = require( 'lodash' );
global.Mousetrap = Mousetrap;
global.window.lodash = lodash;
global.window.matchMedia = () => ( {
	matches: true, addListener: () => {
	},
} );
global.wpMock = {
	blockEditor: {
		getColorObjectByColorValue: () => false,
	},
	element: {
		useRef: () => ( {
			current: {
				contains: () => false,
				focus: () => 0,
				getBoundingClientRect: () => ( { width: 0, height: 0 } ),
				parentNode: {
					getBoundingClientRect: () => ( { width: 0, height: 0, left: 0, right: 0, top: 0, bottom: 0 } ),
				},
				querySelectorAll: () => ( [] ),
			},
		} ),
	},
};
global.window.lodash.debounce = fn => {
	function debounced() {
		return fn();
	}

	debounced.cancel = jest.fn();
	debounced.flush = jest.fn();
	return debounced;
};
global.artbParams = {
	translate: {},
	isValidContrastChecker: true,
	isValidRemoveFormatting: true,
	defaultButtons: {
		'font-color': {
			name: 'font-color',
			title: 'font color',
			icon: 'dashicons-menu',
			style: 'color',
			tagName: 'span',
			className: 'font-color',
			groupName: 'inspector',
			isValid: true,
		},
		'font-size': {
			name: 'font-size',
			title: 'font size',
			icon: 'dashicons-admin-site',
			tagName: 'span',
			className: 'font-size',
			groupName: 'inspector',
			isValid: false,
		},
	},
	settings: [
		{
			name: 'test1',
			title: 'test1',
			groupName: 'group1',
			tagName: 'span',
			className: 'test1',
			icon: 'dashicons-admin-customizer',
			isValid: true,
		},
		{
			name: 'test2',
			title: 'test2',
			groupName: 'group2',
			tagName: 'span',
			className: 'test2',
			icon: 'dashicons-admin-customizer',
			isValid: false,
		},
		{
			name: 'test3-1',
			title: 'test3-1',
			groupName: 'group3',
			tagName: 'span',
			className: 'test3-1',
			icon: 'dashicons-admin-customizer',
			isValid: true,
		},
		{
			name: 'test3-2',
			title: 'test3-2',
			groupName: 'group3',
			tagName: 'span',
			className: 'test3-2',
			icon: 'dashicons-admin-customizer',
			isValid: true,
		},
	],
};

jest.mock( '@wordpress/block-editor', () => ( {
	...jest.requireActual( '@wordpress/block-editor' ),
	getColorObjectByColorValue: ( colors, value ) => global.wpMock.blockEditor.getColorObjectByColorValue( colors, value ),
} ) );
jest.mock( '@wordpress/element', () => ( {
	...jest.requireActual( '@wordpress/element' ),
	useRef: ( colors, value ) => global.wpMock.element.useRef( colors, value ),
} ) );

const blockEditor = require( '@wordpress/block-editor' );
const blocks = require( '@wordpress/blocks' );
const components = require( '@wordpress/components' );
const compose = require( '@wordpress/compose' );
const coreData = require( '@wordpress/core-data' );
const data = require( '@wordpress/data' );
const dom = require( '@wordpress/dom' );
const editor = require( '@wordpress/editor' );
const element = require( '@wordpress/element' );
const formatLibrary = require( '@wordpress/format-library' );
const hooks = require( '@wordpress/hooks' );
const i18n = require( '@wordpress/i18n' );
const isShallowEqual = require( '@wordpress/is-shallow-equal' );
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
	dom,
	editor,
	element,
	formatLibrary,
	hooks,
	i18n,
	isShallowEqual,
	keycodes,
	richText,
	url,
};
