<?php
/**
 * WP_Framework_Custom_Post Configs Config
 *
 * @version 0.0.21
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	// prior default (to nullable)
	'prior_default'              => false,

	// required wordpress version
	'required_wordpress_version' => '4.6', // WP_Post_Type >= 4.6

];