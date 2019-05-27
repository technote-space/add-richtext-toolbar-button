<?php
/**
 * WP_Framework_Core Traits Hook
 *
 * @version 0.0.54
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Core\Traits;

use WP_Framework;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Trait Hook
 * @package WP_Framework_Core\Traits
 * @property WP_Framework $app
 * @mixin Utility
 */
trait Hook {

	/**
	 * @var string $_filter_prefix
	 */
	private $_filter_prefix = null;

	/**
	 * load cache settings
	 */
	private function load_cache_settings() {
		if ( $this->app->isset_shared_object( '_is_valid_hook_cache' ) ) {
			return;
		}

		$this->app->set_shared_object( '_is_valid_hook_cache', ! empty( $this->app->get_config( 'config', 'cache_filter_result' ) ) );
		$prevent_cache = $this->app->get_config( 'config', 'cache_filter_exclude_list', [] );
		$prevent_cache = empty( $prevent_cache ) ? [] : array_combine(
			$prevent_cache,
			array_fill( 0, count( $prevent_cache ), true )
		);
		$this->app->set_shared_object( '_prevent_hook_cache', $prevent_cache );
		$this->app->set_shared_object( '_hook_cache', [] );
	}

	/**
	 * @return string
	 */
	protected function get_filter_prefix() {
		! isset( $this->_filter_prefix ) and $this->_filter_prefix = $this->get_slug( 'filter_prefix', '' ) . $this->app->get_config( 'config', 'filter_separator' );

		return $this->_filter_prefix;
	}

	/**
	 * @return string
	 */
	private function get_framework_filter_prefix() {
		return WP_FRAMEWORK_VENDOR_NAME . '/';
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	private function add_hook_cache( $key, $value ) {
		$cache         = $this->app->get_shared_object( '_hook_cache' );
		$cache[ $key ] = $value;
		$this->app->set_shared_object( '_hook_cache', $cache );
	}

	/**
	 * @param string $key
	 */
	protected function delete_hook_cache( $key ) {
		$cache = $this->app->get_shared_object( '_hook_cache' );
		if ( ! empty( $cache ) && array_key_exists( $key, $cache ) ) {
			unset( $cache[ $key ] );
			$this->app->set_shared_object( '_hook_cache', $cache );
		}
	}

	/**
	 * @param string $key
	 *
	 * @return array
	 */
	private function get_hook_cache( $key ) {
		$this->load_cache_settings();
		$prevent_cache  = $this->app->get_shared_object( '_prevent_hook_cache' );
		$is_valid_cache = ! isset( $prevent_cache[ $key ] ) && $this->app->get_shared_object( '_is_valid_hook_cache' );
		if ( ! $is_valid_cache ) {
			return [ false, null, $is_valid_cache ];
		}

		$cache = $this->app->get_shared_object( '_hook_cache' );
		if ( ! is_array( $cache ) || ! array_key_exists( $key, $cache ) ) {
			return [ false, null, $is_valid_cache ];
		}

		return [ true, $cache[ $key ], $is_valid_cache ];
	}

	/**
	 * @return mixed
	 */
	public function apply_filters() {
		return $this->_apply_filters( $this->get_filter_prefix(), func_get_args() );
	}

	/**
	 * @return mixed
	 */
	public function apply_framework_filters() {
		return $this->_apply_filters( $this->get_framework_filter_prefix(), func_get_args() );
	}

	/**
	 * @param string $prefix
	 * @param array $args
	 *
	 * @return mixed
	 */
	private function _apply_filters( $prefix, $args ) {
		$key = $args[0];

		list( $cache_is_valid, $cache, $is_valid_cache ) = $this->get_hook_cache( $key );
		if ( $cache_is_valid ) {
			return $cache;
		}

		$args[0] = $prefix . $key;
		if ( count( $args ) < 2 ) {
			$args[] = null;
		} else {
			$this->call_if_closure_with_result( $args[1], $args[1], $this->app );
		}
		$default = call_user_func_array( 'apply_filters', $args );

		if ( ! empty( $this->app->setting ) && $this->app->setting->is_setting( $key ) ) {
			$setting = $this->app->setting->get_setting( $key );
			$default = $this->app->array->get( $setting, 'default', $default );
			$this->call_if_closure_with_result( $default, $default, $this->app );
			$value = $this->app->get_option( $args[0], null );
			if ( ! isset( $value ) || $value === '' ) {
				$value = $default;
			}

			$type = $this->app->array->get( $setting, 'type', '' );
			if ( $type ) {
				$method = 'get_' . $type . '_value';
				if ( $this->is_method_callable( $method ) ) {
					$value = call_user_func( [ $this, $method ], $value, $default, $setting );
				}
			}
			if ( ! empty( $setting['translate'] ) && $value === $default ) {
				$value = $this->translate( $value );
			}

			if ( $is_valid_cache ) {
				$this->add_hook_cache( $key, $value );
			}

			return $value;
		}

		if ( $is_valid_cache && count( $args ) <= 2 ) {
			$this->add_hook_cache( $key, $default );
		}

		return $default;
	}

	/**
	 * @param mixed $value
	 * @param mixed $default
	 * @param array $setting
	 *
	 * @return bool
	 */
	protected function get_bool_value(
		/** @noinspection PhpUnusedParameterInspection */
		$value, $default, array $setting
	) {
		if ( is_bool( $value ) ) {
			return $value;
		}
		if ( 'true' === $value ) {
			return true;
		}
		if ( 'false' === $value ) {
			return false;
		}
		if ( isset( $value ) && (string) $value !== '' ) {
			return ! empty( $value );
		}

		return ! empty( $default );
	}

	/**
	 * @param mixed $value
	 * @param mixed $default
	 * @param array $setting
	 *
	 * @return int
	 */
	protected function get_int_value( $value, $default, array $setting ) {
		$default = (int) $default;
		if ( is_numeric( $value ) ) {
			$value = (int) $value;
			if ( $value !== $default ) {
				if ( isset( $setting['min'] ) && $value < (int) $setting['min'] ) {
					$value = (int) $setting['min'];
				}
				if ( isset( $setting['max'] ) && $value > (int) $setting['max'] ) {
					$value = (int) $setting['max'];
				}
			} elseif ( isset( $setting['option'] ) ) {
				$default = isset( $setting['option_default'] ) ? (int) $setting['option_default'] : $default;
				$value   = (int) $this->app->get_option( $setting['option'], $default );
			}
		} else {
			$value = $default;
		}

		return $value;
	}

	/**
	 * @param mixed $value
	 * @param mixed $default
	 * @param array $setting
	 *
	 * @return float
	 */
	protected function get_float_value( $value, $default, array $setting ) {
		$default = (float) $default;
		if ( is_numeric( $value ) ) {
			$value = (float) $value;
			if ( $value !== $default ) {
				if ( isset( $setting['min'] ) && $value < (float) $setting['min'] ) {
					$value = (float) $setting['min'];
				}
				if ( isset( $setting['max'] ) && $value > (float) $setting['max'] ) {
					$value = (float) $setting['max'];
				}
			} elseif ( isset( $setting['option'] ) ) {
				$default = isset( $setting['option_default'] ) ? (float) $setting['option_default'] : $default;
				$value   = (float) $this->app->get_option( $setting['option'], $default );
			}
		} else {
			$value = $default;
		}

		return $value;
	}

	/**
	 * do action
	 */
	public function do_action() {
		$this->_do_action( $this->get_filter_prefix(), func_get_args() );
	}

	/**
	 * do framework action
	 */
	public function do_framework_action() {
		$args = func_get_args();
		$this->_do_action( $this->get_framework_filter_prefix(), $args );
		$this->_do_action( $this->get_filter_prefix(), $args );
	}

	/**
	 * @param string $prefix
	 * @param array $args
	 */
	private function _do_action( $prefix, $args ) {
		$args[0] = $prefix . $args[0];
		call_user_func_array( 'do_action', $args );
	}
}
