<?php
/**
 * WP_Framework
 *
 * @version 0.0.48
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}
define( 'WP_FRAMEWORK_IS_MOCK', false );

/**
 * Class WP_Framework
 * @property bool $is_theme
 * @property string $original_plugin_name
 * @property string $plugin_name
 * @property string $slug_name
 * @property string $plugin_file
 * @property string $plugin_dir
 * @property string $relative_path
 * @property string $package_file
 *
 * @property \WP_Framework_Common\Classes\Models\Define $define
 * @property \WP_Framework_Common\Classes\Models\Config $config
 * @property \WP_Framework_Common\Classes\Models\Setting $setting
 * @property \WP_Framework_Common\Classes\Models\Filter $filter
 * @property \WP_Framework_Common\Classes\Models\Uninstall $uninstall
 * @property \WP_Framework_Common\Classes\Models\Utility $utility
 * @property \WP_Framework_Common\Classes\Models\Array_Utility $array
 * @property \WP_Framework_Common\Classes\Models\String_Utility $string
 * @property \WP_Framework_Common\Classes\Models\File_Utility $file
 * @property \WP_Framework_Common\Classes\Models\Option $option
 * @property \WP_Framework_Common\Classes\Models\User $user
 * @property \WP_Framework_Common\Classes\Models\Input $input
 * @property \WP_Framework_Common\Classes\Models\Deprecated $deprecated
 * @property \WP_Framework_Db\Classes\Models\Db $db
 * @property \WP_Framework_Log\Classes\Models\Log $log
 * @property \WP_Framework_Admin\Classes\Models\Admin $admin
 * @property \WP_Framework_Api\Classes\Models\Api $api
 * @property \WP_Framework_Presenter\Classes\Models\Drawer $drawer
 * @property \WP_Framework_Presenter\Classes\Models\Minify $minify
 * @property \WP_Framework_Mail\Classes\Models\Mail $mail
 * @property \WP_Framework_Test\Classes\Models\Test $test
 * @property \WP_Framework_Cron\Classes\Models\Cron $cron
 * @property \WP_Framework_Custom_Post\Classes\Models\Custom_Post $custom_post
 * @property \WP_Framework_Device\Classes\Models\Device $device
 * @property \WP_Framework_Session\Classes\Models\Session $session
 * @property \WP_Framework_Social\Classes\Models\Social $social
 * @property \WP_Framework_Post\Classes\Models\Post $post
 * @property \WP_Framework_Update\Classes\Models\Update $update
 * @property \WP_Framework_Update_Check\Classes\Models\Update_Check $update_check
 * @property \WP_Framework_Upgrade\Classes\Models\Upgrade $upgrade
 * @property \WP_Framework_Cache\Classes\Models\Cache $cache
 *
 * @method void main_init()
 * @method bool has_initialized()
 * @method array get_mapped_class( string $class )
 * @method mixed get_config( string $name, string | null $key = null, mixed $default = null )
 * @method mixed get_option( string $key, mixed $default = '' )
 * @method mixed get_session( string $key, mixed $default = '' )
 * @method mixed set_session( string $key, mixed $value, int | null $duration = null )
 * @method bool user_can( null | string | false $capability = null )
 * @method void log( mixed $message, mixed $context = null, string $level = '' )
 * @method void add_message( string $message, string $group = '', bool $error = false, bool $escape = true, null | array $override_allowed_html = null )
 * @method string get_page_slug( string $file )
 * @method mixed get_shared_object( string $key, string | null $target = null )
 * @method void set_shared_object( string $key, mixed $object, string | null $target = null )
 * @method bool isset_shared_object( string $key, string | null $target = null )
 * @method void delete_shared_object( string $key, string | null $target = null )
 * @method bool send_mail( string $to, string $subject, string | array $body, string | false $text = false )
 * @method string get_view( \WP_Framework_Core\Interfaces\Package $instance, string $name, array $args = [], bool $echo = false, bool $error = true, bool $remove_nl = false )
 * @method void add_script_view( \WP_Framework_Core\Interfaces\Package $instance, string $name, array $args = [], int $priority = 10 )
 * @method void add_style_view( \WP_Framework_Core\Interfaces\Package $instance, string $name, array $args = [], int $priority = 10 )
 * @method void enqueue_style( \WP_Framework_Core\Interfaces\Package $instance, string $handle, string $file, array $depends = [], string | bool | null $ver = false, string $media = 'all', string $dir = 'css' )
 * @method void enqueue_script( \WP_Framework_Core\Interfaces\Package $instance, string $handle, string $file, array $depends = [], string | bool | null $ver = false, bool $in_footer = true, string $dir = 'js' )
 * @method bool localize_script( \WP_Framework_Core\Interfaces\Package $instance, string $handle, string $name, array $data )
 * @method bool lock_process( string $name, callable $func, int $timeout = 60 )
 */
class WP_Framework {

	/**
	 * @var \WP_Framework[] $_instances
	 */
	private static $_instances = [];

	/**
	 * @var array $_framework_package_versions (package => version)
	 */
	private static $_framework_package_versions = [];

	/**
	 * @var array $_framework_package_plugin_names (package => plugin_name)
	 */
	private static $_framework_package_plugin_names = [];

	/**
	 * @var bool $_packages_loaded
	 */
	private static $_packages_loaded = false;

	/**
	 * @var \WP_Framework\Package_Base[]
	 */
	private static $_packages = [];

	/**
	 * @var array $_framework_cache
	 */
	private static $_framework_cache;

	/**
	 * for debug
	 * @var float $_started
	 */
	private static $_started_at;

	/**
	 * for debug
	 * @var float $_elapsed
	 */
	private static $_elapsed = 0.0;

	/**
	 * @var array $_package_versions (package => version)
	 */
	private $_package_versions;

	/**
	 * @var array $_package_directories (package => directory)
	 */
	private $_package_directories;

	/**
	 * @var \WP_Framework\Package_Base[]
	 */
	private $_available_packages;

	/**
	 * @var string $_framework_root_directory
	 */
	private $_framework_root_directory;

	/**
	 * @var bool $_plugins_loaded
	 */
	private $_plugins_loaded = false;

	/**
	 * @var bool $_framework_initialized
	 */
	private $_framework_initialized = false;

	/**
	 * @var \WP_Framework_Core\Classes\Main $_main
	 */
	private $_main;

	/**
	 * @var bool $_is_uninstall
	 */
	private $_is_uninstall = false;

	/**
	 * @var string $_required_php_version
	 */
	private $_required_php_version;

	/**
	 * @var string $_required_wordpress_version
	 */
	private $_required_wordpress_version;

	/**
	 * @var bool $_not_enough_php_version
	 */
	private $_not_enough_php_version = false;

	/**
	 * @var bool $_not_enough_wordpress_version
	 */
	private $_not_enough_wordpress_version = false;

	/**
	 * @var array $_plugin_data
	 */
	private $_plugin_data;

	/**
	 * @var array $readonly_properties
	 */
	private $_readonly_properties = [
		'is_theme'             => false,
		'original_plugin_name' => '',
		'plugin_name'          => '',
		'slug_name'            => '',
		'plugin_file'          => '',
		'plugin_dir'           => '',
		'relative_path'        => '',
		'package_file'         => '',
	];

	/** @var bool $_is_allowed_access */
	private $_is_allowed_access = false;

	/**
	 * WP_Framework constructor.
	 *
	 * @param string $plugin_name
	 * @param string $plugin_file
	 * @param string|null $slug_name
	 * @param string|null $relative
	 * @param string|null $package
	 */
	private function __construct( $plugin_name, $plugin_file, $slug_name, $relative, $package ) {
		$this->_is_allowed_access   = true;
		$theme_dir                  = str_replace( '/', DS, WP_CONTENT_DIR . DS . 'theme' );
		$relative                   = ! empty( $relative ) ? trim( $relative ) : null;
		$this->is_theme             = preg_match( "#\A{$theme_dir}#", str_replace( '/', DS, $plugin_file ) ) > 0;
		$this->original_plugin_name = $plugin_name;
		$this->plugin_file          = $plugin_file;
		$this->plugin_dir           = dirname( $plugin_file );
		$this->relative_path        = empty( $relative ) ? '' : ( trim( str_replace( '/', DS, $relative ), DS ) . DS );
		$this->package_file         = is_string( $package ) && ! empty( $package ) ? $package : null;
		$this->plugin_name          = strtolower( $this->original_plugin_name );
		$this->slug_name            = ! empty( $slug_name ) ? strtolower( $slug_name ) : $this->plugin_name;
		$this->_is_allowed_access   = false;

		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$this->_plugin_data = $this->is_theme ? wp_get_theme() : get_plugin_data( $this->plugin_file, false, false );

		$this->setup_framework_version();
		$this->setup_actions();
		$this->load_pluggable();
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 * @throws \OutOfRangeException
	 */
	public function __get( $name ) {
		if ( array_key_exists( $name, $this->_readonly_properties ) ) {
			return $this->_readonly_properties[ $name ];
		}

		return $this->get_main()->__get( $name );
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 *
	 * @throws \OutOfRangeException
	 */
	public function __set( $name, $value ) {
		if ( $this->_is_allowed_access && array_key_exists( $name, $this->_readonly_properties ) ) {
			$this->_readonly_properties[ $name ] = $value;
		} else {
			throw new \OutOfRangeException( sprintf( 'you cannot access %s->%s.', static::class, $name ) );
		}
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function __isset( $name ) {
		if ( array_key_exists( $name, $this->_readonly_properties ) ) {
			return ! is_null( $this->_readonly_properties[ $name ] );
		}

		return $this->get_main()->__isset( $name );
	}

	/**
	 * @param string $name
	 * @param array $arguments
	 *
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {
		return $this->get_main()->$name( ...$arguments );
	}

	/**
	 * @param string $name
	 * @param array $arguments
	 */
	public static function __callStatic( $name, $arguments ) {
		if ( preg_match( '#register_uninstall_(.+)\z#', $name, $matches ) ) {
			$plugin_base_name = $matches[1];
			self::uninstall( $plugin_base_name );
		}
	}

	/**
	 * @param string $plugin_name
	 * @param string|null $plugin_file
	 * @param string|null $slug_name
	 * @param string|null $relative
	 * @param string|null $package
	 *
	 * @return WP_Framework
	 */
	public static function get_instance( $plugin_name, $plugin_file = null, $slug_name = null, $relative = null, $package = null ) {
		if ( ! isset( self::$_instances[ $plugin_name ] ) ) {
			if ( empty( $plugin_file ) ) {
				self::wp_die( '$plugin_file is required.', __FILE__, __LINE__ );
			}
			self::report_performance();
			self::run( function () use ( $plugin_name, $plugin_file, $slug_name, $relative, $package ) {
				$instances                        = new static( $plugin_name, $plugin_file, $slug_name, $relative, $package );
				self::$_instances[ $plugin_name ] = $instances;
				self::update_framework_packages( $instances );
			} );
		}

		return self::$_instances[ $plugin_name ];
	}

	/**
	 * for debug
	 */
	private static function report_performance() {
		if ( ! isset( self::$_started_at ) ) {
			self::$_started_at = false;
			if ( defined( 'WP_FRAMEWORK_PERFORMANCE_REPORT' ) && ! defined( 'PHPUNIT_COMPOSER_INSTALL' ) ) {
				self::$_started_at = microtime( true ) * 1000;

				add_action( 'shutdown', function () {
					if ( ! did_action( 'wp_loaded' ) ) {
						return;
					}
					if ( defined( 'WP_UNINSTALL_PLUGIN' ) && WP_UNINSTALL_PLUGIN ) {
						return;
					}
					if ( defined( 'WP_FRAMEWORK_PERFORMANCE_REPORT_EXCLUDE_AJAX' ) ) {
						if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
							return;
						}
						if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
							return;
						}
					}
					if ( defined( 'WP_FRAMEWORK_PERFORMANCE_REPORT_EXCLUDE_CRON' ) && defined( 'DOING_CRON' ) && DOING_CRON ) {
						return;
					}
					if ( defined( 'WP_FRAMEWORK_SUSPEND_PERFORMANCE_REPORT' ) ) {
						return;
					}

					error_log( '' );
					error_log( '' );
					$total = 0;
					foreach ( self::$_instances as $instance ) {
						if ( ! $instance->framework_initialized() ) {
							continue;
						}
						$total += $instance->filter->get_elapsed();
					}
					$total  += self::$_elapsed;
					$global = microtime( true ) * 1000 - self::$_started_at;
					error_log( sprintf( 'shutdown framework: %12.8fms (%12.8fms) / %12.8fms (%.2f%%)', $total, self::$_elapsed, $global, ( $total / $global ) * 100 ) );

					foreach ( self::$_instances as $instance ) {
						if ( ! $instance->framework_initialized() ) {
							continue;
						}
						$elapsed = $instance->filter->get_elapsed();
						error_log( sprintf( '  %12.8fms (%5.2f%% / %5.2f%%) : %s', $elapsed, ( $elapsed / $global ) * 100, ( $elapsed / $total ) * 100, $instance->plugin_name ) );
						if ( defined( 'WP_FRAMEWORK_DETAIL_REPORT' ) ) {
							foreach ( $instance->filter->get_elapsed_details() as $detail ) {
								error_log( '     - ' . $detail );
							}
						}
						if ( $instance->is_valid_package( 'db' ) ) {
							$instance->db->performance_report();
						}
					}
				}, 1 );
			}
		}
	}

	/**
	 * @return \WP_Framework\Package_Base[]
	 */
	public function get_packages() {
		if ( ! isset( $this->_available_packages ) ) {
			$packages                   = $this->get_package_names();
			$this->_package_directories = [];
			foreach ( self::$_packages as $package => $instance ) {
				if ( in_array( $package, $packages ) ) {
					$this->_available_packages[ $package ] = $instance;
				}
			}
		}

		return $this->_available_packages;
	}

	/**
	 * @return string[]
	 */
	public function get_package_names() {
		if ( ! $this->_framework_initialized ) {
			self::wp_die( [ 'framework is not ready.', '<pre>' . wp_debug_backtrace_summary() . '</pre>' ], __FILE__, __LINE__ );
		}

		return array_keys( $this->_package_versions );
	}

	/**
	 * @return string[]
	 */
	public function get_package_directories() {
		if ( ! isset( $this->_package_directories ) ) {
			$this->_package_directories = [];
			foreach ( $this->get_packages() as $package => $instance ) {
				$this->_package_directories[ $package ] = $instance->get_dir();
			}
		}

		return $this->_package_directories;
	}

	/**
	 * @param string $package
	 *
	 * @return bool
	 */
	public function is_valid_package( $package ) {
		return isset( $this->_available_packages[ $package ] );
	}

	/**
	 * @param string $package
	 *
	 * @return \WP_Framework\Package_Base
	 */
	public function get_package_instance( $package = 'core' ) {
		if ( ! isset( $this->_available_packages[ $package ] ) ) {
			self::wp_die( [ 'package is not available.', 'package name: ' . $package ], __FILE__, __LINE__ );
		}

		return $this->_available_packages[ $package ];
	}

	/**
	 * @param string $package
	 *
	 * @return string
	 */
	public function get_package_directory( $package = 'core' ) {
		$dirs = $this->get_package_directories();
		if ( ! isset( $dirs[ $package ] ) ) {
			self::wp_die( [ 'package is not available.', 'package name: ' . $package ], __FILE__, __LINE__ );
		}

		return $dirs[ $package ];
	}

	/**
	 * @param string $package
	 *
	 * @return string
	 */
	public function get_package_version( $package = 'core' ) {
		if ( ! $this->_framework_initialized ) {
			self::wp_die( [ 'framework is not ready.', '<pre>' . wp_debug_backtrace_summary() . '</pre>' ], __FILE__, __LINE__ );
		}
		if ( ! isset( $this->_package_versions[ $package ] ) ) {
			self::wp_die( [ 'package is not available.', 'package name: ' . $package ], __FILE__, __LINE__ );
		}

		return self::$_framework_package_versions[ $package ];
	}

	/**
	 * @return string
	 */
	public function get_framework_version() {
		return $this->get_package_version();
	}

	/**
	 * @param string|null $key
	 *
	 * @return array|string
	 */
	public function get_plugin_data( $key = null ) {
		return empty( $key ) ? $this->_plugin_data : $this->_plugin_data[ $key ];
	}

	/**
	 * @return string
	 */
	public function get_plugin_version() {
		return $this->get_plugin_data( 'Version' );
	}

	/**
	 * @return string
	 */
	public function get_plugin_uri() {
		return $this->get_plugin_data( $this->is_theme ? 'ThemeURI' : 'PluginURI' );
	}

	/**
	 * @return bool
	 */
	public function is_uninstall() {
		return $this->_is_uninstall;
	}

	/**
	 * @return bool
	 */
	public function is_enough_version() {
		return ! $this->_not_enough_php_version && ! $this->_not_enough_wordpress_version;
	}

	/**
	 * @param string|array $message
	 * @param string $file
	 * @param int $line
	 * @param string $title
	 * @param bool $output_file_info
	 */
	public static function wp_die( $message, $file, $line, $title = '', $output_file_info = true ) {
		! is_array( $message ) and $message = [ '[wp content framework]', $message ];
		if ( $output_file_info ) {
			$message[] = 'File: ' . $file;
			$message[] = 'Line: ' . $line;
		}

		if ( is_admin() || ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY ) ) {
			$message = '<ul><li>' . implode( '</li><li>', $message ) . '</li></ul>';
			wp_die( $message, $title );
		} else {
			if ( $title ) {
				error_log( $title );
			}
			error_log( print_r( $message, true ) );
		}
		exit;
	}

	/**
	 * @return WP_Framework[]
	 */
	public function get_instances() {
		if ( ! $this->_framework_initialized ) {
			self::wp_die( [ 'framework is not ready.', '<pre>' . wp_debug_backtrace_summary() . '</pre>' ], __FILE__, __LINE__ );
		}

		return array_filter( self::$_instances, function ( $instance ) {
			/** @var \WP_Framework $instance */
			return $instance->_framework_initialized;
		} );
	}

	/**
	 * @return \WP_Framework_Core\Classes\Main|\WP_Framework_Core\Interfaces\Singleton
	 */
	private function get_main() {
		if ( ! $this->_framework_initialized ) {
			self::wp_die( [ 'framework is not ready.', '<pre>' . wp_debug_backtrace_summary() . '</pre>' ], __FILE__, __LINE__ );
		}
		if ( ! isset( $this->_main ) ) {
			if ( ! class_exists( '\WP_Framework_Core\Classes\Main' ) ) {
				$path = $this->get_package_directory() . DS . 'src' . DS . 'classes' . DS . 'main.php';
				/** @noinspection PhpIncludeInspection */
				require_once $path;
			}
			$this->_main = \WP_Framework_Core\Classes\Main::get_instance( $this );
		}

		return $this->_main;
	}

	/**
	 * @return array
	 */
	private function get_plugin_cache() {
		if ( ! defined( 'WP_FRAMEWORK_FORCE_CACHE' ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			return [ false, null, null ];
		}
		! isset( self::$_framework_cache ) and self::$_framework_cache = get_option( WP_FRAMEWORK_VENDOR_NAME );
		if ( ! is_array( self::$_framework_cache ) || ! isset( self::$_framework_cache[ $this->plugin_name ] ) ) {
			return [ false, null, null ];
		}
		$cache = self::$_framework_cache[ $this->plugin_name ];
		if ( ! is_array( $cache ) || count( $cache ) !== 2 || $cache[0] !== $this->get_plugin_version() || ! is_array( $cache[1] ) || count( $cache[1] ) !== 2 ) {
			return [ false, null, null ];
		}

		return [ true, $cache[1][0], $cache[1][1] ];
	}

	/**
	 * @return bool
	 */
	private function set_plugin_cache() {
		! is_array( self::$_framework_cache ) and self::$_framework_cache = [];
		self::$_framework_cache[ $this->plugin_name ] = [
			$this->get_plugin_version(),
			[
				$this->_framework_root_directory,
				$this->_package_versions,
			],
		];

		return update_option( WP_FRAMEWORK_VENDOR_NAME, self::$_framework_cache );
	}

	/**
	 * @return bool
	 */
	private function delete_plugin_cache() {
		return delete_option( WP_FRAMEWORK_VENDOR_NAME );
	}

	/**
	 * setup framework version
	 */
	private function setup_framework_version() {
		list( $is_valid, $root_directory, $versions ) = $this->get_plugin_cache();
		if ( $is_valid ) {
			$this->_framework_root_directory = $root_directory;
			$this->_package_versions         = $versions;
		} else {
			$vendor_root = $this->plugin_dir . DS . $this->relative_path . 'vendor';
			$installed   = $vendor_root . DS . 'composer' . DS . 'installed.json';
			if ( ! file_exists( $installed ) || ! is_readable( $installed ) ) {
				self::wp_die( 'installed.json not found.', __FILE__, __LINE__ );
			}
			$json = json_decode( file_get_contents( $installed ), true );
			if ( empty( $json ) ) {
				self::wp_die( 'installed.json is invalid.', __FILE__, __LINE__ );
			}

			$additional = false;
			if ( ! empty( $this->package_file ) ) {
				$additional_package = $this->plugin_dir . DS . $this->package_file;
				if ( file_exists( $additional_package ) && is_readable( $additional_package ) ) {
					$additional = @json_decode( file_get_contents( $additional_package ), true );
					if ( ! is_array( $additional ) || empty( $additional ) ) {
						$additional = false;
					}
				}
			}

			$versions = [];
			foreach ( $json as $package ) {
				$name     = $package['name'];
				$exploded = explode( '/', $name );

				if ( count( $exploded ) === 2 ) {
					if ( WP_FRAMEWORK_VENDOR_NAME === $exploded[0] ) {
						$package_name = strtolower( $exploded[1] );
					} elseif ( is_array( $additional ) && in_array( $name, $additional ) ) {
						$package_name = strtolower( $name );
					} else {
						continue;
					}
				} else {
					continue;
				}

				$version                   = $package['version_normalized'];
				$versions[ $package_name ] = $version;
			}
			if ( ! isset( $versions['core'] ) ) {
				self::wp_die( 'installed.json is invalid.', __FILE__, __LINE__ );
			}
			$this->_framework_root_directory = $vendor_root . DS . WP_FRAMEWORK_VENDOR_NAME;
			$this->_package_versions         = $versions;
			$this->set_plugin_cache();
		}
	}

	/**
	 * @param \WP_Framework $app
	 */
	private static function update_framework_packages( \WP_Framework $app ) {
		foreach ( $app->_package_versions as $package => $version ) {
			if ( ! isset( self::$_framework_package_versions[ $package ] ) || version_compare( self::$_framework_package_versions[ $package ], $version, '<' ) ) {
				self::$_framework_package_versions[ $package ]     = $version;
				self::$_framework_package_plugin_names[ $package ] = $app->original_plugin_name;
			}
		}
	}

	/**
	 * initialize framework
	 */
	private static function load_packages() {
		if ( ! class_exists( '\WP_Framework\Package_Base' ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once self::$_instances[ self::$_framework_package_plugin_names['core'] ]->_framework_root_directory . DS . 'core' . DS . 'package_base.php';
		}
		$priority = [];
		$packages = [];
		foreach ( self::$_framework_package_plugin_names as $key => $plugin_name ) {
			$app = self::$_instances[ $plugin_name ];
			if ( strpos( $key, '/' ) !== false ) {
				$directory = dirname( $app->_framework_root_directory ) . DS . $key;
				$exploded  = explode( '/', $key );
				$namespace = ucwords( str_replace( '-', '_', $exploded[0] ), '_' );
				$package   = $exploded[1];
			} else {
				$package   = $key;
				$directory = $app->_framework_root_directory . DS . $package;
				$namespace = 'WP_Framework';
			}

			$class = "\\{$namespace}\Package_" . ucwords( $package, '_' );
			if ( ! class_exists( $class ) ) {
				$path = $directory . DS . 'package_' . $package . '.php';
				if ( ! is_readable( $path ) ) {
					self::wp_die( [ 'invalid package', 'package name: ' . $key ], __FILE__, __LINE__ );
				}
				/** @noinspection PhpIncludeInspection */
				require_once $path;

				if ( ! class_exists( $class ) ) {
					self::wp_die( [ 'invalid package', 'package name: ' . $key, 'class name: ' . $class ], __FILE__, __LINE__ );
				}
			}

			$version = self::$_framework_package_versions[ $key ];
			/** @var \WP_Framework\Package_Base $class */
			$packages[ $key ] = $class::get_instance( $app, $key, $directory, $version );
			$priority[ $key ] = $packages[ $key ]->get_priority();
		}
		array_multisort( $priority, $packages );
		self::$_packages = [];
		foreach ( $packages as $package ) {
			/** @var \WP_Framework\Package_Base $package */
			self::$_packages[ $package->get_package() ] = $package;
		}
	}

	/**
	 * for debug
	 *
	 * @param callable $callback
	 */
	private static function run( $callback ) {
		$start = microtime( true ) * 1000;
		$callback();
		$elapsed          = microtime( true ) * 1000 - $start;
		static::$_elapsed += $elapsed;
	}

	/**
	 * setup actions
	 */
	private function setup_actions() {
		add_action( 'after_setup_theme', function () {
			self::run( function () {
				$this->initialize_framework();
			} );
		} );

		if ( $this->is_theme ) {
			add_action( 'switch_theme', function () {
				$this->filter->do_action( 'app_deactivated', $this );
				$this->delete_plugin_cache();
			} );
		} else {
			add_action( 'plugins_loaded', function () {
				$this->plugins_loaded();
			} );
			add_action( 'deactivated_plugin', function ( $plugin ) {
				if ( $this->define->plugin_base_name === $plugin ) {
					$this->filter->do_action( 'app_deactivated', $this );
					$this->delete_plugin_cache();
				}
			} );
		}

		add_action( 'init', function () {
			if ( $this->is_enough_version() ) {
				$this->main_init();
			}
		}, 1 );
	}

	/**
	 * plugin loaded
	 */
	private function plugins_loaded() {
		if ( $this->_plugins_loaded || $this->is_theme ) {
			return;
		}
		$this->_plugins_loaded = true;
		$this->load_functions();
	}

	/**
	 * @param bool $load_packages
	 */
	private function initialize_framework( $load_packages = false ) {
		$this->plugins_loaded();
		if ( $this->_framework_initialized ) {
			return;
		}
		$this->_framework_initialized = true;

		if ( ! self::$_packages_loaded || $load_packages ) {
			self::$_packages_loaded = true;
			self::load_packages();
		}

		spl_autoload_register( function ( $class ) {
			return $this->get_main()->load_class( $class );
		} );

		$this->check_required_version();
		$this->load_setup();
	}

	/**
	 * @return bool
	 */
	public function framework_initialized() {
		return $this->_framework_initialized;
	}

	/**
	 * @param string $name
	 */
	private function load_plugin_file( $name ) {
		$path = $this->plugin_dir . DS . $name . '.php';
		if ( is_readable( $path ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once $path;
		}
	}

	/**
	 * load pluggable file
	 */
	private function load_pluggable() {
		if ( $this->is_theme ) {
			return;
		}
		$this->load_plugin_file( 'pluggable' );
	}

	/**
	 * load functions file
	 */
	private function load_functions() {
		if ( $this->is_theme ) {
			return;
		}
		$this->load_plugin_file( 'functions' );
	}

	/**
	 * load setup file
	 */
	private function load_setup() {
		if ( ! $this->is_enough_version() ) {
			return;
		}
		$this->load_plugin_file( 'setup' );
	}

	/**
	 * check required version
	 */
	private function check_required_version() {
		global $wp_version;
		$this->_required_php_version         = $this->get_config( 'config', 'required_php_version' );
		$this->_required_wordpress_version   = $this->get_config( 'config', 'required_wordpress_version' );
		$this->_not_enough_php_version       = version_compare( phpversion(), $this->_required_php_version, '<' );
		$this->_not_enough_wordpress_version = ! empty( $wp_version ) && version_compare( $wp_version, $this->_required_wordpress_version, '<' );
		if ( ! $this->is_enough_version() ) {
			$this->set_unsupported();
		}
	}

	/**
	 * set unsupported
	 */
	private function set_unsupported() {
		add_action( 'admin_notices', function () {
			?>
            <div class="notice error notice-error">
				<?php if ( $this->_not_enough_php_version ): ?>
                    <p><?php echo $this->get_unsupported_php_version_message(); ?></p>
				<?php endif; ?>
				<?php if ( $this->_not_enough_wordpress_version ): ?>
                    <p><?php echo $this->get_unsupported_wp_version_message(); ?></p>
				<?php endif; ?>
            </div>
			<?php
		} );
	}

	/**
	 * @return string
	 */
	private function get_unsupported_php_version_message() {
		$messages   = [];
		$messages[] = sprintf( $this->filter->translate( 'Your PHP version is %s.' ), phpversion() );
		$messages[] = $this->filter->translate( 'Please update your PHP.' );
		$messages[] = sprintf( $this->filter->translate( '<strong>%s</strong> requires PHP version %s or above.' ), $this->filter->translate( $this->original_plugin_name ), $this->_required_php_version );

		return implode( '<br>', $messages );
	}

	/**
	 * @return string
	 */
	private function get_unsupported_wp_version_message() {
		global $wp_version;
		$messages   = [];
		$messages[] = sprintf( $this->filter->translate( 'Your WordPress version is %s.' ), $wp_version );
		$messages[] = $this->filter->translate( 'Please update your WordPress.' );
		$messages[] = sprintf( $this->filter->translate( '<strong>%s</strong> requires WordPress version %s or above.' ), $this->filter->translate( $this->original_plugin_name ), $this->_required_wordpress_version );

		return implode( '<br>', $messages );
	}

	/**
	 * @param string $plugin_base_name
	 */
	private static function uninstall( $plugin_base_name ) {
		$app = self::find_plugin( $plugin_base_name );
		if ( ! isset( $app ) ) {
			return;
		}

		$app->_is_uninstall = true;
		if ( $app->is_enough_version() ) {
			$app->main_init();
			$app->uninstall->uninstall();
		}
	}

	/**
	 * @param string $plugin_base_name
	 *
	 * @return \WP_Framework|null
	 */
	private static function find_plugin( $plugin_base_name ) {
		/** @var \WP_Framework $instance */
		foreach ( self::$_instances as $plugin_name => $instance ) {
			if ( $instance->is_theme ) {
				continue;
			}
			$instance->initialize_framework( true );
			if ( $instance->define->plugin_base_name === $plugin_base_name ) {
				return $instance;
			}
		}

		return null;
	}
}

if ( ! defined( 'PHPUNIT_COMPOSER_INSTALL' ) ) {
	require_once __DIR__ . DS . 'classes' . DS . 'wp-rest-request.php';
	require_once __DIR__ . DS . 'classes' . DS . 'wp-rest-response.php';
}
