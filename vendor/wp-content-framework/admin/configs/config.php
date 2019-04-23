<?php
/**
 * WP_Framework_Admin Configs Config
 *
 * @version 0.0.22
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	// main menu title
	'main_menu_title'                => '',

	// contact url
	'contact_url'                    => '',

	// twitter
	'twitter'                        => '',

	// github
	'github'                         => '',

	// menu image url
	'menu_image'                     => '',

	// suppress setting help contents
	'suppress_setting_help_contents' => false,

	// setting page title
	'setting_page_title'             => 'Dashboard',

	// setting page priority
	'setting_page_priority'          => 0,

	// setting page slug
	'setting_page_slug'              => 'setting',

	// detail url
	'detail_url'                     => '',

	// github repo (user/repo)
	'github_repo'                    => '',

	// action links
	'action_links'                   => [
		[
			'url'   => function (
				/** @noinspection PhpUnusedParameterInspection */
				$app, $admin, $plugin_data, $context
			) {
				/** @var \WP_Framework_Admin\Classes\Models\Admin $admin */
				return menu_page_url( $admin->get_menu_slug(), false );
			},
			'label' => 'Settings',
		],
	],

	// plugin row meta
	'plugin_row_meta'                => [],

];