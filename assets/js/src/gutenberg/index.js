import { registerFormatType } from '../../../../../../../../misc/gutenberg/richtext-helpers';

/** @var {{ settings: {id: number, options: {tag_name: string, class_name: string, group_name: string, icon: string}, title: string, style: string, hide: boolean}[] }} artbParams */

Object.keys( artbParams.settings ).forEach( key => {
	const setting = artbParams.settings[ key ];
	registerFormatType( {
		id: setting.id,
		title: setting.title,
		className: setting.options.class_name,
		tagName: setting.options.tag_name,
		group: setting.options.group_name,
		icon: setting.options.icon,
		style: setting.style,
		hide: setting.hide,
	} );
} );
