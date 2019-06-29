/* eslint-disable no-magic-numbers */
import { getDefaultButtonGroupSetting, getColorButtonSetting, getFontSizeButtonSetting, getSettings } from '../src/utils';

describe( 'getDefaultButtonGroupSetting', () => {
	it( 'should return default button group setting', () => {
		artbParams.is_valid_remove_formatting = true;
		artbParams.is_valid_contrast_checker = true;
		const setting = getDefaultButtonGroupSetting( artbParams );
		expect( typeof setting ).toBe( 'object' );
		expect( setting ).toHaveLength( 2 );
		expect( setting[ 0 ] ).toBe( 'arbt--inspector' );
		expect( typeof setting[ 1 ] ).toBe( 'object' );
		expect( setting[ 1 ] ).toHaveProperty( 'toolbarGroup' );
		expect( setting[ 1 ] ).toHaveProperty( 'inspectorSettings' );
		expect( setting[ 1 ] ).toHaveProperty( 'useContrastChecker' );
		expect( setting[ 1 ] ).toHaveProperty( 'additionalInspectors' );
		expect( setting[ 1 ].useContrastChecker ).toBe( artbParams.is_valid_contrast_checker );
		expect( setting[ 1 ].additionalInspectors ).toHaveLength( 1 );
	} );

	it( 'should return default button group setting', () => {
		artbParams.is_valid_remove_formatting = false;
		artbParams.is_valid_contrast_checker = false;
		const setting = getDefaultButtonGroupSetting( artbParams );
		expect( typeof setting ).toBe( 'object' );
		expect( setting ).toHaveLength( 2 );
		expect( setting[ 0 ] ).toBe( 'arbt--inspector' );
		expect( typeof setting[ 1 ] ).toBe( 'object' );
		expect( setting[ 1 ] ).toHaveProperty( 'toolbarGroup' );
		expect( setting[ 1 ] ).toHaveProperty( 'inspectorSettings' );
		expect( setting[ 1 ] ).toHaveProperty( 'useContrastChecker' );
		expect( setting[ 1 ] ).toHaveProperty( 'additionalInspectors' );
		expect( setting[ 1 ].useContrastChecker ).toBe( artbParams.is_valid_contrast_checker );
		expect( setting[ 1 ].additionalInspectors ).toHaveLength( 0 );
	} );
} );

describe( 'getColorButtonSetting', () => {
	it( 'should return color button setting', () => {
		const setting = getColorButtonSetting( artbParams, 'font-color' );
		expect( typeof setting ).toBe( 'object' );
		expect( setting ).toHaveLength( 5 );
		expect( setting [ 0 ] ).toEndWith( artbParams.default_buttons[ 'font-color' ].name );
		expect( setting [ 1 ] ).toBe( artbParams.default_buttons[ 'font-color' ].title );
		expect( typeof setting [ 2 ] ).toBe( 'object' );
		expect( setting [ 3 ] ).toBe( artbParams.default_buttons[ 'font-color' ].style );
		expect( typeof setting [ 4 ] ).toBe( 'object' );
		expect( setting[ 4 ] ).toHaveProperty( 'group' );
		expect( setting[ 4 ] ).toHaveProperty( 'className' );
		expect( setting[ 4 ] ).toHaveProperty( 'createDisabled' );
		expect( setting[ 4 ].className ).toBe( artbParams.default_buttons[ 'font-color' ].class_name );
		expect( setting[ 4 ].createDisabled ).toBe( ! artbParams.default_buttons[ 'font-color' ].is_valid );
	} );
} );

describe( 'getFontSizeButtonSetting', () => {
	it( 'should return font size button setting', () => {
		const setting = getFontSizeButtonSetting( artbParams, 'font-size' );
		expect( typeof setting ).toBe( 'object' );
		expect( setting ).toHaveLength( 4 );
		expect( setting[ 0 ] ).toEndWith( artbParams.default_buttons[ 'font-size' ].name );
		expect( setting[ 1 ] ).toBe( artbParams.default_buttons[ 'font-size' ].title );
		expect( typeof setting[ 2 ] ).toBe( 'object' );
		expect( typeof setting[ 3 ] ).toBe( 'object' );
		expect( setting[ 3 ] ).toHaveProperty( 'group' );
		expect( setting[ 3 ] ).toHaveProperty( 'className' );
		expect( setting[ 3 ] ).toHaveProperty( 'createDisabled' );
		expect( setting[ 3 ].className ).toBe( artbParams.default_buttons[ 'font-size' ].class_name );
		expect( setting[ 3 ].createDisabled ).toBe( ! artbParams.default_buttons[ 'font-size' ].is_valid );
	} );
} );

describe( 'getSettings', () => {
	it( 'should return settings', () => {
		const setting = getSettings( artbParams );
		expect( typeof setting ).toBe( 'object' );
		expect( setting ).toHaveProperty( 'groups' );
		expect( setting ).toHaveProperty( 'settings' );
		expect( setting.groups ).toHaveProperty( 'arbt--item--group1' );
		expect( setting.groups ).toHaveProperty( 'arbt--item--group2' );
		expect( setting.groups ).toHaveProperty( 'arbt--item--group3' );
		expect( setting.groups[ 'arbt--item--group1' ] ).toHaveProperty( 'toolbarGroup' );
		expect( setting.groups[ 'arbt--item--group1' ] ).toHaveProperty( 'icon' );
		expect( setting.groups[ 'arbt--item--group1' ] ).toHaveProperty( 'label' );
		expect( setting.groups[ 'arbt--item--group1' ].label ).toBe( 'group1' );
		expect( setting.groups[ 'arbt--item--group2' ].label ).toBe( 'group2' );
		expect( setting.groups[ 'arbt--item--group3' ].label ).toBe( 'group3' );
		expect( setting.settings ).toHaveLength( 4 );
		expect( setting.settings[ 0 ] ).toHaveLength( 4 );
		expect( setting.settings[ 0 ][ 0 ] ).toEndWith( artbParams.settings[ 0 ].group_name );
		expect( setting.settings[ 0 ][ 1 ] ).toBe( artbParams.settings[ 0 ].name );
		expect( typeof setting.settings[ 0 ][ 2 ] ).toBe( 'object' );
		expect( typeof setting.settings[ 0 ][ 3 ] ).toBe( 'object' );
		expect( setting.settings[ 0 ][ 3 ] ).toHaveProperty( 'title' );
		expect( setting.settings[ 0 ][ 3 ] ).toHaveProperty( 'className' );
		expect( setting.settings[ 0 ][ 3 ] ).toHaveProperty( 'tagName' );
		expect( setting.settings[ 0 ][ 3 ] ).toHaveProperty( 'createDisabled' );
		expect( setting.settings[ 0 ][ 3 ].title ).toBe( artbParams.settings[ 0 ].title );
		expect( setting.settings[ 0 ][ 3 ].className ).toBe( artbParams.settings[ 0 ].class_name );
		expect( setting.settings[ 0 ][ 3 ].tagName ).toBe( artbParams.settings[ 0 ].tag_name );
		expect( setting.settings[ 0 ][ 3 ].createDisabled ).toBe( ! artbParams.settings[ 0 ].is_valid );
	} );
} );
