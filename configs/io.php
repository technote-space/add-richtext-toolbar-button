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

	'setting' => [
		'tag_name',
		'class_name',
		'group_name',
		'icon' => [
			'export' => function ( $v ) {
				if ( preg_match( '#^data:image#', $v ) || preg_match( '#^dashicons-#', $v ) ) {
					return $v;
				}

				return null;
			},
		],
		'style',
		'priority',
		'is_valid_toolbar_button',
	],

];
