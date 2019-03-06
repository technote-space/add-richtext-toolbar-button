<?php
/**
 * WP_Framework_Core Interfaces Loader
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
 * Interface Loader
 * @package WP_Framework_Core\Interfaces
 */
interface Loader extends Singleton, Hook {

	/**
	 * @return string
	 */
	public function get_loader_name();

	/**
	 * @return array
	 */
	public function get_class_list();

	/**
	 * @return int
	 */
	public function get_loaded_count();

}
