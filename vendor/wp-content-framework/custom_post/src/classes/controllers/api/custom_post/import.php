<?php
/**
 * WP_Framework_Custom_Post Classes Controller Api Custom Post Import
 *
 * @version 0.0.34
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Custom_Post\Classes\Controllers\Api\Custom_Post;

use WP_Error;
use WP_Framework_Api\Classes\Controllers\Api\Base;
use WP_Framework_Custom_Post\Interfaces\Custom_Post;
use WP_REST_Request;
use WP_REST_Response;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Import
 * @package WP_Framework_Custom_Post\Classes\Controllers\Api\Custom_Post
 */
class Import extends Base {

	/**
	 * @return string
	 */
	public function get_endpoint() {
		return 'import_custom_post';
	}

	/**
	 * @return string
	 */
	public function get_call_function_name() {
		return 'import_custom_post';
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
		if ( ! is_user_logged_in() ) {
			return '';
		}

		if ( empty( $_FILES['import']['tmp_name'] ) || ! is_uploaded_file( $_FILES['import']['tmp_name'] ) ) {
			return '';
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function get_args_setting() {
		return [
			'post_type' => [
				'required'          => true,
				'description'       => 'post type',
				'validate_callback' => function ( $var ) {
					/** @var \WP_Framework_Custom_Post\Classes\Models\Custom_Post $_custom_post */
					$_custom_post = \WP_Framework_Custom_Post\Classes\Models\Custom_Post::get_instance( $this->app );
					if ( ! $_custom_post->is_valid_custom_post_type( $var ) ) {
						return false;
					}

					/** @var Custom_Post $custom_post */
					$custom_post = $_custom_post->get_custom_post_type( $var );
					if ( empty( $custom_post ) ) {
						return false;
					}

					if ( ! $custom_post->is_support_io() ) {
						return false;
					}

					return true;
				},
			],
		];
	}

	/**
	 * @return bool
	 */
	public function is_only_admin() {
		return true;
	}

	/**
	 * @param WP_REST_Request|array $params
	 *
	 * @return int|WP_Error|WP_REST_Response
	 */
	public function callback( $params ) {
		/** @var \WP_Framework_Custom_Post\Classes\Models\Custom_Post $_custom_post */
		$_custom_post = \WP_Framework_Custom_Post\Classes\Models\Custom_Post::get_instance( $this->app );
		/** @var Custom_Post $custom_post */
		$custom_post = $_custom_post->get_custom_post_type( $params['post_type'] );
		list( $result, $message, $success, $fail ) = $custom_post->import( @file_get_contents( $_FILES['import']['tmp_name'] ) );

		return new WP_REST_Response( [
			'result'  => $result,
			'message' => $message,
			'success' => $success,
			'fail'    => $fail,
		] );
	}
}
