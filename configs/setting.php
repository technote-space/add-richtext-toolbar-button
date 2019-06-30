<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	exit;
}

return [

	9 => [
		'Main Setting' => [
			10 => [
				'is_valid'                   => [
					'label'   => 'Validity',
					'type'    => 'bool',
					'default' => true,
				],
				'is_valid_font_color'        => [
					'label'   => 'Validity of font color button',
					'type'    => 'bool',
					'default' => true,
				],
				'font_color_icon'            => [
					'label'   => 'Font color button icon',
					'default' => 'dashicons-editor-textcolor',
				],
				'is_valid_background_color'  => [
					'label'   => 'Validity of background color button',
					'type'    => 'bool',
					'default' => true,
				],
				'background_color_icon'      => [
					'label'   => 'Background color button icon',
					'default' => 'dashicons-editor-textcolor',
				],
				'is_valid_font_size'         => [
					'label'   => 'Validity of font size button',
					'type'    => 'bool',
					'default' => true,
				],
				'font_size_icon'             => [
					'label'   => 'Font size button icon',
					'default' => 'dashicons-editor-textcolor',
				],
				'test_phrase'                => [
					'label'     => 'Test phrase',
					'default'   => 'Hello world!',
					'translate' => true,
				],
				'default_icon'               => [
					'label'   => 'Default icon',
					'default' => 'dashicons-edit',
				],
				'default_group'              => [
					'label' => 'Default group',
				],
				'is_valid_contrast_checker'  => [
					'label'   => 'Validity of ContrastChecker',
					'type'    => 'bool',
					'default' => false,
				],
				'is_valid_remove_formatting' => [
					'label'   => 'Validity of remove formatting button',
					'type'    => 'bool',
					'default' => true,
				],
				'is_valid_fontawesome'       => [
					'label'   => 'Validity of fontawesome',
					'type'    => 'bool',
					'default' => false,
				],
			],
		],
	],

];
