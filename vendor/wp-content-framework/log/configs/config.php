<?php
/**
 * WP_Framework_Core Configs Config
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

	// capture shutdown error
	'capture_shutdown_error' => defined( 'WP_DEBUG' ) && WP_DEBUG,

	// target shutdown error
	'target_shutdown_error'  => E_ALL & ~E_NOTICE & ~E_WARNING,

	// log level (for developer)
	'log_level'              => [
		'error' => [
			'is_valid_log'  => true,
			'is_valid_mail' => true,
			'roles'         => [
				// 'administrator',
			],
			'emails'        => [
				// 'test@example.com',
			],
		],
		'info'  => [
			'is_valid_log'  => true,
			'is_valid_mail' => false,
			'roles'         => [
				// 'administrator',
			],
			'emails'        => [
				// 'test@example.com',
			],
		],
		// set default level
		''      => 'info',
	],

	// suppress log messages
	'suppress_log_messages'  => [
		'Non-static method WP_Feed_Cache::create() should not be called statically',
	],

];