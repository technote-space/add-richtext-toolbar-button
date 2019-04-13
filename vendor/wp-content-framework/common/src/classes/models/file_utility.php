<?php
/**
 * WP_Framework_Common Classes Models File Utility
 *
 * @version 0.0.43
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Common\Classes\Models;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class File_Utility
 * @package WP_Framework_Common\Classes\Models
 * @method string|false find_folder( string $folder )
 * @method string|false search_for_folder( string $folder, string $base = '.', bool $loop = false )
 * @method bool is_binary( string $text )
 * @method bool chown( string $file, mixed $owner, bool $recursive = false )
 * @method mixed|false get_contents( string $file )
 * @method array|false get_contents_array( string $file )
 * @method bool put_contents( string $file, string $contents, bool | int $mode = false )
 * @method bool|string chmod( string $file, false | int $mode = false, bool $recursive = false )
 * @method bool copy( string $source, string $destination, bool $overwrite = false, false | int $mode = false )
 * @method bool move( string $source, string $destination, bool $overwrite = false )
 * @method bool delete( string $file, bool $recursive = false, bool $type = false )
 * @method bool exists( string $file )
 * @method bool is_file( string $file )
 * @method bool is_dir( string $file )
 * @method bool is_readable( string $file )
 * @method bool is_writable( string $file )
 * @method int|bool atime( string $file )
 * @method int|bool mtime( string $file )
 * @method int|bool size( string $file )
 * @method bool touch( string $file, int $time = 0, int $atime = 0 )
 * @method bool mkdir( string $path, false | int $chmod = false, false | int $chown = false, false | int $chgrp = false )
 * @method bool rmdir( string $path, bool $recursive = false )
 * @method array|bool dirlist( string $path, bool $include_hidden = true, bool $recursive = false )
 */
class File_Utility implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Core\Traits\Hook, \WP_Framework_Common\Traits\Package;

	/**
	 * @var array $_fs_methods
	 */
	private static $_fs_methods = [
		'find_folder',
		'search_for_folder',
		'is_binary',
		'chown',
		'get_contents',
		'get_contents_array',
		'put_contents',
		'chmod',
		'copy',
		'move',
		'delete',
		'exists',
		'is_file',
		'is_dir',
		'is_readable',
		'is_writable',
		'atime',
		'mtime',
		'size',
		'touch',
		'mkdir',
		'rmdir',
		'dirlist',
	];

	/**
	 * @var bool $_fs_initialized
	 */
	private static $_fs_initialized = false;

	/**
	 * @var mixed $_fs_credentials
	 */
	private static $_fs_credentials;

	/**
	 * @var \WP_Filesystem_Base[] $_fs_cache
	 */
	private static $_fs_cache = [];

	/**
	 * @param string $name
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function __call( $name, array $args ) {
		if ( in_array( $name, self::$_fs_methods ) ) {
			return $this->fs()->$name( ...$args );
		}

		\WP_Framework::wp_die( sprintf( 'you cannot access file->%s', $name ), __FILE__, __LINE__ );

		return null;
	}

	/**
	 * @return \WP_Filesystem_Base
	 */
	private function fs() {
		if ( isset( self::$_fs_cache[ $this->app->plugin_name ] ) ) {
			return self::$_fs_cache[ $this->app->plugin_name ];
		}

		if ( ! self::$_fs_initialized ) {
			if ( ! class_exists( "\WP_Filesystem_Base" ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
			}
			if ( ! class_exists( "\WP_Filesystem_Direct" ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
			}

			// ABSPATH . 'wp-admin/includes/file.php' WP_Filesystem
			if ( ! defined( 'FS_CHMOD_DIR' ) ) {
				define( 'FS_CHMOD_DIR', ( fileperms( ABSPATH ) & 0777 | 0755 ) );
			}
			if ( ! defined( 'FS_CHMOD_FILE' ) ) {
				define( 'FS_CHMOD_FILE', ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
			}
			self::$_fs_initialized = true;
		}

		if ( $this->apply_filters( 'use_filesystem_credentials' ) ) {
			return $this->fs_with_credentials();
		}

		self::$_fs_cache[ $this->app->plugin_name ] = $this->fs_direct();

		return self::$_fs_cache[ $this->app->plugin_name ];
	}

	/**
	 * @return \WP_Filesystem_Base
	 */
	private function fs_with_credentials() {
		if ( ! isset( self::$_fs_credentials ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			self::$_fs_credentials = request_filesystem_credentials( '', '', false, false, null );
		}

		if ( \WP_Filesystem( self::$_fs_credentials ) ) {
			global $wp_filesystem;
			if ( $wp_filesystem instanceof \WP_Filesystem_Direct ) {
				self::$_fs_cache[ $this->app->plugin_name ] = $wp_filesystem;
			}

			return $wp_filesystem;
		}

		self::$_fs_cache[ $this->app->plugin_name ] = $this->fs_direct();

		return self::$_fs_cache[ $this->app->plugin_name ];
	}

	/**
	 * setup \WP_Filesystem_Direct
	 */
	private function fs_direct() {
		return new \WP_Filesystem_Direct( false );
	}

	/**
	 * @return int
	 */
	private function get_dir_mode() {
		if ( ! self::$_fs_initialized ) {
			$this->fs();
		}

		return empty( self::$_fs_cache [ $this->app->plugin_name ] ) ? 0777 : FS_CHMOD_DIR;
	}

	/**
	 * @return int
	 */
	private function get_file_mode() {
		if ( ! self::$_fs_initialized ) {
			$this->fs();
		}

		return empty( self::$_fs_cache [ $this->app->plugin_name ] ) ? FS_CHMOD_FILE | 0666 : FS_CHMOD_FILE;
	}

	/**
	 * @param \WP_Framework $app
	 *
	 * @return bool
	 */
	public function delete_upload_dir( \WP_Framework $app ) {
		return $this->delete_dir( $app->define->upload_dir );
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $dir
	 *
	 * @return bool
	 */
	public function delete_plugin_dir( \WP_Framework $app, $dir ) {
		return $this->delete_dir( $app->define->plugin_dir . DS . $dir );
	}

	/**
	 * @param $dir
	 *
	 * @return bool
	 */
	private function delete_dir( $dir ) {
		return $this->delete( $dir, true );
	}

	/**
	 * @param string $path
	 * @param bool|int $chmod
	 * @param bool|int $chown
	 * @param bool|int $chgrp
	 *
	 * @return bool
	 */
	public function mkdir_recursive( $path, $chmod = false, $chown = false, $chgrp = false ) {
		if ( empty( $path ) ) {
			return false;
		}
		if ( $this->exists( $path ) ) {
			return true;
		}

		$path     = str_replace( '\\', '/', $path );
		$segments = explode( '/', $path );
		$dir      = '/';
		foreach ( $segments as $segment ) {
			$dir .= $segment;
			if ( ! $this->is_dir( $dir ) ) {
				if ( $this->is_file( $dir ) ) {
					break;
				}
				if ( ! $this->mkdir( $dir, $chmod || $chown || $chgrp ? $chmod : $this->get_dir_mode(), $chown, $chgrp ) ) {
					return false;
				}
			}
			$dir .= '/';
		}

		return true;
	}

	/**
	 * @param string $file
	 * @param string $contents
	 * @param bool|int $mode
	 * @param bool|int $dir_mode
	 *
	 * @return bool
	 */
	public function put_contents_recursive( $file, $contents, $mode = false, $dir_mode = false ) {
		return $this->mkdir_recursive( dirname( $file ), $dir_mode ? $dir_mode : $this->get_dir_mode() ) && $this->put_contents( $file, $contents, $mode ? $mode : $this->get_file_mode() );
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $path
	 *
	 * @return string
	 */
	private function get_upload_file_path( \WP_Framework $app, $path ) {
		return $app->define->upload_dir . DS . ltrim( str_replace( '/', DS, $path ), DS );
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $path
	 *
	 * @return string
	 */
	private function get_upload_file_link( \WP_Framework $app, $path ) {
		return $app->define->upload_url . '/' . ltrim( str_replace( DS, '/', $path ), '/' );
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $path
	 *
	 * @return bool
	 */
	public function upload_file_exists( \WP_Framework $app, $path ) {
		return $this->exists( $this->get_upload_file_path( $app, $path ) );
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $path
	 * @param mixed $data
	 *
	 * @throws \Exception
	 */
	public function create_upload_file( \WP_Framework $app, $path, $data ) {
		$path = $this->get_upload_file_path( $app, $path );
		if ( false === $this->mkdir_recursive( dirname( $path ), $this->get_dir_mode() ) || false === $this->put_contents( $path, $data, $this->get_file_mode() ) ) {
			throw new \Exception( 'Failed to create file.' );
		}
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $path
	 * @param callable $generator
	 *
	 * @return bool
	 */
	public function create_upload_file_if_not_exists( \WP_Framework $app, $path, $generator ) {
		if ( ! $this->upload_file_exists( $app, $path ) ) {
			if ( isset( $generator ) && is_callable( $generator ) ) {
				try {
					$this->create_upload_file( $app, $path, $generator() );
				} catch ( \Exception $e ) {
					return false;
				}
			} else {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $path
	 *
	 * @return bool
	 */
	public function delete_upload_file( \WP_Framework $app, $path ) {
		return $this->delete( $this->get_upload_file_path( $app, $path ) );
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $path
	 * @param callable|null $generator
	 *
	 * @return bool|string
	 */
	public function get_upload_file_contents( \WP_Framework $app, $path, $generator = null ) {
		if ( $this->create_upload_file_if_not_exists( $app, $path, $generator ) ) {
			return $this->get_contents( $this->get_upload_file_path( $app, $path ) );
		}

		return false;
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $path
	 * @param callable|null $generator
	 *
	 * @return string|false
	 */
	public function get_upload_file_url( \WP_Framework $app, $path, $generator = null ) {
		if ( $this->create_upload_file_if_not_exists( $app, $path, $generator ) ) {
			return $this->get_upload_file_link( $app, $path );
		}

		return false;
	}

	/**
	 * @param string $dir
	 * @param bool $split
	 * @param string $relative
	 * @param array $ignore
	 *
	 * @return array
	 */
	public function scan_dir_namespace_class( $dir, $split = false, $relative = '', array $ignore = [ 'base.php' ] ) {
		$dir  = rtrim( $dir, DS );
		$list = [];
		if ( is_dir( $dir ) ) {
			foreach ( scandir( $dir ) as $file ) {
				if ( $file === '.' || $file === '..' || in_array( $file, $ignore ) ) {
					continue;
				}

				$path = rtrim( $dir, DS ) . DS . $file;
				if ( is_file( $path ) ) {
					if ( $this->app->string->ends_with( $file, '.php' ) ) {
						if ( $split ) {
							$list[] = [ $relative, ucfirst( $this->app->get_page_slug( $file ) ), $path ];
						} else {
							$list[] = $relative . ucfirst( $this->app->get_page_slug( $file ) );
						}
					}
				} elseif ( is_dir( $path ) ) {
					$list = array_merge( $list, $this->scan_dir_namespace_class( $path, $split, $relative . ucfirst( $file ) . '\\', $ignore ) );
				}
			}
		}

		return $list;
	}
}
