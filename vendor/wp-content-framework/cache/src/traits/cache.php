<?php
/**
 * WP_Framework_Cache Traits Cache
 *
 * @version 0.0.1
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Cache\Traits;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Trait Cache
 * @package WP_Framework_Cache\Traits
 * @property \WP_Framework $app
 */
trait Cache {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Core\Traits\Hook, Package;

	/**
	 * @param string $key
	 * @param string $group
	 *
	 * @return bool
	 */
	public abstract function exists( $key, $group = 'default' );

	/**
	 * @param string $key
	 * @param string $group
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public abstract function get( $key, $group = 'default', $default = null );

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param string $group
	 * @param null|int $expire
	 *
	 * @return bool
	 */
	public abstract function set( $key, $value, $group = 'default', $expire = null );

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param string $group
	 * @param null|int $expire
	 *
	 * @return bool
	 */
	public function replace( $key, $value, $group = 'default', $expire = null ) {
		if ( ! $this->exists( $key, $group ) ) {
			return false;
		}

		return $this->set( $key, $value, $group, $expire );
	}

	/**
	 * @param string $key
	 * @param string $group
	 *
	 * @return bool
	 */
	public abstract function delete( $key, $group = 'default' );

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

}
