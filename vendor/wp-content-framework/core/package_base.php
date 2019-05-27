<?php
/**
 * WP_Framework Package Base
 *
 * @version 0.0.54
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework;

use WP_Framework;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Package_Base
 * @package WP_Framework
 */
abstract class Package_Base {

	/**
	 * @var Package_Base[] $_instances
	 */
	private static $_instances = [];

	/**
	 * @var array
	 */
	private $_caches = [];

	/**
	 * @var WP_Framework $_app
	 */
	private $_app;

	/**
	 * @var array $_configs
	 */
	private $_configs = [];

	/**
	 * @var string $_version
	 */
	private $_version;

	/**
	 * @var string $_package
	 */
	protected $_package;

	/**
	 * @var string $_dir
	 */
	protected $_dir;

	/**
	 * @var string $_url
	 */
	protected $_url;

	/**
	 * @var string $_namespace
	 */
	protected $_namespace;

	/**
	 * @param WP_Framework $app
	 * @param string $package
	 * @param string $dir
	 * @param string $version
	 *
	 * @return Package_Base
	 */
	public static function get_instance( WP_Framework $app, $package, $dir, $version ) {
		if ( ! isset( self::$_instances[ $package ] ) ) {
			self::$_instances[ $package ] = new static( $app, $package, $dir, $version );
		} else {
			self::$_instances[ $package ]->setup( $app, $package, $dir, $version );
		}

		return self::$_instances[ $package ];
	}

	/**
	 * Main constructor.
	 *
	 * @param WP_Framework $app
	 * @param string $package
	 * @param string $dir
	 * @param string $version
	 */
	private function __construct( $app, $package, $dir, $version ) {
		$this->setup( $app, $package, $dir, $version );
		$this->initialize();
	}

	/**
	 * @param WP_Framework $app
	 * @param string $package
	 * @param string $dir
	 * @param string $version
	 */
	private function setup( $app, $package, $dir, $version ) {
		$this->_app     = $app;
		$this->_package = $package;
		$this->_dir     = $dir;
		$this->_version = $version;

		$this->_namespace = null;
		$this->_configs   = [];
		$this->_url       = null;
	}

	/**
	 * @return string
	 */
	public function get_package() {
		return $this->_package;
	}

	/**
	 * @return string
	 */
	public function get_namespace() {
		! isset( $this->_namespace ) and $this->_namespace = 'WP_Framework_' . ucwords( $this->_package, '_' );

		return $this->_namespace;
	}

	/**
	 * initialize
	 */
	protected function initialize() {

	}

	/**
	 * @return int
	 */
	public abstract function get_priority();

	/**
	 * @return array
	 */
	public function get_configs() {
		return [];
	}

	/**
	 * @param $name
	 * @param WP_Framework $app
	 *
	 * @return array
	 */
	public function get_config( $name, $app = null ) {
		$key = $name . ( $app ? '/' . $app->plugin_name : '' );
		if ( ! isset( $this->_caches['config'][ $key ] ) ) {
			if ( ! isset( $this->_configs[ $name ] ) ) {
				if ( ! in_array( $name, $this->get_configs() ) ) {
					$this->_configs[ $name ] = [];
				} else {
					$this->_configs[ $name ] = $this->load_package_config( $name );
				}
			}

			$config = $this->_configs[ $name ];
			if ( $app ) {
				$config = array_replace_recursive( $config, $this->load_plugin_config( $name, $app ) );
			}
			$this->_caches['config'][ $key ] = $config;
		}

		return $this->_caches['config'][ $key ];
	}

	/**
	 * @param string $class
	 *
	 * @return bool
	 */
	public function load_class( $class ) {
		$class = $this->trim_namespace( $class );
		if ( $class ) {
			$class = strtolower( $class );
			$path  = $this->get_dir() . DS . 'src' . DS . str_replace( '\\', DS, $class ) . '.php';
			if ( is_readable( $path ) ) {
				/** @noinspection PhpIncludeInspection */
				require_once $path;

				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $namespace
	 *
	 * @return array
	 */
	public function namespace_to_dir( $namespace ) {
		if ( ! isset( $this->_caches['namespace_to_dir'][ $namespace ] ) ) {
			$relative = $this->trim_namespace( $namespace );
			if ( $relative ) {
				$this->_caches['namespace_to_dir'][ $namespace ] = [ $this->get_dir() . DS . 'src', $relative ];
			} else {
				$this->_caches['namespace_to_dir'][ $namespace ] = [ null, null ];
			}
		}

		return $this->_caches['namespace_to_dir'][ $namespace ];
	}

	/**
	 * @param string $string
	 *
	 * @return string|false
	 */
	protected function trim_namespace( $string ) {
		$namespace = $this->get_namespace();
		$string    = ltrim( $string, '\\' );
		if ( preg_match( "#\A{$namespace}\\\\(.+)\z#", $string, $matches ) ) {
			return $matches[1];
		}

		return false;
	}

	/**
	 * @return string
	 */
	public function get_dir() {
		return $this->_dir;
	}

	/**
	 * @return string
	 */
	public function get_version() {
		return $this->_version;
	}

	/**
	 * @return string
	 */
	public function get_url() {
		if ( ! isset( $this->_url ) ) {
			$url        = $this->_app->is_theme ? get_template_directory_uri() : plugins_url( '', $this->_app->plugin_file );
			$relative   = str_replace( DS, '/', $this->_app->relative_path );
			$vendor     = WP_FRAMEWORK_VENDOR_NAME;
			$this->_url = "{$url}/{$relative}vendor/{$vendor}/{$this->_package}";
		}

		return $this->_url;
	}

	/**
	 * @return bool
	 */
	protected function is_valid_assets() {
		return false;
	}

	/**
	 * @return bool
	 */
	protected function is_valid_view() {
		return false;
	}

	/**
	 * @return bool
	 */
	protected function is_valid_translate() {
		return false;
	}

	/**
	 * @return bool
	 */
	protected function is_valid_admin() {
		return false;
	}

	/**
	 * @return bool
	 */
	protected function is_valid_api() {
		return false;
	}

	/**
	 * @return bool
	 */
	protected function is_valid_cron() {
		return false;
	}

	/**
	 * @param string $name
	 *
	 * @return array
	 */
	protected function load_package_config( $name ) {
		$package_config = $this->load_config_file( $this->get_dir() . DS . 'configs', $name );

		return apply_filters( 'wp_framework/load_package_config', $package_config, $name, $package_config );
	}

	/**
	 * @param string $name
	 * @param WP_Framework $app
	 *
	 * @return array
	 */
	protected function load_plugin_config( $name, $app ) {
		$plugin_config = $this->load_config_file( $app->plugin_dir . DS . 'configs' . DS . $name, $this->get_package() );

		return apply_filters( 'wp_framework/load_plugin_config', $plugin_config, $name, $plugin_config, $app );
	}

	/**
	 * @param string $dir
	 * @param string $name
	 *
	 * @return array
	 */
	protected function load_config_file( $dir, $name ) {
		$path = rtrim( $dir, DS ) . DS . $name . '.php';
		if ( ! file_exists( $path ) ) {
			return [];
		}
		/** @noinspection PhpIncludeInspection */
		$config = include $path;
		if ( ! is_array( $config ) ) {
			$config = [];
		}

		return $config;
	}

	/**
	 * @param bool $allow_multiple
	 *
	 * @return array
	 */
	public function get_assets_settings( $allow_multiple = false ) {
		return $this->get_settings_common( 'view', $allow_multiple ? 'assets/m' : 'assets/s', function () {
			return [ $this->get_assets_dir() => $this->get_assets_url() ];
		}, 'get_assets_settings', 'is_valid_assets', function ( $default ) use ( $allow_multiple ) {
			if ( $allow_multiple ) {
				$settings                            = $default;
				$settings[ $this->get_assets_dir() ] = $this->get_assets_url();
			} else {
				$settings                            = [];
				$settings[ $this->get_assets_dir() ] = $this->get_assets_url();
				foreach ( $default as $k => $v ) {
					$settings[ $k ] = $v;
				}
			}

			return $settings;
		} );
	}

	/**
	 * @return array
	 */
	public function get_views_dirs() {
		return $this->get_settings_common( 'view', 'views', function () {
			return [ $this->get_views_dir() ];
		}, 'get_views_dirs', 'is_valid_view', function ( $default ) {
			$dirs   = [];
			$dirs[] = $this->get_views_dir();
			foreach ( $default as $dir ) {
				$dirs[] = $dir;
			}

			return $dirs;
		} );
	}

	/**
	 * @return array
	 */
	public function get_translate_settings() {
		return $this->get_settings_common( 'common', 'translate', function () {
			return [ $this->get_textdomain() => $this->get_language_dir() ];
		}, 'get_translate_settings', 'is_valid_translate', function ( $default ) {
			$settings                            = [];
			$settings[ $this->get_textdomain() ] = $this->get_language_dir();
			foreach ( $default as $k => $v ) {
				$settings[ $k ] = $v;
			}

			return $settings;
		} );
	}

	/**
	 * @return array
	 */
	public function get_admin_namespaces() {
		return $this->get_settings_common( '', 'admin_namespace', function () {
			return [ $this->get_admin_namespace() ];
		}, 'get_admin_namespaces', 'is_valid_admin', function ( $default ) {
			$namespaces   = [];
			$namespaces[] = $this->get_admin_namespace();
			foreach ( $default as $namespace ) {
				$namespaces[] = $namespace;
			}

			return $namespaces;
		} );
	}

	/**
	 * @return array
	 */
	public function get_api_namespaces() {
		return $this->get_settings_common( '', 'api_namespace', function () {
			return [ $this->get_api_namespace() ];
		}, 'get_api_namespaces', 'is_valid_api', function ( $default ) {
			$namespaces   = [];
			$namespaces[] = $this->get_api_namespace();
			foreach ( $default as $dir ) {
				$namespaces[] = $dir;
			}

			return $namespaces;
		} );
	}

	/**
	 * @return array
	 */
	public function get_cron_namespaces() {
		return $this->get_settings_common( '', 'cron_namespace', function () {
			return [ $this->get_cron_namespace() ];
		}, 'get_cron_namespaces', 'is_valid_cron', function ( $default ) {
			$namespaces   = [];
			$namespaces[] = $this->get_cron_namespace();
			foreach ( $default as $dir ) {
				$namespaces[] = $dir;
			}

			return $namespaces;
		} );
	}

	/**
	 * @param string $default_package
	 * @param string $cache_key
	 * @param callable $get_default
	 * @param string $called_method
	 * @param string $validity_check_method
	 * @param callable $merge_settings
	 *
	 * @return array
	 */
	private function get_settings_common( $default_package, $cache_key, $get_default, $called_method, $validity_check_method, $merge_settings ) {
		if ( ! isset( $this->_caches[ $cache_key ] ) ) {
			if ( $default_package === $this->_package ) {
				$this->_caches[ $cache_key ] = $get_default();
			} else {
				if ( $this->_app->is_valid_package( $default_package ) ) {
					$default = $this->_app->get_package_instance( $default_package )->$called_method();
				} else {
					$default = [];
				}
				if ( ! $this->$validity_check_method() ) {
					$this->_caches[ $cache_key ] = $default;
				} else {
					$this->_caches[ $cache_key ] = $merge_settings( $default );
				}
			}
		}

		return $this->_caches[ $cache_key ];
	}

	/**
	 * @return string
	 */
	protected function get_textdomain() {
		return 'wp_framework-' . $this->_package;
	}

	/**
	 * @return string
	 */
	protected function get_assets_dir() {
		return $this->get_dir() . DS . 'assets';
	}

	/**
	 * @return string
	 */
	protected function get_assets_url() {
		return $this->get_url() . '/assets';
	}

	/**
	 * @return string
	 */
	protected function get_views_dir() {
		return $this->get_dir() . DS . 'src' . DS . 'views';
	}

	/**
	 * @return string
	 */
	protected function get_language_dir() {
		return $this->get_dir() . DS . 'languages';
	}

	/**
	 * @return string
	 */
	protected function get_admin_namespace() {
		return $this->get_namespace() . '\\Classes\\Controllers\\Admin\\';
	}

	/**
	 * @return string
	 */
	protected function get_api_namespace() {
		return $this->get_namespace() . '\\Classes\\Controllers\\Api\\';
	}

	/**
	 * @return string
	 */
	protected function get_cron_namespace() {
		return $this->get_namespace() . '\\Classes\\Crons\\';
	}
}
