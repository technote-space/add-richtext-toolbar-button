<?php
/**
 * WP_Framework_Core Traits Nonce
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
 * Trait Nonce
 * @package WP_Framework_Core\Traits
 * @property WP_Framework $app
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
	protected function get_nonce_key() {
		$slug       = $this->get_slug( 'nonce_key', '_nonce' );
		$nonce_slug = $this->get_nonce_slug();

		return $this->apply_filters( 'get_nonce_key', $slug . '_' . $nonce_slug, $slug, $nonce_slug );
	}

	/**
	 * @return string
	 */
	protected function get_nonce_action() {
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
	 * @param bool $check_user
	 *
	 * @return bool
	 */
	protected function nonce_check( $check_user = true ) {
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
