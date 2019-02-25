<?php
/**
 * @version 1.0.7
 * @author technote-space
 * @since 1.0.0
 * @since 1.0.3 #35
 * @since 1.0.7 #61
 * @copyright technote-space All Rights Reserved
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
					'label'   => 'validity',
					'type'    => 'bool',
					'default' => true,
				],
				'is_valid_font_color'        => [
					'label'   => 'validity of font color button',
					'type'    => 'bool',
					'default' => true,
				],
				'font_color_icon'            => [
					'label'   => 'font color button icon',
					'default' => 'dashicons-editor-textcolor',
				],
				'is_valid_background_color'  => [
					'label'   => 'validity of background color button',
					'type'    => 'bool',
					'default' => true,
				],
				'background_color_icon'      => [
					'label'   => 'background color button icon',
					'default' => 'dashicons-editor-textcolor',
				],
				'is_valid_font_size'         => [
					'label'   => 'validity of font size button',
					'type'    => 'bool',
					'default' => true,
				],
				'font_size_icon'             => [
					'label'   => 'font size button icon',
					'default' => 'dashicons-editor-textcolor',
				],
				'test_phrase'                => [
					'label'     => 'test phrase',
					'default'   => 'Hello world!',
					'translate' => true,
				],
				'default_icon'               => [
					'label'   => 'default icon',
					'default' => 'dashicons-edit',
				],
				'default_group'              => [
					'label' => 'default group',
				],
				'is_valid_contrast_checker'  => [
					'label'   => 'validity of ContrastChecker',
					'type'    => 'bool',
					'default' => false,
				],
				'is_valid_remove_formatting' => [
					'label'   => 'validity of remove formatting button',
					'type'    => 'bool',
					'default' => true,
				],
			],
		],
	],

];