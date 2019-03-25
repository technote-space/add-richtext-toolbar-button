<?php
/**
 * WP_Framework_Cache Interfaces Cache
 *
 * @version 0.0.8
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Cache\Interfaces;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Interface Cache
 * @package WP_Framework_Cache\Interfaces
 */
interface Cache extends \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook {

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	public function exists( $key, $group = 'default', $common = false );

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( $key, $group = 'default', $common = false, $default = null );

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param string $group
	 * @param bool $common
	 * @param null|int $expire
	 *
	 * @return bool
	 */
	public function set( $key, $value, $group = 'default', $common = false, $expire = null );

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param string $group
	 * @param bool $common
	 * @param null|int $expire
	 *
	 * @return bool
	 */
	public function replace( $key, $value, $group = 'default', $common = false, $expire = null );

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	public function delete( $key, $group = 'default', $common = false );

	/**
	 * @return bool
	 */
	public function flush();

	/**
	 * @return bool
	 */
	public function close();

	/**
	 * switch blog
	 */
	public function switch_blog();

}
