<?php
/**
 * WP_Framework_Cache Classes Models Cache Option
 *
 * @version 0.0.1
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

	use \WP_Framework_Cache\Traits\Cache;

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
	 *
	 * @return array
	 */
	private function get_option( $key, $group ) {
		$cache = $this->app->option->get_grouped( $key, $this->get_cache_group( $group ), null );
		if ( empty( $cache ) || ! is_array( $cache ) || count( $cache ) !== 2 ) {
			return [ false, null ];
		}
		list( $value, $time ) = $cache;

		return [ empty( $time ) || $time >= time(), $value ];
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param mixed $value
	 * @param null|int $expire
	 *
	 * @return bool
	 */
	private function set_option( $key, $group, $value, $expire ) {
		$expire = (int) $expire;

		return $this->app->option->set_grouped( $key, $this->get_cache_group( $group ), [
			$value,
			$expire > 0 ? time() + $expire : null,
		] );
	}

	/**
	 * @param string $key
	 * @param string $group
	 *
	 * @return bool
	 */
	private function delete_option( $key, $group ) {
		return $this->app->option->delete_grouped( $key, $this->get_cache_group( $group ) );
	}

	/**
	 * @param string $key
	 * @param string $group
	 *
	 * @return bool
	 */
	public function exists( $key, $group = 'default' ) {
		return $this->app->option->exists( $key, $this->get_cache_group( $group ) );
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( $key, $group = 'default', $default = null ) {
		list( $is_valid, $value ) = $this->get_option( $key, $group );

		return $is_valid ? $value : $default;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param string $group
	 * @param null|int $expire
	 *
	 * @return bool
	 */
	public function set( $key, $value, $group = 'default', $expire = null ) {
		return $this->set_option( $key, $group, $value, $expire );
	}

	/**
	 * @param string $key
	 * @param string $group
	 *
	 * @return bool
	 */
	public function delete( $key, $group = 'default' ) {
		return $this->exists( $key, $group ) ? $this->delete_option( $key, $group ) : false;
	}

	/**
	 * @return bool
	 */
	public function flush() {
		$this->app->option->clear_group_option( $this->get_cache_group_prefix() );

		return true;
	}
}
