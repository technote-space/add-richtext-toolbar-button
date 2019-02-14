<?php
/**
 * WP_Framework_Core Traits Nonce
 *
 * @version 0.0.21
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Core\Traits;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Trait Nonce
 * @package WP_Framework_Core\Traits
 * @property \WP_Framework $app
 * @mixin Hook
 */
trait Nonce {

	/**
	 * @return string
	 */
	abstract public function get_nonce_slug();

	/**
	 * @return string
	 */
	private function get_nonce_key() {
		$slug       = $this->get_slug( 'nonce_key', '_nonce' );
		$nonce_slug = $this->get_nonce_slug();

		return $this->apply_filters( 'get_nonce_key', $slug . '_' . $nonce_slug, $slug, $nonce_slug );
	}

	/**
	 * @return string
	 */
	private function get_nonce_action() {
		$slug       = $this->get_slug( 'nonce_action', '_nonce_action' );
		$nonce_slug = $this->get_nonce_slug();

		return $this->apply_filters( 'get_nonce_action', $slug . '_' . $nonce_slug, $slug, $nonce_slug );
	}

	/**
	 * @param bool $check_user
	 *
	 * @return string
	 */
	protected function create_nonce( $check_user = true ) {
		return $this->wp_create_nonce( $this->get_nonce_action(), $check_user );
	}

	/**
	 * @param string $action
	 * @param bool $check_user
	 *
	 * @return string
	 */
	private function wp_create_nonce( $action, $check_user = true ) {
		if ( $check_user ) {
			$user = wp_get_current_user();
			$uid  = (int) $user->ID;
		} else {
			$uid = - 1;
		}

		$token = wp_get_session_token();
		$i     = wp_nonce_tick();

		return substr( wp_hash( $i . '|' . $action . '|' . $uid . '|' . $token, 'nonce' ), - 12, 10 );
	}

	/**
	 * @param bool $check_user
	 *
	 * @return bool
	 */
	private function nonce_check( $check_user = true ) {
		$nonce_key = $this->get_nonce_key();

		return ! $this->need_nonce_check( $nonce_key ) || $this->verify_nonce( $this->app->input->request( $nonce_key, '' ), $check_user );
	}

	/**
	 * @param string $nonce
	 * @param bool $check_user
	 *
	 * @return false|int
	 */
	public function verify_nonce( $nonce, $check_user = true ) {
		return $nonce && $this->wp_verify_nonce( $nonce, $this->get_nonce_action(), $check_user );
	}

	/**
	 * @param string $nonce
	 * @param string $action
	 * @param bool $check_user
	 *
	 * @return bool|int
	 */
	private function wp_verify_nonce( $nonce, $action, $check_user = true ) {
		if ( empty( $nonce ) ) {
			return false;
		}

		if ( $check_user ) {
			$user = wp_get_current_user();
			$uid  = (int) $user->ID;
		} else {
			$uid = - 1;
		}

		$token = wp_get_session_token();
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
	 * @return bool
	 */
	protected function is_post() {
		return $this->app->input->is_post();
	}

	/**
	 * @param string $nonce_key
	 *
	 * @return bool
	 */
	protected function need_nonce_check(
		/** @noinspection PhpUnusedParameterInspection */
		$nonce_key
	) {
		return $this->is_post();
	}
}
