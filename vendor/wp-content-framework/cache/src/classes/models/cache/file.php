<?php
/**
 * WP_Framework_Cache Classes Models Cache File
 *
 * @version 0.0.11
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

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Core\Traits\Hook, \WP_Framework_Cache\Traits\Cache;

	/**
	 * @var string[] $_dir
	 */
	private $_dir = [];

	/**
	 * @var string[][] $_group_dir_cache
	 */
	private $_group_dir_cache = [];

	/**
	 * @var array $_cache
	 */
	private $_cache = [];

	/**
	 * @param bool $common
	 *
	 * @return string
	 */
	private function get_cache_relative_dir( $common ) {
		return 'cache' . DS . ( $common && is_multisite() ? 'common' : $this->app->define->blog_id );
	}

	/**
	 * @param bool $common
	 *
	 * @return string
	 */
	private function get_cache_root_dir( $common ) {
		! isset( $this->_dir[ $common ] ) and $this->_dir[ $common ] = $this->app->define->plugin_dir . DS . $this->get_cache_relative_dir( $common ) . DS;

		return $this->_dir[ $common ];
	}

	/**
	 * @param $group
	 * @param bool $common
	 *
	 * @return string
	 */
	private function get_group_dir( $group, $common ) {
		if ( ! isset( $this->_group_dir_cache[ $group ][ $common ] ) ) {
			$this->_group_dir_cache[ $group ][ $common ] = $this->get_cache_root_dir( $common ) . urlencode( $group ) . DS;
		}

		return $this->_group_dir_cache[ $group ][ $common ];
	}

	/**
	 * @param string|null $key
	 * @param string $group
	 * @param bool $common
	 *
	 * @return string
	 */
	private function get_cache_path( $key, $group, $common ) {
		return $this->get_group_dir( $group, $common ) . ( isset( $key ) ? urlencode( $key ) . '.php' : '' );
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	private function exists_cache( $key, $group, $common ) {
		$path = $this->get_cache_path( $key, $group, $common );

		return $this->app->file->is_readable( $path );
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 *
	 * @return array
	 */
	private function read_cache( $key, $group, $common ) {
		$path = $this->get_cache_path( $key, $group, $common );
		if ( ! $this->app->file->is_readable( $path ) ) {
			return [ false, null, null ];
		}

		$contents = $this->app->file->get_contents( $path );
		if ( false === $contents ) {
			$this->app->file->delete( $path );

			return [ false, null, null ];
		}

		$contents = substr( $contents, 7 );
		if ( false === $contents ) {
			$this->app->file->delete( $path );

			return [ false, null, null ];
		}

		$cache = @unserialize( $contents );
		if ( ! is_array( $cache ) || count( $cache ) !== 2 ) {
			$this->app->file->delete( $path );

			return [ false, null, null ];
		}

		list( $value, $time ) = $cache;
		$is_valid = empty( $time ) || $time >= time();
		if ( ! $is_valid ) {
			$this->app->file->delete( $path );
		}

		return [ $is_valid, $value, $time ];
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 * @param mixed $value
	 * @param null|int $time
	 *
	 * @return bool
	 */
	private function write_cache( $key, $group, $common, $value, $time ) {
		return $this->app->file->put_contents_recursive( $this->get_cache_path( $key, $group, $common ), '<?php/*' . serialize( [
				$value,
				$time,
			] ) );
	}

	/**
	 * @param string|null $key
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	private function delete_cache( $key, $group, $common ) {
		$path   = $this->get_cache_path( $key, $group, $common );
		$return = $this->app->file->exists( $path ) && $this->app->file->delete( $path, ! isset( $key ) );
		if ( $return && isset( $key ) && empty( $this->get_cache_list( $group, $common ) ) ) {
			$this->app->file->delete( dirname( $path ) );
		}

		return $return;
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	public function exists( $key, $group = 'default', $common = false ) {
		return $this->app->array->exists( $this->_cache, [ $group, $key, (int) $common ] ) || $this->exists_cache( $key, $group, $common );
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
		$cache = $this->app->array->get( $this->_cache, [ $group, $key, (int) $common ] );
		if ( isset( $cache ) ) {
			list( $value, $time ) = $cache;
			$is_valid = empty( $time ) || $time >= time();
			if ( $is_valid ) {
				return $value;
			}
			$this->delete( $key, $group, $common );
		} else {
			list( $is_valid, $value, $time ) = $this->read_cache( $key, $group, $common );
			if ( $is_valid ) {
				$this->_cache[ $group ][ $key ][ $common ] = [ $value, $time ];

				return $value;
			}
		}

		return $default;
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
		$time                                      = $expire > 0 ? time() + $expire : null;
		$this->_cache[ $group ][ $key ][ $common ] = [ $value, $time ];

		return $this->write_cache( $key, $group, $common, $value, $time );
	}

	/**
	 * @param string $key
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	public function delete( $key, $group = 'default', $common = false ) {
		unset( $this->_cache[ $group ][ $key ][ $common ] );

		return $this->delete_cache( $key, $group, $common );
	}

	/**
	 * @param string $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	public function delete_group( $group, $common = false ) {
		unset( $this->_cache[ $group ] );

		return $this->delete_cache( null, $group, $common );
	}

	/**
	 * @param string $group
	 * @param bool $common
	 *
	 * @return array
	 */
	public function get_cache_list( $group, $common = false ) {
		$dirlist = $this->app->file->dirlist( $this->get_group_dir( $group, $common ) );

		return empty( $dirlist ) ? [] : $this->app->array->map( $this->app->array->filter( $dirlist, function ( $value ) {
			return $this->app->string->ends_with( $value['name'], '.php' );
		} ), function ( $item ) {
			return substr( $item['name'], 0, - 4 );
		} );
	}

	/**
	 * @return bool
	 */
	public function flush() {
		$this->_cache = [];
		$this->app->file->delete_plugin_dir( $this->app, $this->get_cache_relative_dir( false ) );
		$this->app->file->delete_plugin_dir( $this->app, $this->get_cache_relative_dir( true ) );

		return true;
	}

	/**
	 * switch blog
	 */
	public function switch_blog() {
		$this->_dir             = null;
		$this->_group_dir_cache = [];
		$this->_cache           = [];
	}
}
