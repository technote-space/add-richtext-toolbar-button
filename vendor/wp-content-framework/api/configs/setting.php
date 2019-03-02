<?php
/**
 * WP_Framework_Api Configs Setting
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

	'999' => [
		'Others' => [
			'10' => [
				'use_admin_ajax'          => [
					'label'   => 'Use admin-ajax.php instead of wp-json.',
					'type'    => 'bool',
					'default' => false,
				],
				'get_nonce_check_referer' => [
					'label'   => 'Whether to check referer when get nonce.',
					'type'    => 'bool',
					'default' => true,
				],
				'check_referer_host'      => [
					'label'   => 'Server host name which used to check referer host name.',
					'default' => function ( $app ) {
						/** @var \WP_Framework $app */
						return $app->input->server( 'HTTP_HOST', '' );
					},
				],
			],
		],
	],

];