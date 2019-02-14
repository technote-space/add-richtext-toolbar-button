<?php
/**
 * WP_Framework_Cron Classes Models Cron
 *
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Cron\Classes\Models;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Cron
 * @package WP_Framework_Cron\Classes\Models
 */
class Cron implements \WP_Framework_Core\Interfaces\Loader {

	use \WP_Framework_Core\Traits\Loader, \WP_Framework_Cron\Traits\Package;

	/**
	 * load
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function load() {
		$this->get_class_list();
	}

	/**
	 * @return string
	 */
	protected function get_instanceof() {
		return '\WP_Framework_Cron\Classes\Crons\Base';
	}

	/**
	 * @return array
	 */
	public function get_cron_class_names() {
		$list = $this->get_class_list();

		return array_keys( $list );
	}

	/**
	 * @return array
	 */
	protected function get_namespaces() {
		$namespaces   = [];
		$namespaces[] = $this->app->define->plugin_namespace . '\\Classes\\Crons';
		if ( $this->is_valid_package( 'log' ) ) {
			$namespaces[] = $this->get_package_namespace( 'log' );
		}

		return $namespaces;
	}
}
