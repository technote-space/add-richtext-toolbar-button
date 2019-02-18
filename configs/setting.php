<?php
/**
 * @version 1.0.0
 * @author technote-space
 * @since 1.0.0
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
				'is_valid'                  => [
					'label'   => 'validity',
					'type'    => 'bool',
					'default' => true,
				],
				'is_valid_font_color'       => [
					'label'   => 'font color button validity',
					'type'    => 'bool',
					'default' => false,
				],
				'font_color_icon'           => [
					'label'   => 'font color button icon',
					'default' => 'dashicons-editor-textcolor',
				],
				'is_valid_background_color' => [
					'label'   => 'background color button validity',
					'type'    => 'bool',
					'default' => false,
				],
				'background_color_icon'     => [
					'label'   => 'background color button icon',
					'default' => 'dashicons-editor-textcolor',
				],
				'is_valid_font_size'        => [
					'label'   => 'font size button validity',
					'type'    => 'bool',
					'default' => false,
				],
				'font_size_icon'            => [
					'label'   => 'font size button icon',
					'default' => 'dashicons-editor-textcolor',
				],
				'test_phrase'               => [
					'label'     => 'test phrase',
					'default'   => 'Hello world!',
					'translate' => true,
				],
				'default_icon'              => [
					'label'   => 'default icon',
					'default' => 'dashicons-edit',
				],
				'default_group'             => [
					'label' => 'default group',
				],
			],
		],
	],

];