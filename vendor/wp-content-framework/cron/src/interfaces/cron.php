<?php
/**
 * WP_Framework_Cron Interfaces Cron
 *
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Cron\Interfaces;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Interface Cron
 * @package WP_Framework_Cron\Interfaces
 */
interface Cron extends \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Common\Interfaces\Uninstall {

	/**
	 * run
	 */
	public function run();

	/**
	 * run now
	 */
	public function run_now();

}
