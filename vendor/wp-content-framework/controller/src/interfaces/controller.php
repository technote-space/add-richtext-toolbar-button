<?php
/**
 * WP_Framework_Controller Interfaces Controller
 *
 * @version 0.0.5
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Controller\Interfaces;

use WP_Framework_Core\Interfaces\Singleton;
use WP_Framework_Presenter\Interfaces\Presenter;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Interface Controller
 * @package WP_Framework_Controller\Interfaces
 */
interface Controller extends Singleton, Presenter {

	/**
	 * @return null|string|false
	 */
	public function get_capability();

}
