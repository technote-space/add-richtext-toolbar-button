<?php
/**
 * WP_REST_Response
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

define( 'WP_FRAMEWORK_MOCK_REST_RESPONSE', ! class_exists( 'WP_REST_Response' ) );

if ( WP_FRAMEWORK_MOCK_REST_RESPONSE ) {
	// < v4.4

	/**
	 * Class WP_REST_Response
	 */
	class WP_REST_Response {
		/**
		 * Response data.
		 * @var mixed
		 */
		public $data;

		/**
		 * Response headers.
		 *
		 * @var array
		 */
		public $headers;

		/**
		 * Response status.
		 *
		 * @var int
		 */
		public $status;

		/**
		 * Constructor.
		 *
		 * @param mixed $data Response data. Default null.
		 * @param int $status Optional. HTTP status code. Default 200.
		 * @param array $headers Optional. HTTP header map. Default empty array.
		 */
		public function __construct( $data = null, $status = 200, $headers = [] ) {
			$this->data    = $data;
			$this->status  = $status;
			$this->headers = $headers;
		}
	}
}
