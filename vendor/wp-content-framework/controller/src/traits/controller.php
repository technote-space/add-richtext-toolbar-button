<?php
/**
 * WP_Framework_Controller Traits Controller
 *
 * @version 0.0.5
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Controller\Traits;

use WP_Framework;
use WP_Framework_Core\Traits\Singleton;
use WP_Framework_Presenter\Traits\Presenter;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Trait Controller
 * @package WP_Framework_Controller\Traits
 * @property WP_Framework $app
 */
trait Controller {

	use Singleton, Presenter;

}
