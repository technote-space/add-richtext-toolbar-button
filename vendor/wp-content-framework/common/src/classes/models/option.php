<?php
/**
 * WP_Framework_Common Classes Models Option
 *
 * @version 0.0.36
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
 * Class Option
 * @package WP_Framework_Common\Classes\Models
 */
class Option implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Common\Interfaces\Uninstall {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Core\Traits\Hook, \WP_Framework_Common\Traits\Uninstall, \WP_Framework_Common\Traits\Package;

	/**
	 * @var array $_options
	 */
	private $_options;

	/**
	 * @var bool $_suspend_reload
	 */
	private $_suspend_reload = false;

	/**
	 * @var array $_option_name_cache
	 */
	private $_option_name_cache = [];

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
	 * @param string|null $group
	 *
	 * @return array
	 */
	private function get_options( $group ) {
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
	private function reload_options( $group ) {
		if ( $this->_suspend_reload ) {
			return $this->get_options( $group );
		}

		$this->flush( $group );

		return $this->get_options( $group );
	}

	/**
	 * @param string|null $group
	 */
	public function flush( $group = null ) {
		! isset( $group ) and $group = 'default';
		if ( isset( $this->_options[ $group ] ) ) {
			unset( $this->_options[ $group ] );
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
	private function get_group_option_name_prefix() {
		return $this->get_slug( 'group_option_name', '_options' ) . '/';
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
	 *
	 * @return bool
	 */
	public function exists( $key, $group = null ) {
		return array_key_exists( $key, $this->get_options( $group ) );
	}

	/**
	 * @param string $key
	 * @param string $default
	 *
	 * @return mixed
	 */
	public function get( $key, $default = '' ) {
		return $this->get_grouped( $key, null, $default );
	}

	/**
	 * @param string $key
	 * @param string|null $group
	 * @param string $default
	 *
	 * @return mixed
	 */
	public function get_grouped( $key, $group, $default = '' ) {
		return $this->apply_filters( 'get_option', $this->app->array->get( $this->get_options( $group ), $key, $default ), $key, $default, $group );
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function set( $key, $value ) {
		return $this->set_grouped( $key, null, $value );
	}

	/**
	 * @param string $key
	 * @param string|null $group
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function set_grouped( $key, $group, $value ) {
		$options        = $this->reload_options( $group );
		$suspend_reload = $this->_suspend_reload;
		$prev           = array_key_exists( $key, $options ) ? $options[ $key ] : null;
		if ( $prev !== $value || ! array_key_exists( $key, $options ) ) {
			$options[ $key ]       = $value;
			$this->_suspend_reload = true;
			$this->do_action( 'changed_option', $key, $value, $prev, $group );
			$this->_suspend_reload = $suspend_reload;

			return $this->save( $group, $options );
		}

		return false;
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	public function delete( $key ) {
		return $this->delete_grouped( $key, null );
	}

	/**
	 * @param string $key
	 * @param string|null $group
	 *
	 * @return bool
	 */
	public function delete_grouped( $key, $group ) {
		$options        = $this->reload_options( $group );
		$suspend_reload = $this->_suspend_reload;
		if ( $this->exists( $key, $group ) ) {
			$prev = $options[ $key ];
			unset( $options[ $key ] );
			$this->_suspend_reload = true;
			$this->do_action( 'deleted_option', $key, $prev );
			$this->_suspend_reload = $suspend_reload;

			return $this->save( $group, $options );
		}

		return true;
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
	 *
	 * @return bool
	 */
	private function save( $group, $options ) {
		foreach ( $options as $key => $value ) {
			if ( is_string( $value ) ) {
				$options[ $key ] = htmlspecialchars( $value );
			}
		}

		$this->flush( $group );

		return update_option( $this->get_option_name( $group ), $options );
	}

	/**
	 * @param null|string $group_prefix
	 *
	 * @return array
	 */
	public function get_group_options( $group_prefix = null ) {
		$prefix = $this->get_group_option_name_prefix();
		isset( $group_prefix ) and $prefix .= $group_prefix;

		/** @noinspection SqlResolve */
		return $this->app->array->pluck_unique( $this->wpdb()->get_results( $this->wpdb()->prepare(
			"SELECT option_name FROM {$this->get_wp_table('options')} WHERE option_name LIKE %s",
			str_replace( [ '\\', '%', '_' ], [ '\\\\', '\%', '\_' ], $prefix ) . '%'
		) ), 'option_name' );
	}

	/**
	 * @param string|null $group_prefix
	 */
	public function clear_group_option( $group_prefix ) {
		foreach ( $this->get_group_options( $group_prefix ) as $option ) {
			delete_option( $option );
		}
	}

	/**
	 * clear option
	 */
	public function clear_option() {
		delete_option( $this->get_option_name() );
		$this->clear_group_option( null );
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
