<?php
/**
 * WP_Framework_Controller Classes Controller Base
 *
 * @version 0.0.5
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Controller\Classes\Controllers;

use WP_Framework_Controller\Traits\Controller;
use WP_Framework_Core\Traits\Hook;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Base
 * @package WP_Framework_Controller\Classes\Controllers
 */
abstract class Base implements \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Controller\Interfaces\Controller {

	use Hook, Controller;

}
