<?php
/**
 * WP_Framework_Api Classes Models Api
 *
 * @version 0.0.13
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Api\Classes\Models;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Admin
 * @package WP_Framework_Api\Classes\Models
 */
class Api implements \WP_Framework_Core\Interfaces\Loader, \WP_Framework_Presenter\Interfaces\Presenter, \WP_Framework_Core\Interfaces\Nonce {

	use \WP_Framework_Core\Traits\Loader, \WP_Framework_Presenter\Traits\Presenter, \WP_Framework_Core\Traits\Nonce, \WP_Framework_Api\Traits\Package;

	/**
	 * @var bool $_use_all_api
	 */
	private $_use_all_api = false;

	/**
	 * @var string[] $_use_apis
	 */
	private $_use_apis = [];

	/**
	 * @return bool
	 */
	private function use_admin_ajax() {
		return $this->app->utility->defined( 'WP_FRAMEWORK_MOCK_REST_REQUEST' ) || $this->apply_filters( 'use_admin_ajax' );
	}

	/**
	 * @return string
	 */
	public function get_nonce_slug() {
		return 'wp_rest';
	}

	/**
	 * @param bool $flag
	 */
	public function set_use_all_api_flag( $flag ) {
		$this->_use_all_api = $flag;
	}

	/**
	 * @param string $name
	 */
	public function add_use_api_name( $name ) {
		$this->_use_apis[ $name ] = true;
	}

	/**
	 * setup settings
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_settings() {
		if ( ! is_admin() ) {
			return;
		}
		if ( $this->app->utility->defined( 'WP_FRAMEWORK_MOCK_REST_REQUEST' ) ) {
			$this->app->setting->remove_setting( 'use_admin_ajax' );
		}
		if ( $this->is_empty() ) {
			$this->app->setting->remove_setting( 'use_admin_ajax' );
			$this->app->setting->remove_setting( 'get_nonce_check_referer' );
			$this->app->setting->remove_setting( 'check_referer_host' );
		}
	}

	/**
	 * register script
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function register_script() {
		if ( $this->app->utility->doing_ajax() ) {
			return;
		}
		if ( ! $this->_use_all_api && empty( $this->_use_apis ) ) {
			return;
		}
		if ( $this->use_admin_ajax() ) {
			$this->register_ajax_script();
		} else {
			$this->register_json_script();
		}
	}

	/**
	 * register api for wp-json
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function register_rest_api() {
		if ( $this->use_admin_ajax() || ! $this->app->utility->doing_ajax() ) {
			return;
		}
		foreach ( $this->get_api_controllers() as $api ) {
			/** @var \WP_Framework_Api\Classes\Controllers\Api\Base $api */
			register_rest_route( $this->get_api_namespace(), $api->get_endpoint(), [
				'methods'             => strtoupper( $api->get_method() ),
				'permission_callback' => function () use ( $api ) {
					return $this->app->user_can( $api->get_capability() );
				},
				'args'                => $api->get_args_setting(),
				'callback'            => [ $api, 'callback' ],
			] );
		}
	}

	/**
	 * register api for admin-ajax.php
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function register_ajax_api() {
		if ( ! $this->use_admin_ajax() || ! $this->app->utility->doing_ajax() ) {
			return;
		}
		foreach ( $this->get_api_controllers() as $api ) {
			/** @var \WP_Framework_Api\Classes\Controllers\Api\Base $api */
			$action   = $this->get_api_namespace() . '_' . $api->get_endpoint();
			$callback = function () use ( $api ) {
				$this->ajax_action_execute( $api );
			};
			add_action( 'wp_ajax_' . $action, $callback );
			add_action( 'wp_ajax_nopriv_' . $action, $callback );
		}
	}

	/**
	 * @param callable $get_view_params
	 */
	private function register_script_common( callable $get_view_params ) {
		$functions = [];
		$scripts   = [];
		if ( ! empty( $this->_use_apis ) ) {
			$this->_use_apis['get_nonce'] = true;
		}
		/** @var \WP_Framework_Api\Classes\Controllers\Api\Base $api */
		foreach ( $this->get_api_controllers() as $api ) {
			$name = $api->get_call_function_name();
			if ( ! $this->_use_all_api && empty( $this->_use_apis[ $name ] ) ) {
				continue;
			}
			$functions[ $name ] = [
				'method'   => $api->get_method(),
				'endpoint' => $api->get_endpoint(),
			];
			$script             = is_admin() ? $api->admin_script() : $api->front_script();
			if ( ! empty( $script ) ) {
				$scripts[] = $script;
			}
		}
		if ( ! empty( $functions ) ) {
			$this->add_script_view( 'include/script/api', array_merge( [
				'namespace' => $this->get_api_namespace(),
				'functions' => $functions,
			], $get_view_params( $functions ) ), 9 );
			foreach ( $scripts as $script ) {
				$this->add_script( $script );
			}
		}
	}

	/**
	 * register script for wp-json
	 */
	private function register_json_script() {
		return $this->register_script_common( function () {
			return [
				'endpoint'      => rest_url(),
				'nonce'         => $this->wp_create_nonce( $this->get_nonce_slug() ),
				'is_admin_ajax' => false,
			];
		} );
	}

	/**
	 * register script for admin-ajax.php
	 */
	private function register_ajax_script() {
		return $this->register_script_common( function () {
			return [
				'endpoint'      => $this->apply_filters( 'admin_ajax', admin_url( 'admin-ajax.php' ) ),
				'nonce_key'     => $this->get_nonce_key(),
				'nonce_value'   => $this->create_nonce(),
				'is_admin_ajax' => true,
			];
		} );
	}

	/**
	 * @return array
	 */
	public function get_nonce_data() {
		return [
			'nonce'         => $this->wp_create_nonce( $this->get_nonce_slug() ),
			'nonce_key'     => $this->get_nonce_key(),
			'nonce_value'   => $this->create_nonce(),
			'is_admin_ajax' => $this->use_admin_ajax(),
		];
	}

	/**
	 * @param \WP_Framework_Api\Classes\Controllers\Api\Base $api
	 */
	private function ajax_action_execute( \WP_Framework_Api\Classes\Controllers\Api\Base $api ) {
		$result = $this->get_ajax_action_result( $api );
		if ( ! is_wp_error( $result ) && ! ( $result instanceof \WP_REST_Response ) ) {
			$result = new \WP_REST_Response( $result );
		}
		if ( is_wp_error( $result ) ) {
			$result = $this->error_to_response( $result );
		}

		foreach ( $result->headers as $key => $value ) {
			$value = preg_replace( '/\s+/', ' ', $value );
			header( sprintf( '%s: %s', $key, $value ) );
		}
		status_header( $result->status );
		wp_send_json( $result->data );
	}

	/**
	 * @param \WP_Error $error
	 *
	 * @return \WP_REST_Response
	 */
	private function error_to_response( \WP_Error $error ) {
		$error_data = $error->get_error_data();
		if ( is_array( $error_data ) && isset( $error_data['status'] ) ) {
			$status = $error_data['status'];
		} else {
			$status = 500;
		}

		$errors = [];
		foreach ( (array) $error->errors as $code => $messages ) {
			foreach ( (array) $messages as $message ) {
				$errors[] = [ 'code' => $code, 'message' => $message, 'data' => $error->get_error_data( $code ) ];
			}
		}

		$data = $errors[0];
		if ( count( $errors ) > 1 ) {
			array_shift( $errors );
			$data['additional_errors'] = $errors;
		}
		$response = new \WP_REST_Response( $data, $status );

		return $response;
	}

	/**
	 * @param \WP_Framework_Api\Classes\Controllers\Api\Base $api
	 *
	 * @return int|\WP_Error|\WP_REST_Response
	 */
	private function get_ajax_action_result( \WP_Framework_Api\Classes\Controllers\Api\Base $api ) {
		if ( ! $this->nonce_check() ) {
			return new \WP_Error( 'rest_forbidden', 'Forbidden', [ 'status' => 403 ] );
		}
		if ( ! $this->app->user_can( $api->get_capability() ) ) {
			return new \WP_Error( 'rest_forbidden', 'Forbidden', [ 'status' => 403 ] );
		}
		if ( strtoupper( $api->get_method() ) !== $this->app->input->method() ) {
			return new \WP_Error( 'rest_no_route', __( 'No route was found matching the URL and request method' ), [ 'status' => 404 ] );
		}

		if ( ! $this->app->input->is_post() ) {
			$params = $this->app->input->get();
		} else {
			$params = $this->app->input->post();
		}

		$args           = $api->get_args_setting();
		$required       = [];
		$invalid_params = [];
		$request        = new \WP_REST_Request( $this->app->input->method() );
		$request->set_query_params( wp_unslash( $this->app->input->get() ) );
		$request->set_body_params( wp_unslash( $this->app->input->post() ) );
		foreach ( $args as $name => $setting ) {
			if ( array_key_exists( 'default', $setting ) && ! array_key_exists( $name, $params ) ) {
				$params[ $name ] = $setting['default'];
			}
			if ( ! isset( $params[ $name ] ) ) {
				if ( ! empty( $setting['required'] ) ) {
					$required[] = $name;
				}
				continue;
			}
			if ( ! empty( $setting['validate_callback'] ) ) {
				$valid_check = call_user_func( $setting['validate_callback'], $params[ $name ], $request, $name );
				if ( false === $valid_check ) {
					$invalid_params[ $name ] = __( 'Invalid parameter.' );
					continue;
				}
				if ( is_wp_error( $valid_check ) ) {
					$invalid_params[ $name ] = $valid_check->get_error_message();
					continue;
				}
			}
			if ( ! empty( $setting['sanitize_callback'] ) && is_callable( $setting['sanitize_callback'] ) ) {
				$sanitized_value = call_user_func( $setting['sanitize_callback'], $params[ $name ], $request, $name );
				if ( is_wp_error( $sanitized_value ) ) {
					$invalid_params[ $name ] = $sanitized_value->get_error_message();
				} else {
					$params[ $name ] = $sanitized_value;
				}
			}
		}

		if ( ! empty( $required ) ) {
			return new \WP_Error( 'rest_missing_callback_param', sprintf( __( 'Missing parameter(s): %s' ), implode( ', ', $required ) ), [
				'status' => 400,
				'params' => $required,
			] );
		}

		if ( $invalid_params ) {
			return new \WP_Error( 'rest_invalid_param', sprintf( __( 'Invalid parameter(s): %s' ), implode( ', ', array_keys( $invalid_params ) ) ), [
				'status' => 400,
				'params' => $invalid_params,
			] );
		}

		return $api->callback( $params );
	}

	/**
	 * @return bool
	 */
	protected function need_nonce_check() {
		if ( $this->app->input->request( 'action' ) === $this->get_api_namespace() . '_nonce' ) {
			return false;
		}

		return true;
	}

	/**
	 * @return array
	 */
	protected function get_namespaces() {
		$namespaces = [ $this->app->define->plugin_namespace . '\\Classes\\Controllers\\Api\\' ];
		foreach ( $this->app->get_packages() as $package ) {
			foreach ( $package->get_api_namespaces() as $namespace ) {
				$namespaces[] = $namespace;
			}
		}

		return $namespaces;
	}

	/**
	 * @return string
	 */
	protected function get_instanceof() {
		return '\WP_Framework_Api\Classes\Controllers\Api\Base';
	}

	/**
	 * @return array
	 */
	private function get_api_controllers() {
		if ( $this->is_empty() ) {
			return [];
		}

		$api_controllers = $this->get_class_list();
		if ( ! $this->app->utility->doing_ajax() ) {
			/** @var \WP_Framework_Api\Classes\Controllers\Api\Base $class */
			foreach ( $api_controllers as $name => $class ) {
				if ( ! $class->is_valid() || ( is_admin() && $class->is_only_front() ) || ( ! is_admin() && $class->is_only_admin() ) ) {
					unset( $api_controllers[ $name ] );
				}
			}
		}

		return $api_controllers;
	}

	/**
	 * @param mixed $result
	 * @param \WP_REST_Server $server
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function rest_pre_dispatch(
		/** @noinspection PhpUnusedParameterInspection */
		$result,
		\WP_REST_Server $server,
		\WP_REST_Request $request
	) {
		if ( $this->use_admin_ajax() ) {
			return $result;
		}

		$namespaces = $request->get_route();
		if ( strpos( $namespaces, $this->get_api_namespace() ) === 1 ) {
			return null;
		}

		return $result;
	}

	/**
	 * @return string
	 */
	private function get_api_namespace() {
		if ( $this->use_admin_ajax() ) {
			return $this->get_slug( 'api_namespace', '' );
		}

		return $this->get_slug( 'api_namespace', '' ) . '/' . $this->app->get_config( 'config', 'api_version' );
	}

	/**
	 * @return bool
	 */
	public function is_empty() {
		// 1 は nonce 更新用のライブラリ提供のAPI

		$cache = $this->cache_get( 'is_empty' );
		if ( isset( $cache ) ) {
			return $cache;
		}

		$result = $this->get_loaded_count() <= 1;
		$this->cache_set( 'is_empty', $result );

		return $result;
	}
}
