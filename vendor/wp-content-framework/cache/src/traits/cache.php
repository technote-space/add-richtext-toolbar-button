<?php
/**
 * WP_Framework_Cache Traits Cache
 *
 * @version 0.0.13
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Cache\Traits;

use WP_Framework;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Trait Cache
 * @package WP_Framework_Cache\Traits
 * @property WP_Framework $app
 */
trait Cache {

	use Package;

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	public abstract function exists( $key, $group = 'default', $common = false );

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public abstract function get( $key, $group = 'default', $common = false, $default = null );

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param string $group
	 * @param bool $common
	 * @param null|int $expire
	 *
	 * @return bool
	 */
	public abstract function set( $key, $value, $group = 'default', $common = false, $expire = null );

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param string $group
	 * @param bool $common
	 * @param null|int $expire
	 *
	 * @return bool
	 */
	public function replace( $key, $value, $group = 'default', $common = false, $expire = null ) {
		if ( ! $this->exists( $key, $group, $common ) ) {
			return false;
		}

		return $this->set( $key, $value, $group, $common, $expire );
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	public abstract function delete( $key, $group = 'default', $common = false );

	/**
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	public abstract function delete_group( $group, $common = false );

	/**
	 * @param string $group
	 * @param bool $common
	 *
	 * @return array
	 */
	public abstract function get_cache_list( $group, $common = false );

	/**
	 * @return bool
	 */
	public function flush() {
		return true;
	}

	/**
	 * @return bool
	 */
	public function close() {
		return true;
	}

	/**
	 * switch blog
	 */
	public function switch_blog() {

	}
}
