import { Common, RichText } from '@technote-space/register-grouped-format-type';

const { getRemoveFormatButton } = RichText;
const { getTranslator } = Common.Helpers;
const { Icon } = Common.Components;

const PREFIX = 'arbt--';
const getName = name => PREFIX + name;
const getIcon = params => icon => Icon( { icon: icon, defaultIcon: params.default_icon } );
const INSPECTOR_GROUP = getName( 'inspector' );
const TOOL_BUTTON_GROUP = getName( 'tool-button' );

/**
 * @param {{is_valid_contrast_checker, is_valid_remove_formatting}} params params
 * @returns {array} group setting
 */
export const getDefaultButtonGroupSetting = params => {
	const translate = getTranslator( params );
	return [
		INSPECTOR_GROUP,
		{
			toolbarGroup: INSPECTOR_GROUP,
			inspectorSettings: {
				title: translate( 'Inline Text Settings' ),
				initialOpen: true,
			},
			useContrastChecker: params.is_valid_contrast_checker,
			additionalInspectors: params.is_valid_remove_formatting ? [ getRemoveFormatButton( translate( 'Remove All formatting' ) ) ] : [],
		},
	];
};

/**
 * @param {{default_buttons:{name, title, icon, style, class_name, is_valid}, default_icon}} params params
 * @param {string} key key
 * @returns {array} color button setting
 */
export const getColorButtonSetting = ( params, key ) => {
	const setting = params.default_buttons[ key ];
	return [
		getName( setting.name ),
		setting.title,
		getIcon( params )( setting.icon ),
		setting.style,
		{
			group: INSPECTOR_GROUP,
			className: setting.class_name,
			createDisabled: ! setting.is_valid,
		},
	];
};

/**
 * @param {{default_buttons:{name, title, icon, class_name, is_valid}, default_icon}} params params
 * @param {string} key key
 * @returns {array} font size button setting
 */
export const getFontSizeButtonSetting = ( params, key ) => {
	const setting = params.default_buttons[ key ];
	return [
		getName( setting.name ),
		setting.title,
		getIcon( params )( setting.icon ),
		{
			group: INSPECTOR_GROUP,
			className: setting.class_name,
			createDisabled: ! setting.is_valid,
		},
	];
};

/**
 * @param {{settings:{name, title, class_name, tag_name, is_valid, group_name, icon}[], default_icon}} params params
 * @returns {{settings: Array, groups}} settings
 */
export const getSettings = params => {
	const groups = {};
	const settings = [];
	Object.keys( params.settings ).forEach( key => {
		const setting = params.settings[ key ];
		const group = getName( 'item--' + setting.group_name );
		if ( ! ( group in groups ) ) {
			groups[ group ] = {
				toolbarGroup: TOOL_BUTTON_GROUP,
				icon: getIcon( params )( setting.icon ),
				label: setting.group_name,
			};
		}
		settings.push( [
			group,
			setting.name,
			getIcon( params )( setting.icon ),
			{
				title: setting.title,
				className: setting.class_name,
				tagName: setting.tag_name,
				createDisabled: ! setting.is_valid,
			},
		] );
	} );
	return { groups, settings };
};
