<?php
/**
 * @version 1.0.12
 * @author technote-space
 * @since 1.0.0
 * @since 1.0.3 #28
 * @since 1.0.12 changed required wordpress version (4.7.0 ⇒ 5.0.3)
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	exit;
}

return [

	// required wordpress version
	'required_wordpress_version'     => '5.0.3',

	// main menu title
	'main_menu_title'                => 'Add RichText Toolbar Button',

	// db version
	'db_version'                     => '0.0.6',

	// menu image url
	'menu_image'                     => 'icon-24x24.png',

	// suppress setting help contents
	'suppress_setting_help_contents' => true,

	// setting page title
	'setting_page_title'             => 'Detail Settings',

	// setting page priority
	'setting_page_priority'          => 100,

	// setting page slug
	'setting_page_slug'              => 'dashboard',
];
