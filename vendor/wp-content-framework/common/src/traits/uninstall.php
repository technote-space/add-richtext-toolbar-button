<?php
/**
 * WP_Framework_Common Traits Uninstall
 *
 * @version 0.0.1
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Common\Traits;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Trait Uninstall
 * @package WP_Framework_Common\Traits
 */
trait Uninstall {

	/**
	 * uninstall
	 */
	public abstract function uninstall();

	/**
	 * @return int
	 */
	public function get_uninstall_priority() {
		return 10;
	}
}
