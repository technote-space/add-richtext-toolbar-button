<?php
/**
 * WP_Framework_Core Interfaces Nonce
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
 * Interface Nonce
 * @package WP_Framework_Core\Interfaces
 */
interface Nonce {

	/**
	 * @return string
	 */
	public function get_nonce_slug();

	/**
	 * @param string $nonce
	 *
	 * @return false|int
	 */
	public function verify_nonce( $nonce );

}
