<?php
/**
 * WP_Framework_Controller Interfaces Controller
 *
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Controller\Interfaces;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Interface Controller
 * @package WP_Framework_Controller\Interfaces
 */
interface Controller extends \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Presenter\Interfaces\Presenter {

	/**
	 * @return null|string|false
	 */
	public function get_capability();

}
