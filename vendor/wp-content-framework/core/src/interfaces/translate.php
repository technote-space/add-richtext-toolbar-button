<?php
/**
 * WP_Framework_Core Interfaces Translate
 *
 * @version 0.0.12
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
 * Interface Translate
 * @package WP_Framework_Core\Interfaces
 */
interface Translate {

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function translate( $value );

}
