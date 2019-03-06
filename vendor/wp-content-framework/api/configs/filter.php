<?php
/**
 * WP_Framework_Api Configs Filter
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

	'api' => [
		'${prefix}app_initialized' => [
			'setup_settings',
		],
		'rest_api_init'            => [
			'register_rest_api',
		],
		'admin_init'               => [
			'register_ajax_api',
		],
		'wp_footer'                => [
			'register_script',
		],
		'admin_footer'             => [
			'register_script',
		],
		'rest_pre_dispatch'        => [
			'rest_pre_dispatch' => 999,
		],
	],

];