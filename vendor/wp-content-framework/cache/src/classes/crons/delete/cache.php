<?php
/**
 * WP_Framework_Cache Crons Delete Cache
 *
 * @version 0.0.13
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Cache\Classes\Crons\Delete;

use WP_Framework_Cron\Classes\Crons\Base;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Cache
 * @package WP_Framework_Cache\Classes\Crons\Delete
 */
class Cache extends Base {

	/**
	 * @return int
	 */
	protected function get_interval() {
		/** @var \WP_Framework_Cache\Classes\Models\Cache $cache */
		$cache = \WP_Framework_Cache\Classes\Models\Cache::get_instance( $this->app );
		if ( ! $cache->is_valid_cron_delete() ) {
			return - 1;
		}

		return $this->apply_filters( 'delete_cache_interval' );
	}

	/**
	 * @return string
	 */
	protected function get_hook_name() {
		return $this->get_hook_prefix() . 'delete_cache';
	}

	/**
	 * execute
	 */
	protected function execute() {
		/** @var \WP_Framework_Cache\Classes\Models\Cache $cache */
		$cache = \WP_Framework_Cache\Classes\Models\Cache::get_instance( $this->app );
		$this->app->log( 'delete cache', $cache->delete_expired_cache() );
	}
}
