<?php
/**
 * WP_Framework_Core Interfaces Utility
 *
 * @version 0.0.54
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Core\Interfaces;

use wpdb;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Interface Utility
 * @package WP_Framework_Core\Interfaces
 */
interface Utility {

	/**
	 * @return wpdb
	 */
	public function wpdb();

	/**
	 * @param string $table
	 * @param string $as
	 *
	 * @return string
	 */
	public function alias( $table, $as );

	/**
	 * @param string $table
	 * @param null|string $as
	 *
	 * @return string
	 */
	public function get_wp_table( $table, $as = null );

	/**
	 * @param string $key
	 * @param false|null|string $check_version
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function cache_get( $key, $check_version = false, $default = null );

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param false|null|string $check_version
	 * @param null|int $expire
	 *
	 * @return bool
	 */
	public function cache_set( $key, $value, $check_version = false, $expire = null );

}
