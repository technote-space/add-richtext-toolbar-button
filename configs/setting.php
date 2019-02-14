<?php
/**
 * @version 0.0.1
 * @author technote-space
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
				'is_valid'      => [
					'label'   => 'validity',
					'type'    => 'bool',
					'default' => true,
				],
				'test_phrase'   => [
					'label'     => 'test phrase',
					'default'   => 'Hello world!',
					'translate' => true,
				],
				'default_icon'  => [
					'label'   => 'default icon',
					'default' => 'dashicons-edit',
				],
				'default_group' => [
					'label' => 'default group',
				],
			],
		],
	],

];