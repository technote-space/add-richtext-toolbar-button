<?php
/**
 * WP_Framework Package Custom_Post
 *
 * @version 0.0.26
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Package_Custom_Post
 * @package WP_Framework
 */
class Package_Custom_Post extends Package_Base {

	/**
	 * @return int
	 */
	public function get_priority() {
		return 120;
	}

	/**
	 * @return bool
	 */
	protected function is_valid_view() {
		return true;
	}

	/**
	 * @return bool
	 */
	protected function is_valid_translate() {
		return true;
	}

	/**
	 * @return bool
	 */
	protected function is_valid_api() {
		return true;
	}

	/**
	 * @return array
	 */
	public function get_configs() {
		return [
			'config',
			'deprecated',
			'filter',
			'map',
			'slug',
		];
	}
}
