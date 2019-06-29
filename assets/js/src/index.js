import { Common, RichText } from '@technote-space/register-grouped-format-type';
import { getDefaultButtonGroupSetting, getColorButtonSetting, getFontSizeButtonSetting, getSettings } from './utils';

const { registerFormatTypeGroup, registerGroupedFormatType } = RichText;
const { getToolbarButtonProps, getColorButtonProps, getFontSizesButtonProps } = Common.Helpers;


// register default buttons
{
	registerFormatTypeGroup( ...getDefaultButtonGroupSetting( artbParams ) );
	registerGroupedFormatType( getColorButtonProps( ...getColorButtonSetting( artbParams, 'font-color' ) ) );
	registerGroupedFormatType( getColorButtonProps( ...getColorButtonSetting( artbParams, 'background-color' ) ) );
	registerGroupedFormatType( getFontSizesButtonProps( ...getFontSizeButtonSetting( artbParams, 'font-size' ) ) );
}

// register buttons
{
	const { groups, settings } = getSettings( artbParams );
	Object.keys( groups ).forEach( group => {
		registerFormatTypeGroup( group, groups[ group ] );
	} );
	settings.forEach( setting => {
		registerGroupedFormatType( getToolbarButtonProps( ...setting ) );
	} );
}

/** @var {{settings:{name, title, class_name, tag_name, is_valid, group_name, icon}[], default_buttons:{}, default_icon}} artbParams */
