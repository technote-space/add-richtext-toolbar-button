<?php
/**
 * WP_Framework_Upgrade Interfaces Upgrade
 *
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Upgrade\Interfaces;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Interface Upgrade
 * @package WP_Framework_Upgrade\Interfaces
 */
interface Upgrade {

	/**
	 * @return array { array {
	 * @type string $version target version
	 * @type callable|string $callback upgrade callback or method name
	 * } }
	 */
	public function get_upgrade_methods();

}
