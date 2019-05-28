<?php
/**
 * WP_Framework_Common Classes Models Option
 *
 * @version 0.0.49
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Common\Classes\Models;

use WP_Framework_Common\Traits\Package;
use WP_Framework_Core\Traits\Hook;
use WP_Framework_Core\Traits\Singleton;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Option
 * @package WP_Framework_Common\Classes\Models
 */
class Option implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Common\Interfaces\Uninstall {

	use Singleton, Hook, \WP_Framework_Common\Traits\Uninstall, Package;

	/**
	 * @var array $_options
	 */
	private $_options;

	/**
	 * @var array $_site_options
	 */
	private $_site_options;

	/**
	 * @var array $_option_name_cache
	 */
	private $_option_name_cache = [];

	/**
	 * @var array $_site_option_name_cache
	 */
	private $_site_option_name_cache = [];

	/**
	 * @var int $_blog_id
	 */
	private $_blog_id;

	/**
	 * app deactivated
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function app_deactivated() {
		$this->delete( '__app_activated' );
	}

	/**
	 * app activated
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function app_activated() {
		$this->set( '__app_activated', true );
	}

	/**
	 * @return bool
	 */
	public function is_app_activated() {
		return ! empty( $this->get( '__app_activated' ) );
	}

	/**
	 * @param int $new_blog
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function switch_blog( $new_blog ) {
		if ( $new_blog === $this->_blog_id ) {
			return;
		}

		$this->_options = [];
	}

	/**
	 * @param string|null $group
	 * @param bool $common
	 *
	 * @return array
	 */
	private function get_options( $group, $common ) {
		if ( $common && is_multisite() ) {
			return $this->get_site_options( $group );
		}

		! isset( $this->_options ) and $this->_options = [];
		! isset( $group ) and $group = 'default';

		if ( ! isset( $this->_options[ $group ] ) ) {
			$this->_options[ $group ] = wp_parse_args(
				$this->get_option( $group ), []
			);
			$this->_options[ $group ] = $this->unescape( $this->_options[ $group ] );
		}

		return $this->_options[ $group ];
	}

	/**
	 * @param string|null $group
	 *
	 * @return array
	 */
	private function get_site_options( $group ) {
		! isset( $this->_site_options ) and $this->_site_options = [];
		! isset( $group ) and $group = 'default';

		if ( ! isset( $this->_site_options[ $group ] ) ) {
			$this->_site_options[ $group ] = wp_parse_args(
				$this->get_site_option( $group ), []
			);
			$this->_site_options[ $group ] = $this->unescape( $this->_site_options[ $group ] );
		}

		return $this->_site_options[ $group ];
	}

	/**
	 * @param string|null $group
	 * @param bool $common
	 *
	 * @return array
	 */
	private function reload_options( $group, $common ) {
		$this->flush( $group, $common );

		return $this->get_options( $group, $common );
	}

	/**
	 * @param string|null $group
	 * @param bool $common
	 */
	public function flush( $group = null, $common = false ) {
		! isset( $group ) and $group = 'default';
		if ( $common && is_multisite() ) {
			if ( isset( $this->_site_options[ $group ] ) ) {
				unset( $this->_site_options[ $group ] );
			}
		} else {
			if ( isset( $this->_options[ $group ] ) ) {
				unset( $this->_options[ $group ] );
			}
		}
	}

	/**
	 * @param string $group
	 *
	 * @return array
	 */
	private function get_option( $group ) {
		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}

		return get_option( $this->get_option_name( $group ), [] );
	}

	/**
	 * @param string $group
	 *
	 * @return array
	 */
	private function get_site_option( $group ) {
		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}

		return get_site_option( $this->get_site_option_name( $group ), [] );
	}

	/**
	 * @return string
	 */
	private function get_group_option_name_prefix() {
		return $this->get_slug( 'group_option_name', '_options' ) . '/';
	}

	/**
	 * @param string|null $group
	 *
	 * @return string
	 */
	public function get_option_name( $group = null ) {
		! isset( $group ) and $group = 'default';
		if ( ! isset( $this->_option_name_cache[ $group ] ) ) {
			if ( 'default' === $group ) {
				$this->_option_name_cache[ $group ] = $this->apply_filters( 'get_option_name', $this->get_slug( 'option_name', '_options' ) );
			} else {
				$this->_option_name_cache[ $group ] = $this->apply_filters( 'get_group_option_name', $this->get_group_option_name_prefix() . $group, $group );
			}
		}

		return $this->_option_name_cache[ $group ];
	}

	/**
	 * @return string
	 */
	private function get_group_site_option_name_prefix() {
		return $this->get_slug( 'group_site_option_name', '_options' ) . '/';
	}

	/**
	 * @param string|null $group
	 *
	 * @return string
	 */
	public function get_site_option_name( $group = null ) {
		! isset( $group ) and $group = 'default';
		if ( ! isset( $this->_site_option_name_cache[ $group ] ) ) {
			if ( 'default' === $group ) {
				$this->_site_option_name_cache[ $group ] = $this->apply_filters( 'get_site_option_name', $this->get_slug( 'site_option_name', '_options' ) );
			} else {
				$this->_site_option_name_cache[ $group ] = $this->apply_filters( 'get_group_site_option_name', $this->get_group_site_option_name_prefix() . $group, $group );
			}
		}

		return $this->_site_option_name_cache[ $group ];
	}

	/**
	 * @param string $option
	 *
	 * @return bool
	 */
	public function is_managed_option_name( $option ) {
		if ( $option === $this->get_option_name() ) {
			return true;
		}

		if ( ! is_multisite() && preg_match( '/^' . preg_quote( $this->get_group_site_option_name_prefix(), '/' ) . '/', $option ) > 0 ) {
			return true;
		}

		return preg_match( '/^' . preg_quote( $this->get_group_option_name_prefix(), '/' ) . '/', $option ) > 0;
	}

	/**
	 * @param array $options
	 *
	 * @return array
	 */
	private function unescape( $options ) {
		foreach ( $options as $key => $value ) {
			if ( is_string( $value ) ) {
				$options[ $key ] = stripslashes( htmlspecialchars_decode( $options[ $key ] ) );
			}
		}

		return $options;
	}

	/**
	 * @param string $key
	 * @param string|null $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	public function exists( $key, $group = null, $common = false ) {
		return array_key_exists( $key, $this->get_options( $group, $common ) );
	}

	/**
	 * @param string $key
	 * @param string $default
	 * @param bool $common
	 *
	 * @return mixed
	 */
	public function get( $key, $default = '', $common = false ) {
		return $this->get_grouped( $key, null, $default, $common );
	}

	/**
	 * @param string $key
	 * @param string|null $group
	 * @param string $default
	 * @param bool $common
	 *
	 * @return mixed
	 */
	public function get_grouped( $key, $group, $default = '', $common = false ) {
		return $this->apply_filters( 'get_option', $this->app->array->get( $this->get_options( $group, $common ), $key, $default ), $key, $default, $group, $common );
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param bool $common
	 *
	 * @return bool
	 */
	public function set( $key, $value, $common = false ) {
		return $this->set_grouped( $key, null, $value, $common );
	}

	/**
	 * @param string $key
	 * @param string|null $group
	 * @param mixed $value
	 * @param bool $common
	 *
	 * @return bool
	 */
	public function set_grouped( $key, $group, $value, $common = false ) {
		$options = $this->reload_options( $group, $common );
		$prev    = array_key_exists( $key, $options ) ? $options[ $key ] : null;
		if ( $prev !== $value || ! array_key_exists( $key, $options ) ) {
			$options[ $key ] = $value;
			$result          = $this->save( $group, $options, $common );
			$this->do_action( 'changed_option', $key, $value, $prev, $group, $common );

			return $result;
		}

		return false;
	}

	/**
	 * @param string $key
	 * @param bool $common
	 *
	 * @return bool
	 */
	public function delete( $key, $common = false ) {
		return $this->delete_grouped( $key, null, $common );
	}

	/**
	 * @param string|null $key
	 * @param string|null $group
	 * @param bool $common
	 *
	 * @return bool
	 */
	public function delete_grouped( $key, $group, $common = false ) {
		$options = $this->reload_options( $group, $common );
		if ( ! isset( $key ) ) {
			return empty( $options ) ? false : $this->save( $group, [], $common );
		}

		if ( $this->exists( $key, $group, $common ) ) {
			$prev = $options[ $key ];
			unset( $options[ $key ] );
			$result = $this->save( $group, $options, $common );
			$this->do_action( 'deleted_option', $key, $prev, $common );

			return $result;
		}

		return false;
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return bool
	 */
	public function set_post_value( $key, $default = null ) {
		$post = $this->app->input->post( $key );
		if ( ! isset( $post ) && ! isset( $default ) ) {
			return false;
		}
		$result = $this->set( $key, isset( $post ) ? $post : $default );
		$this->delete_hook_cache( preg_replace( '/^' . preg_quote( $this->get_filter_prefix(), '/' ) . '/', '', $key ) );

		return $result;
	}

	/**
	 * @param string $group
	 * @param array $options
	 * @param bool $common
	 *
	 * @return bool
	 */
	private function save( $group, $options, $common ) {
		foreach ( $options as $key => $value ) {
			if ( is_string( $value ) ) {
				$options[ $key ] = htmlspecialchars( $value );
			}
		}

		$this->flush( $group, $common );

		if ( $common && is_multisite() ) {
			return update_site_option( $this->get_site_option_name( $group ), $options );
		}

		return update_option( $this->get_option_name( $group ), $options );
	}

	/**
	 * @param null|string $group_prefix
	 *
	 * @return array
	 */
	private function get_group_options( $group_prefix = null ) {
		$prefix = $this->get_group_option_name_prefix();
		isset( $group_prefix ) and $prefix .= $group_prefix;

		/** @noinspection SqlResolve */
		return $this->app->array->pluck_unique( $this->wpdb()->get_results( $this->wpdb()->prepare(
			"SELECT option_name FROM {$this->get_wp_table('options')} WHERE option_name LIKE %s",
			str_replace( [ '\\', '%', '_' ], [ '\\\\', '\%', '\_' ], $prefix ) . '%'
		) ), 'option_name' );
	}

	/**
	 * @param null|string $group_prefix
	 *
	 * @return array
	 */
	private function get_group_site_options( $group_prefix = null ) {
		$prefix = $this->get_group_site_option_name_prefix();
		isset( $group_prefix ) and $prefix .= $group_prefix;

		/** @noinspection SqlResolve */
		return $this->app->array->pluck_unique( $this->wpdb()->get_results( $this->wpdb()->prepare(
			"SELECT meta_key FROM {$this->get_wp_table('sitemeta')} WHERE meta_key LIKE %s",
			str_replace( [ '\\', '%', '_' ], [ '\\\\', '\%', '\_' ], $prefix ) . '%'
		) ), 'meta_key' );
	}

	/**
	 * @param string|null $group_prefix
	 * @param bool $common
	 */
	public function clear_group_option( $group_prefix, $common ) {
		if ( $common && is_multisite() ) {
			foreach ( $this->get_group_site_options( $group_prefix ) as $option ) {
				delete_site_option( $option );
			}
		} else {
			foreach ( $this->get_group_options( $group_prefix ) as $option ) {
				delete_option( $option );
			}
		}
	}

	/**
	 * clear option
	 */
	public function clear_option() {
		delete_option( $this->get_option_name() );
		$this->clear_group_option( null, false );

		if ( is_multisite() ) {
			delete_site_option( $this->get_site_option_name() );
			$this->clear_group_option( null, true );
		}
	}

	/**
	 * uninstall
	 */
	public function uninstall() {
		$this->clear_option();
	}

	/**
	 * @return int
	 */
	public function get_uninstall_priority() {
		return 1000;
	}
}
