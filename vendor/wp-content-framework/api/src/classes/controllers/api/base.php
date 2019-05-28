<?php
/**
 * WP_Framework_Api Classes Controller Api Base
 *
 * @version 0.0.14
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Api\Classes\Controllers\Api;

use WP_Error;
use WP_Framework_Api\Traits\Package;
use WP_Framework_Core\Traits\Helper\Validate;
use WP_REST_Request;
use WP_REST_Response;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Base
 * @package WP_Framework_Api\Classes\Controllers\Api
 */
abstract class Base extends \WP_Framework_Controller\Classes\Controllers\Base implements \WP_Framework_Core\Interfaces\Helper\Validate {

	use Validate, Package;

	/**
	 * @return string
	 */
	public abstract function get_endpoint();

	/**
	 * @return string
	 */
	public abstract function get_call_function_name();

	/**
	 * @return string
	 */
	public abstract function get_method();

	/**
	 * @return array
	 */
	public function get_args_setting() {
		return [];
	}

	/**
	 * @return bool
	 */
	public function is_valid() {
		return true;
	}

	/**
	 * @return bool
	 */
	public function is_only_admin() {
		return false;
	}

	/**
	 * @return bool
	 */
	public function is_only_front() {
		return false;
	}

	/**
	 * @return false|string
	 */
	public function common_script() {
		return false;
	}

	/**
	 * @return false|string
	 */
	public function admin_script() {
		return $this->common_script();
	}

	/**
	 * @return false|string
	 */
	public function front_script() {
		return $this->common_script();
	}

	/**
	 * @param WP_REST_Request|array $params
	 *
	 * @return int|WP_Error|WP_REST_Response
	 */
	public function callback(
		/** @noinspection PhpUnusedParameterInspection */
		$params
	) {
		return new WP_REST_Response( null, 404 );
	}
}
