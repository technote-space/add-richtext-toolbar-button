<?php
/**
 * WP_Framework_Common Configs Filter
 *
 * @version 0.0.41
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	'define' => [
		'switch_blog' => [
			'switch_blog' => 1,
		],
	],

	'option' => [
		'${prefix}app_activated'   => [
			'app_activated',
		],
		'${prefix}app_deactivated' => [
			'app_deactivated',
		],
		'switch_blog'              => [
			'switch_blog' => 2,
		],
	],

	'system' => [
		'${framework}initialize' => [
			'app_initialized' => 9,
		],
	],

	'uninstall' => [
		'${prefix}app_activated' => [
			'register_uninstall',
		],
	],

];