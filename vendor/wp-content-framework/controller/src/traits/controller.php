<?php
/**
 * WP_Framework_Controller Traits Controller
 *
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Controller\Traits;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Trait Controller
 * @package WP_Framework_Controller\Traits
 * @property \WP_Framework $app
 */
trait Controller {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Presenter\Traits\Presenter;

}
