import { setupGlobal } from '@technote-space/gutenberg-test-helper';

setupGlobal({
	globalParams: {
		artbParams: {
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
		},
	},
});
