<?php
/**
 * WP_Framework_Cron Traits Cron
 *
 * @version 0.0.6
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Cron\Traits;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Trait Cron
 * @package WP_Framework_Cron\Traits
 * @property \WP_Framework $app
 */
trait Cron {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Core\Traits\Hook, \WP_Framework_Common\Traits\Uninstall;

	/**
	 * initialize
	 */
	protected final function initialize() {
		add_action( $this->get_hook_name(), function () {
			$this->run();
		} );
		$this->set_cron_event();
	}

	/**
	 * set cron event
	 */
	private function set_cron_event() {
		$interval = $this->get_interval();
		if ( $interval > 0 ) {
			if ( ! wp_next_scheduled( $this->get_hook_name() ) ) {
				if ( $this->is_running_cron_process() ) {
					return;
				}
				wp_schedule_single_event( time() + $interval, $this->get_hook_name() );
			}
		}
	}

	/**
	 * @return bool
	 */
	private function is_running_cron_process() {
		if ( get_site_transient( $this->get_transient_key() ) ) {
			return true;
		}

		return false;
	}

	/**
	 * lock
	 */
	private function lock_cron_process() {
		set_site_transient( $this->get_transient_key(), microtime(), $this->apply_filters( 'cron_process_expire', $this->get_expire(), $this->get_hook_name() ) );
	}

	/**
	 * unlock
	 */
	private function unlock_cron_process() {
		delete_site_transient( $this->get_transient_key() );
	}

	/**
	 * @return int
	 */
	protected function get_interval() {
		return - 1;
	}

	/**
	 * @return int
	 */
	protected function get_expire() {
		return 10 * MINUTE_IN_SECONDS;
	}

	/**
	 * @return string
	 */
	protected function get_hook_prefix() {
		return $this->app->slug_name . '-';
	}

	/**
	 * @return string
	 */
	protected function get_hook_name() {
		return $this->get_hook_prefix() . $this->get_file_slug();
	}

	/**
	 * @return string
	 */
	protected function get_transient_key() {
		return $this->get_hook_name() . '-transient';
	}

	/**
	 * clear event
	 */
	protected function clear_event() {
		wp_clear_scheduled_hook( $this->get_hook_name() );
	}

	/**
	 * run
	 */
	public final function run() {
		if ( $this->is_running_cron_process() ) {
			return;
		}
		set_time_limit( 0 );
		$this->lock_cron_process();
		$this->do_framework_action( 'before_cron_run', $this->get_hook_name() );
		$this->execute();
		$this->do_framework_action( 'after_cron_run', $this->get_hook_name() );
		$this->set_cron_event();
		$this->unlock_cron_process();
	}

	/**
	 * run now
	 */
	public final function run_now() {
		$this->clear_event();
		$this->run();
	}

	/**
	 * execute
	 */
	protected function execute() {

	}

	/**
	 * uninstall
	 */
	public function uninstall() {
		$this->clear_event();
		$this->unlock_cron_process();
	}
}
