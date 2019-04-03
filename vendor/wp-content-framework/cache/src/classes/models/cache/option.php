<?php
/**
 * WP_Framework_Cache Classes Models Cache Option
 *
 * @version 0.0.10
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Cache\Classes\Models\Cache;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Option
 * @package WP_Framework_Cache\Classes\Models\Cache
 */
class Option implements \WP_Framework_Cache\Interfaces\Cache {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Core\Traits\Hook, \WP_Framework_Cache\Traits\Cache;

	/**
	 * @param string $group
	 *
	 * @return string
	 */
	private function get_cache_group( $group ) {
		return $this->get_cache_group_prefix() . $group;
	}

	/**
	 * @return string
	 */
	private function get_cache_group_prefix() {
		return 'cache/';
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 *
	 * @return array
	 */
	private function get_option( $key, $group, $common ) {
		$cache = $this->app->option->get_grouped( $key, $this->get_cache_group( $group ), null, $common );
		if ( empty( $cache ) || ! is_array( $cache ) || count( $cache ) !== 2 ) {
			return [ false, null ];
		}
		list( $value, $time ) = $cache;
		$is_valid = empty( $time ) || $time >= time();
		if ( ! $is_valid ) {
			$this->delete( $key, $group, $common );
		}

		return [ $is_valid, $value ];
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 * @param mixed $value
	 * @param null|int $expire
	 *
	 * @return bool
	 */
	private function set_option( $key, $group, $common, $value, $expire ) {
		$expire = (int) $expire;

		return $this->app->option->set_grouped( $key, $this->get_cache_group( $group ), [
			$value,
			$expire > 0 ? time() + $expire : null,
		], $common );
	}

	/**
	 * @param string|null $key
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	private function delete_option( $key, $group, $common ) {
		return $this->app->option->delete_grouped( $key, $this->get_cache_group( $group ), $common );
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	public function exists( $key, $group = 'default', $common = false ) {
		return $this->app->option->exists( $key, $this->get_cache_group( $group ), $common );
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( $key, $group = 'default', $common = false, $default = null ) {
		list( $is_valid, $value ) = $this->get_option( $key, $group, $common );

		return $is_valid ? $value : $default;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param string $group
	 * @param bool $common
	 * @param null|int $expire
	 *
	 * @return bool
	 */
	public function set( $key, $value, $group = 'default', $common = false, $expire = null ) {
		return $this->set_option( $key, $group, $common, $value, $expire );
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	public function delete( $key, $group = 'default', $common = false ) {
		return $this->exists( $key, $group, $common ) ? $this->delete_option( $key, $group, $common ) : false;
	}

	/**
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	public function delete_group( $group, $common = false ) {
		return $this->delete_option( null, $group, $common );
	}

	/**
	 * @return bool
	 */
	public function flush() {
		$this->app->option->clear_group_option( $this->get_cache_group_prefix(), false );
		$this->app->option->clear_group_option( $this->get_cache_group_prefix(), true );

		return true;
	}
}
