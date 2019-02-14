<?php
/**
 * WP_Framework_Upgrade Configs Filter
 *
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	'upgrade' => [
		'${prefix}app_initialized' => [
			'setup_settings',
		],
		'init'                     => [
			'upgrade',
		],
		'admin_init'               => [
			'setup_update',
		],
	],

];