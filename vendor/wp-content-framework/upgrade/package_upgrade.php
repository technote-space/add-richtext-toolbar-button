<?php
/**
 * WP_Framework Package Upgrade
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
 * Class Package_Upgrade
 * @package WP_Framework
 */
class Package_Upgrade extends Package_Base {

	/**
	 * @return int
	 */
	public function get_priority() {
		return 20;
	}

	/**
	 * @return bool
	 */
	protected function is_valid_view() {
		return true;
	}

	/**
	 * @return array
	 */
	public function get_configs() {
		return [
			'config',
			'filter',
			'map',
			'setting',
		];
	}
}
