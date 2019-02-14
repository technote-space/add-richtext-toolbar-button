<?php
/**
 * WP_Framework_Common Configs Filter
 *
 * @version 0.0.21
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	'option' => [
		'${prefix}app_activated'   => [
			'app_activated',
		],
		'${prefix}app_deactivated' => [
			'app_deactivated',
		],
	],

	'uninstall' => [
		'${prefix}app_activated' => [
			'register_uninstall',
		],
	],

];