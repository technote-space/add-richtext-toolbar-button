<?php
/**
 * WP_Framework_Cache Configs Setting
 *
 * @version 0.0.13
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	'100' => [
		'Performance' => [
			'100' => [
				'cache_type' => [
					'label'   => 'Cache type (option or file)',
					'default' => function ( $app ) {
						/** @var WP_Framework $app */
						return ! $app->utility->defined( 'WP_FRAMEWORK_FORCE_CACHE' ) && $app->utility->defined( 'WP_DEBUG' ) ? '\WP_Framework_Cache\Classes\Models\Cache\None' : 'option';
					},
				],
			],
		],
	],

	'999' => [
		'Others' => [
			'10' => [
				'delete_cache_interval' => [
					'label'   => 'Delete cache interval',
					'default' => DAY_IN_SECONDS,
					'min'     => MINUTE_IN_SECONDS,
				],
			],
		],
	],

];