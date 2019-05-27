<?php
/**
 * WP_Framework_Core Interfaces Singleton
 *
 * @version 0.0.54
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Core\Interfaces;

use WP_Framework;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Interface Singleton
 * @package WP_Framework_Core\Interfaces
 */
interface Singleton extends Readonly, Translate, Utility, Package {

	/**
	 * @param WP_Framework $app
	 *
	 * @return \WP_Framework_Core\Traits\Singleton
	 */
	public static function get_instance( WP_Framework $app );

	/**
	 * @param string $config_name
	 * @param string $suffix
	 *
	 * @return string
	 */
	public function get_slug( $config_name, $suffix = '-' );

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function is_filter_callable( $name );

	/**
	 * @param string $method
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function filter_callback( $method, array $args );

}
