<?php
/**
 * WP_Framework_Admin Configs Filter
 *
 * @version 0.0.23
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	'admin' => [
		'admin_menu'          => [
			'add_menu'  => 9,
			'sort_menu' => 11,
		],
		'in_admin_header'     => [
			'setup_help',
			'do_page_action',
		],
		'admin_notices'       => [
			'admin_notice',
		],
		'plugin_action_links' => [
			'plugin_action_links',
		],
		'plugin_row_meta'     => [
			'plugin_row_meta',
		],
	],

];