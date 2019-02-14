<?php
/**
 * WP_Framework Package Controller
 *
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Package_Controller
 * @package WP_Framework
 */
class Package_Controller extends Package_Base {

	/**
	 * initialize
	 */
	protected function initialize() {

	}

	/**
	 * @return int
	 */
	public function get_priority() {
		return 20;
	}
}
