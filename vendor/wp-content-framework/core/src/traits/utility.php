<?php
/**
 * WP_Framework_Core Traits Utility
 *
 * @version 0.0.46
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Core\Traits;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Trait Utility
 * @package WP_Framework_Core\Traits
 * @property \WP_Framework $app
 */
trait Utility {

	/**
	 * @var string $_cache_version
	 */
	private $_cache_version;

	/**
	 * @return \wpdb
	 */
	public function wpdb() {
		/** @var \wpdb $wpdb */
		global $wpdb;

		return $wpdb;
	}

	/**
	 * @param string $table
	 * @param string $as
	 *
	 * @return string
	 */
	public function alias( $table, $as ) {
		return "{$table} as {$as}";
	}

	/**
	 * @param string $table
	 * @param null|string $as
	 *
	 * @return string
	 */
	public function get_wp_table( $table, $as = null ) {
		$table = $this->wpdb()->{$table};

		return $as ? $this->alias( $table, $as ) : $table;
	}

	/**
	 * @return \WP_Framework_Db\Classes\Models\Query\Builder
	 */
	protected function builder() {
		return $this->app->db->builder();
	}

	/**
	 * @param string $table
	 * @param null|string $as
	 *
	 * @return \WP_Framework_Db\Classes\Models\Query\Builder
	 */
	protected function table( $table, $as = null ) {
		return $this->builder()->table( $as ? $this->alias( $table, $as ) : $table );
	}

	/**
	 * @param string $table
	 * @param null|string $as
	 *
	 * @return \WP_Framework_Db\Classes\Models\Query\Builder
	 */
	protected function wp_table( $table, $as = null ) {
		return $this->table( $this->get_wp_table( $table, $as ) );
	}

	/**
	 * @param $value
	 *
	 * @return \WP_Framework_Db\Classes\Models\Query\Expression
	 */
	protected function raw( $value ) {
		return $this->app->db->get_raw( $value );
	}

	/**
	 * @return string
	 */
	public function wp_version() {
		global $wp_version;

		return $wp_version;
	}

	/**
	 * @param string $version
	 * @param string $operator
	 *
	 * @return bool
	 */
	public function compare_wp_version( $version, $operator ) {
		return version_compare( $this->wp_version(), $version, $operator );
	}

	/**
	 * @param string|mixed $method
	 *
	 * @return bool
	 */
	protected function is_method_callable( $method ) {
		return is_string( $method ) && method_exists( $this, $method ) && is_callable( [ $this, $method ] );
	}

	/**
	 * @param mixed $func
	 *
	 * @return bool
	 */
	protected function is_closure( $func ) {
		return $func instanceof \Closure;
	}

	/**
	 * @param \Closure $func
	 * @param mixed ...$args
	 *
	 * @return mixed
	 */
	protected function call_closure( $func, ...$args ) {
		return call_user_func_array( $func, $args );
	}

	/**
	 * @param mixed $func
	 * @param mixed ...$args
	 */
	protected function call_if_closure( $func, ...$args ) {
		if ( $this->is_closure( $func ) ) {
			$this->call_closure( $func, ...$args );
		}
	}

	/**
	 * @param mixed $func
	 * @param mixed $result
	 * @param mixed ...$args
	 */
	protected function call_if_closure_with_result( $func, &$result, ...$args ) {
		if ( $this->is_closure( $func ) ) {
			$result = $this->call_closure( $func, ...$args );
		}
	}

	/**
	 * @return string
	 */
	private function get_cache_version() {
		if ( ! isset( $this->_cache_version ) ) {
			$cache = $this->app->get_shared_object( '_cache_version' );
			if ( ! isset( $cache ) ) {
				$versions = [
					$this->wp_version(),
					$this->app->get_framework_version(),
					$this->app->get_plugin_version(),
					$this->app->get_config( 'config', 'db_version', '0.0.0' ),
					$this->app->utility->get_framework_plugins_hash(),
				];
				$cache    = $this->app->string->implode( $versions, '/' );
				$this->app->set_shared_object( '_cache_version', $cache );
			}
			$this->_cache_version = $cache;
		}

		return $this->_cache_version;
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 * @param false|null|string $check_version
	 *
	 * @return mixed
	 */
	public function cache_get( $key, $default = null, $check_version = false ) {
		$cache = $this->app->cache->get( $key, $this->get_class_name_slug() );
		if ( ! is_array( $cache ) || count( $cache ) !== 2 ) {
			return $default;
		}

		list( $data, $version ) = $cache;
		if ( isset( $check_version ) ) {
			false === $check_version and $check_version = $this->get_cache_version();
			if ( $version !== $check_version ) {
				$this->app->cache->delete( $key, $this->get_class_name_slug() );

				return $default;
			}
		}

		return $data;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param false|null|string $check_version
	 * @param null|int $expire
	 *
	 * @return bool
	 */
	public function cache_set( $key, $value, $check_version = false, $expire = null ) {
		false === $check_version and $check_version = $this->get_cache_version();

		return $this->app->cache->set( $key, [
			$value,
			$check_version,
		], $this->get_class_name_slug(), $expire );
	}

	/**
	 * @param bool $check_user
	 *
	 * @return string
	 */
	private function get_session_token( $check_user ) {
		$cookie = wp_parse_auth_cookie( '', 'logged_in' );

		return $check_user && ! empty( $cookie['token'] ) ? $cookie['token'] : '';
	}

	/**
	 * @see \wp_create_nonce
	 *
	 * @param string $action
	 * @param bool $check_user
	 *
	 * @return string
	 */
	protected function wp_create_nonce( $action, $check_user = true ) {
		if ( $check_user ) {
			$user = wp_get_current_user();
			$uid  = (int) $user->ID;
		} else {
			$uid = - 1;
		}

		$token = $this->get_session_token( $check_user );
		$i     = wp_nonce_tick();

		return substr( wp_hash( $i . '|' . $action . '|' . $uid . '|' . $token, 'nonce' ), - 12, 10 );
	}

	/**
	 * @see \wp_verify_nonce
	 *
	 * @param string $nonce
	 * @param string $action
	 * @param bool $check_user
	 *
	 * @return bool|int
	 */
	protected function wp_verify_nonce( $nonce, $action, $check_user = true ) {
		if ( empty( $nonce ) ) {
			return false;
		}

		if ( $check_user ) {
			$user = wp_get_current_user();
			$uid  = (int) $user->ID;
		} else {
			$uid = - 1;
		}

		$token = $this->get_session_token( $check_user );
		$i     = wp_nonce_tick();

		// Nonce generated 0-12 hours ago
		$expected = substr( wp_hash( $i . '|' . $action . '|' . $uid . '|' . $token, 'nonce' ), - 12, 10 );
		if ( hash_equals( $expected, $nonce ) ) {
			$this->do_framework_action( 'verified_nonce', 1 );

			return 1;
		}

		// Nonce generated 12-24 hours ago
		$expected = substr( wp_hash( ( $i - 1 ) . '|' . $action . '|' . $uid . '|' . $token, 'nonce' ), - 12, 10 );
		if ( hash_equals( $expected, $nonce ) ) {
			$this->do_framework_action( 'verified_nonce', 2 );

			return 2;
		}

		$this->do_framework_action( 'verify_nonce_failed', $nonce, $action, $check_user, $uid, $token );

		return false;
	}

	/**
	 * for debug
	 *
	 * @param mixed $value
	 * @param bool $exit
	 */
	protected function d( $value, $exit = true ) {
		echo '<pre>';
		var_export( $value );
		echo '</pre>';
		if ( $exit ) {
			exit;
		}
	}

	/**
	 * for debug
	 *
	 * @param mixed $value
	 * @param bool $exit
	 */
	protected function l( $value, $exit = false ) {
		error_log( print_r( $value, true ) );
		if ( $exit ) {
			exit;
		}
	}
}
