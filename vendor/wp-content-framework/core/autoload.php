<?php
/*  Copyright 2019 technote-space (email : technote.space@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/**
 * WP_Framework autoload
 *
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'PHPUNIT_COMPOSER_INSTALL' ) ) {
	exit;
}

if ( defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}

define( 'WP_CONTENT_FRAMEWORK', 'WP_Framework' );

define( 'WP_FRAMEWORK_VENDOR_NAME', 'wp-content-framework' );

define( 'WP_FRAMEWORK_BOOTSTRAP', __FILE__ );

define( 'WP_FRAMEWORK_REQUIRED_PHP_VERSION', '5.6' );

define( 'WP_FRAMEWORK_REQUIRED_WP_VERSION', '3.9.3' );

if ( ! defined( 'DS' ) ) {
	define( 'DS', DIRECTORY_SEPARATOR );
}

if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
	require_once dirname( __FILE__ ) . DS . 'src' . DS . 'framework_mock.php';

	return;
}

global $wp_version;
if (
	version_compare( phpversion(), WP_FRAMEWORK_REQUIRED_PHP_VERSION, '<' ) ||
	( ! empty( $wp_version ) && version_compare( $wp_version, WP_FRAMEWORK_REQUIRED_WP_VERSION, '<' ) )
) {
	// unsupported version
	require_once dirname( __FILE__ ) . DS . 'src' . DS . 'framework_mock.php';

	return;
}

require_once dirname( __FILE__ ) . DS . 'src' . DS . 'framework.php';
