<?php
/**
 * WP_Framework_Update Configs Filter
 *
 * @version 0.0.2
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	'update' => [
		'admin_init'               => [
			'setup_update',
		],
	],

];