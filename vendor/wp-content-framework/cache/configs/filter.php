<?php
/**
 * WP_Framework_Cache Configs Filter
 *
 * @version 0.0.11
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	'cache' => [
		'${prefix}app_activated'        => [
			'clear_cache',
		],
		'${prefix}app_deactivated'      => [
			'clear_cache',
		],
		'switch_blog'                   => [
			'switch_blog',
		],
		'${prefix}post_load_admin_page' => [
			'setup_settings',
		],
	],

];