<?php
/**
 * WP_Framework_Core Interfaces Hook
 *
 * @version 0.0.1
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Core\Interfaces;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Interface Hook
 * @package WP_Framework_Core\Interfaces
 */
interface Hook {

	/**
	 * @return mixed
	 */
	public function apply_filters();

	/**
	 * do action
	 */
	public function do_action();

}
