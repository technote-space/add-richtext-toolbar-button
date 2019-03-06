<?php
/**
 * WP_Framework_Core Configs Setting
 *
 * @version 0.0.1
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	'999' => [
		'Others' => [
			'10' => [
				'admin_menu_position' => [
					'label'   => 'Admin menu position',
					'type'    => 'int',
					'default' => 100,
					'min'     => 0,
				],
			],
		],
	],

];