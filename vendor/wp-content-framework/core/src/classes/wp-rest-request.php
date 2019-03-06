<?php
/**
 * WP_REST_Request
 *
 * @version 0.0.1
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}

define( 'WP_FRAMEWORK_MOCK_REST_REQUEST', ! class_exists( 'WP_REST_Request' ) );

if ( WP_FRAMEWORK_MOCK_REST_REQUEST ) {
	// < v4.4

	/**
	 * Class WP_REST_Request
	 */
	class WP_REST_Request {
		/**
		 * HTTP method.
		 *
		 * @var string
		 */
		protected $method = '';

		/**
		 * Route matched for the request.
		 *
		 * @var string
		 */
		protected $route;

		/**
		 * Attributes (options) for the route that was matched.
		 *
		 * @var array Attributes for the request.
		 */
		protected $attributes = [];

		/**
		 * Parameters passed to the request.
		 *
		 * @var array Contains GET, POST and FILES keys mapping to arrays of data.
		 */
		protected $params;

		/**
		 * Constructor.
		 *
		 * @param string $method Optional. Request method. Default empty.
		 * @param string $route Optional. Request route. Default empty.
		 * @param array $attributes Optional. Request attributes. Default empty array.
		 */
		public function __construct( $method = '', $route = '', $attributes = [] ) {
			$this->method     = strtoupper( $method );
			$this->route      = $route;
			$this->attributes = $attributes;
		}

		/**
		 * Sets parameters from the query string.
		 *
		 * @param array $params Parameter map of key to value.
		 */
		public function set_query_params( $params ) {
			$this->params['GET'] = $params;
		}

		/**
		 * Sets parameters from the body.
		 *
		 * @param array $params Parameter map of key to value.
		 */
		public function set_body_params( $params ) {
			$this->params['POST'] = $params;
		}

		/**
		 * Retrieves a parameter from the request.
		 *
		 * @param string $key Parameter name.
		 *
		 * @return mixed|null Value if set, null otherwise.
		 */
		public function get_param( $key ) {
			foreach ( [ 'POST', 'GET' ] as $method ) {
				if ( isset( $this->params[ $method ][ $key ] ) ) {
					return $this->params[ $method ][ $key ];
				}
			}

			return null;
		}
	}
}
