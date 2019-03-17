<?php
/**
 * WP_Framework_Session Classes Models Session
 *
 * @version 0.0.1
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Session\Classes\Models;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Session
 * @package WP_Framework_Session\Classes\Models
 */
class Session implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Core\Traits\Hook, \WP_Framework_Session\Traits\Package;

	/**
	 * @var bool $_session_initialized
	 */
	private static $_session_initialized = false;

	/**
	 * @var bool $_is_valid_session
	 */
	private static $_is_valid_session = false;

	/**
	 * @var bool $_session_regenerated
	 */
	private static $_session_regenerated = false;

	/**
	 * initialize
	 */
	protected function initialize() {
		if ( ! self::$_session_initialized ) {
			self::$_session_initialized = true;
			$this->check_session();
		}
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	public function get_session_key( $key ) {
		return $this->apply_filters( 'session_key', $this->get_slug( 'session_name', '-session' ) ) . '-' . $key;
	}

	/**
	 * @return string
	 */
	private function get_user_check_name() {
		return $this->apply_filters( 'session_user_name', 'user_check' );
	}

	/**
	 * check
	 */
	private function check_session() {
		if ( ! isset( $_SESSION ) ) {
			@session_start();
		}
		if ( isset( $_SESSION ) ) {
			self::$_is_valid_session = true;
		}
		$this->security_process();
	}

	/**
	 * security
	 */
	private function security_process() {
		$check = $this->get( $this->get_user_check_name() );
		if ( ! isset( $check ) ) {
			$this->set( $this->get_user_check_name(), $this->app->user->user_id );
		} else {
			if ( $check != $this->app->user->user_id ) {
				// prevent session fixation
				$this->regenerate();
				$this->set( $this->get_user_check_name(), $this->app->user->user_id );
			}
		}
	}

	/**
	 * regenerate
	 */
	public function regenerate() {
		if ( self::$_is_valid_session ) {
			if ( ! self::$_session_regenerated ) {
				self::$_session_regenerated = true;
				session_regenerate_id( true );
			}
		}
	}

	/**
	 * destroy
	 */
	public function destroy() {
		if ( self::$_is_valid_session ) {
			$_SESSION = [];
			setcookie( session_name(), '', time() - 1, '/' );
			session_destroy();
			self::$_is_valid_session = false;
		}
	}

	/**
	 * @param mixed $data
	 *
	 * @return bool
	 */
	private function _expired( $data ) {
		if ( ! isset( $data['expire'] ) ) {
			return false;
		}

		return $data['expire'] < time();
	}

	/**
	 * @param string $key
	 * @param mixed $data
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	private function _get( $key, $data, $default ) {
		if ( ! is_array( $data ) || ! array_key_exists( 'value', $data ) ) {
			return $default;
		}
		if ( $this->_expired( $data ) ) {
			$this->_delete( $key );

			return $default;
		}

		return $data['value'];
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param int|null $duration
	 */
	private function _set( $key, $value, $duration = null ) {
		$data = [
			'value' => $value,
		];
		if ( isset( $duration ) && $duration > 0 ) {
			$data['expire'] = time() + $duration;
		}
		$_SESSION[ $key ] = $data;
	}

	/**
	 * @param string $key
	 */
	private function _delete( $key ) {
		unset( $_SESSION[ $key ] );
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	public function expired( $key ) {
		if ( ! self::$_is_valid_session ) {
			return false;
		}
		$key = $this->get_session_key( $key );
		if ( ! array_key_exists( $key, $_SESSION ) ) {
			return false;
		}

		return $this->_expired( $_SESSION[ $key ] );
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		if ( ! self::$_is_valid_session ) {
			return $default;
		}
		$key = $this->get_session_key( $key );
		if ( array_key_exists( $key, $_SESSION ) ) {
			return $this->_get( $key, $_SESSION[ $key ], $default );
		}

		return $default;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param int|null $duration
	 */
	public function set( $key, $value, $duration = null ) {
		if ( ! self::$_is_valid_session ) {
			return;
		}
		$key = $this->get_session_key( $key );
		$this->_set( $key, $value, $duration );
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	public function exists( $key ) {
		if ( ! self::$_is_valid_session ) {
			return false;
		}

		$key = $this->get_session_key( $key );
		if ( ! array_key_exists( $key, $_SESSION ) ) {
			return false;
		}

		return ! $this->_expired( $_SESSION[ $key ] );
	}

	/**
	 * @param string $key
	 */
	public function delete( $key ) {
		if ( ! self::$_is_valid_session ) {
			return;
		}
		$key = $this->get_session_key( $key );
		if ( array_key_exists( $key, $_SESSION ) ) {
			$this->_delete( $key );
		}
	}
}
