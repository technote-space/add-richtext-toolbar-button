<?php
/**
 * WP_Framework_Cache Classes Models Cache File
 *
 * @version 0.0.7
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
 * Class File
 * @package WP_Framework_Cache\Classes\Models\Cache
 */
class File implements \WP_Framework_Cache\Interfaces\Cache {

	use \WP_Framework_Cache\Traits\Cache;

	/**
	 * @var string $_dir
	 */
	private $_dir;

	/**
	 * @var string[] $_group_dir_cache
	 */
	private $_group_dir_cache = [];

	/**
	 * @var array $_cache
	 */
	private $_cache = [];

	/**
	 * @param $group
	 *
	 * @return string
	 */
	private function get_group_dir( $group ) {
		if ( ! isset( $this->_group_dir_cache[ $group ] ) ) {
			! isset( $this->_dir ) and $this->_dir = $this->app->define->plugin_dir . DS . 'cache' . DS;
			$this->_group_dir_cache[ $group ] = $this->_dir . urlencode( $group ) . DS;
			if ( ! file_exists( $this->_group_dir_cache[ $group ] ) ) {
				@mkdir( $this->_group_dir_cache[ $group ], 0744, true );
			}
		}

		return $this->_group_dir_cache[ $group ];
	}

	/**
	 * @param string $key
	 * @param string $group
	 *
	 * @return string
	 */
	private function get_cache_path( $key, $group ) {
		return $this->get_group_dir( $group ) . urlencode( $key ) . '.php';
	}

	/**
	 * @param string $key
	 * @param string $group
	 *
	 * @return bool
	 */
	private function exists_cache( $key, $group ) {
		$path = $this->get_cache_path( $key, $group );

		return is_readable( $path );
	}

	/**
	 * @param string $key
	 * @param string $group
	 *
	 * @return array
	 */
	private function read_cache( $key, $group ) {
		$path = $this->get_cache_path( $key, $group );
		if ( ! is_readable( $path ) ) {
			return [ false, null ];
		}

		$contents = @file_get_contents( $path, LOCK_EX );
		if ( false === $contents ) {
			@unlink( $path );

			return [ false, null ];
		}

		$contents = substr( $contents, 7 );
		if ( false === $contents ) {
			@unlink( $path );

			return [ false, null ];
		}

		$cache = @unserialize( $contents );
		if ( ! is_array( $cache ) || count( $cache ) !== 2 ) {
			@unlink( $path );

			return [ false, null ];
		}

		list( $value, $time ) = $cache;
		$is_valid = empty( $time ) || $time >= time();
		if ( ! $is_valid ) {
			@unlink( $path );
		}

		return [ $is_valid, $value ];
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param mixed $value
	 * @param null|int $expire
	 *
	 * @return bool
	 */
	private function write_cache( $key, $group, $value, $expire ) {
		$path   = $this->get_cache_path( $key, $group );
		$result = false !== @file_put_contents( $path, '<?php/*' . serialize( [
					$value,
					$expire > 0 ? time() + $expire : null,
				] ), LOCK_EX );
		if ( $result ) {
			chmod( $path, 0644 );
		}

		return $result;
	}

	/**
	 * @param string $key
	 * @param string $group
	 *
	 * @return bool
	 */
	private function delete_cache( $key, $group ) {
		return @unlink( $this->get_cache_path( $key, $group ) );
	}

	/**
	 * @param string $key
	 * @param string $group
	 *
	 * @return bool
	 */
	public function exists( $key, $group = 'default' ) {
		return $this->app->array->exists( $this->_cache, [ $group, $key ] ) || $this->exists_cache( $key, $group );
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( $key, $group = 'default', $default = null ) {
		return $this->app->array->get( $this->_cache, [ $group, $key ], function () use ( $key, $group, $default ) {
			list( $is_valid, $value ) = $this->read_cache( $key, $group );
			if ( $is_valid ) {
				$this->_cache[ $group ][ $key ] = $value;

				return $value;
			}

			return $default;
		} );
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
		$this->_cache[ $group ][ $key ] = $value;

		return $this->write_cache( $key, $group, $value, $expire );
	}

	/**
	 * @param string $key
	 * @param string $group
	 *
	 * @return bool
	 */
	public function delete( $key, $group = 'default' ) {
		unset( $this->_cache[ $group ][ $key ] );

		return $this->delete_cache( $key, $group );
	}

	/**
	 * @return bool
	 */
	public function flush() {
		$this->_cache = [];
		$this->app->file->delete_plugin_dir( $this->app, 'cache' );

		return true;
	}
}
