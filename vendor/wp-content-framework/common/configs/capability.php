<?php
/**
 * WP_Framework_Common Configs Capability
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

	// user can
	'default_user'            => 'manage_options',

	// admin
	'admin_capability'        => 'manage_options',

	// admin menu
	'admin_menu'              => 'manage_options',

	// admin notice
	'admin_notice_capability' => 'manage_options',

];