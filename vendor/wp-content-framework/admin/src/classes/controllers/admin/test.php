<?php
/**
 * WP_Framework_Admin Classes Controller Test
 *
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Admin\Classes\Controllers\Admin;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Test
 * @package WP_Framework_Admin\Classes\Controllers\Admin
 */
class Test extends Base {

	/**
	 * @return int
	 */
	public function get_load_priority() {
		return $this->is_valid_package('test') && $this->app->test->is_valid() ? $this->apply_filters( 'test_page_priority', $this->app->utility->definedv( 'WP_DEBUG' ) ? 900 : - 1 ) : - 1;
	}

	/**
	 * @return string
	 */
	public function get_page_title() {
		return $this->apply_filters( 'test_page_title', 'Test' );
	}

	/**
	 * post
	 */
	protected function post_action() {
		$action = $this->app->input->post( 'action' );
		if ( method_exists( $this, $action ) && is_callable( [ $this, $action ] ) ) {
			$this->$action();
		}
	}

	/**
	 * @return array
	 */
	public function get_view_args() {
		return [
			'tests' => $this->app->test->get_test_class_names(),
		];
	}

	/**
	 * do test
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function do_test() {
		foreach ( $this->app->test->do_tests() as list( $success, $result ) ) {
			$this->app->add_message( $result, 'test', ! $success, false );
		}
	}
}
