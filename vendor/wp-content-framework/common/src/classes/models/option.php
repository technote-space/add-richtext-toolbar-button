<?php
/**
 * WP_Framework_Common Classes Models Option
 *
 * @version 0.0.17
 * @author technote-space
 * @copyright technote-space All Rights Reserved
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
	 * @var bool $_initialized
	 */
	private $_initialized = false;

	/**
	 * initialize
	 */
	protected function initialize() {
		$this->reload_options();
		$this->_initialized = true;
	}

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
	 * reload options
	 */
	public function reload_options() {
		if ( $this->_suspend_reload ) {
			return;
		}
		$this->_options = wp_parse_args(
			$this->get_option(), []
		);
		$this->unescape_options();
	}

	/**
	 * @return array
	 */
	private function get_option() {
		if ( $this->_initialized ) {
			if ( function_exists( 'wp_cache_flush' ) ) {
				wp_cache_flush();
			}
		}

		return get_option( $this->get_option_name(), [] );
	}

	/**
	 * @return string
	 */
	public function get_option_name() {
		return $this->apply_filters( 'get_option_name', $this->get_slug( 'option_name', '_options' ) );
	}

	/**
	 * unescape options
	 */
	private function unescape_options() {
		foreach ( $this->_options as $key => $value ) {
			if ( is_string( $value ) ) {
				$this->_options[ $key ] = stripslashes( htmlspecialchars_decode( $this->_options[ $key ] ) );
			}
		}
	}

	/**
	 * @param string $key
	 * @param string $default
	 *
	 * @return mixed
	 */
	public function get( $key, $default = '' ) {
		if ( array_key_exists( $key, $this->_options ) ) {
			return $this->apply_filters( 'get_option', $this->_options[ $key ], $key, $default );
		}

		return $this->apply_filters( 'get_option', $default, $key, $default );
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function set( $key, $value ) {
		$this->reload_options();
		$suspend_reload         = $this->_suspend_reload;
		$prev                   = isset( $this->_options[ $key ] ) ? $this->_options[ $key ] : null;
		$this->_options[ $key ] = $value;
		if ( $prev !== $value ) {
			$this->_suspend_reload = true;
			$this->do_action( 'changed_option', $key, $value, $prev );
			$this->_suspend_reload = $suspend_reload;
		}

		return $this->save();
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	public function delete( $key ) {
		$this->reload_options();
		$suspend_reload = $this->_suspend_reload;
		if ( array_key_exists( $key, $this->_options ) ) {
			$prev = $this->_options[ $key ];
			unset( $this->_options[ $key ] );
			$this->_suspend_reload = true;
			$this->do_action( 'deleted_option', $key, $prev );
			$this->_suspend_reload = $suspend_reload;

			return $this->save();
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
	 * @return bool
	 */
	private function save() {
		$options = $this->_options;
		foreach ( $options as $key => $value ) {
			if ( is_string( $value ) ) {
				$options[ $key ] = htmlspecialchars( $value );
			}
		}

		return update_option( $this->get_option_name(), $options );
	}

	/**
	 * clear option
	 */
	public function clear_option() {
		delete_option( $this->get_option_name() );
		$this->initialize();
	}

	/**
	 * uninstall
	 */
	public function uninstall() {
		delete_option( $this->get_option_name() );
	}

	/**
	 * @return int
	 */
	public function get_uninstall_priority() {
		return 200;
	}
}
