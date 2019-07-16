const { Fragment } = wp.element;
const { addFilter } = wp.hooks;

import './plugin.scss';

window.artbParams = window.artbParams || {
	isValidContrastChecker: true,
	isValidRemoveFormatting: true,
	defaultIcon: 'dashicons-edit',
	defaultButtons: {
		'font-color': {
			name: 'font-color',
			title: 'Font color',
			icon: 'dashicons-editor-textcolor',
			className: 'font-color',
			style: 'color',
			isValid: true,
		},
		'background-color': {
			name: 'background-color',
			title: 'Background color',
			icon: 'dashicons-editor-textcolor',
			className: 'background-color',
			style: 'background-color',
			isValid: true,
		},
		'font-size': {
			name: 'font-size',
			title: 'Font size',
			icon: 'dashicons-editor-textcolor',
			className: 'font-size',
			isValid: true,
		},
	},
	settings: [
		{
			'icon': null,
			'title': '蛍光ペン(青)',
			'name': 'setting-5288',
			'selector': 'span.artb-5288',
			'tagName': 'span',
			'className': 'artb-5288',
			'groupName': '蛍光ペン(青)-5288',
			'isValidToolbarButton': 1,
			'isValid': 1,
		},
		{
			'icon': null,
			'title': '蛍光ペン(赤)',
			'name': 'setting-5286',
			'selector': 'span.artb-5286',
			'tagName': 'span',
			'className': 'artb-5286',
			'groupName': 'ペン',
			'isValidToolbarButton': 1,
			'isValid': 1,
		},
		{
			'icon': null,
			'title': '蛍光ペン(緑)',
			'name': 'setting-5287',
			'selector': 'span.artb-5287',
			'tagName': 'span',
			'className': 'artb-5287',
			'groupName': 'ペン',
			'isValidToolbarButton': 1,
			'isValid': 1,
		},
		{
			'icon': null,
			'title': '蛍光ペン(黄色)',
			'name': 'setting-5289',
			'selector': 'span.artb-5289',
			'tagName': 'span',
			'className': 'artb-5289',
			'groupName': 'ペン',
			'isValidToolbarButton': 1,
			'isValid': 1,
		},
	],
};

addFilter( 'gh-pages.renderContent', 'plugin/renderContent', () => <Fragment>
	<p>This page is demonstration of <a href="https://github.com/technote-space/add-richtext-toolbar-button">Add RichText Toolbar Button</a></p>
	<p>YThis plugin makes it easy to add RichText toolbar button.</p>
	<img className='playground__content__screenshot' src='./screenshot.gif'/>
</Fragment> );
