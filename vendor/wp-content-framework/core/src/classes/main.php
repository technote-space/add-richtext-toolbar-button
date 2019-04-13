<?php
/**
 * WP_Framework_Core Classes Main
 *
 * @version 0.0.53
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Core\Classes;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Main
 * @package WP_Framework_Core\Classes
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
 * @property \WP_Framework_Common\Classes\Models\System $system
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
 */
class Main {

	/**
	 * @var Main[] $_instances
	 */
	private static $_instances = [];

	/**
	 * @var array $_shared_object
	 */
	private static $_shared_object = [];

	/**
	 * @var \WP_Framework $app
	 */
	protected $app;

	/**
	 * @var bool $_initialized
	 */
	private $_initialized = false;

	/**
	 * @var array $_properties
	 */
	private $_properties;

	/**
	 * @var array $_class_map
	 */
	private $_class_map;

	/**
	 * @var array $_class_target_package
	 */
	private $_class_target_package;

	/**
	 * @var array $_namespace_target_package
	 */
	private $_namespace_target_package;

	/**
	 * @var \WP_Framework|null[] $_alternative_instances
	 */
	private $_alternative_instances;

	/**
	 * @var array $_property_instances
	 */
	private $_property_instances = [];

	/**
	 * @param \WP_Framework $app
	 *
	 * @return Main
	 */
	public static function get_instance( \WP_Framework $app ) {
		! isset( self::$_instances[ $app->plugin_name ] ) and self::$_instances[ $app->plugin_name ] = new self( $app );

		return self::$_instances[ $app->plugin_name ];
	}

	/**
	 * Main constructor.
	 *
	 * @param \WP_Framework $app
	 */
	private function __construct( \WP_Framework $app ) {
		$this->app = $app;
		$this->initialize();
	}

	/**
	 * @param string $name
	 *
	 * @return \WP_Framework_Core\Interfaces\Singleton
	 * @throws \OutOfRangeException
	 */
	public function __get( $name ) {
		return $this->get( $name );
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function __isset( $name ) {
		return array_key_exists( $name, $this->_properties );
	}

	/**
	 * initialize
	 */
	protected function initialize() {
		$this->_properties               = [];
		$this->_class_target_package     = [];
		$this->_namespace_target_package = [];
		$this->_alternative_instances    = [];
		$this->_class_map                = [];
		foreach ( $this->app->get_packages() as $package ) {
			$map = $package->get_config( 'map', $this->app );
			foreach ( $map as $name => $class ) {
				if ( is_array( $class ) ) {
					foreach ( $class as $k => $v ) {
						$class                                 = ltrim( $v, '\\' );
						$this->_properties[ $k ]               = $class;
						$this->_class_target_package[ $class ] = $name;
						if ( ! $this->app->is_valid_package( $name ) ) {
							$this->_alternative_instances[ $class ] = $this->get_alternative_instance( $name );
						}
					}
				} else {
					$class                                 = ltrim( $class, '\\' );
					$this->_properties[ $name ]            = $class;
					$this->_class_target_package[ $class ] = $package->get_package();
				}
			}
			$map = $package->get_config( 'class_map', $this->app );
			foreach ( $map as $class_map ) {
				foreach ( $class_map as $from => $to ) {
					$from                               = ltrim( $from, '\\' );
					$to                                 = ltrim( $to, '\\' );
					$this->_class_map [ $from ]         = $to;
					$this->_class_target_package[ $to ] = $package->get_package();
				}
			}
			$this->_namespace_target_package[ $package->get_namespace() ] = $package->get_package();
		}
	}

	/**
	 * @param string $package
	 *
	 * @return \WP_Framework|null
	 */
	private function get_alternative_instance( $package ) {
		foreach ( $this->app->get_instances() as $instance ) {
			if ( $instance->is_valid_package( $package ) ) {
				return $instance;
			}
		}

		return null;
	}

	/**
	 * @param string $name
	 *
	 * @return \WP_Framework_Core\Interfaces\Singleton
	 */
	public function get( $name ) {
		if ( isset( $this->_properties[ $name ] ) ) {
			$class = $this->_properties[ $name ];
			if ( ! isset( $this->_property_instances[ $class ] ) ) {
				/** @var \WP_Framework_Core\Interfaces\Singleton $class */
				try {
					$this->_property_instances[ $class ] = $class::get_instance( $this->app );
				} catch ( \Exception $e ) {
					\WP_Framework::wp_die( $e->getMessage(), __FILE__, __LINE__ );
				}
			}

			return $this->_property_instances[ $class ];
		}
		\WP_Framework::wp_die( $name . ' is undefined.', __FILE__, __LINE__ );

		return null;
	}

	/**
	 * @param string $class
	 *
	 * @return bool
	 */
	public function load_class( $class ) {
		$dirs  = null;
		$class = ltrim( $class, '\\' );
		if ( isset( $this->_property_instances[ $this->_properties['define'] ] ) && preg_match( "#\A{$this->define->plugin_namespace}(.+)\z#", $class, $matches ) ) {
			$class = $matches[1];
			$dirs  = $this->define->plugin_src_dir;
		} elseif ( isset( $this->_class_target_package[ $class ] ) ) {
			if ( array_key_exists( $class, $this->_alternative_instances ) ) {
				if ( ! isset( $this->_alternative_instances[ $class ] ) ) {
					$this->_alternative_instances[ $class ] = $this->get_alternative_instance( $this->_class_target_package[ $class ] );
					if ( ! isset( $this->_alternative_instances[ $class ] ) ) {
						$this->_alternative_instances[ $class ] = $this->app;
					}
				}
				$instance = $this->_alternative_instances[ $class ];
			} else {
				$instance = $this->app;
			}
			if ( $instance->get_package_instance( $this->_class_target_package[ $class ] )->load_class( $class ) ) {
				return true;
			}
		} elseif ( preg_match( '#\A(\w+)\\\\#', $class, $matches ) && isset( $this->_namespace_target_package[ $matches[1] ] ) ) {
			if ( $this->app->get_package_instance( $this->_namespace_target_package[ $matches[1] ] )->load_class( $class ) ) {
				return true;
			}
		}

		if ( isset( $dirs ) ) {
			$class = ltrim( $class, '\\' );
			$class = strtolower( $class );
			! is_array( $dirs ) and $dirs = [ $dirs ];
			foreach ( $dirs as $dir ) {
				$path = $dir . DS . str_replace( '\\', DS, $class ) . '.php';
				if ( is_readable( $path ) ) {
					/** @noinspection PhpIncludeInspection */
					require_once $path;

					return true;
				}
			}
		}

		return false;
	}

	/**
	 * main init
	 */
	public function main_init() {
		if ( $this->_initialized ) {
			return;
		}
		$this->_initialized = true;

		$this->filter->do_action( 'app_initialize', $this );
	}

	/**
	 * @return bool
	 */
	public function has_initialized() {
		return $this->_initialized;
	}

	/**
	 * @param string $class
	 *
	 * @return array
	 */
	public function get_mapped_class( $class ) {
		return isset( $this->_class_map[ $class ] ) ? [ true, $this->_class_map[ $class ] ] : [ false, $class ];
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function is_loaded( $name ) {
		return isset( $this->_property_instances[ $this->app->plugin_name ][ $name ] );
	}

	/**
	 * @deprecated
	 * @return string
	 */
	public function get_plugin_uri() {
		return $this->app->get_plugin_data( $this->app->is_theme ? 'ThemeURI' : 'PluginURI' );
	}

	/**
	 * @deprecated
	 * @return array
	 */
	public function get_package_versions() {
		return $this->app->array->combine( array_map( function ( $package ) {
			/** @var \WP_Framework\Package_Base $package */
			return [
				'version' => $package->get_version(),
				'package' => $package->get_package(),
			];
		}, $this->app->get_packages() ), 'package', 'version' );
	}

	/**
	 * @param string $name
	 * @param string|null $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get_config( $name, $key = null, $default = null ) {
		return $this->config->get( $name, $key, $default );
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get_option( $key, $default = '' ) {
		return $this->option->get( $key, $default );
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get_session( $key, $default = null ) {
		return $this->session->get( $key, $default );
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param int|null $duration
	 */
	public function set_session( $key, $value, $duration = null ) {
		$this->session->set( $key, $value, $duration );
	}

	/**
	 * @param null|string|false $capability
	 *
	 * @return bool
	 */
	public function user_can( $capability = null ) {
		return $this->user->user_can( $capability );
	}

	/**
	 * @param mixed $message
	 * @param mixed $context
	 * @param string $level
	 */
	public function log( $message, $context = null, $level = '' ) {
		if ( ! $this->app->is_valid_package( 'log' ) ) {
			$this->error_log( $message, $context );

			return;
		}
		if ( $message instanceof \Exception ) {
			$this->log->log( $message->getMessage(), isset( $context ) ? $context : $message->getTraceAsString(), empty( $level ) ? 'error' : $level );
		} elseif ( $message instanceof \WP_Error ) {
			$this->log->log( $message->get_error_message(), isset( $context ) ? $context : $message->get_error_data(), empty( $level ) ? 'error' : $level );
		} else {
			$this->log->log( $message, $context, $level );
		}
	}

	/**
	 * @param mixed $message
	 * @param mixed $context
	 */
	private function error_log( $message, $context ) {
		if ( $message instanceof \Exception ) {
			error_log( $message->getMessage() );
			error_log( print_r( isset( $context ) ? $context : $message->getTraceAsString(), true ) );
		} elseif ( $message instanceof \WP_Error ) {
			error_log( $message->get_error_message() );
			error_log( print_r( isset( $context ) ? $context : $message->get_error_data(), true ) );
		}
	}

	/**
	 * @param string $message
	 * @param string $group
	 * @param bool $error
	 * @param bool $escape
	 * @param null|array $override_allowed_html
	 */
	public function add_message( $message, $group = '', $error = false, $escape = true, $override_allowed_html = null ) {
		if ( ! $this->app->is_valid_package( 'admin' ) ) {
			return;
		}
		$this->admin->add_message( $message, $group, $error, $escape, $override_allowed_html );
	}

	/**
	 * @param string $to
	 * @param string $subject
	 * @param string|array $body
	 * @param string|false $text
	 *
	 * @return bool
	 */
	public function send_mail( $to, $subject, $body, $text = false ) {
		if ( ! $this->app->is_valid_package( 'mail' ) ) {
			return false;
		}

		return $this->mail->send( $to, $subject, $body, $text );
	}

	/**
	 * @param \WP_Framework_Core\Interfaces\Package $instance
	 * @param string $name
	 * @param array $args
	 * @param bool $echo
	 * @param bool $error
	 * @param bool $remove_nl
	 *
	 * @return string
	 */
	public function get_view( \WP_Framework_Core\Interfaces\Package $instance, $name, array $args = [], $echo = false, $error = true, $remove_nl = false ) {
		if ( ! $this->app->is_valid_package( 'presenter' ) ) {
			return '';
		}

		$this->drawer->set_package( $instance );

		return $this->drawer->get_view( $name, $args, $echo, $error, $remove_nl );
	}

	/**
	 * @param \WP_Framework_Core\Interfaces\Package $instance
	 * @param string $name
	 * @param array $args
	 * @param int $priority
	 */
	public function add_script_view( \WP_Framework_Core\Interfaces\Package $instance, $name, array $args = [], $priority = 10 ) {
		if ( ! $this->app->is_valid_package( 'presenter' ) ) {
			return;
		}

		$this->drawer->set_package( $instance );
		$this->drawer->add_script_view( $name, $args, $priority );
	}

	/**
	 * @param \WP_Framework_Core\Interfaces\Package $instance
	 * @param string $name
	 * @param array $args
	 * @param int $priority
	 */
	public function add_style_view( \WP_Framework_Core\Interfaces\Package $instance, $name, array $args = [], $priority = 10 ) {
		if ( ! $this->app->is_valid_package( 'presenter' ) ) {
			return;
		}

		$this->drawer->set_package( $instance );
		$this->drawer->add_style_view( $name, $args, $priority );
	}

	/**
	 * @param \WP_Framework_Core\Interfaces\Package $instance
	 * @param string $handle
	 * @param string $file
	 * @param array $depends
	 * @param string|bool|null $ver
	 * @param string $media
	 * @param string $dir
	 */
	public function enqueue_style( \WP_Framework_Core\Interfaces\Package $instance, $handle, $file, array $depends = [], $ver = false, $media = 'all', $dir = 'css' ) {
		if ( ! $this->app->is_valid_package( 'presenter' ) ) {
			return;
		}

		$this->drawer->set_package( $instance );
		$this->drawer->enqueue_style( $handle, $file, $depends, $ver, $media, $dir );
	}

	/**
	 * @param \WP_Framework_Core\Interfaces\Package $instance
	 * @param string $handle
	 * @param string $file
	 * @param array $depends
	 * @param string|bool|null $ver
	 * @param bool $in_footer
	 * @param string $dir
	 */
	public function enqueue_script( \WP_Framework_Core\Interfaces\Package $instance, $handle, $file, array $depends = [], $ver = false, $in_footer = true, $dir = 'js' ) {
		if ( ! $this->app->is_valid_package( 'presenter' ) ) {
			return;
		}

		$this->drawer->set_package( $instance );
		$this->drawer->enqueue_script( $handle, $file, $depends, $ver, $in_footer, $dir );
	}

	/**
	 * @param \WP_Framework_Core\Interfaces\Package $instance
	 * @param string $handle
	 * @param string $name
	 * @param array $data
	 *
	 * @return bool
	 */
	public function localize_script( \WP_Framework_Core\Interfaces\Package $instance, $handle, $name, array $data ) {
		if ( ! $this->app->is_valid_package( 'presenter' ) ) {
			return false;
		}

		$this->drawer->set_package( $instance );

		return $this->drawer->localize_script( $handle, $name, $data );
	}

	/**
	 * @param string $file
	 *
	 * @return string
	 */
	public function get_page_slug( $file ) {
		return basename( $file, '.php' );
	}

	/**
	 * @param string $key
	 * @param string|null $target
	 *
	 * @return mixed
	 */
	public function get_shared_object( $key, $target = null ) {
		! isset( $target ) and $target = $this->app->plugin_name;

		return isset( self::$_shared_object[ $target ][ $key ] ) ? self::$_shared_object[ $target ][ $key ] : null;
	}

	/**
	 * @param string $key
	 * @param mixed $object
	 * @param string|null $target
	 */
	public function set_shared_object( $key, $object, $target = null ) {
		! isset( $target ) and $target = $this->app->plugin_name;
		self::$_shared_object[ $target ][ $key ] = $object;
	}

	/**
	 * @param string $key
	 * @param string|null $target
	 *
	 * @return bool
	 */
	public function isset_shared_object( $key, $target = null ) {
		! isset( $target ) and $target = $this->app->plugin_name;

		return isset( self::$_shared_object[ $target ] ) && array_key_exists( $key, self::$_shared_object[ $target ] );
	}

	/**
	 * @param string $key
	 * @param string|null $target
	 */
	public function delete_shared_object( $key, $target = null ) {
		! isset( $target ) and $target = $this->app->plugin_name;
		unset( self::$_shared_object[ $target ][ $key ] );
	}

	/**
	 * @param string $name
	 * @param callable $func
	 * @param int $timeout
	 *
	 * @return bool
	 */
	public function lock_process( $name, callable $func, $timeout = 60 ) {
		return $this->utility->lock_process( $this->app, $name, $func, $timeout );
	}

	/**
	 * @return bool
	 */
	public function is_enough_version() {
		if ( ! isset( $this->_property_instances[ $this->_properties['system'] ] ) ) {
			return true;
		}

		return $this->system->is_enough_version();
	}

	/**
	 * load all packages
	 */
	public function load_all_packages() {
		foreach ( $this->_properties as $name => $class ) {
			if ( ! $this->app->is_valid_package( $name ) ) {
				continue;
			}
			$this->$name;
		}
	}
}
