<?php
/**
 * WP_Framework_Common Classes Models Uninstall
 *
 * @version 0.0.1
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Common\Classes\Models;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Uninstall
 * @package WP_Framework_Common\Classes\Models
 */
class Uninstall implements \WP_Framework_Core\Interfaces\Loader {

	use \WP_Framework_Core\Traits\Loader, \WP_Framework_Common\Traits\Package;

	/**
	 * @var callable[] $_uninstall
	 */
	private $_uninstall = [];

	/**
	 * register uninstall
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function register_uninstall() {
		if ( $this->app->is_theme ) {
			return;
		}
		register_uninstall_hook( $this->app->define->plugin_base_name, [
			"\WP_Framework",
			"register_uninstall_" . $this->app->define->plugin_base_name,
		] );
	}

	/**
	 * @return array
	 */
	protected function get_namespaces() {
		return [
			$this->app->define->plugin_namespace . '\\Classes',
		];
	}

	/**
	 * @return string
	 */
	protected function get_instanceof() {
		return '\WP_Framework_Common\Interfaces\Uninstall';
	}

	/**
	 * uninstall
	 */
	public function uninstall() {
		$uninstall        = $this->_uninstall;
		$this->_uninstall = [];
		ksort( $uninstall );
		if ( ! is_multisite() ) {
			foreach ( $uninstall as $priority => $items ) {
				foreach ( $items as $item ) {
					if ( is_callable( $item ) ) {
						call_user_func( $item );
					}
				}
			}
		} else {
			/** @var \wpdb $wpdb */
			global $wpdb;
			$current_blog_id = get_current_blog_id();
			$blog_ids        = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );

				foreach ( $uninstall as $priority => $items ) {
					foreach ( $items as $item ) {
						if ( is_callable( $item ) ) {
							call_user_func( $item );
						}
					}
				}
			}
			switch_to_blog( $current_blog_id );
		}
	}

	/**
	 * @param callable $callback
	 * @param int $priority
	 */
	public function add_uninstall( callable $callback, $priority = 10 ) {
		$this->_uninstall[ $priority ][] = $callback;
	}
}
