<?php
/**
 * WP_Framework_Common Classes Models Uninstall
 *
 * @version 0.0.49
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Common\Classes\Models;

use WP_Framework_Common\Traits\Package;
use WP_Framework_Core\Traits\Loader;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Uninstall
 * @package WP_Framework_Common\Classes\Models
 */
class Uninstall implements \WP_Framework_Core\Interfaces\Loader {

	use Loader, Package;

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
	 * @return bool
	 */
	protected function is_common_cache_class_settings() {
		return true;
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
					$this->call_if_closure( $item );
				}
			}
		} else {
			$current_blog_id = get_current_blog_id();
			/** @noinspection SqlResolve */
			$blog_ids = $this->wpdb()->get_col( "SELECT blog_id FROM {$this->get_wp_table('blogs')}" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );

				foreach ( $uninstall as $priority => $items ) {
					foreach ( $items as $item ) {
						$this->call_if_closure( $item );
					}
				}
				delete_option( WP_FRAMEWORK_VENDOR_NAME );
			}
			switch_to_blog( $current_blog_id );
		}
		delete_site_option( WP_FRAMEWORK_VENDOR_NAME );
	}

	/**
	 * @param callable $callback
	 * @param int $priority
	 */
	public function add_uninstall( callable $callback, $priority = 10 ) {
		$this->_uninstall[ $priority ][] = $callback;
	}
}
