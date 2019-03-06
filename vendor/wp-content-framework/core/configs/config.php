<?php
/**
 * WP_Framework_Core Configs Config
 *
 * @version 0.0.37
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	// required php version
	'required_php_version'       => WP_FRAMEWORK_REQUIRED_PHP_VERSION,

	// required wordpress version
	'required_wordpress_version' => WP_FRAMEWORK_REQUIRED_WP_VERSION,

	// filter separator
	'filter_separator'           => '/',

];