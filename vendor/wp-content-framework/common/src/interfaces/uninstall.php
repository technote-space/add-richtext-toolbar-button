<?php
/**
 * WP_Framework_Common Interfaces Uninstall
 *
 * @version 0.0.1
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Common\Interfaces;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Interface Uninstall
 * @package WP_Framework_Common\Interfaces
 */
interface Uninstall {

	/**
	 * uninstall
	 */
	public function uninstall();

	/**
	 * @return int
	 */
	public function get_uninstall_priority();

}
