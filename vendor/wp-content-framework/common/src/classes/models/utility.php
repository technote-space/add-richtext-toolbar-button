<?php
/**
 * WP_Framework_Common Classes Models Utility
 *
 * @version 0.0.35
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
 * Class Utility
 * @package WP_Framework_Common\Classes\Models
 */
class Utility implements \WP_Framework_Core\Interfaces\Singleton {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Common\Traits\Package;

	/**
	 * @var float $_tick
	 */
	private $_tick;

	/**
	 * @var array $_active_plugins
	 */
	private $_active_plugins;

	/**
	 * @var string $_active_plugins_hash
	 */
	private $_active_plugins_hash;

	/**
	 * @var string $_framework_plugin_hash
	 */
	private $_framework_plugin_hash;

	/**
	 * @return bool
	 */
	protected static function is_shared_class() {
		return true;
	}

	/**
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function value( $value ) {
		return $value instanceof \Closure ? $value( $this->app ) : $value;
	}

	/**
	 * @return string
	 */
	public function uuid() {
		$pid  = getmypid();
		$node = $this->app->input->server( 'SERVER_ADDR', '0.0.0.0' );
		list( $timeMid, $timeLow ) = explode( ' ', microtime() );

		return sprintf( "%08x%04x%04x%02x%02x%04x%08x", (int) $timeLow, (int) substr( $timeMid, 2 ) & 0xffff,
			mt_rand( 0, 0xfff ) | 0x4000, mt_rand( 0, 0x3f ) | 0x80, mt_rand( 0, 0xff ), $pid & 0xffff, $node );
	}

	/**
	 * @param string $c
	 *
	 * @return bool
	 */
	public function defined( $c ) {
		if ( defined( $c ) ) {
			$const = @constant( $c );
			if ( $const ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $c
	 * @param null $default
	 *
	 * @return mixed|null
	 */
	public function definedv( $c, $default = null ) {
		if ( defined( $c ) ) {
			$const = @constant( $c );

			return $const;
		}

		return $this->value( $default );
	}

	/**
	 * @param string $data
	 * @param string $key
	 *
	 * @return false|string
	 */
	public function create_hash( $data, $key ) {
		return hash_hmac( function_exists( 'hash' ) ? 'sha256' : 'sha1', $data, $key );
	}

	/**
	 * @return bool
	 */
	public function doing_ajax() {
		if ( $this->definedv( 'REST_REQUEST' ) ) {
			return true;
		}

		if ( function_exists( 'wp_doing_ajax' ) ) {
			return wp_doing_ajax();
		}

		return ! ! $this->definedv( 'DOING_AJAX' );
	}

	/**
	 * @return bool
	 */
	public function doing_cron() {
		return ! ! $this->definedv( 'DOING_CRON' );
	}

	/**
	 * @return bool
	 */
	public function is_autosave() {
		return ! ! $this->definedv( 'DOING_AUTOSAVE' );
	}

	/**
	 * @param bool $except_ajax
	 *
	 * @return bool
	 */
	public function is_admin( $except_ajax = true ) {
		return is_admin() && ( ! $except_ajax || ! $this->doing_ajax() );
	}

	/**
	 * @return bool
	 */
	public function was_admin() {
		return $this->is_admin_url( $this->app->input->referer() );
	}

	/**
	 * @param string $url
	 *
	 * @return bool
	 */
	public function is_admin_url( $url ) {
		return $this->app->string->starts_with( $url, admin_url() );
	}

	/**
	 * @return bool
	 */
	public function is_changed_host() {
		return $this->app->input->host() !== $this->app->input->referer_host();
	}

	/**
	 * @return bool
	 */
	public function is_changed_admin() {
		return $this->is_admin() !== $this->was_admin();
	}

	/**
	 * @param array $unset
	 *
	 * @return array
	 */
	public function get_debug_backtrace( array $unset = [] ) {
		$backtrace = debug_backtrace();
		foreach ( $backtrace as $k => $v ) {
			// 大量のデータになりがちな object と args を削除や編集
			unset( $backtrace[ $k ]['object'] );
			if ( ! empty( $backtrace[ $k ]['args'] ) ) {
				$backtrace[ $k ]['args'] = $this->parse_backtrace_args( $backtrace[ $k ]['args'] );
			} else {
				unset( $backtrace[ $k ]['args'] );
			}
			if ( ! empty( $unset ) ) {
				foreach ( $v as $key => $value ) {
					if ( in_array( $key, $unset ) ) {
						unset( $backtrace[ $k ][ $key ] );
					}
				}
			}
		}

		return $backtrace;
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	private function parse_backtrace_args( array $args ) {
		return $this->app->array->map( $args, function ( $d ) {
			$type = gettype( $d );
			if ( 'array' === $type ) {
				return $this->parse_backtrace_args( $d );
			} elseif ( 'object' === $type ) {
				$type = get_class( $d );
			} elseif ( 'resource' !== $type && 'resource (closed)' !== $type && 'NULL' !== $type && 'unknown type' !== $type ) {
				if ( 'boolean' === $type ) {
					$d = var_export( $d, true );
				}
				$type .= ': ' . $d;
			}

			return $type;
		} );
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

	/**
	 * @param string $type
	 * @param bool $detect_text
	 *
	 * @return string
	 */
	public function parse_db_type( $type, $detect_text = false ) {
		switch ( true ) {
			case stristr( $type, 'TINYINT(1)' ) !== false:
				return 'bool';
			case stristr( $type, 'INT' ) !== false:
				return 'int';
			case stristr( $type, 'BIT' ) !== false:
				return 'bool';
			case stristr( $type, 'BOOLEAN' ) !== false:
				return 'bool';
			case stristr( $type, 'DECIMAL' ) !== false:
				return 'number';
			case stristr( $type, 'FLOAT' ) !== false:
				return 'float';
			case stristr( $type, 'DOUBLE' ) !== false:
				return 'number';
			case stristr( $type, 'REAL' ) !== false:
				return 'number';
			case $detect_text && stristr( $type, 'TEXT' ) !== false:
				return 'text';
		}

		return 'string';
	}

	/**
	 * @param array|string $tags
	 *
	 * @return bool
	 */
	public function has_shortcode( $tags ) {
		if ( empty( $tags ) ) {
			return false;
		}

		$post = get_post();
		if ( empty( $post ) || ! $post instanceof \WP_Post ) {
			return false;
		}
		! is_array( $tags ) and $tags = [ $tags ];
		$content = $post->post_content;
		foreach ( $tags as $tag ) {
			if ( has_shortcode( $content, $tag ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function is_valid_tinymce_color_picker() {
		return $this->compare_wp_version( '4.0.0', '>=' );
	}

	/**
	 * @return bool
	 */
	public function can_use_block_editor() {
		return $this->compare_wp_version( '5.0.0', '>=' );
	}

	/**
	 * @return bool
	 */
	public function is_block_editor() {
		if ( ! is_admin() ) {
			return false;
		}
		$current_screen = get_current_screen();

		return ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) || ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() );
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $name
	 * @param callable $func
	 * @param int $timeout
	 *
	 * @return bool
	 */
	public function lock_process( \WP_Framework $app, $name, callable $func, $timeout = 60 ) {
		$name         .= '__LOCK_PROCESS__';
		$timeout_name = $name . 'TIMEOUT__';
		$option       = $app->option;
		$option->flush();
		$check = $option->get( $name );
		if ( ! empty( $check ) ) {
			$expired = $option->get( $timeout_name, 0 ) < time();
			if ( ! $expired ) {
				return false;
			}
		}
		$rand = md5( uniqid() );
		$option->set( $name, $rand );
		$option->flush();
		if ( $option->get( $name ) != $rand ) {
			return false;
		}
		$option->set( $timeout_name, time() + $timeout );
		try {
			$func();
		} catch ( \Exception $e ) {
			$app->log( $e );
		} finally {
			$option->delete( $name );
			$option->delete( $timeout_name );
		}

		return true;
	}

	/**
	 * @param bool $combine
	 *
	 * @return array
	 */
	public function get_active_plugins( $combine = true ) {
		if ( ! isset( $this->_active_plugins ) ) {
			$option = get_option( 'active_plugins', [] );
			if ( is_multisite() ) {
				$option = array_merge( $option, array_keys( get_site_option( 'active_sitewide_plugins' ) ) );
				$option = array_unique( $option );
			}
			$this->_active_plugins = $combine ? $this->app->array->combine( $option, null ) : array_values( $option );
		}

		return $this->_active_plugins;
	}

	/**
	 * @return string
	 */
	public function get_active_plugins_hash() {
		! isset( $this->_active_plugins_hash ) and $this->_active_plugins_hash = sha1( json_encode( $this->get_active_plugins( false ) ) );

		return $this->_active_plugins_hash;
	}

	/**
	 * @return array
	 */
	private function get_framework_plugins() {
		return $this->app->array->map( $this->app->get_instances(), function ( $instance ) {
			/** @var \WP_Framework $instance */
			return $instance->plugin_name . '/' . $instance->get_plugin_version();
		} );
	}

	/**
	 * @return string
	 */
	public function get_framework_plugins_hash() {
		! isset( $this->_framework_plugin_hash ) and $this->_framework_plugin_hash = sha1( json_encode( $this->get_framework_plugins() ) );

		return $this->_framework_plugin_hash;
	}

	/**
	 * @param string $plugin
	 *
	 * @return bool
	 */
	public function is_active_plugin( $plugin ) {
		return in_array( $plugin, $this->get_active_plugins( false ) );
	}

	/**
	 * for debug
	 */
	public function timer_start() {
		$this->_tick = microtime( true ) * 1000;
	}

	/**
	 * for debug
	 *
	 * @param string $format
	 */
	public function timer_tick( $format = '%12.8f' ) {
		if ( ! isset( $this->_tick ) ) {
			$this->timer_start();

			return;
		}
		$now     = microtime( true ) * 1000;
		$elapsed = $now - $this->_tick;
		error_log( sprintf( $format, $elapsed ) );
		$this->_tick = $now;
	}
}
