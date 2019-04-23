<?php
/**
 * WP_Framework_Cache Classes Models Cache
 *
 * @version 0.0.12
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Cache\Classes\Models;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Cache
 * @package WP_Framework_Cache\Classes\Models
 */
class Cache implements \WP_Framework_Core\Interfaces\Loader, \WP_Framework_Cache\Interfaces\Cache, \WP_Framework_Common\Interfaces\Uninstall {

	use \WP_Framework_Core\Traits\Loader, \WP_Framework_Cache\Traits\Cache, \WP_Framework_Common\Traits\Uninstall;

	/**
	 * @var \WP_Framework_Cache\Interfaces\Cache $_cache
	 */
	private $_cache;

	/**
	 * initialized
	 */
	protected function initialized() {
		$cache_class = '\WP_Framework_Cache\Classes\Models\Cache\Option';
		$cache_type  = $this->app->get_config( 'config', 'cache_type', $this->apply_filters( 'cache_type' ) );
		if ( 'option' !== $cache_type ) {
			if ( 'file' === $cache_type ) {
				$cache_class = '\WP_Framework_Cache\Classes\Models\Cache\File';
			} elseif ( class_exists( $cache_type ) && is_subclass_of( $cache_type, '\WP_Framework_Cache\Interfaces\Cache' ) ) {
				$cache_class = $cache_type;
			}
		}
		/** @var \WP_Framework_Core\Traits\Singleton $cache_class */
		$this->_cache = $cache_class::get_instance( $this->app );
	}

	/**
	 * setup settings
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_settings() {
		if ( ! $this->is_valid_cron_delete() ) {
			$this->app->setting->remove_setting( 'delete_cache_interval' );
		}
	}

	/**
	 * clear cache
	 */
	private function clear_cache() {
		foreach ( $this->get_class_list() as $class ) {
			/** @var \WP_Framework_Cache\Interfaces\Cache $class */
			$class->flush();
		}
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	public function exists( $key, $group = 'default', $common = false ) {
		return $this->_cache->exists( $key, $group, $common );
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
		return $this->_cache->get( $key, $group, $common, $default );
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
		return $this->_cache->set( $key, $value, $group, $common, $expire );
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
	public function replace( $key, $value, $group = 'default', $common = false, $expire = null ) {
		return $this->_cache->replace( $key, $value, $group, $common, $expire );
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	public function delete( $key, $group = 'default', $common = false ) {
		return $this->_cache->delete( $key, $group, $common );
	}

	/**
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	public function delete_group( $group, $common = false ) {
		return $this->_cache->delete_group( $group, $common );
	}

	/**
	 * @param string $group
	 * @param bool $common
	 *
	 * @return array
	 */
	public function get_cache_list( $group, $common = false ) {
		return $this->_cache->get_cache_list( $group, $common );
	}

	/**
	 * @return bool
	 */
	public function flush() {
		return $this->_cache->flush();
	}

	/**
	 * @return bool
	 */
	public function close() {
		return $this->_cache->close();
	}

	/**
	 * switch blog
	 */
	public function switch_blog() {
		$this->_cache->switch_blog();
	}

	/**
	 * @return bool
	 */
	public function is_valid_cron_delete() {
		if ( ! $this->is_valid_package( 'cron' ) ) {
			return false;
		}

		$cache = $this->cache_get( 'is_valid_cron_delete' );
		if ( isset( $cache ) ) {
			return $cache;
		}

		$result = ! empty( $this->get_delete_cache_group() ) || ! empty( $this->get_delete_cache_common_group() );
		$this->cache_set( 'is_valid_cron_delete', $result );

		return $result;
	}

	/**
	 * @return array
	 */
	private function get_delete_cache_group() {
		return $this->app->array->to_array( $this->app->get_config( 'config', 'delete_cache_group' ) );
	}

	/**
	 * @return array
	 */
	private function get_delete_cache_common_group() {
		return $this->app->array->to_array( $this->app->get_config( 'config', 'delete_cache_common_group' ) );
	}

	/**
	 * @return array
	 */
	public function delete_expired_cache() {
		$count   = 0;
		$deleted = 0;
		$default = '___check_deleted___' . $this->app->utility->uuid();
		foreach ( $this->get_delete_cache_group() as $group ) {
			foreach ( $this->get_cache_list( $group, false ) as $key ) {
				$default === $this->_cache->get( $key, $group, false, $default ) and $deleted ++;
				$count ++;
			}
		}
		foreach ( $this->get_delete_cache_common_group() as $group ) {
			foreach ( $this->get_cache_list( $group, true ) as $key ) {
				$default === $this->_cache->get( $key, $group, true, $default ) and $deleted ++;
				$count ++;
			}
		}

		return [
			'total'   => $count,
			'deleted' => $deleted,
		];
	}

	/**
	 * uninstall
	 */
	public function uninstall() {
		$this->clear_cache();
	}

	/**
	 * @return int
	 */
	public function get_uninstall_priority() {
		return 50;
	}

	/**
	 * @return array
	 */
	protected function get_namespaces() {
		return [
			$this->app->define->plugin_namespace . '\\Classes\\Models\\Cache',
			'\WP_Framework_Cache\\Classes\\Models\\Cache',
		];
	}

	/**
	 * @return string
	 */
	protected function get_instanceof() {
		return '\WP_Framework_Cache\Interfaces\Cache';
	}
}
