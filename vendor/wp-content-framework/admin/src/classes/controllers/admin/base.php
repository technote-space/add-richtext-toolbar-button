<?php
/**
 * WP_Framework_Admin Classes Controller Admin Base
 *
 * @version 0.0.1
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Admin\Classes\Controllers\Admin;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Base
 * @package WP_Framework_Admin\Classes\Controllers\Admin
 */
abstract class Base extends \WP_Framework_Controller\Classes\Controllers\Base implements \WP_Framework_Admin\Interfaces\Controller\Admin, \WP_Framework_Admin\Interfaces\Admin {

	use \WP_Framework_Admin\Traits\Controller\Admin, \WP_Framework_Admin\Traits\Admin, \WP_Framework_Admin\Traits\Package;

}
