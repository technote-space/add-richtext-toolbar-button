<?php
/**
 * WP_Framework_Admin Interfaces Controller Admin
 *
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Admin\Interfaces\Controller;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Interface Admin
 * @package WP_Framework_Admin\Interfaces\Controller
 */
interface Admin extends \WP_Framework_Controller\Interfaces\Controller, \WP_Framework_Core\Interfaces\Nonce {

	/**
	 * @return string
	 */
	public function get_page_title();

	/**
	 * @return string
	 */
	public function get_menu_name();

	/**
	 * @param string $relative_namespace
	 */
	public function set_relative_namespace( $relative_namespace );

	/**
	 * @return string
	 */
	public function get_page_slug();

	/**
	 * @return string
	 */
	public function presenter();

	/**
	 * setup help
	 */
	public function setup_help();

}
