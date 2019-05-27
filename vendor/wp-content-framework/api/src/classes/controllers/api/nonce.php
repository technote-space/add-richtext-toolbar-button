<?php
/**
 * WP_Framework_Api Classes Controller Api Nonce
 *
 * @version 0.0.14
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Api\Classes\Controllers\Api;

use WP_Error;
use WP_Framework_Api\Classes\Models\Api;
use WP_REST_Request;
use WP_REST_Response;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Nonce
 * @package WP_Framework_Api\Classes\Controllers\Api
 */
class Nonce extends Base {

	/**
	 * @return string
	 */
	public function get_endpoint() {
		return 'nonce';
	}

	/**
	 * @return string
	 */
	public function get_call_function_name() {
		return 'get_nonce';
	}

	/**
	 * @return string
	 */
	public function get_method() {
		return 'post';
	}

	/**
	 * @return null|string|false
	 */
	public function get_capability() {
		if ( ! $this->apply_filters( 'get_nonce_check_referer' ) ) {
			return false;
		}
		$referer = $this->app->input->server( 'HTTP_REFERER' );
		if ( $referer ) {
			$referer = parse_url( $referer );
			$referer = false === $referer ? null : $referer['host'];
		}
		if ( $referer ) {
			$http_host = $this->apply_filters( 'check_referer_host' );
			if ( ! empty( $http_host ) && stristr( $referer, $http_host ) !== false ) {
				return false;
			}
		}

		return '';
	}

	/**
	 * @param WP_REST_Request|array $params
	 *
	 * @return int|WP_Error|WP_REST_Response
	 */
	public function callback( $params ) {
		global $current_user;
		$current_user = null;

		/** @var Api $api */
		$api = Api::get_instance( $this->app );

		return new WP_REST_Response( $api->get_nonce_data() );
	}
}
