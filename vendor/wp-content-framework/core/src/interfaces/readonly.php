<?php
/**
 * WP_Framework_Core Interfaces Readonly
 *
 * @version 0.0.54
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Core\Interfaces;

use OutOfRangeException;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Interface Readonly
 * @package WP_Framework_Core\Interfaces
 */
interface Readonly {

	/**
	 * @param string $name
	 * @param mixed $value
	 *
	 * @throws OutOfRangeException
	 */
	public function __set( $name, $value );

	/**
	 * @param string $name
	 *
	 * @return mixed
	 * @throws OutOfRangeException
	 */
	public function __get( $name );

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function __isset( $name );

}
