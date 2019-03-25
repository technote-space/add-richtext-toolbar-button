<?php
/**
 * WP_Framework Package Common
 *
 * @version 0.0.39
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
 * Class Package_Common
 * @package WP_Framework
 */
class Package_Common extends Package_Base {

	/**
	 * @return int
	 */
	public function get_priority() {
		return 1;
	}

	/**
	 * @return array
	 */
	public function get_configs() {
		return [
			'capability',
			'config',
			'deprecated',
			'filter',
			'setting',
			'slug',
		];
	}
}
